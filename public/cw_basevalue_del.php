<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="财务基本参数";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
/*原则上不给删除
$y=0;
$Lens=count($checkid);
for($i=0;$i<$checkid;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}
//删除数据库记录
$DelSql = "DELETE FROM $DataPublic.cw3_basevalue WHERE Id IN ($Ids)"; 
$DelRresult = mysql_query($DelSql);
if ($DelRresult && mysql_affected_rows()>0){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功.<br>";
	}
else{
	$OperationResult="N";
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. $DelSql </div><br>";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.cw3_basevalue");
*/
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
//$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
//$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>