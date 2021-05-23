<?php   
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
switch($Action){
	case 1://批准
		$updateSQL = "UPDATE $DataPublic.kqqjsheet Q SET Q.Estate=0 WHERE Q.Id='$Id'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
		echo"<div class='greenB'>已批准</div>";
		}
		break;
	case 2://不批准,则删除记录
		$updateSQL = "UPDATE $DataPublic.kqqjsheet Q SET Q.Estate=3 WHERE Q.Id='$Id'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
		echo"<div class='redB'>未批准</div>";
		}
		break;
	}
?>