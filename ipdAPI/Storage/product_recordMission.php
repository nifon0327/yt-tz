<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$POrderId = $_POST["POrderId"];
	$GroupMonitor = $_POST["Monitor"];
	$action = $_POST["Action"];
	
	$DateTime = date("Y-m-d H:i:s");
	$success = "N";
	
	$missionHandleSql = "";
	switch($action){
		case "add":{
			//$queryGroupId = mysql_query("Select Id From $DataIn.staffGroup Where GroupLeader = '$GroupMonitor' and Estate = '1'");
			//$groupIdResult = mysql_fetch_assoc($queryGroupId);
			$groupId = $GroupMonitor;
			$missionHandleSql = "replace into $DataIn.sc1_mission (Id, POrderId, Operator, DateTime, Estate, FinishTime) values (NULL, '$POrderId', '$groupId', '$DateTime', '1', NULL)";
		}
		break;
		case "delete":{
			$missionHandleSql = "delete From $DataIn.sc1_mission Where POrderId = '$POrderId'";
		}
		break;
	}
	
	if($missionHandleSql != ""){
		if(mysql_query($missionHandleSql)){
			$success = "Y";
		}
	}
	
	echo $success;
	
?>