<?php 
//步骤1：$DataIn.productimg 二合一已更新电信---yang 20120801
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$delSql="DELETE FROM $DataIn.product_certification WHERE Picture='$ImgName'";
$result1 = mysql_query($delSql);
if($result1){echo "Y";}
else echo "N";
$FilePath="../download/productcer/".$ImgName;
if(file_exists($FilePath)){
	unlink($FilePath);
	}
?>