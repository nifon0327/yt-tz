<?php   
//电信-zxq 2012-08-01
session_start();
//登记退出时间
include "basic/parameter.inc";
$eTime=date("Y-m-d H:i:s");
$loginlogSql="UPDATE $DataIn.loginlog SET eTime='$eTime' WHERE uId='$Login_Id' ORDER BY Id DESC LIMIT 1";
$loginlogRow=mysql_query($loginlogSql);
session_unset();
session_destroy();
echo "<SCRIPT LANGUAGE=JavaScript>";
echo "location.href='./'";
echo "</script>";
//清除在线记录
//echo "<SCRIPT LANGUAGE=JavaScript>"; 
//echo "opener='';close();parent.parent.opener='';parent.window.close();"; 
//echo
?>