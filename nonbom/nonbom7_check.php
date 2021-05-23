<?php 
//ewen 2013-03-04 OK
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//编号、条码、单位、单价、
$BackInfo=0;
$checkSql1=mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE Name='$checkName' LIMIT 1",$link_id);
if($checkRow1=mysql_fetch_array($checkSql1)){
	$Number=$checkRow1["Number"];
	//返回信息
	if($Number!=0 && $Number!=""){
    	$BackInfo=$Number;
		}
	}
echo $BackInfo;
?>