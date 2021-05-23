<?php 
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$mySql="SELECT S.StockId,S.StuffId,(S.AddQty+S.FactualQty) AS Qty,S.BuyerId,S.CompanyId,A.StuffCname,P.Forshort,M.Name
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId
WHERE 1 AND S.StockId='$CID' AND S.Mid>0 LIMIT 1";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	//已收货数量
	$StockId=$myRow["StockId"];
	$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
	$rkQty=mysql_result($rkTemp,0,"Qty");
	$rkQty=$rkQty==""?0:$rkQty;
	$unQty=$myRow["Qty"]-$rkQty;
	//流水号，未收数量，配件ID，配件名称，采购ID，采购名称，供应商ID，供应商名称
	$RebackSTR=$StockId."".$unQty."".$myRow["StuffId"]."".$myRow["StuffCname"]."".$myRow["BuyerId"]."".$myRow["Name"]."".$myRow["CompanyId"]."".$myRow["Forshort"];
	}
//表ID流水号配件ID配件名单价订单数量已用库存需求数量增购数量供应商
//echo $RebackSTR;
echo"<script>window.returnValue='$RebackSTR';window.close(); </script>";
?>