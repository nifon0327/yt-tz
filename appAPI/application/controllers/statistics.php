<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends MC_Controller {
    public $Mc_StorageValue   =0;//暂存值
    public $Mc_debtValue    = 0;
    public $Mc_TotalValue   = 0;
     
    public function index()
	{
	     $data['jsondata']=array('status'=>'','message'=>'','rows'=>array());
		 $this->load->view('output_json',$data);
	}
	
	//测试用
	public function get_mysql(){
		 $this->load->model('StatisticsModel');
		 echo $this->StatisticsModel->get_banksql();
	}
	
	//期数列表
	public function periods()
	{
	    $startDate=$this->config->item('system_opened');
	    $startMonth = date('Ym',strtotime($startDate));
	    $endDate = date('Y-m-01');
	    $endMonth = date('Ym',strtotime($endDate));
	    
	    $dataArray=array();
	    do{
	          $Month = date('Y-m',strtotime($endDate));
	          $dataArray[]=array(
	                 'id'       =>$Month,
	                 'name' =>$endMonth. '期'
	          ); 
	        $endDate = date('Y-m-d',strtotime("$endDate -1 month")); 
	        $endMonth = date('Ym',strtotime($endDate));
	     }while($startMonth<=$endMonth);
	     
	     $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$dataArray);
		 $this->load->view('output_json',$data);

	}
	
	public function main()
	{
	     $params = $this->input->post();
	     $Month  = element('month',$params,date('Y-m'));
	     
	     $this->load->model('StatisticsModel');
	     $rowArray = $this->StatisticsModel->get_main_menus();
	     $rownums =count($rowArray);
		 $dataArray=array();
		
		 for ($i = 0; $i < $rownums; $i++) 
		 {
		      $rows =$rowArray[$i];
		      
		      $subArray = array();
		      
		      $col2Value = ''; $arrowSign=0;
		      if ($rows['CallMethod']!=''){
		          $this->Mc_StorageValue=0;
			      $subArray = $this->$rows['CallMethod']($rows['FirstIds'],$Month);
			      
			      $col2Value = round($this->Mc_StorageValue);
			      if ($Month==date('Y-m')){
				      if ($rows['IncomeSign']==1){
					      $arrowSign = $col2Value>$rows['TotalValue']?1:($col2Value<$rows['TotalValue']?-1:0);
				      }else{
					        if ($rows['IncomeSign']==2){
					             $arrowSign = $col2Value>$rows['TotalValue']?-1:($col2Value<$rows['TotalValue']?1:0);
				            }
				      }
			      }
			      
			      if ($rows['TotalDate']!=$this->Date &&  $Month==date('Y-m'))
			      {
				       $this->StatisticsModel->update_totalvalue($rows['Id'],$col2Value);
			      }
			      $col2Value = $col2Value>0?'¥' . number_format($col2Value):''; 
		      }
		      
		      $dataArray[]=array(
								'tag'        =>'stHead',
								'method' =>'segment',
								'Id'           =>$rows['Id'],
								'showArrow'  =>$rows['IncomeSign']>0?'1':'',
								'open'            =>count($subArray)>0?1:0,
								'IncomeSign' =>$rows['IncomeSign'],
								'arrowImg'     =>'UpAccessory_gray',
							   'arrowSign'     =>$arrowSign,
								'title'      =>'   ' . $rows['Name'],
								'col1'       =>'',
								'col2'       =>"$col2Value",
								'data'      =>$subArray
							   );
		 }
		 
		 $PorfitValue = $this->Mc_TotalValue - $this->Mc_debtValue;
		  $footerArray=array(
								'tag'        =>'Footer',
								'val1'       =>'¥'. number_format($this->Mc_debtValue),
								'val2'       =>'¥'. number_format($PorfitValue),
								'total'      =>'¥'. number_format($this->Mc_TotalValue)
							   );
		 
		 $marr =explode('-', $Month);
		 
		 $navTitle=$this->versionToNumber($this->AppVersion)>431?'资产负债表|(' . $marr[1]. '期)':'资产负债表';
	     $data['jsondata']=array('status'=>'','message'=>'','rows'=>$dataArray,'footer'  =>$footerArray,'navTitle'=>$navTitle );
		 $this->load->view('output_json',$data);
	}
	
	//货币存款
	function get_bank_amount($FirstIds='',$Month='')
	{
		   $this->load->model('MyBankinfoModel');
		   $this->load->model('StatisticsModel');
		   
		   $dataArray = array();
		   $totalAmount = 0;
		   $records = $this->MyBankinfoModel->get_bankinfo();
		   foreach($records as $rows){
		         $BandId = $rows["Id"];
			     $bankrecords = $this->StatisticsModel->get_bank_amount($BandId,$Month,0);
			      $titleImg =$this->MyBankinfoModel->get_bank_logo($BandId);
			     foreach($bankrecords as $bankrows){
			        $Amount = $bankrows['TotalAmount'];
			        $PreChar =  $bankrows['PreChar'];
			        $Rate = $bankrows['Rate'];
				     if ($Amount!=0){
				     
				          $totalAmount+=$Amount * $Rate;
				         
					      $dataArray[]=array(
					               'tag'          =>'item',
									'method' =>'segment',
									'Id'           =>$rows['Id'],
									'title'        =>'  ' . $rows['ShortTitle'],
									'titleImg'  =>$titleImg,
									'value'     => $PreChar . number_format($Amount),
									//'beling'    =>array('beling'=>'1'),
							        //'changeSign' =>'-1',
					       );
				     }
			     }
		   }
		   
		   $this->Mc_StorageValue=$totalAmount; 
		   $this->Mc_TotalValue+=$totalAmount;
		   return $dataArray;
	}
	
	//应收款
	function get_receivable($FirstIds='',$Month=''){
	
       $this->load->model('ChartColorModel');
	   $this->load->model('StatisticsModel');
	   $this->load->model('AcFirsttypeModel'); 
	   
	   $dataArray = array();
	   $totalAmount = 0;
	   $OverAmount = 0;
	   $FirstIdArray=explode(',', $FirstIds);
	   
	   foreach($FirstIdArray as $FirstId){
		 switch($FirstId){
		     case 1122://未收货款
					   $records = $this->StatisticsModel->get_accounts_receivable($FirstId,$Month);
					  
					   foreach($records as $rows){
					          $RmbAmount = $rows['RmbAmount'];
					          $OverAmount+=$rows['OverAmount'];
					          $totalAmount+=$RmbAmount;
					   }
					   
					   $subAmount = 0;
					   $n = 1;
					   $subArray = array();
					   $pieArray  = array();
					   $sumPercent = 0;
					   $chartColors = array('COLOR:','#61a6ce','#85b9d8','#d9e9f3','#f1f1f1');
					   foreach($records as $rows){
					       $CompanyId = $rows['CompanyId'];
					       $Forshort      = $rows['Forshort'];
					       $RmbAmount = $rows['RmbAmount'];
			
					      // $chartColor =  $this->ChartColorModel->get_chartcolor($CompanyId);
					       $chartColor = $chartColors[$n];
					       $percent = round($RmbAmount/$totalAmount*100);
					       $sumPercent+=$percent;
					       $subAmount+=$RmbAmount;
					       $subArray[]=array(
					                                   'title'=>"$Forshort",
					                                   //'percent'=>"$percent%",
					                                   'percent'=>array(
																	'isAttribute'=>'1',
																	'attrDicts'  =>array(
																      array('Text'=>"$percent",'FontSize'=>"15"),
															          array('Text'=>'%','FontSize'=>"9")
																	   )
															),
					                                   'value'=>'¥' . number_format($RmbAmount),
					                                   'color'=>"$chartColor"
					                              );
					       $pieArray[] = array('value'=>"$percent",'color'=>"$chartColor");
						   $n++;
					       if ($n>3) break;    
					}
					
					if ($n>3){
					      $otherPercent  =100-$sumPercent;
					      $otherAmount = $totalAmount-$subAmount;
					     $lightgray  =$this->colors->get_color('lightgray2');
						  $subArray[]=array(
					                                   'title'=>"其他客人",
					                                  // 'percent'=>"$otherPercent%",
					                                   'percent'=>array(
															'isAttribute'=>'1',
															'attrDicts'  =>array(
														      array('Text'=>"$otherPercent",'FontSize'=>"15"),
													          array('Text'=>'%','FontSize'=>"9")
															   )
															),
					                                   'value'=>'¥' . number_format($otherAmount),
					                                   'color'=>"$lightgray"
					                              );
					      $pieArray[] = array('value'=>"$otherPercent",'color'=>"$lightgray");                     
					}
					
					$overPrecent =$totalAmount>0?round($OverAmount/$totalAmount*100):0;
					
				    $pie2 = array(
					    array('value'=>'' . $overPrecent,'color'=>'#FF0000'),
					    array('value'=>'' . 100-$overPrecent,'color'=>'#clear')
				    );
			
					   $dataArray[]=array(
							               'tag'          =>'chart',
											'method' =>'receivable',
											'open'      =>'0',
											'animate'  =>'1',
											'crowdId'  =>$FirstId,
											'onTap'   =>array(                      //连接旧代码，以后需修改
											                     'modelId' =>'122',
											                     'title'         =>'应收',
											                    ),
											'Id'           =>$FirstId,
											'month'   =>$Month,
											'title'        =>'应收  ¥' . number_format($totalAmount),
											'percent'  =>array(
																	'isAttribute'=>'1',
																	'attrDicts'  =>array(
																      array('Text'=>"$overPrecent",'FontSize'=>"15"),
															          array('Text'=>'%','FontSize'=>"9")
																	   )
															),
											'subdata' =>$subArray,
											'pie'         =>$pieArray,
										    'innerpie'=>$pie2
							       );
			    break;
		  default:
	              
				  $Amount  = $this->StatisticsModel->get_nopay_amount($FirstId,$Month);
				  $types       = $this->AcFirsttypeModel->get_records($FirstId);
				  $title = $types['Name'];
				  
				  $dataArray[]=array(
							               'tag'          =>'item',
											'method' =>'segment',
											'open'      =>'0',
											'animate'  =>'1',
											'Id'           =>$FirstId,
											'crowdId' =>$FirstId,
											'title'        =>$title,
											'value'     => '¥' . number_format($Amount)
							       );
					$totalAmount+=$Amount;
			        break;
			 }
		}
		
	    $this->Mc_StorageValue=$totalAmount; 
		$this->Mc_TotalValue+=$totalAmount;
        return $dataArray;		 
	}
	
	//库存
	function get_stockamounts($FirstIds='',$Month=''){
	   
	    $this->load->model('CkrksheetModel'); 
	    $this->load->model('StatisticsModel');
	    $this->load->model('AcFirsttypeModel'); 
	    
	    $FirstIdArray=explode(',', $FirstIds);
	   
	   foreach($FirstIdArray as $FirstId){
				 switch($FirstId){
				     case 1403://原材料
				           if ($Month!='' && $Month!=date('Y-m')){
				                /*
				                $records = $this->CkrksheetModel->get_stock_month_amount($Month,'all');
				                 $totalQty         = round($records['Qty']);
						         $totalAmount = round($records['Amount']);
						         $records = null;
						         $dataArray[]=array(
										               'tag'          =>'item',
														'method' =>'stock',
														'Id'           =>$FirstId,
														'title'        =>'原材料',
														'value'     => '¥' . number_format($totalAmount)
										       );
							   */
							   $totalAmount=$orderAmount=$M1Amount=$M3Amount=0;
							   $records = $this->StatisticsModel->get_statistics_data($FirstId,$Month);
							   if ($records!=""){
								   $totalAmount = round($records['TotalValue']);
							       $otherValue    = $records['OtherValue'];

								   if ($otherValue!=""){
									    $ovals = explode("|", $otherValue);
									    if (count($ovals)==3){
										     $orderAmount =round($ovals[0]);
									         $M1Amount =round($ovals[1]);
									         $M3Amount =round($ovals[2]);
									    }
									 }
							   }		       
				           }else{
					            $records = $this->CkrksheetModel->get_stock_amount('all');
						        $totalQty         = round($records['Qty']);
						        $totalAmount = round($records['Amount']);
						        $records = null;
								        
							    $records = $this->CkrksheetModel->get_order_amount('all','');
						        $orderQty        = round($records['OrderQty']);   //订单需求数量
						        $orderAmount= round($records['Amount']);      //订单需求金额
						        $M1Amount   = round($records['M1Amount']);//一个月内未有下单
						        $M3Amount   = round($records['M3Amount']);//三个月内未有下单
						}        
				        $M0Amount = $totalAmount-$M1Amount;
				        $M1Amount = $M1Amount - $M3Amount;
				        
				        $M1Percent  = $totalAmount>0?round($M1Amount/$totalAmount*50):0;
				        $M3Percent  = $totalAmount>0?round($M3Amount/$totalAmount*50):0;
				        $M0Percent  = 50 - $M1Percent - $M3Percent;
				        
				        $orderPercent = $totalAmount>0?round($orderAmount/$totalAmount*50):0;
				        $orderPercent2 =$totalAmount>0?round($orderAmount/$totalAmount*100):0;
				        
				        $subArray = array();
				        $pieArray  = array();
				       
				       $valArray[]  = array('title'=>'1〜3个月','percent'=>2*$M1Percent,'value'=>$M1Amount,   'color'=>'#d9e9f3');
				       $valArray[]  = array('title'=>'>3个月',   'percent'=>2*$M3Percent,'value'=>$M3Amount,   'color'=>'#FF0000');
				       $valArray[]  = array('title'=>'有单' ,       'percent'=>$orderPercent2,'value'=>$orderAmount,'color'=>'#04db32');
				       $valArray[]  = array('title'=>'<1个月',    'percent'=>2*$M0Percent,'value'=>$M0Amount,    'color'=>'#85b9d8');
				       
				       foreach($valArray as $vals){
				            $subArray[]=array(
						                                   'title'=>$vals['title'],
						                                   'percent'=>array(
																		'isAttribute'=>'1',
																		'attrDicts'  =>array(
																	      array('Text'=>'' . $vals['percent'],'FontSize'=>"15"),
																          array('Text'=>'%','FontSize'=>"9")
																		   )
																),
						                                   'value'=>'¥' . number_format($vals['value']),
						                                   'color' =>$vals['color']
						                              );                  
						 }
						 
					    $pieArray = array(
							     array('value'=>'' . $M0Percent,'color'=>'#85b9d8'),
							     array('value'=>'' . $M1Percent,'color'=>'#d9e9f3'),
							     array('value'=>'' . $M3Percent,'color'=>'#FF0000'),
							     array('value'=>'50','color'=>'#clear')
					    );
					    
					   $pie2Array = array(
						       array('value'=>'' . $orderPercent,'color'=>'#04db32'),
						       array('value'=>'' . 100-$orderPercent,'color'=>'#clear')
					    );
				
			      
					   $dataArray[]=array(
							               'tag'          =>'chart1',
											'method' =>'stock',
											'title'        =>'原材料',
											'Id'           =>$FirstId,
											'value'      =>'¥' . number_format($totalAmount),
											'subdata' =>$subArray,
											'pie'         =>$pieArray,
										    'innerpie'=>$pie2Array
							       );
	
							break;
					default:
					          if ($Month!='' && $Month!=date('Y-m')){
					                $Amount=0;
							        $records = $this->StatisticsModel->get_statistics_data($FirstId,$Month);
							      if ($records!=""){
								      $Amount = round($records['TotalValue']);
								   }
					          }else{
						            $Amount  = $this->StatisticsModel->get_totals_amount($FirstId);
					          }
					          if ($FirstId==14030){
						            $title = '在制品';
					          }else{
						          $types       = $this->AcFirsttypeModel->get_records($FirstId);
							     $title = $types['Name'];
					          }
							  
							  $dataArray[]=array(
										               'tag'          =>'item',
														'method' =>'segment',
														'Id'           =>$FirstId,
														'title'        =>$title,
														'value'     => '¥' . number_format($Amount)
										       );
								$totalAmount+=$Amount;
					       break;
				    }
		 }
					       	       	       
	       $this->Mc_StorageValue=$totalAmount;
	       $this->Mc_TotalValue+=$totalAmount;
		   return $dataArray;
	}
	
	//非流动资产
	function get_fixedassets($FirstIds='',$Month='')
	{
	   //1601,1511,1701,1603,1801
	   $this->load->model('AcFirsttypeModel'); 
	   
	   $totalAmount = 0;
	   $dataArray= array();
	   $FirstIdArray=explode(',', $FirstIds);
	   
	   foreach($FirstIdArray as $FirstId){
	           $Amount  = $this->StatisticsModel->get_totals_amount($FirstId,$Month);
	           $types       = $this->AcFirsttypeModel->get_records($FirstId);
	            $title = $types['Name'];
			   $dataArray[]=array(
							               'tag'          =>'item',
											'method' =>'segment',
											'Id'           =>$FirstId,
											'title'        =>$title,
											'value'     => '¥' . number_format($Amount)
						  );
			  $totalAmount+=$Amount;
		}
		
	   $this->Mc_StorageValue=$totalAmount;
	   $this->Mc_TotalValue+=$totalAmount;		  
	   return $dataArray;
	}
	
	//应付
	function get_payable($FirstIds='',$Month='')
	{
	     $this->load->model('StatisticsModel');
	     $this->load->model('AcFirsttypeModel'); 
	    
	    $totalAmount =0;
	    $FirstIdArray=explode(',', $FirstIds);
	   foreach($FirstIdArray as $FirstId){
	        
              $Amount  = $this->StatisticsModel->get_nopay_amount($FirstId,$Month);
			  $types       = $this->AcFirsttypeModel->get_records($FirstId);
			  $title = $types['Name'];
			  
			  $method = 'segment';
			  $onTaps  = array();
			  
			  if ($FirstId==2202){
			     $method = 'receivable';
				 $onTaps =array(                      //连接旧代码，以后需修改
				                     'modelId' =>'11830',
				                     'title'         =>'应付'
								);
			  }
			  $dataArray[]=array(
						               'tag'          =>'item',
										'method' =>$method,
										'onTap'   => $onTaps,
										'Id'           =>$FirstId,
										'title'        =>$title,
										'value'     => '¥' . number_format($Amount)
						       );
				$totalAmount+=$Amount;
		}
		$this->Mc_StorageValue=$totalAmount;
	    $this->Mc_debtValue+=$totalAmount;
	       					
	   return $dataArray;
	}
	
	//利润表
	function get_profitstatement($FirstIds='',$Month=''){
	
	   $opendate=$this->config->item('system_opened');
	   $openYear = date('Y',strtotime($opendate));
	   $thisYear = date('Y');
	   
	   $FirstId = '0';
	   $dataArray =array();
	    for ($year = $openYear; $year <=$thisYear; $year++){
		    
		     $dataArray[]=array(
					                'tag'         =>'item',
									'method' =>'report',
									'onTap'   =>array(                      //连接旧代码，以后需修改
									                     'modelId' =>'125',
									                     'title'         =>'损益表',
									                     'info'         =>'_new|NoPay'
									                    ),
									'Id'           =>$FirstId,
									'title'        =>'' . $year,
									'value'     => '¥' . number_format(0)
				  );
	    }
	    return $dataArray;
	}
	
	//应收明细
	function  receivable()
	{
	     $params = $this->input->post();
	     $FirstId  = element('Id',$params,'0');
	     $Month  = element('month',$params,'');
	     $upTag  = element('upTag',$params,'');
	     
	     $this->load->model('StatisticsModel');
	     
	     $dataArray=array();
	     switch($FirstId){
	         case 1122:
	                  $totalAmount = 0;
	                  $records = $this->StatisticsModel->get_accounts_receivable($FirstId,$Month);

					   foreach($records as $rows){
					          $RmbAmount = $rows['RmbAmount'];
					          $totalAmount+=$RmbAmount;
					   }
					   
					   $this->load->model('TradeObjectModel');
	              	   $logoPath = $this->TradeObjectModel->get_logo_path();
		
					   foreach($records as $rows){
					       $CompanyId = $rows['CompanyId'];
					       $Logo = $rows['Logo'];
					       $RmbAmount = $rows['RmbAmount'];
					       $percent = $totalAmount>0?round($RmbAmount/$totalAmount*100):'';
					      // $percent = $percent==0?'':$percent;
					       $dataArray[]=array(
					                'tag'         =>'clientcell',
									'method' =>'receivable_client',
								    'crowdId' =>$FirstId . '-' . $CompanyId,
								    'Id'           =>$CompanyId,
								    'title'       =>$rows['Forshort'],
								    'titleImg' =>$Logo==''?'':$logoPath . $Logo,
								    'deadline'=>'payed_' . $rows['PayMode'],
								    'starImg'  =>'',
								     'percent' =>'' . $percent,
								    'value'     =>  $rows['PreChar'] . number_format($rows['Amount'])
				          );
					       
					   }

	            break;
	     }
	     
	      $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$dataArray );
		   $this->load->view('output_json',$data);
	}
	
}
