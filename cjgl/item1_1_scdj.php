<?php
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取产品资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
if($fromAction ==1){
	include"item1_1_scdj_1.php"; //研砼组装登记页面
}else if ($fromAction ==2){
	include"item1_1_scdj_2.php"; //研砼皮套半成品登记页面
}
?>