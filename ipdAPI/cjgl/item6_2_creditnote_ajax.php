<?php 
/*
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.stuffdata
$DataPublic.staffmain
$DataIn.trade_object
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
二合一已更新
*/
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//客户名称
$CheckSql=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataIn.trade_object WHERE CompanyId='$CompanyId' LIMIT 1",$link_id));
$Forshort=$CheckSql["Forshort"];

//读取该客户的最后一个CREDITNOTE名称
$maxInvoiceNO=mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain M WHERE Sign='-1' AND CompanyId='$CompanyId' ORDER BY Date DESC,InvoiceNO LIMIT 1",$link_id);
if($maxRow=mysql_fetch_array($maxInvoiceNO)){
	$maxNO=$maxRow["InvoiceNO"];
	$formatArray=explode(" ",$maxNO);
	$maxNum=trim(preg_replace("/([^0-9]+)/i","",$formatArray[3]))+1;//提取编号
	$NewInvoiceNO=$Forshort." credit note ".$maxNum;
	}
else{
	$NewInvoiceNO=$Forshort." credit note 001";
	}
//读取客户文档模板
$checkBank=mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE 1 AND CompanyId='$CompanyId' ORDER BY Id",$link_id);
$ModelStr="";
if($BankRow=mysql_fetch_array($checkBank)){
	do{
		$Id=$BankRow["Id"];
		$Title=$BankRow["Title"];
		$NewInvoiceNO=$NewInvoiceNO."~".$Id."|".$Title;
		}while($BankRow=mysql_fetch_array($checkBank));
	}
echo $NewInvoiceNO;
?>