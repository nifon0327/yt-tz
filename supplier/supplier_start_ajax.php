<?php   
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
switch($Action){
	case 1://更新检讨报告已读状态
		$updateSQL = "UPDATE $DataIn.errorcasedata  SET ReadState=1 WHERE Id='$Id'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
		       echo   "Y";
		}
		break;
	}
?>