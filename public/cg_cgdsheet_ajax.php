<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.stuffdata
$DataIn.trade_object
$DataPublic.staffmain
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$StockType="";
$StockInfo="";
if($ActionTo==1){//清空库存
	$StockType="and S.StockQty>0";
	$StockInfo="或没有使用库存";
	}
$mySql="SELECT S.Id,S.StockId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.BuyerId,S.CompanyId,A.StuffCname,P.Forshort,M.Name
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId
WHERE 1 and S.StockId='$StockId' and S.Mid=0 and S.Estate=0 $StockType LIMIT 1";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	echo "".$myRow["Id"]."".$myRow["StockId"]."".$myRow["StuffId"]."".$myRow["StuffCname"]."".$myRow["Price"]."".$myRow["OrderQty"]."".$myRow["StockQty"]."".$myRow["FactualQty"]."".$myRow["AddQty"]."".$myRow["Name"]."".$myRow["Forshort"]."".$myRow["BuyerId"]."".$myRow["CompanyId"];
	}
else{
	echo "记录异常：读取不到资料或需求单未审核".$StockInfo;
	}
//表ID流水号配件ID配件名单价订单数量已用库存需求数量增购数量供应商
?>