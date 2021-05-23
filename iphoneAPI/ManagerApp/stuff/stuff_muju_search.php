<?php 

$searchSourceList = array();
{
$searchSourceSql = mysql_query("SELECT A.Id,A.GoodsId,A.GoodsName,A.Price,F.PreChar,D.Forshort
FROM $DataPublic.nonbom4_goodsdata A
LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=A.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.currencydata F ON F.Id=D.Currency
WHERE 1  AND B.Id in (22,23,24,25) AND A.Estate>0");
while ($searchSourceRow = mysql_fetch_assoc($searchSourceSql)) {
	$GoodsId = $searchSourceRow["GoodsId"];
	$GoodsName = $searchSourceRow["GoodsName"];
	$Price = $searchSourceRow["Price"];
	$PreChar = $searchSourceRow["PreChar"];
	$Forshort = $searchSourceRow["Forshort"];
	$Price = $PreChar.$Price;
	
	$searchSourceList[]= array("EditType"=>"1","PlaceHolder"=>"$Price","FiledName"=>"$GoodsId-$GoodsName","FieldKey"=>"nonbom","FieldVal"=>"$GoodsId","ContentTxt"=>"$Forshort");
	
}
$jsonArray = array("searchSource"=>$searchSourceList);
}


?>
