<?php
	
	include_once "../../basic/parameter.inc";
	
	$POrderId = $_POST["POrderId"];
	$finishTime = date('Y-m-d H:i:s');

	$success = "N";
	$UpdateSql="UPDATE $DataIn.yw1_ordersheet A
				Left Join $DataIn.sc1_mission B On B.POrderId = A.POrderId
			    Set A.scFrom=0, A.Estate=2, B.Estate = 0, B.FinishTime = '$finishTime'
			    Where A.POrderId='$POrderId'";

	if(mysql_query($UpdateSql))
	{
		$success = "Y";
	}

	echo $success;
?>