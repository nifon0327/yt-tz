<?php

	include "../../basic/parameter.inc";

	$upDataSheet = $_POST["sheet"];
	$checkid = $_POST["ids"];
	$lockTag = $_POST["lock"];

	$checkid = explode(":",$checkid);
	$Log_Funtion = ($lockTag == "0")?"锁定":"解锁";
	$Lens=count($checkid);
	for($i=0;$i<$Lens;$i++)
	{
		$Id=$checkid[$i];
		if($Id!="")
		{
			$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}

	$sql = "UPDATE $DataIn.$upDataSheet SET Locks = '$lockTag' WHERE Id IN ($Ids)";

	$result = mysql_query($sql);
	if($result)
	{
		$Log="记录成功 $Log_Funtion.";
	}
	else
	{
		$Log="记录$Log_Funtion 失败!";
		//$OperationResult="N";
	}

	$result = array();
	$result[] = $Log;

	echo json_encode($result);

?>