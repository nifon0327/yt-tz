<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck1_rksheet
$DataIn.ck9_stocksheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="入库记录";		//需处理
$upDataSheet="$DataIn.ck1_rksheet";	//需处理
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
	case 20://?
		$Log_Funtion="主入库单更新";
		$Remark=FormatSTR($Remark);
		$upSql = "UPDATE $DataIn.ck1_rkmain SET Date='$Date',
		Remark='$Remark',modifier='$Operator',modified ='$DateTime' WHERE Id='$Mid'";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log="入库主单资料更新成功.<br>";
			}
		else{
			$Log="<div class='redB'>入库主单资料更新失败! $upSql </div><br>";
			$OperationResult="N";
			}
		break;
	default:
		$Log_Funtion="入库数据更新";
		$changeQty = $Operators*1*$changeQty;
		
		$upSql = "UPDATE $upDataSheet R SET R.Qty=R.Qty+$changeQty WHERE R.Id=$Id ";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log="入库数据更新成功.<br>";
			}
		else{
			$Log="<div class='redB'>入库数据更新失败 $upSql !</div><br>";
			$OperationResult="N";
			}
		//更新需求单的入库状态:2部分入库，1未入库，0已全部入库
		$uprkSign="UPDATE $DataIn.cg1_stocksheet SET rkSign=(CASE 
			WHEN (SELECT SUM(Qty) AS Qty FROM $upDataSheet WHERE StockId = '$StockId')>0 THEN 2
			ELSE 1 END) WHERE StockId='$StockId'";
		$upRkAction=mysql_query($uprkSign);
		
		// 如果请款通过就退回去
		$delqkSign="delete from $DataIn.cw1_fkoutsheet  WHERE StockId='$StockId' AND Mid=0";
		$upRkAction=mysql_query($delqkSign);
			if($upRkAction && mysql_affected_rows()>0){
				$Log.="请款删除成功，订单流水号. $StockId <br>";
				}
			else{
			     $Log.="<div class='redB'>请款删除失败!</div>$delqkSign<br>";
			     $OperationResult="N";
				}		
		
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseDate=$chooseDate&CompanyId=$CompanyId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  