<?php
	
	include_once "../../basic/parameter.inc";
	
	$productType = $_POST["type"];
	//$productType = "8055";
	
	$productReader = "Select P.Id,P.ProductId,P.cName,P.Estate FROM $DataIn.productdata P
					  LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
					  Where P.CompanyId In (1004, 1059, 1064, 1066, 1080, 1065)
					  And P.TypeId = '$productType'
					  And P.TestStandard = '1'
					  ORDER BY P.Estate DESC,P.Id DESC ";
	
	$products = array();
	$productResult = mysql_query($productReader);
	while($productRow = mysql_fetch_assoc($productResult))
	{
		$productId = $productRow["ProductId"];
		$cName = $productRow["cName"];
		$id = $productRow["Id"];
		
		$checkAllQty= mysql_query("SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders 
								   FROM
								   ( SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									 LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									 WHERE P.eCode LIKE (SELECT eCode FROM $DataIn.productdata WHERE ProductId='$productId') 
									 GROUP BY OrderPO) A",$link_id);
		
		$checkRow = mysql_fetch_assoc($checkAllQty);
		$allQty = $checkRow["ALLQTY"];
		$orders = $checkRow["Orders"];
		
		$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$productId'",$link_id);
		$shipQtyRow = mysql_fetch_assoc($checkShipQty);
		$shipQty = $shipQtyRow["ShipQty"];
		
		$allQty = intval($allQty);
		$allQtyCount = number_format($allQty);
		
		$shipQty = intval($shipQty);
		$shipQtyCount = number_format($shipQty);
		
		$products[] = array("$productId", "$cName", "$id", "$allQtyCount", "$orders", "$shipQtyCount");
	}
	
	echo json_encode($products);
	
?>