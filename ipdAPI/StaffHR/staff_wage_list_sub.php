<?php 
	
	include "../../basic/parameter.inc";
	
	$month = $_POST["Month"];
	//$month = "2012-08";
	$location = $_POST["location"];
	//$location = "0";
	$type = $_POST["type"];
	$dataFrom = ($location == "0")?"d7":"ptsub";
	$date = date("Y-m-d");
	$payDate = substr($date, 0, 7);
	
	$wagelist = array();
	
	$wageSql = "SELECT M.Name, L.Sign, M.Number FROM $dataFrom.cwxzsheet S 
				LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
				LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
				LEFT JOIN $DataPublic.jobdata  J ON J.Id=S.JobId
				Left Join $DataPublic.wage_list_sign L On L.Number = S.Number And L.SignMonth = '$month'
				WHERE 1 
				and S.Month='$month' 
				and (S.Estate = '0' or S.Estate = '3')
				and M.Number != '10001'
				and M.Estate = '1'
				order by B.SortId,S.JobId,M.ComeIn";
					  
	$wageResult = mysql_query($wageSql);
	while($wageRows = mysql_fetch_assoc($wageResult))
	{
		if($type != "forPdf")
		{
			$signFlag = ($wageRows["Sign"])?"已签":"未签";
		}
		else
		{
			$signFlag = $wageRows["Sign"];
		}
		$wagelist[] = array($wageRows["Name"], $signFlag, $wageRows["Number"]); 
	}
	
	echo json_encode($wagelist);
	
?>