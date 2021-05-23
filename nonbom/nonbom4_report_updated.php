<?php 
//$DataIn.电信---yang 20120801
//步骤1： $DataIn.ck9_stocksheet 二合一已更新
if($ipadTag == "yes")
{
	include_once "../basic/parameter.inc";
}
else
{
	include "../model/modelhead.php";
}
//锁定
//$LockSql=" LOCK TABLES $DataIn.ck9_stocksheet WRITE";$LockRes=@mysql_query($LockSql);
$Date=date("y-m-d");
$wStockQty=$wStockQty<0?0:$wStockQty;
$oStockQty=$oStockQty<0?0:$oStockQty;
$lStockQty=$lStockQty<0?0:$lStockQty;
$Sql = "UPDATE $DataPublic.nonbom5_goodsstock SET wStockQty='$wStockQty',oStockQty='$oStockQty' ,lStockQty='$lStockQty' WHERE GoodsId='$GoodsId'";
$Result = mysql_query($Sql);

if($ipadTag == "yes")
{
	if($Result)
	{
		echo "Y";
	}
	else
	{
		echo "N";
	}
}

//解锁
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
?>