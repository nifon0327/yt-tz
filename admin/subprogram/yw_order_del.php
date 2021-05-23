<?php   
/*
更新:订单删除
*/
exit;
if($Id!=""){
		//读取删单信息
		$sheetResult = mysql_query("SELECT * FROM $DataIn.yw1_orderdeleted 
		WHERE Id='$Id' ORDER BY Id DESC",$link_id);
		if($sheetRow = mysql_fetch_array($sheetResult)){
			$OrderNumber=$sheetRow["OrderNumber"];
			$OrderPO=$sheetRow["OrderPO"];
			$POrderId=$sheetRow["POrderId"];
			$ProductId=$sheetRow["ProductId"];
			$Qty=$sheetRow["Qty"];
	        $Price=$sheetRow["Price"];
			$PIResult=mysql_query("SELECT PI.Id AS Pid 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
			WHERE S.POrderId='$POrderId'",$link_id);
			$PI=mysql_result($PIResult,0,"Pid");
			
			
			//删除订单:订单非待出或已出状态
			$delOrderSql="DELETE A,B 
			FROM $DataIn.yw1_ordersheet A
			LEFT JOIN $DataIn.sc1_cjtj B ON B.POrderId=A.POrderId
			 WHERE A.POrderId='$POrderId' 
			 AND A.Estate!='4' 
			 AND A.Estate!='0' 
			 AND (A.POrderId not in (select PorderId from $DataIn.ch1_shipsheet))";
			 //echo $delOrderSql;
			$delOrderRresult = mysql_query($delOrderSql);
			if($delOrderRresult && mysql_affected_rows()>0){
				$Log.="&nbsp;&nbsp; $x - 订单流水号为 $POrderId 的 $Log_Item 和生产记录 删除成功.<br>";
				$filename="../download/pipdf/".$PI.".pdf";
                if(file_exists($filename)){
				  unlink($filename);
				  $delPISql="DELETE FROM $DataIn.yw3_pisheet WHERE Id='$PI'";
                  $delPIResult = mysql_query($delPISql);
				  if($delPIResult){
				       $Log.="PI 删除成功<br>";
				       }
				   else{
				        $Log.="<div class='redB'>PI 删除失败!<br></div>";
				       }
				}
				$x+=1;	
				//读取下属配件需求单
				//先清除没有下单且增购数量和使用库存数量为0的需求单
				//处理已下单或有增购的需求单
				$Stuff_Temp=mysql_query("SELECT * FROM $DataIn.cg1_stocksheet WHERE POrderId='$POrderId' ORDER BY Id DESC",$link_id); 
				if($Stuff_myrow = mysql_fetch_array($Stuff_Temp)){
					do{
						$m=1;
						$Mid=$Stuff_myrow["Mid"];
						$StuffId=$Stuff_myrow["StuffId"];
						$StockId=$Stuff_myrow["StockId"];
						$BuyerId=$Stuff_myrow["BuyerId"];
						$OrderQty=$Stuff_myrow["OrderQty"];
						$StockQty=$Stuff_myrow["StockQty"];
						$FactualQty=$Stuff_myrow["FactualQty"];
						$AddQty=$Stuff_myrow["AddQty"];
						 if ($Mid==0 && $AddQty=0 && $StockQty==0){
                                            $delscStuffSql="DELETE FROM $DataIn.cg1_stocksheet WHERE StockId='$StockId' LIMIT 1";
		                                    $delscStuffRresult = mysql_query($delscStuffSql);
                                            if($delscStuffRresult){
		                                                $Log.="配件需求单( $StockId )已成功删除.<br>";
                                                }
                                            else{
                                                 $Log.="<div class=redB>配件需求单( $StockId )删除失败,请告知管理员处理!</div><br>";
		                                           $OperationResult="N";
                                                }
                                    }
                            else{
				                       include "del_model_order.php";
                                  }
						 include "del_model_llqty.php";
						$StuffId=$Stuff_myrow["StuffId"];
                		include"../model/subprogram/stuff_Property.php";//配件属性   
               		    if($ComboxMainSign==1){  //母配件删除
                       		    include "del_model_combox.php";
                 		  }
						$m++;
						}while ($Stuff_myrow = mysql_fetch_array($Stuff_Temp));
					}
					//删除需备料订单资料
					$delSql="DELETE FROM $DataIn.yw9_blsheet WHERE POrderId='$POrderId'";
	                $delResult=mysql_query($delSql);	
				/**********************************///更新删除订单状态
				$UpdateEstate="update $DataIn.yw1_orderdeleted set Estate=0 WHERE Id='$Id'";
				$UpdateResult=@mysql_query($UpdateEstate);
				
				//删除订单状态资料
					$delSql2="DELETE FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId'";
	                $delResult=mysql_query($delSql2);
					
				}
			else{
				$Log.="<div class='redB'>$x - &nbsp;&nbsp;订单流水号为 $POrderId 的 订单删除失败(原因：订单已出货？！！).</div><br>";
				$OperationResult="N";
				}			
			}//end if($sheetRow = mysql_fetch_array($sheetResult))
	}//end if ($Id!="")
?>