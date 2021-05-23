<?php 
//电信-zxq 2012-08-01
/*
$DataIn.online		在线用户
$DataIn.smsdata		短消息
*/
//已更新
include "../basic/chksession.php" ;
header("Content-Type: text/html; charset=utf-8");	
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
$cSign=$_SESSION["Login_cSign"];
$loginTitle=0;
$LastTime=time();
$lastactivity=$LastTime-300;
$checkOL=mysql_query("SELECT * FROM $DataIn.online WHERE sId=$onlineId",$link_id);
if($regOLRow = mysql_fetch_array($checkOL)){
	$upSql = "UPDATE $DataIn.online SET LastTime=$LastTime WHERE uId=$Login_Id";
	$upResult = mysql_query($upSql);
	//登记最后在线时间
	$eTime=date("Y-m-d H:i:s");
	$loginlogSql="UPDATE $DataIn.loginlog SET eTime='$eTime' WHERE uId='$Login_Id' ORDER BY Id DESC LIMIT 1";
	$loginlogRow=mysql_query($loginlogSql);
	}
else{
	$checkIP=mysql_query("SELECT IP FROM $DataIn.online WHERE uId=$Login_Id",$link_id);
	if($rowIPRow = mysql_fetch_array($checkIP)){
		$loginTitle=$rowIPRow["IP"];//重新登录
		}
	else{
		$loginTitle=1;				//被退出
		}
	}

if ($Login_uType!=3 )$loginTitle=1;	  //不是供应商帐号
//删除没有正常响影的用户	
$delSql=mysql_query("DELETE FROM $DataIn.online WHERE LastTime<$lastactivity");
$delResult = mysql_query($delSql);

//更新维护信息
$checkInfo=mysql_query("SELECT cRemark FROM $DataPublic.sys_updateinfo WHERE Estate=1 AND cSign=$cSign  LIMIT 1",$link_id);
if($checkInfoRow=mysql_fetch_array($checkInfo)){
	$ReInfo=$checkInfoRow["cRemark"];
	}
else{
	$ReInfo="";
	}
echo $Numbers."`".$smsNumbers."`".$loginTitle."`".$ReInfo;
?>