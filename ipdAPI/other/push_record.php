<?php 

	include "../basic/parameter.inc";
	
	$token = $_POST["token"];
	$token = substr($token,1,strlen($token));
	$token = substr($token,0,strlen($token)-1);
	
	$bundleId = $_POST["bundlId"];
	$cSign = $_POST["cSign"];
	$date = date("Y-m-d");
	$sql = "Replace into $DataIn.push_app (Id,token,bundleId,Number,Date) Values (NULL,'$token','$bundleId','$cSign','$date')";
	$tokenResult = mysql_query($sql);
	/*
	$hasTokenSql = "Select * From $DataIn.push_app Where token = '$token'";
	$hasTokenResult = mysql_query($hasTokenSql);
	if(mysql_num_rows($hasTokenResult) == 0)
	{
		//$dd = "Insert Into $DataIn.push_app (Id,token,bundleId,Date) Values (NULL,'$token','$bundleId','$date')";
		$tokenResult = mysql_query("Insert Into $DataIn.push_app (Id,token,bundleId,Date) Values (NULL,'$token','$bundleId','$date')");
	}
	*/
	//$info[] = $tokenResult;
	//echo json_encode($info);

?>