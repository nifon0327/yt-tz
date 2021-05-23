<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$isBl = $_POST["isBl"];
	
	$searchRow = "";
	if($isBl == "yes")
	{
		$searchRow = " Where A.Estate = '1' ";
	}
	else
	{
		$searchRow = " Where A.Estate > '0' ";
	}
	
	$missions = array();
	$missionListSql = "Select SUM(B.Qty) as Qty,  C.GroupName, A.Operator From $DataIn.sc1_mission A
					Left Join $DataIn.yw1_ordersheet B On B.POrderId = A.POrderId
					Left Join $DataIn.staffgroup C On C.Id = A.Operator
					$searchRow
					and B.scFrom != '0'
					Group by A.Operator
					Order By C.Id";
	
	$missionResult = mysql_query($missionListSql);
	while($missionRow = mysql_fetch_assoc($missionResult))
	{
		$qty = $missionRow["Qty"];
		$groupLeader = $missionRow["Operator"];
		
		$countOperatorSql = mysql_query("Select Count(Operator) as count From $DataIn.sc1_mission A
										 Left Join $DataIn.yw1_ordersheet B On B.POrderId = A.POrderId
									     $searchRow and A.Operator = '$groupLeader' and B.scFrom != '0'");
		$countOperatorResult = mysql_fetch_assoc($countOperatorSql);
		$count = $countOperatorResult["count"];
		
		$line = str_replace("组装", "Line ", $missionRow["GroupName"]);
		$missions[] = array("Qty"=>"$qty", "Count"=>"$count", "Line"=>"$line");
	}
	
	echo json_encode($missions);
	
?>