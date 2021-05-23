<?php 
//电信-ZX  2012-08-01
include "../basic/parameter.inc";
include "../model/modelfunction.php";
include "kqcode/kq_function.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$updateSQL = "UPDATE $DataPublic.kqqjsheet Q SET Q.Estate=0 WHERE Q.Id='$Id'";
$updateResult = mysql_query($updateSQL);
if ($updateResult && mysql_affected_rows()>0){
	echo "<div style='color: #009900;font-weight: bold;'>&nbsp;&nbsp;&nbsp;&nbsp;已批准</div>";
	}
?>