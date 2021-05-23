<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$Del = "DELETE FROM $DataIn.online WHERE sId='$onlineId' LIMIT 1"; 
$Del_result = mysql_query($Del);
//登记退出时间
$eTime=date("Y-m-d H:i:s");
$loginlogSql="UPDATE $DataIn.loginlog SET eTime='$eTime' WHERE uId='$Login_Id' ORDER BY Id DESC LIMIT 1";
$loginlogRow=mysql_query($loginlogSql);
//$OPTIMIZE = mysql_query("OPTIMIZE TABLE $DataIn.online ");
session_start();
session_unset();
session_destroy();
?>