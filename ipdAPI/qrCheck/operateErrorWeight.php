<?php
	
	include "../../basic/parameter.inc";
	include "../../model/subprogram/weightSet.php";
	
	$boxId = $_POST["boxId"];
	$pOrderId = $_POST["POrderId"];
	$type = $_POST["type"];
	$weight = $_POST["weight"];
	$productId = $_POST["productId"];
	
	/*
$pOrderId = "201401240805";
	$type = "pass";
	$boxId = "I20140408173903000001";
	$weight = "7.64";
	$productId = "86318";
*/
	
	$alert = "更新失败";
	$success = "N";
	
	switch($type){
		case "reset":{
			//先算出平均值
			$maxWeightsSql = mysql_query("SELECT count(Weight) as count FROM $DataIn.sc1_cjtj WHERE sPOrderId = '$pOrderId'");
			$weightCountResult = mysql_fetch_assoc($maxWeightsSql);
			$count = $weightCountResult['count'];

			if($count > 10){
				$limitValueSql = mysql_query("SELECT MAX(Weight) as MaxWeight,MIN(Weight) as MinWeight FROM $DataIn.sc1_cjtj Where sPOrderId = '$pOrderId' and Weight != 0 and Weight < 100");
				$limitValueReuslt = mysql_fetch_assoc($limitValueSql);
				$maxWeight = $limitValueReuslt['MaxWeight'];
				$minWeight = $limitValueReuslt['MinWeight'];

				$avgerWeightSql = mysql_query("Select AVG(Weight) as weight From $DataIn.sc1_cjtj Where sPOrderId = '$pOrderId' and Weight != 0 and Weight < 100 and weight not in ($maxWeight, $minWeight)");
			}
			else{
				$avgerWeightSql = mysql_query("Select AVG(Weight) as weight From $DataIn.sc1_cjtj Where sPOrderId = '$pOrderId' and Weight != 0 and Weight < 100");
			}
			$avgerWeightResult = mysql_fetch_assoc($avgerWeightSql);
			$avgWeight = $weight;
			if(mysql_num_rows($avgerWeightSql) == 0){
				$avgWeight = sprintf("%.2f", $avgerWeightResult["weight"]+0.005);
			}
			if(setProductWeight($avgWeight, $productId)){
				if(setProductPass($boxId, $weight, $DataIn)){
					$success = "Y";
					$alert = "更新成功";
				}
			}
			
		}
		break;
		case "pass":{
			if(setProductPass($boxId, $weight, $DataIn)){
				$success = "Y";
				$alert = "更新成功";
			}
		}
		break;
		case "delete":{
			$deleteWeightList = "Delete From $DataIn.weightchangelist Where BoxId = '$boxId'";
			if(mysql_query($deleteWeightList)){
				$deleteQty = "delete From $DataIn.sc1_cjtj Where BoxId = '$boxId'";
				if(mysql_query($deleteQty)){
					$success = "Y";
					$alert = "更新成功";
				}
			}
		}
		break;
	}
	
	echo  json_encode(array("$success", "$alert"));
	
?>

<?php
	function setProductPass($boxId, $weight, $DataIn){
		$updateProductPassSql = "Update $DataIn.weightchangelist A
							   Left Join $DataIn.sc1_cjtj B On A.boxId = B.boxId
							   Set A.Estate='0',B.Estate='1',B.Weight='$weight'
							   Where A.boxId = '$boxId'";
							   
		//echo $updateProductPassSql;
		return mysql_query($updateProductPassSql);
	}
	
?>