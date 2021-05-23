<?php
	
	function setProductWeight($weight, $productId)
	{
		$weight = $weight * 1000;
		include("weightCalculate.php");
		
		$signleWeight = round(($weight-$extraWeight)/$boxPcs,2);
		$setProductWeight = "Update $DataIn.productdata Set Weight = '$signleWeight', maxWeight='0.00',minWeight='0.00' Where ProductId = '$productId'";
		$setProductWeightResult = mysql_query($setProductWeight);
		
		return $setProductWeightResult;
	}
		
?>