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
$Sql = "UPDATE $DataIn.ck9_stocksheet SET tStockQty='$tStockQty',oStockQty='$oStockQty',Date='$Date' WHERE StuffId='$StuffId'";
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