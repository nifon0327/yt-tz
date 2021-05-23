<?php 

	include "../../basic/parameter.inc";
	
	$Operator = $_POST["opration"];
	$Login_cSign = $_POST["cSign"];
	
	$newMonth = $_POST["mon"];
	$branch = $_POST["branch"];
	$Type = $_POST["type"];
	$ids = $_POST["ids"];
	$note = $_POST["note"];
	$Date=date("Y-m-d");
	
	$idList = explode(",",$ids);
	
	if($Type == "全部")
	{
		$Type = "";
	}
	else
	{
		$tResult=mysql_query("SELECT Id FROM $DataPublic.rs_sbtype Where Name = '$Type'",$link_id);
		$tRow = mysql_fetch_assoc($tResult);
		$Type = $tRow["Id"];
	}
	
	$result = array();
	
	$count = ($idList[0] == "")?0:count($idList);
	
	for($i=0;$i<$count;$i++)
	{
		$tmpId = $idList[$i];
		$numberResult = mysql_query("Select Number From $DataPublic.staffmain Where Id= '$tmpId'");
		$numberRow = mysql_fetch_assoc($numberResult);
		$StaffSTR = $numberRow["Number"];
		
		$inSql = "INSERT INTO $DataPublic.sbdata (Id,Number,Type,sMonth,eMonth,Note,Date,Estate,Locks,Operator) VALUES (NULL,'$StaffSTR','$Type','$sMonth','','$Note','$Date','1','0','$Operator')";
		
		$inResult = @mysql_query($inSql);
		if($inResult)
		{
			$result[] = $StaffSTR."加入成功!";
		}
		else
		{
			$result[] = $StaffSTR."加入失败!";
		}
	}
	
	echo json_encode($result);

?>