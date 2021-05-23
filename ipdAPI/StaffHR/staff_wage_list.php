<?php 
	
	include "../../basic/parameter.inc";
	
	//$wageArray = array();
	
	$wageSql = "Select A.Id,A.cSign,A.Month,A.Estate,A.FileName,B.Name From $DataPublic.wage_list A
				Left Join $DataPublic.staffmain B On B.Number = A.Operator
				WHERE A.cSign = 7
				Order By A.Month Desc";		
	
	$wageResult = mysql_query($wageSql);
	
	$wageArray = array();
	while($wageRows = mysql_fetch_assoc($wageResult))
	{
		$cSign = $wageRows["cSign"];
		
		$Id = $wageRows["Id"];
		$Month = $wageRows["Month"];
		$fileName = $wageRows["FileName"];
		$Name = $wageRows["Name"];
		$Estate = $wageRows["Estate"];
		
		$wageArray[] = array("Id"=>"$Id", "Month"=>"$Month", "Filename"=>"$fileName", "Csign"=>"$cSign", "State"=>"$Estate");
		
	}
	
	echo json_encode($wageArray);
	
?>