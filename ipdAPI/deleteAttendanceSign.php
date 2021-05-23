<?php

	include_once("../model/modelhead.php");
	
	isFinishAssemble('A20150327083630000024', $DataIn);






	function isFinishAssemble($boxId, $DataIn){
		$finishResult = 'N';
		$getPOrderIdSql = "SELECT POrderId FROM $DataIn.sc1_cjtj WHERE BoxId = '$boxId'";
		$getPOrderIdResult = mysql_query($getPOrderIdSql);
		$POrderIdRow = mysql_fetch_assoc($getPOrderIdResult);
		$POrderId = $POrderIdRow['POrderId'];
		if($POrderId && $POrderId !== ''){
			$isFinishSql = "SELECT SUM(sc.Qty) as Qty, ordersheet.Qty as orderQty
						From $DataIn.sc1_cjtj AS sc
						Left Join $DataIn.yw1_ordersheet ordersheet On ordersheet.POrderId = sc.POrderId
						Where sc.POrderId = '$POrderId'
						And sc.Estate = 1";
			echo $isFinishSql.'<br>';
			$isFinishResult = mysql_query($isFinishSql);
			$isFinishRow = mysql_fetch_assoc($isFinishResult);
			$scQty = $isFinishRow['Qty'];
			$orderQty = $isFinishRow['orderQty'];
			if(intval($orderQty) == intval($scQty)){
				$updateScStateSql = "UPDATE $DataIn.yw1_ordersheet Set scFrom=0 WHERE POrderId = $POrderId";
				echo $updateScStateSql;
				// if(mysql_query($updateScStateSql)){
				// 	$finishResult = 'Y';
				// }else{
				// 	$finishResult = 'E';
				// }
			}else{
				$finishResult = 'Y';
			}

		}
	}
?>