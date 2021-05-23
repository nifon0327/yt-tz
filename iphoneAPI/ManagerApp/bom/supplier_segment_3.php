<?php
//已下单记录

$d=strtotime("-12 Months");
$CheckMonth=date("Y-m",$d);   

$SearchRows=" AND DATE_FORMAT(M.Date,'%Y-%m')>='$CheckMonth' ";

$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"210,32,48, 15","Align"=>"L"));
                         
 //图标设置           
 $IconSet=array("Col2"=>array("Name"=>"scdj_11","Frame"=>"113.5,35,10,10"),
                          "Col3"=>array("Name"=>"cgdj_1","Frame"=>"200,35,10,10")
                          );
 

{
		       $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
                $curWeeks=$dateResult["CurWeek"];
                
		        $TotalQty=0;$TotalAmount=0; $TotalOverQty=0;$TotalOverCount=0;
		        /*
			        
			       
			        
		        */
		        
		        $sql = "SELECT 
			           DATE_FORMAT(M.Date,'%Y-%m') Month,
			           COUNT(*) AS Counts,
			            SUM(A.Qty) AS Qty,
		               SUM((A.Qty)*S.Price*D.Rate) AS Amount,
			           SUM(if((A.Qty-A.rkQty)>0,1,0)) AS noCounts,
		               SUM(A.Qty-A.rkQty) AS noQty,
		               SUM(if(A.Qty>S.OrderQty,1,0)) AS abQty,
		               SUM((A.Qty-A.rkQty)*S.Price*D.Rate) AS noAmount
					     FROM (
									    SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE S.Mid>0 AND M.CompanyId NOT IN (getSysConfig(106)) $SearchRows GROUP BY S.StockId 
									  
									)A 
						LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=A.StockId
							LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId  
						LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
						WHERE 1 $SearchRows GROUP BY  Month ORDER BY Month DESC";
						
						//echo($sql);
		        $myResult=mysql_query($sql,$link_id);
   $CompanyQty=0;$CompanyAmount=0;$CompanyCount=0;
		               $OverQty=0; $OverAmount=0;$OverCount=0;
		               $TotalOverQty=0; $TotalOverAmount=0;$TotalOverCount=0;
		               $hidden=1;
				 if($myRow = mysql_fetch_array($myResult)){
		            
		              do{

		                     $Month=$myRow["Month"];
		                     $CompanyQty=$myRow["Qty"];
		                     $CompanyAmount=$myRow["Amount"];
		                     $CompanyCount=$myRow["Counts"];
		                      $COUNT_1+=$CompanyCount;
		                      
		                     $OverCount=$myRow["noCounts"];
		                     $OverQty=$myRow["noQty"];
		                     $OverAmount=$myRow["noAmount"];
		                     
		                       $TotalQty+=$CompanyQty;
				               $TotalAmount+=$CompanyAmount;
				               $TotalOverQty+=$OverQty;
				               $TotalOverAmount+=$OverAmount;
				               $TotalOverCount+=$OverCount;
						               
					            $OverPre_2=$CompanyAmount>0?round($OverAmount/$CompanyAmount*100):0;
                                $OverPre_1=100-$OverPre_2;
                            //   $LegendArray=array("$OverPre_1","$OverPre_2");
                   
                               $AddRow=array();
					           if ($OverQty>0){
					                 $OverQty=number_format($OverQty);
					                 $OverAmount=number_format($OverAmount);
						            $AddRow=array(
											          array("Text"=>"$OverQty","Color"=>"#FF0000","Copy"=>"Col1",
																		       "RLText"=>"($OverCount)","RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
								                     array("Text"=>"¥$OverAmount","Copy"=>"Col3","Color"=>"#FF0000")
								                   );
					           }
					           
					            //统计交货准时率
							    $PuncSelectType=2;
							    $checkMonth=date("Y-m");
						     //    include "submodel/cg_punctuality.php";
					           
					           $CompanyQty=number_format($CompanyQty);
					           $CompanyAmount=number_format($CompanyAmount);
					           
					           $abQty = $myRow['abQty'];
					           $abQty = $abQty>0?$abQty:'';

					           $upMargin = count($AddRow) > 0 ? 7 : 0;
					           
					           
					           $titleObj = array("Text"=>"$Month","light"=>"13","Margin"=>"0,$upMargin,0,0");
					           
							    $timeMon = strtotime($Month);
							    $titleObj['isAttribute']='1';
							    $titleObj['attrDicts']=array(
								   		array('Text'    =>strtoupper(date('M', $timeMon)) ,
								   			  'FontSize'=>'14',
								   			  'FontWeight'=>'bold',
								   			  'Color'   =>"#3b3e41"),
								   		array('Text'    =>"\n".date('Y', $timeMon),
								   			  'FontSize'=>'9',
								   			  'Color'   =>"#727171")
								   		);
					           
						       $tempArray=array(
								                      "Id"=>"$CompanyId",
								                      //"RowSet"=>array("bgColor"=>"#EEEEEE"),
								                      "onTap"=>array("Target"=>"ordered","Args"=>"$Month"),
								                      "Title"=>$titleObj,
'Col2'=>array('Text'=>$abQty.'', 'Color'=>'#FF0000','light'=>'12',"Margin"=>"-128,$upMargin,0,0",'Align'=>'L'),
   "Col1"=>array("Text"=>"$CompanyQty","light"=>"13","RLText"=>"($CompanyCount)",
														                       "RLColor"=>"#BBBBBB","Margin"=>"-30,0,30,0"),
								                       "Col3"=>array("Text"=>"¥$CompanyAmount","light"=>"13","Margin"=>"-10,0,10,0"),
								                 //      "Legend"=>$LegendArray,
								                       "AddRow"=>$AddRow
								                   );
					       $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"segment","onTap"=>"1","hidden"=>"$hidden","data"=>array(),"Layout"=>$Layout,"IconSet"=>$IconSet); 
					        
					        $dataArray=array();
						   
		               }while($myRow = mysql_fetch_array($myResult));
		           
		           		    }
		}
				
$jsonArray=array("data"=>$jsondata); 
?>