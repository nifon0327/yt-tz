<?php 
//ewen 2013-02-25 OK
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//编号、条码、单位、单价、
//申购中未审核数量、采购库存(包括已审核)、最低库存、默认供应商c
$checkSql1=mysql_query("SELECT A.TradeId,A.GoodsId,A.Price,A.Unit,B.wStockQty,B.oStockQty,B.mStockQty,B.CompanyId, C.mainType,C.BuyerId,E.AddTaxValue,T.Forshort AS Company
					   FROM $DataPublic.nonbom4_goodsdata A
					   LEFT JOIN $DataPublic.nonbom5_goodsstock  B ON B.GoodsId=A.GoodsId
					   LEFT JOIN $DataPublic.nonbom2_subtype C ON C.Id=A.TypeId
					   LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId
					   LEFT JOIN $DataPublic.nonbom3_retailermain E ON E.CompanyId=D.CompanyId
					   LEFT JOIN $DataPublic.trade_object T ON T.Id = A.TradeId
					   WHERE A.GoodsName='$checkName' LIMIT 1",$link_id);
if($checkRow1=mysql_fetch_array($checkSql1)){
    $TradeId = $checkRow1["TradeId"];
    $Company = $checkRow1["Company"];
	$GoodsId=$checkRow1["GoodsId"];
	$Price=$checkRow1["Price"];
	$Unit=$checkRow1["Unit"];
	/*为保证申购前数据的准确性，可考虑先更新库存数量，然后再取数据
	已下单总数－已领用总数+转入数量-报废数量＝采购库存
	已入库总数－已领用总数+转入数量-报废数量＝在库
	*/
	$wStockQty=del0($checkRow1["wStockQty"]);
	$oStockQty=del0($checkRow1["oStockQty"]);
	$mStockQty=del0($checkRow1["mStockQty"]);
	$CompanyId=$checkRow1["CompanyId"];
	$mainType=$checkRow1["mainType"];
	$BuyerId=$checkRow1["BuyerId"];
	$AddTaxValue=$checkRow1["AddTaxValue"];
	//读取申购未下单数量
	$checkSql2=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom6_cgsheet WHERE GoodsId='$GoodsId' AND Mid='0'",$link_id));
	$checkQty=$checkSql2["Qty"];//申购中的数量
	//返回信息
	if($GoodsId!=0 && $GoodsId!=""){
    	echo $GoodsId.",".$Unit.",".$wStockQty.",".$oStockQty.",".$mStockQty.",".$checkQty.",".$Price.",".$mainType.",".$BuyerId.",".$CompanyId.",".$AddTaxValue.",".$TradeId.",".$Company;
		}
}
else{
	echo $checkName;
}
?>