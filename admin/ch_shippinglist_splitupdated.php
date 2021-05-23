<?php

$ActionId = $_GET["ActionId"];
if($ActionId =="shipSign"){
	include "../basic/chksession.php";
	header("Content-Type: text/html; charset=utf-8");
	header("expires:mon,26jul199705:00:00gmt");
	header("cache-control:no-cache,must-revalidate");
	header("pragma:no-cache");
	include "../basic/parameter.inc";
	include "../model/modelfunction.php";
	
	
}else{
    include "../model/modelhead.php";
	$fromWebPage=$funFrom."_".$From;
	$nowWebPage=$funFrom."_splitupdated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	$Log_Item="订单出货";		//需处理
	$Log_Funtion="拆分";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId";
}

$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
 case  148://审核出货
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:$Ids.",".$Id;
				$x++;
			}
		}
		$Log_Funtion="审核出货";
            $UpSql="UPDATE $DataIn.ch1_shipsplit C  SET C.Estate=1 WHERE C.Id IN ($Ids)";
			$UpResult = mysql_query($UpSql);
			if($UpResult){
				    $Log="审核出货状态更新成功.<br>";
                }
           else{
					$Log.="<div class='redB'>审核出货状态更新失败.$UpSql</div><br>";
					$OperationResult="N";
                    }
		 break;

        case 149: // 拆分出货
                 if($SplitQty>0){
                       $In_Sql="INSERT INTO $DataIn.ch1_shipsplit(Id,POrderId,ShipId,Qty,ShipType,
                       Estate,OrderSign,shipSign)VALUES(NULL,'$POrderId','0','$SplitQty','$ShipType','1','$Id','0')";
                        $In_Result=@mysql_query($In_Sql); 
                        if($In_Result && mysql_affected_rows()>0){
						        	$Log.="订单流水号 $Id 的订单拆分出货成功";
           			        	    $UpSql="UPDATE $DataIn.ch1_shipsplit C  SET C.Qty=C.Qty-$SplitQty  WHERE C.Id='$Id'";
						        	$UpResult = mysql_query($UpSql);
                                }
                           else{
						        	$Log.="<div class='redB'>订单流水号 $Id 的订单拆分出货失败$In_Sql</div>";
						         	$OperationResult="N";
                                 }
                       }
               else{
							$Log="<div class='redB'>拆分的数量不能小于等于0</div>";
							$OperationResult="N";
                      }
		       break;

        case 150: // 取消拆分
               $checkShipResult=mysql_fetch_array(mysql_query("SELECT  COUNT(*) AS shipCount  FROM $DataIn.ch1_shipsplit C WHERE  C.Id='$Id' and EXISTS (SELECT S.Id FROM $DataIn.ch1_shipsheet S WHERE S.POrderId=C.POrderId) ",$link_id));
               $shipCount=$checkShipResult["shipCount"];
               if ($shipCount>0){
	               $Log.="<div class='redB'>订单流水号 $POrderId 的订单取消拆分已有分批出货记录，不能取消!</div>";
				   $OperationResult="N";
               }
               else{
	               $CheckOrderSplit=mysql_fetch_array(mysql_query("SELECT POrderId,OrderSign,Qty  FROM $DataIn.ch1_shipsplit WHERE  Id='$Id' ",$link_id));
	               $OrderSign=$CheckOrderSplit["OrderSign"];
	               $POrderId=$CheckOrderSplit["POrderId"];
	               $Qty=$CheckOrderSplit["Qty"];
	               if($OrderSign>0){
	                         $UpdateSql="UPDATE  $DataIn.ch1_shipsplit  SET  Qty=Qty+$Qty WHERE POrderId='$POrderId' AND OrderSign=0";
	                         $UpdateResult=@mysql_query($UpdateSql);
	                         $QuitSign=0;
	                         if($UpdateResult &&mysql_affected_rows()>0){
	                                 $DelSql="DELETE FROM $DataIn.ch1_shipsplit WHERE  Id='$Id' ";
	                                  $DelResult=@mysql_query($DelSql);
	                                 if($DelResult && mysql_affected_rows()>0)$QuitSign=1;
	                             }
	                         if($QuitSign>0){
							        	$Log.="订单流水号 $POrderId 的订单取消拆分出货成功";
	                                  }
	                          else{
							        	$Log.="<div class='redB'>订单流水号 $POrderId 的订单取消拆分出货失败!</div>";
								        $OperationResult="N";
	                                  }
	                    }
	              else{
								$Log="<div class='redB'>此记录为原始记录，不能拆分还原!</div>";
								$OperationResult="N";
	                     }
              }
              break;
			  
	      case "ShipType":	
		  
			  $sql = "UPDATE $DataIn.ch1_shipsplit SET ShipType='$tempShipType' 
			  WHERE POrderId='$POrderId' AND Id='$sId' ";
			  //echo "$sql";
			  $result = mysql_query($sql,$link_id);
			  if ($result){
					$Log="订单流水号为 $POrderId 的出货方式更新成功.<br>";
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 的出货方式更新失败.</div><br>";
				$OperationResult="N";
				}
			break;
			
			
	      case "shipSign":	
		  
		      $CheckResult=mysql_fetch_array(mysql_query("SELECT shipSign FROM $DataIn.ch1_shipsplit 
		      WHERE Id=$Id",$link_id));
              $shipSign=$CheckResult["shipSign"];
             
              if($shipSign ==1){
	              $changeShipSign = 0 ;
              }else{
	              $changeShipSign = 1 ;
              }
			  $sql = "UPDATE $DataIn.ch1_shipsplit SET shipSign='$changeShipSign' WHERE  Id='$Id' ";
			  //echo $sql;
			  $result = mysql_query($sql,$link_id);
			  if ($result){
					echo $changeShipSign;
				}
			
			break;
			
			
		 case "updateSplitQty":	
		 
		       $CheckSplitQty = mysql_fetch_array(mysql_query("SELECT SP.Qty,Y.Qty AS OrderQty
		       FROM $DataIn.ch1_shipsplit  SP 
		       LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SP.POrderId
			   WHERE SP.Id ='$Id'",$link_id));
			   $OrderQty = $CheckSplitQty["OrderQty"];
			   $thisSplitQty = $CheckSplitQty["Qty"];
			   
			   $CheckTotalSplitQty=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ch1_shipsplit  
			   WHERE POrderId='$POrderId'",$link_id));
		       $TotalSplitQty=$CheckTotalSplitQty["Qty"];
		       $overSplitQty = $TotalSplitQty -$OrderQty;
		       
		       if($overSplitQty>0){
			       
			       if($thisSplitQty<=$overSplitQty){
				       
				       $delSql = "DELETE  FROM $DataIn.ch1_shipsplit WHERE Id = '$Id'";
				       $delResult  = mysql_query($delSql);
				       if($delResult && mysql_affected_rows()>0){				        
					        echo "Y";
				       }
			       }else{
				       
				       $updateQty = $thisSplitQty -$overSplitQty;
				       $updateSql = "UPDATE  $DataIn.ch1_shipsplit SET Qty = $updateQty WHERE Id = '$Id'";
				       $updateResult  = mysql_query($updateSql);
				       if($updateResult && mysql_affected_rows()>0){				        
					        echo "Y";
				       }
			       }
		       }
		 
		       $UpdateOrderSql = "UPDATE  $DataIn.yw1_ordersheet Y 
		       LEFT JOIN ( SELECT SUM(Qty) AS shipQty,POrderId FROM $DataIn.ch1_shipsheet WHERE POrderId ='$POrderId') S ON S.POrderId = Y.POrderId  
		       SET Estate = 0 
		       WHERE Y.POrderId='$POrderId' AND S.shipQty = Y.Qty ";
		       $UpdateOrderResult = mysql_query($UpdateOrderSql);
		 
		 break;	
		
	      case "ToOutName":	
		  
			  //$sql = "UPDATE $DataIn.ch1_shipsplit SET ShipType='$tempShipType' WHERE POrderId='$POrderId' AND Id='$sId' ";
			  //echo "$sql";
			  //$result = mysql_query($sql,$link_id);
			  	$Sign=1; ////Sign: 1, yworder_sheet:Id, 2: ch1_shipsplit.ID ->Mid
			  	if($sId*1>0){
				  	$Sign=2;
			  	}
				$Result = mysql_query("SELECT Id FROM $DataIn.yw7_clientOutData WHERE MId='$sId' AND POrderId='$POrderId' ",$link_id);
				if ($myrow = mysql_fetch_array($Result)) {
					//删除数据库记录
					$Del = "DELETE FROM $DataIn.yw7_clientOutData WHERE MId='$sId' AND POrderId='$POrderId' "; 
					$result = mysql_query($Del);
				}
				//Sign: 1, yworder_sheet:Id, 2: ch1_shipsplit.ID ->Mid //
				if ($tempToOutName!=""){
					$IN_recode="INSERT INTO $DataIn.yw7_clientOutData (Id,Mid,POrderId,ToOutId,Remark,Sign,Estate,Locks,Date,Operator) VALUES (NULL,'$sId','$POrderId','$tempToOutName','','$Sign','1','0','$DateTime','$Operator')";
					$res=@mysql_query($IN_recode);
					
					if ($res){
						$Log="订单流水号为 $POrderId ($sId)的出货指定转发对象成功.<br>";
					}
					else{
						$Log="<div class=redB>订单流水号为 $POrderId($sId) 的出货指定转发对象失败.</div><br>";
						$OperationResult="N";
					}
				}
				
		break;	
		
		case "OrderPO":
		
			$Result = mysql_query("SELECT Id FROM $DataIn.yw7_clientOrderPo WHERE MId='$sId' AND POrderId='$POrderId' ",$link_id);
			if ($myrow = mysql_fetch_array($Result)) {
				//删除数据库记录
				$Del = "DELETE FROM $DataIn.yw7_clientOrderPo WHERE MId='$sId' AND POrderId='$POrderId' "; 
				$result = mysql_query($Del);
			}
			
			if ($tempOrderPO!=""){
				$IN_recode="INSERT INTO $DataIn.yw7_clientOrderPo (Id,Mid,POrderId,OrderPO,Remark,Estate,Locks,Date,Operator) 
				VALUES (NULL,'$sId','$POrderId','$tempOrderPO','','1','0','$DateTime','$Operator')";
				$res=@mysql_query($IN_recode);
				
				if ($res){
					$Log="订单流水号为 $POrderId ($sId)的出货PO 指定:$tempOrderPO 成功.<br>";
				}
				else{
					$Log="<div class=redB>订单流水号为 $POrderId($sId) 的出货PO 指定:$tempOrderPO 失败.</div><br>";
					$OperationResult="N";
				}
			}			
				
				
			break;		
		
		
	      case "taxtype":	
			  $sql = "UPDATE $DataIn.yw1_ordersheet SET taxtypeId='$taxtypeId' WHERE POrderId='$POrderId'  ";
			  $result = mysql_query($sql,$link_id);
			  if ($result){
					$Log="订单流水号为 $POrderId 产品报关方式.<br>";
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 产品报关方式更新失败.</div><br>";
				$OperationResult="N";
				}
			break;		
	}
if($ActionId !="shipSign"){
		$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
		$IN_res=@mysql_query($IN_recode);
		
		include "../model/logpage.php";
}
?>