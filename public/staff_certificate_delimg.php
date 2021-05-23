<?php 
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
include "../model/modelfunction.php";

$delSql="DELETE FROM $DataIn.staff_Certificate WHERE Picture='$ImgName'";
$result1 = mysql_query($delSql);
$FilePath="../download/Certificate/".$ImgName;
if(file_exists($FilePath)){
	unlink($FilePath);
	}
?>