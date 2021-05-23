<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="补仓记录";		//需处理
$upDataSheet="$DataIn.ck3_bcsheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 20:
		$Log_Funtion="主补仓单更新";
		$upSql = "UPDATE $DataIn.ck3_bcmain SET Date='$Date',BillNumber='$BillNumber' WHERE Id='$Mid'";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log="补仓主单资料更新成功.<br>";
			}
		else{
			$Log="<div class='redB'>补仓主单资料更新失败!</div><br>";
			$OperationResult="N";
			}
		break;
	default:
		$Log_Funtion="补仓数据更新";
		$thSTR="";
		if($Operators<0){	//减少补仓数量的条件 在库>=增加的数量
			$thSTR=" and K.tStockQty>=$changeQty";
			}
		//$LockSql=" LOCK TABLES $upDataSheet T WRITE,$DataIn.ck9_stocksheet K WRITE";$LockRes=@mysql_query($LockSql);
		$upSql = "UPDATE $upDataSheet T LEFT JOIN $DataIn.ck9_stocksheet K ON T.StuffId=K.StuffId SET T.Qty=T.Qty+$changeQty*$Operators,K.tStockQty=K.tStockQty+$changeQty*$Operators WHERE T.Id=$Id $thSTR";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log="补仓数据更新成功.<br>";
			}
		else{
			$Log="<div class='redB'>补仓数据更新失败!</div><br>";
			$OperationResult="N";
			}
		//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseDate=$chooseDate&CompanyId=$CompanyId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  