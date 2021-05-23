<?php
	
	include_once "../../basic/parameter.inc";
	
	$whiteListSql = "Select * from $DataIn.dsrc_list";
	$whiteListResult = mysql_query($whiteListSql);
	$whiteList = array();
	while($whiteRow = mysql_fetch_assoc($whiteListResult))
	{
		$id = $whiteRow["Id"];
		$cardNumber = $whiteRow["CardNumber"];
		$cardHolder = $whiteRow["CardHolder"];
		$carType = $whiteRow["Type"];
		if($cardHolder == "")
		{
			$cardHolder = "未设置";
		}
		$carBoard = $whiteRow["CarNum"];
		
		
		
		$whiteList[] = array("$cardNumber", "$cardHolder", "$carBoard", "$id", "$carType");
	}
	
	echo json_encode($whiteList);
	
?>