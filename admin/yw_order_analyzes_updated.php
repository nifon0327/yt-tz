<?php   
//电信-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_m";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="已备料订单拆分";//需处理
$Log_Funtion="审核";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;
$Id=$checkid[0];
switch ($ActionId){
    case 17:
        include "../admin/subprogram/yw_order_split.php";
	   break;
	case 15:
	   $DelResult="DELETE FROM $DataIn.yw10_ordersplit WHERE Id='$Id'";
	   $DelSql=mysql_query($DelResult);
	   if($DelSql){
	       $Log.="记录退回成功!<br>";
	        }
		else{
		    $Log.="<div class='redB'>记录退回失败! $DelResult</div><br>";
			 $OperationResult="N";
		   }
	   
	   break;
	case 162:
	{
		$updateStuffStateSql = "update $DataIn.yw10_ordersplit set Estate = '2' Where Id = '$Id'";
		mysql_query($updateStuffStateSql);
		$returnReasonSql = "Insert Into $DataPublic.returnreason (Id, tableId, targetTable, Reason, DateTime) Values (NULL, '$Id', '$DataIn.yw10_ordersplit','$ReturnReasons', '$DateTime')";
		mysql_query($returnReasonSql);

	}
	break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>