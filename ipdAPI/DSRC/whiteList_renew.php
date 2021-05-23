<?php
	
	include_once "../../basic/parameter.inc";
	
	$upload = $_POST["upLoad"];
	$delete = $_POST["delete"];
	$operator = $_POST["operator"];
	
	$Date=date("Y-m-d");
	$upLoadSucceed = "N";
	$deleteSucceed = "N";
	$succeed = "N";
	
	//处理新增
	if($upload != "")
	{
		$eachCard = explode("|", $upload);
		$queueSet = array();
		for($i =0 ;$i < count($eachCard); $i++)
		{
			$detailInfo = explode(":", $eachCard[$i]);
			$cardNumber = $detailInfo[0];
			$cardHolder = $detailInfo[1];
			$carBorad = $detailInfo[2];
			$queueSet[] = "(NULL, '$cardNumber', '$cardHolder', '$carBorad', '$Date', '$operator')";
		}
			
		$targetQueue = implode(",", $queueSet);
		$uploadQuery = "Insert Into $DataIn.dsrc_list (id, cardNumber, cardHolder, CarNum, Date, Operator) Values $targetQueue";
		
		if(mysql_query($uploadQuery))
		{
			$upLoadSucceed = "Y";
		}
	}
	
		
	//处理删除
	if($delete != "")
	{
		$deleteSql = "Delete From $DataIn.dsrc_list Where id in ($delete)";
		
		if(mysql_query($deleteSql))
		{
			$deleteSucceed = "Y";
		}
	}
	
	if(($upLoadSucceed == "Y" && $deleteSucceed == "Y") || ($upLoadSucceed == "Y" && $delete == "") || ($upload == "" && $deleteSucceed == "Y"))
	{
		$succeed = "Y";
	}
	
	echo $uploadQuery;
	
?>