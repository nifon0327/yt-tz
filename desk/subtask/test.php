<?php
/*
require(dirname(__FILE__)."/../../syb/sybpath.php");//切换至损益表目录syb
require_once "../model/modelhead.php";
//汇率参数
$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
//最后月份
$CheckTime=date("Y-m");
$TempPayDatetj=" AND DATE_FORMAT(M.PayDate,'%Y-%m')='$CheckTime'";
$TempDatetj=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
$TempMonthtj="  AND M.Month='$CheckTime'";
$TempSendDatetj=" AND DATE_FORMAT(M.SendDate,'%Y-%m')='$CheckTime'";
$TempqkDatetj=" AND DATE_FORMAT(M.qkDate,'%Y-%m')='$CheckTime'";
$TempDateTax=" AND DATE_FORMAT(M.TaxDate,'%Y-%m')='$CheckTime'";
 $TempDateModelf=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
$TempDeliveryDate=" AND DATE_FORMAT(M.DeliveryDate,'%Y-%m')='$CheckTime'";
$Subscript=0;			//数组起始下标
$SumType_A=array();unset($SumType_A);

$checkItemNum = mysql_fetch_array(mysql_query("SELECT count(*) AS ItemNum FROM $DataPublic.sys8_pandlmain WHERE Estate=1 ",$link_id));
$ItemNum=$checkItemNum["ItemNum"];

require_once "desk_pandl_data.php";

$lastMonthAmount=$SumType_A[0][0];
for($i=1;$i<7;$i++){
	$lastMonthAmount-=$SumType_A[0][$i];
	}
$lastMonthAmount=sprintf("%.2f",$lastMonthAmount);
*/
include "phpinfo.php";
?>