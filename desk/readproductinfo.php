<?php   
//电信-zxq 2012-08-01
/*
配件分类页面
已更新
*/
//include "../basic/chksession.php" ;
include "basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$mySql=mysql_query("SELECT P.cName,P.eCode FROM stuffdata D 
	LEFT JOIN $DataIn.pands N ON N.StuffId=D.StuffId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=N.ProductId 
	WHERE 1 AND D.StuffCname LIKE '%$BarCode%' LIMIT 1",$link_id);
if($myRow=mysql_fetch_array($mySql)){
	echo $myRow["cName"]."<br>".$myRow["eCode"];
	}

?>