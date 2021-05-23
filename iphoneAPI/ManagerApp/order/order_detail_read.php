<?php 
//读取订单详细信息
    include "../../basic/downloadFileIP.php";
    //权限
    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
    if($TRow = mysql_fetch_array($TResult)){
       $ReadPower=1;
    }
    else{
       $ReadPower=0;
    }
     //$PorderId="201207021701";
    //当前汇率
    $checkCurrency=mysql_query("SELECT Symbol,Rate FROM $DataPublic.currencydata WHERE Estate=1 AND Id>1 ORDER BY Id",$link_id);
     while ($checkCurrencyRow=mysql_fetch_array($checkCurrency)); 
        {
            $TempRate=strval($checkCurrencyRow["Symbol"])."Rate"; 
            $$TempRate=$checkCurrencyRow["Rate"];	
        }
        
     $today=date("Y-m-d");    
    $mySql="SELECT  M.CompanyId,M.OrderDate,M.Operator,M.ClientOrder,S.POrderId,S.OrderPO,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.bjRemark,P.Weight,U.Name AS Unit,C.Forshort,PI.Leadtime,PI.PI 
                        FROM $DataIn.yw1_ordersheet S 
                        LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
                        LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
                        LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
                        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
                        WHERE  S.PorderId='$POrderId'  LIMIT 1";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
                    $OrderPO=$myRow["OrderPO"];
                    $POrderId=$myRow["POrderId"];
                    $ProductId=$myRow["ProductId"];
                    $Forshort=$myRow["Forshort"];
                    $cName=$myRow["cName"];
                    $eCode=$myRow["eCode"];
                    $Qty = $myRow["Qty"];
                    $Unit=$myRow["Unit"];
                    $Price=$myRow["Price"];
                    $Amount=sprintf("%.2f",$Qty*$Price);	
                    $bjRemark=$myRow["bjRemark"];
                    $Remark=$myRow["PackRemark"];
                    $Leadtime=$myRow["Leadtime"];
                    $OrderDate=$myRow["OrderDate"];
                    if ($Leadtime!="" && $myRow["Estate"]>0){
	                   $LeadDay=ceil((strtotime($Leadtime)-strtotime($today))/3600/24);
	                   if ($LeadDay<-1000) $LeadDay="";
                    }
                    else{
	                    $LeadDay="";
                    }
                    
                    $ClientOrder=$myRow["ClientOrder"];
                    if($ClientOrder!=""){
                          $OrderFile="download/clientorder/" . $ClientOrder;
                           $ClientOrder=1;
                    }
                    else{
                        $ClientOrder=0;
	                    $OrderFile="";
                    }
                    
                    $Weight=(float)$myRow["Weight"];
                    $WeightSTR="";
                    $productId=$ProductId;
                      include "../../model/subprogram/weightCalculate.php";
                      if ($Weight>0){
	                       $extraWeight=$extraWeight == "error"?"":$extraWeight+($Weight*$boxPcs); 
	                       $WeightSTR=$Weight>0?"$eCode|$Weight|$boxPcs|$extraWeight":"";
                      }
                      
                    $TestStandard=$myRow["TestStandard"];
                    //检查是否需更改标准图
                    if ($TestStandard==1){
		                    $checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id",$link_id);
						    if($checkteststandardRow = mysql_fetch_array($checkteststandard)){	
						         $TestStandard=9;
						    }
				    }
                    if ($TestStandard*1>0){
                        $ImagePath="download/teststandard/T$ProductId.jpg";
                        $IconImage="download/productIcon/" . $ProductId . ".jpg";
                    }
                    else{
                       $ImagePath=""; 
                    }
                    
                    $Operator=$myRow["Operator"];
                     include "../../model/subprogram/staffname.php";
                    
               $CompanyId=$myRow["CompanyId"];
                include "../subprogram/currency_read.php";//$Rate、$PreChar
                if ($ReadPower==1){
		              /*毛利计算*//////////// 
		             $saleRmbAmount=sprintf("%.3f",$Qty*$Price*$Rate);//转成人民币的卖出金额
		             include "order_Profit.php";
		             $profitRMB2PC=$profitRMB2PC . "%";
		             $profitRMB2=$profitRMB2. "($profitRMB2PC)";
		        }
		         else{
		              $profitRMB2PC="";$profitRMB2="";$profitColor="";$profitbgColor="";
		         }       

	              //下单次数
		           $checkAllQty= mysql_query("
								  SELECT count(*) AS Orders FROM( 
									SELECT S.Id FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.eCode LIKE (SELECT eCode FROM $DataIn.productdata WHERE ProductId='$ProductId' LIMIT 1) GROUP BY OrderPO
									)A
								  ",$link_id);	  
		          $Orders=mysql_result($checkAllQty,0,"Orders");
		          
		          //已出货数量
		            $checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
	            	$ShipQtySum=mysql_result($checkShipQty,0,"ShipQty");
	            	
		          //退货数量
					$eCode=$myRow["eCode"];
					$checkReturnedQty= mysql_query("SELECT SUM(Qty) AS ReturnedQty FROM $DataIn.product_returned WHERE eCode='$eCode'",$link_id);
					$ReturnedQty=mysql_result($checkReturnedQty,0,"ReturnedQty");
					if($ReturnedQty>0 && $ShipQtySum>0){
						//退货百分比
						$ReturnedPercent=sprintf("%.1f",(($ReturnedQty/$ShipQtySum)*1000));
						$ReturnedQty.="(" . $ReturnedPercent . "‰)";
					}else{
						$ReturnedQty="";
					}
					
				//拆分订单
				$splitQty="";$SPOrderId="";$QtyOnTap=0;
                $checkSplit=mysql_query("SELECT S.SPOrderId,Y.Qty FROM $DataIn.yw1_ordersplit S 
                   LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.SPOrderId 
                WHERE S.OPOrderId='$POrderId' LIMIT 1",$link_id);
			 if($splitRow = mysql_fetch_array($checkSplit)){
		            $SPOrderId=$splitRow["SPOrderId"]; 
		            $splitQty="(" . number_format($splitRow["Qty"]) . ")";
		            $QtyOnTap=1;
              }
                        
	           $ShipQtySum=number_format($ShipQtySum);
				$Qty=number_format($Qty);
				$Amount=number_format($Amount,2);
				$Price=sprintf("%0.2f", $Price);
				
				//下单日期
				if ($myRow["Estate"]==0){
					//已出货数量
		            $checkShipDate= mysql_query("SELECT M.Date FROM $DataIn.ch1_shipsheet  S 
		            LEFT JOIN $DataIn.ch1_shipmain  M ON M.Id=S.Mid 
		            WHERE S.POrderId='$POrderId'",$link_id);
	            	$ShipDate=mysql_result($checkShipDate,0,"Date");
	            	$odDays=floor((strtotime($ShipDate)-strtotime($OrderDate))/3600/24);
				}
				else{
				      $odDays=floor((strtotime($today)-strtotime($OrderDate))/3600/24);
				}
				//对备料时间进行排序
             $blDateResult=mysql_query("SELECT DISTINCT S.StockId,B.Date,M.Name FROM $DataIn.yw9_blmain B 
             LEFT JOIN $DataPublic.staffmain M ON M.Number=B.Operator
             LEFT JOIN $DataIn.ck5_llsheet S ON S.Pid=B.Id WHERE S.StockId LIKE '%$POrderId%' ORDER BY B.Date",$link_id);
             if($blRow=mysql_fetch_array($blDateResult)){
                  $j=0;$k=1;
                     do{
                          $blStockId=$blRow["StockId"];
                          $blName=$blRow["Name"];
                          $blDate=$blRow["Date"];
                          if($j==0){
                                            $ValueArray[$j]=array(0=>$blStockId,1=> $blName,2=>$blDate,3=>$k);
                                            }
                          else {
                                       if($tempDate==$blDate)$ValueArray[$j]=array(0=>$blStockId,1=> $blName,2=>$blDate,3=>$k);
                                        else{$k++; $ValueArray[$j]=array(0=>$blStockId,1=> $blName,2=>$blDate,3=>$k);}
                                  }
                          $tempDate=$blDate;
                         $j++;
                         }while($blRow=mysql_fetch_array($blDateResult));
                   }
				//产品BOM信息
				$bomArray=array();
				$OrderSignColor=1;//订单状态色：有未下采购单，则为白色
				$StockResult = mysql_query("SELECT 
					S.Mid,S.StockId,S.StuffId,S.OrderQty,S.AddQty,S.FactualQty,OP.Property, 
					M.Date,A.StuffCname,A.TypeId,A.Picture,B.Name,C.Forshort,C.Currency,ST.mainType,MT.TypeColor,K.tStockQty 
					FROM $DataIn.cg1_stocksheet S
					LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
					LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
					LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
					LEFT JOIN $DataPublic.staffmain B ON B.Number=S.BuyerId
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
			        LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
			        LEFT JOIN $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2
					WHERE S.POrderId='$POrderId' ORDER BY ST.mainType,S.StockId ",$link_id);
		
					if ($StockRows = mysql_fetch_array($StockResult)) {
							  do{ 
								    $Mid=$StockRows["Mid"];
									$StockId=$StockRows["StockId"];
						            $StuffId=$StockRows["StuffId"];
									$BuyDate=$StockRows["Date"];
									$StuffCname=$StockRows["StuffCname"];
									$Forshort=$StockRows["Forshort"];
									$Buyer=$StockRows["Name"];
									$OrderQty=$StockRows["OrderQty"];
									$FactualQty=$StockRows["AddQty"]+$StockRows["FactualQty"];
									$Picture=$StockRows["Picture"];
									$TypeId=$StockRows["TypeId"];
									$mainType=$StockRows["mainType"];
									$TypeColor=$StockRows["TypeColor"];
						            $tStockQty=$StockRows["tStockQty"];  
						            
						            //检查采购单锁定状态
						            $cgLocks="";
								    $CheckStockSql=mysql_query("SELECT * FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1",$link_id);
									 if(mysql_num_rows($CheckStockSql)>0) {      
											   $OrderSignColor=2;	 //采购锁定，蓝色
											   $cgLocks=2;
									 }
									 
									 //需采购订单
									 $cgDays="";$cgColor="";$llQty="";
									 if ($mainType<2){
										 $checkllQty=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id));
										     $llQty=$checkllQty["llQty"];
											 $llQty=$llQty==""?0:$llQty;
											 if($llQty>$OrderQty){//领料总数大于订单数,提示出错
												     $llBgColor="#FF0000";
												    }
											else{
												if($llQty==$OrderQty){//刚好全领，绿色
													   $llBgColor="#009900";
													    }
												else{				//未领完，黄色
													    $llBgColor="#FF6633";
													  }
											}
											$llEstate=$checkllQty["llEstate"];
											//$llQty=number_format($llQty);
											//$llQty=$llEstate>0?"★$llQty":$llQty;
											
											if ($Mid>0){
												$CheckRkSql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS rkQty,MAX(M.Date) AS rkDate FROM $DataIn.ck1_rksheet S
												LEFT JOIN $DataIn.ck1_rkmain M ON  S.Mid=M.Id 
												WHERE StockId ='$StockId'",$link_id));
												$rkQty=$CheckRkSql["rkQty"];
												if ($rkQty==$FactualQty){
													$rkDate=$CheckRkSql["rkDate"];
													 $cgDays=floor((strtotime($rkDate)-strtotime($BuyDate))/3600/24);
													 $cgColor="#009900";
												}
												else{
										            $cgDays=floor((strtotime($today)-strtotime($BuyDate))/3600/24);
										         } 
											}
											else{
											    if ($FactualQty>0)  {
											         $cgColor="#FFCC00";
											     }
											     else{
												     $mainType=7;
											     }
											}
											$tStockColor=$tStockQty>=($OrderQty-$llQty)?"":"#FF0000";
											$llQty=number_format($llQty);
									        $llQty=$llEstate>0?"★$llQty":$llQty;
									        if ($StockRows["Property"]==2)  $mainType=2;
								    }
								    else{
									     if ($mainType==3){
									         //已完成的工序数量
											$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId' AND C.TypeId='$TypeId'",$link_id));
											$scQty=$CheckscQty["scQty"]==""?0:$CheckscQty["scQty"];
											$llBgColor=$scQty==$OrderQty?"#009900":"#FF6633";
											$llQty=number_format($scQty);
									     }
									     $Buyer="";$Forshort="";
								    }
								   $blorder="";
		                           for($k=0;$k<count($ValueArray);$k++){//备料时间排序
		                           if($ValueArray[$k][0]==$StockId && $ValueArray[$k][3]!=""){
		                                    $blorder="(". $ValueArray[$k][3].")"; break;
		                                   }
		                           }
								   $OrderQty=number_format($OrderQty);
								   $tStockQty=number_format($tStockQty);
								   $PicutreImage=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
								    $ForshortColor=$StockRows["Currency"]==2?"#FF0000":"";
								    
							   $PropertySTR=""; 
							     if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
								        $PropertySTR="c1";
								 }
							    else{
								   $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId=$StuffId ORDER BY Property",$link_id);
							       while($PropertyRow=mysql_fetch_array($PropertyResult)){
							                $Property=$PropertyRow["Property"];
							                  if($Property>0) $PropertySTR=$PropertySTR==""?$Property:"|$Property";
							        }
                                }

									$bomArray[]=array(
									       "StockId"=>"$StockId",
									       "Index"=>array("Text"=>"$cgDays","Value"=>"$mainType","Locks"=>"$cgLocks","Color"=>"$cgColor"),
									       "Picture"=>array("Value"=>"$Picture","File"=>"$PicutreImage"), 
									       "BgColor"=>array("Color"=>"$TypeColor","Opacity"=>"1.0"),
									       "cName"=>array("Text"=>"$StuffCname","rtIcon"=>"$PropertySTR"),
									       "OrderQty"=>array("Text"=>"$OrderQty"),
									       "tStockQty"=>array("Text"=>"$tStockQty","Color"=>"$tStockColor"),
									       "Buyer"=>array("Text"=>"$Buyer"),
                                           "LlQty"=>array("Text"=>"$llQty$blorder","Color"=>"$llBgColor"),
                                           "Provider"=>array("Text"=>"$Forshort","Color"=>"$ForshortColor")
									 ); 
									 
								}while($StockRows = mysql_fetch_array($StockResult));
						}
						
					    if ($OrderSignColor==1){
						    //工序总数
							$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
							FROM $DataIn.cg1_stocksheet G
							LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
							LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
							WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
							$gxQty=$CheckgxQty["gxQty"];
							//已完成的工序数量
							$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId'",$link_id));
							$scQty=$CheckscQty["scQty"];
				
							if($gxQty!=$scQty){
								$OrderSignColor=3;//黄色
								}
				 }
				 
				 //锁定订单
				 $Locks="";
				$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=2",$link_id);
				if(mysql_num_rows($checkExpress)>0){
					$OrderSignColor="4";//红色
					$Locks=1;
				}
				//$Locks=$Locks==""?$cgLocks:$Locks;
						             
	    		$jsonArray= array(
	    		      "Id"=>"$POrderId",
	    		     "cName"=>array("Text"=>"$cName","eCode"=>"$eCode"),
	    		     "Operator"=>array("Text"=>"$Operator"),
	    		     "PO"=>array("Text"=>"$OrderPO","Tag"=>"Image","onTap"=>"$ClientOrder","File"=>"$OrderFile"),
	    		     "Qty"=>array("Text"=>"$Qty$splitQty","Tag"=>"Split","onTap"=>"$QtyOnTap","Args"=>"$SPOrderId"),
	    		     "Price"=>array("Text"=>"$PreChar$Price"),
	    		     "PI"=>array("Text"=>"$Leadtime","Tag"=>"PIDate","Days"=>"$LeadDay"),
	    		     "Icon"=>array("Text"=>"$odDays","oSign"=>"$OrderSignColor","Locks"=>"$Locks",
	    		                   "Tag"=>"Image","onTap"=>"$TestStandard","File"=>"$IconImage"),
	    		     "Picture"=>array("Value"=>"$TestStandard","Tag"=>"Image","File"=>"$ImagePath","Args"=>"$WeightSTR"),
	    		     "Profit"=>array("Text"=>"¥$profitRMB2","Value"=>"$profitRMB2PC"),
	    		     "oProfit"=>array("Text"=>"¥$GrossProfit"),
	    		     "Shiped"=>array("Text"=>"$ShipQtySum ($Orders)","onTap"=>"$Orders","Tag"=>"Shiped","Args"=>"$ProductId"),
	    		     "Offer"=>array("Text"=>"$bjRemark"),
	    		     "Remark"=>array("Text"=>"$Remark"),
	    		     "BOM"=>$bomArray
	    		);      
      }    
?>