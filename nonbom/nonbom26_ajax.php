<?php 
//ewen 2013-02-25 OK
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
include "nobom_config.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//编号、条码、单位、单价、
//申购中未审核数量、采购库存(包括已审核)、最低库存、默认供应商

$SearchRows= 'AND  (F.TypeId IN (' .  $APP_CONFIG['NOBOM_FIXEDASSET_TYPEID'] . ')  OR  A.AssetType=2)'; 

$checkSql1=mysql_query("SELECT A.GoodsId,A.Price,A.Salvage,A.DepreciationId,F.Name AS TypeName 
					   FROM $DataPublic.nonbom4_goodsdata A
	                    LEFT JOIN $DataPublic.nonbom2_subtype T  ON T.Id=A.TypeId 
	                    LEFT JOIN $DataPublic.acfirsttype F ON F.FirstId=T.FirstId
					   WHERE A.GoodsName='$checkName' $SearchRows  LIMIT 1",$link_id);	 
if($checkRow1=mysql_fetch_array($checkSql1)){
	$GoodsId=$checkRow1["GoodsId"];
	$Price=$checkRow1["Price"];
	$Salvage=$checkRow1["Salvage"];
	$DepreciationId=$checkRow1["DepreciationId"];
	$TypeName=$checkRow1["TypeName"];

	//返回信息
	if($GoodsId>0){
    	   echo $GoodsId."|".$TypeName."|".$Price."|".$DepreciationId."|".$Salvage;
		}
}
else{
	echo "";
}
?>