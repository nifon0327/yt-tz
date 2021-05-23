<?php 
	
	include "../../basic/parameter.inc";
	
	$pushType = $_POST["type"];
	$pushType = "noHealth";
	
	if($pushType == "noHealth")
	{
		$data = array();
	
		$noHealthSql = "SELECT M.Name, M.Number, M.BranchId
								   FROM $DataPublic.staffmain M
								   LEFT JOIN $DataPublic.staffsheet S ON S.Number = M.Number
								   WHERE S.HealthPhoto =  '0'
								   AND M.Estate =  '1' 
								   AND M.BranchId IN ( 5, 7, 9 ) 
								   ORDER BY M.BranchId, M.Number";
								   
		$noHealthResult = mysql_query($noHealthSql);
		while($noHealthRow = mysql_fetch_assoc($noHealthResult))
		{
			$Name = $noHealthRow["Name"];
			$Number = $noHealthRow["Number"];
			$Branch = $noHealthRow["BranchId"];
			$BranchResult = mysql_query("Select Name From $DataPublic.branchdata Where Id = '$Branch'");
			$BranchRow = mysql_fetch_assoc($BranchResult);
			$Branch = $BranchRow["Name"];
			
			$data[] = array($Name,$Number,$Branch);	
		}			
	}
	
	echo json_encode($data);
	
	
?>