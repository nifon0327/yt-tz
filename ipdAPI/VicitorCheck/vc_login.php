<?php
	
	include_once "../../basic/parameter.inc";
	
	$UserName = $_POST["username"];
	$Password = $_POST["password"];
	$Password = md5($Password);
	
	$mySql="SELECT U.uType,U.Id,U.uName,U.Number,U.Estate,M.Name,M.GroupId,G.GroupName,G.TypeId
			FROM $DataIn.UserTable U 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=U.Number
			LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
			WHERE 1 
			AND U.uName='$UserName' 
			AND U.uPwd='$Password' 
			AND U.uType=1 
			ORDER BY U.Id 
			LIMIT 1";
	
	$loginResult = mysql_query($mySql);
	if($loginResult)
	{
		$succeed = "Y";
		$loginRow = mysql_fetch_assoc($loginResult);
		$operatorNumber = $loginRow["Number"];
		$operatorName = $loginRow["Name"];
		
		if($operatorNumber == "10744" || $operatorNumber == "11008")
		{
			$power = "31";
		}
		else
		{
			$power = "1";
		}
		
	}
	else
	{
		$succeed = "N";
	}
	
	echo json_encode(array("$succeed", "$operatorNumber", "$operatorName", "$power"));

	
?>