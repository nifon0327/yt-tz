<?php
	
	include "../basic/parameter.inc";
	include "../model/subprogram/weightSet.php";
	
	$productId = "95625";
	$weight = "3.71";
	$weight = $weight * 1000;
	include("../model/subprogram/weightCalculate.php");
	$signleWeight = round(($weight-$extraWeight)/$boxPcs,2);
	
	echo "$signleWeight  $extraWeight   $boxPcs   $erorType";

?>