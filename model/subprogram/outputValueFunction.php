<?php

	function outputValue($POrderId, $qty, $DataIn, $link_id)
	{
		$sumPrice = 0.0;
		$selectProdcutValueSql = "Select A.Price From $DataIn.cg1_stocksheet A
								  Left join $DataIn.StuffData B On B.StuffId = A.StuffId
								  Left join $DataIn.StuffType C On C.TypeId = B.TypeId
								  Where A.POrderId = '$POrderId'
								  And C.mainType = '3'";


		$productPriceResult = mysql_query($selectProdcutValueSql, $link_id);
		if(mysql_num_rows($productPriceResult) > 0)
		{
			$productPriceRow = mysql_fetch_assoc($productPriceResult);
			$price = $productPriceRow["Price"];
			$sumPrice = round($price * $qty, 2);

		}

		return sprintf("%.2f",$sumPrice);

	}

?>