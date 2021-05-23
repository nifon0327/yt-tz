<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
$Log_Item="登陆资料";
$Log_Funtion="更新";
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$upDataSheet="$DataIn.usertable";
$oPsw=MD5($oPsw);
$nPsw=MD5($nPsw);
$UpdateSql="Update $upDataSheet SET uName='$nUser',uPwd='$nPsw',Operator='$Operator',Date='$DateTime' WHERE uName='$oUser' AND uPwd='$oPsw' AND Number='$Operator'";
$UpdateResult = mysql_query($UpdateSql);
if ($UpdateResult && mysql_affected_rows()>0){
	$Log="用户 $nUser $Operator 登陆资料更新成功!";
	$loginlogSql="UPDATE $DataIn.loginlog SET eTime='$eTime' WHERE uId='$Login_Id' ORDER BY Id DESC LIMIT 1";
	$loginlogRow=mysql_query($loginlogSql);
	session_unset();
	session_destroy();
	}
else{//删除失败.
	$Log="<div class='redB'>用户 $nUser $Operator  登陆资料更新失败!</div>";
	$OperationResult="N";
	}
echo $OperationResult;
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>