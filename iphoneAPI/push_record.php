<?php

	include "../basic/parameter.inc";
	
	$token = $_POST["token"];
	$token = substr($token,1,strlen($token));
	$token = substr($token,0,strlen($token)-1);
	
	$bundleId = $_POST["bundleId"];
	$userId = strtolower($_POST["userId"]);
	$date = date("Y-m-d");
	if ($bundleId!=""){
		  if ($bundleId=="AshCloudApp"){
			  $sql = "Replace into $DataIn.push_mainapp(Id,token,bundleId,userId,Date) Values (NULL,'$token','$bundleId','$userId','$date')";
			  $tokenResult = mysql_query($sql);
		  }
		  else{
			$sql = "Replace into $DataIn.push_clientapp(Id,token,bundleId,userId,Date) Values (NULL,'$token','$bundleId','$userId','$date')";
			$tokenResult = mysql_query($sql);
		   }
	}
?>