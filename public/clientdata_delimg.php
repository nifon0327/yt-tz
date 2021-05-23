<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$delSql="DELETE FROM $DataIn.clientimg WHERE Picture='$ImgName'";
$result1 = mysql_query($delSql);
if($result1){echo "Y";}
else echo "N";
$FilePath="../download/clientfile/".$ImgName;
if(file_exists($FilePath)){
	unlink($FilePath);
	}
?>