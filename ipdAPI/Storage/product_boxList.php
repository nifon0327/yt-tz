<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$POrderId = $_POST["POrderId"];
	
	$boxList = array();
	$boxListSql = "Select A.Id, A.boxId, A.Date, A.Qty, A.Weight, A.Estate, B.Weight as RealWeigh, B.Id as WId From $DataIn.sc1_cjtj A
				   Left Join $DataIn.weightchangelist B On B.BoxId = A.boxId
				   Where A.POrderId = '$POrderId' And A.TypeId = '7100' Order by A.Estate, A.Date";
				   
	$boxListResult = mysql_query($boxListSql);
	while($boxListRow = mysql_fetch_assoc($boxListResult))
	{
		$Id = $boxListRow["Id"];
		$boxId = $boxListRow["boxId"];
		$date = $boxListRow["Date"];
		$qty = $boxListRow["Qty"];
		$weight = $boxListRow["Weight"];
		$estate = $boxListRow["Estate"];
		$realWeight = $boxListRow["RealWeigh"];
		$wId = $boxListRow["WId"];
		
		$boxList[] = array("Id"=>"$Id", "BoxId"=>"$boxId", "Date"=>"$date", "Qty"=>"$qty", "Weight"=>"$weight", "Estate"=>"$estate", "RealWeight"=>"$realWeight", "wId"=>"$wId");
		
	}
	
	echo json_encode($boxList);
	
?>