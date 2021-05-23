<?php 
//电信-ZX
//$DataPublic.info2_telmsg 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="来电留言记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
for($i=0;$i<count($checkid);$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		}
	}
//$LockSql=" LOCK TABLES $DataPublic.info2_telmsg WRITE";$res=@mysql_query($LockSql);
//删除领料记录
$delSql = "DELETE FROM $DataPublic.info2_telmsg WHERE Id IN ($Ids) and Estate=0"; 
$delRresult = mysql_query($delSql);
if ($delRresult && mysql_affected_rows()>0){
	$Log="&nbsp;&nbsp;ID在( $Ids )的 $TitleSTR 成功.<br>";
	}
else{
	$OperationResult="N";
	$Log="<div class='redB'>ID在( $Ids )的 $TitleSTR 失败.</div><br>";
	}//end if ($Del_result)
//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.info2_telmsg");
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>