<?php
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Date=date("Y-m-d");
switch($Action){
	case 1:
		$updateSQL = "UPDATE $DataPublic.aqsc01 SET Sort=$SortId WHERE Id='$Id'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
		     echo "Y";
		    }
         else{ echo "N";}
		break;
	case 2:
		$updateSQL = "UPDATE $DB.aqsc01 SET Sort=$SortId WHERE Id='$Id'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
		     echo "Y";
		    }
         else{ echo "N";}
		break;
}
?>