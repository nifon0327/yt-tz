<?php 
//电信-zxq 2012-08-01
//$DataIn.ck9_stocksheet / $DataIn.cg1_stocksheet 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="需求单";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
switch($ActionId){
	case "0":	//需求单置换
		//流水号/配件ID/采购/供应商
		$dataArray=explode("|",$newData);
		$Id=$dataArray[0];
		$StuffId=$dataArray[1];
		$BuyerId=$dataArray[2];
		$CompanyId=$dataArray[3];		
		//锁定表
		//$LockSql=" LOCK TABLES $DataIn.ck9_stocksheet K WRITE,$DataIn.cg1_stocksheet C WRITE"; $LockRes=@mysql_query($LockSql);
		$updateSQL1 = "UPDATE $DataIn.cg1_stocksheet C,$DataIn.ck9_stocksheet K SET K.oStockQty=K.oStockQty+C.StockQty WHERE C.Id='$Id' and C.StuffId=K.StuffId";
		$updateResult1 = mysql_query($updateSQL1);
		if($updateResult1){
			$Log.="1 - ID号为 $Id 的原配件可用库存退回成功!</div><br>";
			$checkStock=mysql_query("SELECT K.oStockQty FROM $DataIn.ck9_stocksheet K WHERE K.StuffId='$StuffId' LIMIT 1",$link_id);
			if($checkRow = mysql_fetch_array($checkStock)){
				$oStockQty=$checkRow["oStockQty"];
				//可用库存与订单数量比较
				if($newOrderQty<=$oStockQty){//库存足够
					$newStockQty=$newOrderQty;
					$newFactualQty=0;
					$newoStockQty=$oStockQty-$newOrderQty;
					}
				else{
					if($oStockQty>0){		//部分
						$newStockQty=$oStockQty;
						$newFactualQty=$newOrderQty-$oStockQty;
						$newoStockQty=0;
						}
					else{
						$newStockQty=0;
						$newFactualQty=$newOrderQty;
						$newoStockQty=0;
						}				
					}
				//需求单更新
				$updateSQL2 = "UPDATE $DataIn.cg1_stocksheet C SET C.StuffId='$StuffId',C.Price='$newPrice',C.OrderQty='$newOrderQty',C.StockQty='$newStockQty',
				C.FactualQty='$newFactualQty',C.AddQty='0',C.BuyerId='$BuyerId',C.CompanyId='$CompanyId',C.AddRemark='$AddRemark0',C.Estate='1' 
				WHERE C.Id='$Id'";
				$updateResult2 = mysql_query($updateSQL2);
				if($updateResult2){
					$Log.="2 - 需求单置换成功!<br>";
					//库存更新
					$updateSQL3 = "UPDATE $DataIn.ck9_stocksheet K SET K.oStockQty='$newoStockQty' WHERE K.StuffId='$StuffId'";
					$updateResult3 = mysql_query($updateSQL3);
					if($updateResult3){
						$Log.="3 - 置换后的配件 $StuffId 可用库存更新成功!<br>";
						}
					else{
						$Log.="<div class='redB'>3 - 置换后的配件 $StuffId 可用库存更新失败!</div><br>";
						$OperationResult="N";
						}
					}
				else{
					$Log.="<div class='redB'>2 - 需求单置换失败!</div><br>";
					$OperationResult="N";
					}
				}			
			}
		else{
			$Log="<div class='redB'>1 - ID号为 $Id 的原配件可用库存退回失败,置换操作失败!</div><br>";
			$OperationResult="N";
			}
		//解锁表
		//$unLockSql="UNLOCK TABLES"; $unLockRes=@mysql_query($unLockSql);
		break;
	case "1"://清除使用的库存数量
		$dataArray=explode("|",$newData);
		$Id=$dataArray[0];
		$changeQty=$dataArray[1];
		$BuyerId=$dataArray[2];
		$CompanyId=$dataArray[3];
		$updateSQL = "UPDATE $DataIn.cg1_stocksheet C,$DataIn.ck9_stocksheet K SET K.oStockQty=K.oStockQty+'$changeQty',C.StockQty='$newStockQty',C.FactualQty=C.OrderQty-'$newStockQty',C.AddRemark='$AddRemark1',C.Estate='1' WHERE C.Id='$Id' and C.StuffId=K.StuffId";
		$updateResult = mysql_query($updateSQL);
		if($updateResult){
			$Log="ID号为 $Id 的需求单清除库除的操作成功!";
			}
		else{
			$Log="<div class='redB'>ID号为 $Id 的需求单清除库除的操作失败!</div>";
			$OperationResult="N";
			}		
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId&Number=$BuyerId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
