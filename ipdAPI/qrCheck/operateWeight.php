<?php
	
	include "../../basic/parameter.inc";
	
	$productId = $_POST["productId"];
	$boxId = $_POST["boxId"];
	$Estate = $_POST["type"];
	$weight = $_POST["weight"];
	$timeStamp = date('Y-m-d H:i:s');
	
	$success = "K";
	
	if($Estate == 1 or $Estate == 3){
		$insertBoxWeightSql = "Insert Into $DataIn.weightchangelist (Id, ProductId, BoxId, Weight, TimeStamp, Estate) Values (NULL, '$productId', '$boxId', '$weight', '$timeStamp', '$Estate')";
		//echo $insertBoxWeightSql;
		if(mysql_query($insertBoxWeightSql)){
			$success = "ok";
		}else{
			$success = $insertBoxWeightSql;
		}
	}
	else if($Estate == 0){ //通过记录
		if(setProductPass($boxId, $weight, $DataIn)){
			$success = "ok";
		}else{
			$success = "Update $DataIn.sc1_cjtj Set Estate = '1', Weight = '$weight' Where boxId = '$boxId'";
		}
	}
	else if($Estate == 4){ //从服务器端删除记录
		$deleteQty = "delete From $DataIn.sc1_cjtj Where BoxId = '$boxId'";
		if(mysql_query($deleteQty)){
			$success = "ok";
			$alert = "更新成功";
		}
	}
	
	echo "$success";
	
?>

<?php
	
	function setProductPass($boxId, $weight, $DataIn){
		$updateProductPassSql = "Update $DataIn.sc1_cjtj Set Estate = '1', Weight = '$weight' Where boxId = '$boxId'";
		$result = mysql_query($updateProductPassSql);
		isFinishAssemble($boxId, $DataIn);
		return $result;
	}

	function isFinishAssemble($boxId, $DataIn){
		$finishResult = 'N';
		$getPOrderIdSql = "SELECT POrderId FROM $DataIn.sc1_cjtj WHERE BoxId = '$boxId'";
		$getPOrderIdResult = mysql_query($getPOrderIdSql);
		$POrderIdRow = mysql_fetch_assoc($getPOrderIdResult);
		$POrderId = $POrderIdRow['POrderId'];
		if($POrderId && $POrderId !== ''){
			$isFinishSql = "SELECT SUM(sc.Qty) as Qty, ordersheet.Qty as orderQty
						From $DataIn.sc1_cjtj AS sc
						Left Join $DataIn.yw1_scsheet ordersheet On ordersheet.sPOrderId = sc.sPOrderId
						Where sc.POrderId = '$POrderId'
						And sc.Estate = 1";
			$isFinishResult = mysql_query($isFinishSql);
			$isFinishRow = mysql_fetch_assoc($isFinishResult);
			$scQty = $isFinishRow['Qty'];
			$orderQty = $isFinishRow['orderQty'];
			if(intval($orderQty) == intval($scQty)){
				$updateScStateSql = "UPDATE $DataIn.yw1_scsheet Set scFrom=0 WHERE sPOrderId = $POrderId";
				if(mysql_query($updateScStateSql)){
					$finishResult = 'Y';
				}else{
					$finishResult = 'E';
				}
			}else{
				$finishResult = 'Y';
			}

		}
	}
	
?>