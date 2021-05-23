<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$groups = array();
	$groupListSql = "Select A.GroupName,B.Number,B.Name,A.Id
					From $DataIn.staffgroup A
					Left Join $DataPublic.staffmain B On B.Number = A.GroupLeader
					Where A.TypeId = '7100' and A.Estate = '1'";
	
	$groupListResult = mysql_query($groupListSql);
	while($groupListRow = mysql_fetch_assoc($groupListResult))
	{
		$groupName = $groupListRow["GroupName"];
		$groupName = str_replace("组装", "Line ", $groupName);
		$number = $groupListRow["Id"];
		$name = $groupListRow["Name"];
		
		$groups[] = array("$groupName  $name", "$number", "$name");
	}
	
	echo json_encode($groups);
	
?>