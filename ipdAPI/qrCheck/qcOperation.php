<?php
	
	include "../../basic/parameter.inc";
	include "../../model/subprogram/weightSet.php";
	
	$action = $_POST["action"];
	$weight = $_POST["weight"];
	$weight = str_ireplace("kg", "", $weight);
	$boxId = $_POST["BoxId"];
	$productId = $_POST["ProductId"];
	$Id = $_POST["Id"];
	
	//BoxId = H20131224090217000008;
    //Id = 744;
    //ProductId = 91908;
    //action = 5;
    //weight = "3.41";
    
    /*
    $action = "5";
    $weight = "3.41";
    $boxId = ""
	*/
	$success = "N";
	$alert = "";
	switch($action){
		case "0":{
			//一切正常，将产量登记状态改变
			if(setProductPass($boxId, $weight, $DataIn))
			{
				$success = "Y";
			}
		}
		break;
		case "1":
		case "2":{
			//1为超重、2为欠重,改变产品相应的峰值
			$state = ($action == "1")?"maxWeight":"minWeight";
			$updateProductWeightSql = "Update $DataIn.productdata Set $state = '$weight' Where ProductId = '$productId'";
			if(mysql_query($updateProductWeightSql)){
				//setProductPass($boxId, $DataIn);
				$success = "Y";
				$updateOver = "Update $DataIn.weightchangelist A
							   Left Join $DataIn.sc1_cjtj B On A.boxId = B.boxId
							   Set A.Estate='0',B.Estate='1',B.Weight='$weight'
							   Where A.Id = '$Id'";
				if($updateErrorResult = mysql_query($updateOver)){
					$alert = "更新成功";
				}
				else{
					$alert = "更新失败";
				}
			}
		}
		break;
		case "3":{
			//重置产品单重	
			if(setProductWeight($weight, $productId)){
				if($updateErrorResult = mysql_query("Update $DataIn.weightchangelist Set Estate= '0' Where Id='$Id'")){
					if(setProductPass($boxId, $weight, $DataIn)){
						$success = "Y";
						$alert = "更新成功";
					}
				}
			}
			else{
				$alert = "更新失败";
			}
					
		}
		break;
		case "5":{
			$updateOver = "Update $DataIn.weightchangelist A
							   Left Join $DataIn.sc1_cjtj B On A.boxId = B.boxId
							   Set A.Estate='0',B.Estate='1',B.Weight='$weight'
							   Where A.Id = '$Id'";
			if($updateErrorResult = mysql_query($updateOver)){
				$alert = "更新成功";
			}
			else{
					$alert = "更新失败";
			}
		}
		break;
		case "6":{
			$deleteQty = "delete From $DataIn.sc1_cjtj Where BoxId = '$boxId'";
			if(mysql_query($deleteQty)){
				$success = "Y";
				$alert = "更新成功";
			}else{
				$alert = "更新失败";
			}

		}
		break;
		case "del":{
			
			$deleteWeightList = "Delete From $DataIn.weightchangelist Where BoxId = '$boxId'";
			if(mysql_query($deleteWeightList)){
				$deleteQty = "delete From $DataIn.sc1_cjtj Where BoxId = '$boxId'";
				if(mysql_query($deleteQty)){
					$success = "Y";
					$alert = "更新成功";
				}else{
					$alert = "更新失败";
				}
			}else{
				$alert = "更新失败";
			}
		}
		break;
	}
	
	echo  json_encode(array("$success", "$alert"));
	
	function setProductPass($boxId, $weight, $DataIn)
	{
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
						Where sc.sPOrderId = '$POrderId'
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