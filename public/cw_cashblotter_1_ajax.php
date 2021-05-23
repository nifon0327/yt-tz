<?php
//现金流水帐明细
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");


$ValueArray=explode("~",$TempValue);
$ItemId=$ValueArray[0];
$PayDate=$ValueArray[1];
$Id_Remark=$ValueArray[2];
/*
1	BOM采购货款支付 		OK
2	客户退款支出				OK
3	BOM采购预付订金		OK
4	非BOM采购货款支付	OK
5	非BOM采购预付订金	OK
6	BOM供应商税款			OK
7	客户货款收入				OK
8	预收客户货款				OK
9 开发费用						OK
10 行政费用					OK
11其它收入						OK
12汇兑转入						OK
13汇兑转出						OK
14BOM供应商扣款			OK
15员工薪资						OK
16试用工薪资					OK
17员工借支						OK
18社保缴费						OK
19假日加班费					OK
20快递费						OK
21寄样费						OK
22节日奖金						OK
23总务采购费用				OK
24中港运费/报关/商检费用OK
25退税金额						OK
26模具退回费用				OK
27体检费用						OK
28入仓费						OK
29Forward杂费				OK
30 货款返利				?
32  车辆费用   OK
34  其它奖金   OK
*/
$ItemId=$ItemId==13?12:$ItemId;
echo "<table cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
include "cashblotter/cw_cashblotter_1_".$ItemId.".php";
echo "</table>";
?>