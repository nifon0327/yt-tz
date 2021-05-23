<?php 
//电信-zxq 2012-08-01
//$DataIn.ch5_sampsheet 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="随货样品";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		}
	}
$DelSql= "DELETE FROM $DataIn.ch5_sampsheet WHERE 1 AND Estate=1 and Id IN ($Ids)";
$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
	$Log="ID号在( $Ids )的随货样品删除操作成功.<br>";
	//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ch5_sampsheet");
	}
else{
	$Log="<div class='redB'>ID号在( $Ids )的随货样品删除操作失败.</div><br>";
	$OperationResult="N";
	}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";

?>