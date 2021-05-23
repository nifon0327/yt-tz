<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$action = $_POST["Action"];
	$boxId = $_POST["boxId"];
	
	$success = "N";
	switch($action)
	{
		case "0":
		{
			$updateProductPassSql = "Update $DataIn.sc1_cjtj Set Estate = '1' Where boxId = '$boxId'";
			if(mysql_query($updateProductPassSql))
			{
				$success = "Y";
			}
		}
		break;
		case "1":
		{
			$deleteProductSql = "Delete From $DataIn.sc1_cjtj Where boxId = '$boxId'";
			if(mysql_query($deleteProductSql))
			{
				$success = "Y";
			}
		}
		break;
	}
	
	echo $success;
	
?>