<?php 
//电信-zxq 2012-08-01

include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="显示屏信息记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		}
	}

$delSql = "DELETE FROM $DataPublic.ot2_display WHERE Id IN ($Ids)"; 
$delRresult = mysql_query($delSql);

if ($delRresult && mysql_affected_rows()>0){
	$Log="&nbsp;&nbsp;ID在( $Ids )的 $TitleSTR 成功.<br>";
	}
else{
	$OperationResult="N";
	$Log="<div class='redB'>ID在( $Ids )的 $TitleSTR 失败.</div><br>";
	}//end if ($Del_result)
//$sql="UNLOCK TABLES"; $res=@mysql_query($sql);
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.ot2_cam");
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>