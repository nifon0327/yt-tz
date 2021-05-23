<?php   
//电信---yang 20120801
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$mySql=mysql_query("SELECT P.cName,P.eCode FROM $DataIn.stuffdata D 
	LEFT JOIN $DataIn.pands N ON N.StuffId=D.StuffId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=N.ProductId 
	WHERE 1 AND D.StuffCname LIKE '%$BarCode%' LIMIT 1",$link_id);
/*
echo "SELECT P.cName,P.eCode FROM $DataIn.stuffdata D 
	LEFT JOIN $DataIn.pands N ON N.StuffId=D.StuffId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=N.ProductId 
	WHERE 1 AND D.StuffCname LIKE '%$BarCode%' LIMIT 1";
*/
if($myRow=mysql_fetch_array($mySql)){
	echo $myRow["cName"]."<br>".$myRow["eCode"];
	}

?>