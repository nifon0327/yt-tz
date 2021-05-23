<?php 

	include "../../basic/parameter.inc";
	
	$Login_cSign = $_POST["csign"];
	$Login_cSign = "7";
	$no = 0;
	$sbDataArray = array();
	$mySql="SELECT S.Id,S.Number,S.Type,S.sMonth,S.eMonth,S.Note,S.Date,S.Estate,S.Locks,S.Operator,
	M.Name,M.BranchId,M.JobId,M.Estate AS mEsate,T.Name AS Type
	FROM $DataPublic.sbdata S,$DataPublic.staffmain M,$DataPublic.rs_sbtype T 
	WHERE 1 AND S.Number=M.Number  AND M.cSign='$Login_cSign' AND S.Type=T.Id
	ORDER BY  M.Estate DESC,S.Estate DESC,M.BranchId,M.JobId,M.Number,S.Id DESC limit 0,100";
	$myResult = mysql_query($mySql);
	while ($myRow = mysql_fetch_array($myResult))
	{
		$no++;
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Type=$myRow["Type"];
		$sMonth =$myRow["sMonth"];
		$mEsate =$myRow["mEsate"];
		//$Name=$mEsate==1?$myRow["Name"]:"<span class='redB'>$myRow[Name]</span>";
		$Name = $myRow["Name"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$sMonth =$myRow["sMonth"];
		$eMonth =$myRow["eMonth"]==0?"":$myRow["eMonth"];
		$Note =$myRow["Note"]==""?"":$myRow["Note"];
		
		$Estate =$myRow["Estate"]==1?"√":"×";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../../admin/subprogram/staffname.php";
		
		$sbDataArray[] = array("$no","$Name","$Number","$Type","$Branch","$Job","$sMonth","$eMonth","$Note","$Estate","$Date","$Operator","$Id");
	}
	
	echo json_encode($sbDataArray);

?>