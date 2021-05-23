<?php   
//电信-zxq 2012-08-01
//$DataIn.ch6_creditnote 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="扣款资料";//需处理
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
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		$x++;
		}
	}
//$LockSql=" LOCK TABLES $DataIn.ch6_creditnote WRITE";$res=@mysql_query($LockSql);
$DelSql= "DELETE FROM $DataIn.ch6_creditnote WHERE 1 AND Estate=1 AND Id IN ($Ids)";
$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
	$Log="ID号在( $Ids )的 $TitleSTR 成功.<br>";
	}
else{
	$Log="<div class='redB'>ID号在( $Ids )的 $TitleSTR 失败.</div><br>";
	$OperationResult="N";
	}
//操作日志
//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ch6_creditnote");
$chooseDate=$x==$IdCount?"":$chooseDate;
$ALType="chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>