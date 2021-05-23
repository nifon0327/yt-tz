<?php   
//2010.12.08更新$DataIn.电信---yang 20120801
//$LockSql=" LOCK TABLES $DataIn.cg1_stocksheet WRITE,$DataIn.ck9_stocksheet WRITE"; $LockRes=@mysql_query($LockSql);
switch($Mid){
	case "0"://需求单情况1：未下单
		if($AddQty==0){//	A：没有增购,可能存在使用库存
			$Del_SDS = "DELETE FROM $DataIn.cg1_stocksheet WHERE StockId='$StockId' LIMIT 1";
			$result_SDS = mysql_query($Del_SDS);
			if($result_SDS && mysql_affected_rows()>0){
				$Log.="配件需求单( $StockId )已成功删除.<br>";
				
				}
			else{
				$Log.="<div class=redB>配件需求单( $StockId )删除失败,请告知管理员处理!</div><br>";
				$OperationResult="N";
				}
			}
		else{//		B: 有增购,可能存在使用库存
			$read_Sql2 = "SELECT oStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY Id LIMIT 1";
			$read_result2 = mysql_query($read_Sql2);
			if($read_row2 = mysql_fetch_array($read_result2)){
				$oStockQty=$read_row2["oStockQty"];
			}
			if($oStockQty+$StockQty>=$AddQty){//可用库存大于增购数量，则可以完全删除需求单
				$Del_SDS = "DELETE FROM $DataIn.cg1_stocksheet WHERE StockId='$StockId' LIMIT 1";
				$result_SDS = mysql_query($Del_SDS);
				if($result_SDS){													
					$Log.="配件需求单( $StockId )没有下采购单但有增购，已成功删除.<br>";

					}
				else{
					$Log.="<div class=redB>配件需求单( $StockId )没有下采购单但有增购，删除失败!<div><br>";
					$OperationResult="N";
					}
				}
			else{//有其它订单用到该增购数量
				$newAddQty=$AddQty-$oStockQty-$StockQty;
				//需求单转特采单
				$To_SQL = "UPDATE $DataIn.cg1_stocksheet SET OrderQty='0',StockQty='0',POrderId='',AddQty='0',FactualQty='$newAddQty',Estate='0'  WHERE StockId='$StockId' LIMIT 1";
				$To_SQL = mysql_query($To_SQL);
				if($To_SQL){
					}
				else{
					$Log.="<div class=redB>配件需求单( $StockId )没有下采购单,增购有被使用 $newAddQty PCS，但转特采单失败!<div><br>";$OperationResult="N";
					}
				}
			
			}
		break;
		default://已下单,转为特采单，订单数量返回可用库存
			$upSQL = "UPDATE $DataIn.cg1_stocksheet SET POrderId='',OrderQty='0',StockQty='0',AddQty='0',FactualQty=FactualQty+'$AddQty',AddRemark='因取消配件而转的特采单!',Estate='0'  WHERE StockId='$StockId'";
			$upResult = mysql_query($upSQL);
			if($upResult && mysql_affected_rows()>0){
				//转特采单后，将订单数量（使用库存+需购）返回可用库存，增购数量不用返回（增购时已加入可用库存）
						}
			else{
				$Log.="<div class=redB>已采购的配件需求单( $StockId )未能成功转为特采单! </div><br>";$OperationResult="N";
				}
		break;
	}
//$unLockSql="UNLOCK TABLES"; $unLockRes=@mysql_query($unLockSql);
?>