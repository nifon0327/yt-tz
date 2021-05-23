<?php 
//电信-zxq 2012-08-01
//$DataIn.ch3_forward 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="Forward杂费";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		$x++;
		}
	}
$DelSql= "DELETE FROM $DataIn.ch3_forward WHERE 1 AND Estate=1 AND Id IN ($Ids)";
$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
	$Log="ID号在( $Ids )的 $TitleSTR 成功.<br>";
	//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ch3_forward");
	}
else{
	$Log="<div class='redB'>ID号在( $Ids )的 $TitleSTR 失败.</div><br>";
	$OperationResult="N";
	}
	$DelSql1="DELETE FROM $DataIn.ch3_forward_invoice WHERE 1 AND Mid IN ($Ids)";
	$DelResult1=mysql_query($DelSql1);
//操作日志
$chooseDate=$x==$IdCount?"":$chooseDate;
$ALType="chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>