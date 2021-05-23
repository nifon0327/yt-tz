<?php 

	include "../../basic/parameter.inc";
	
	$glArray = array();
	$no = 0;
	$mySql="SELECT S.Id,S.Month,S.Months,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,M.Name,M.Number  FROM $DataPublic.rs_kcgl S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
WHERE 1  AND S.Estate=1 ORDER BY S.Date DESC";
	$myResult = mysql_query($mySql,$link_id);
	
	while($myRow = mysql_fetch_array($myResult))
	{
		
		$m=1;
		$no++;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Number = $myRow["Number"];
		$Months=$myRow["Months"];
		$Month=$myRow["Month"];
		$Remark=$myRow["Remark"]==""?"":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"√":"×";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		
		$pResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Operator ORDER BY Number LIMIT 1",$link_id);
		if($pRow = mysql_fetch_array($pResult))
		{
			$Operator=$pRow["Name"];
		}
		
		$glArray[] = array("$no","$Name","$Number","$Months","$Month","$Remark","$Estate","$Date","$Operator","$Id");
		
	}
	
	echo json_encode($glArray);
	
?>