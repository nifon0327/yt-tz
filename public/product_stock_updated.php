<?php 
if($ipadTag == "yes")
{
	include_once "../basic/parameter.inc";
}
else
{
	include "../model/modelhead.php";
}

$Date=date("y-m-d");
$Sql = "UPDATE $DataIn.productstock SET tStockQty='$lastQty',Date='$Date' WHERE ProductId='$ProductId'";
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

?>