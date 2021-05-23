<?php
	
	include "../../basic/parameter.inc";
	
	$month = $_POST["month"];
	//$month = "2013-05";
	$cSign = $_POST["cSign"];
	//$cSign = "7";
	$database = ($cSign == "7")?$DataIn:$DataOut;
	
	$signList = array();
	
	$getDetailWageListSql = "SELECT M.Name, L.Sign, L.Id, M.Number, D.Name as Branch, E.GroupName  FROM $database.cwxzsheet S 
							LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
							Left Join $DataPublic.wage_list_sign L On L.Number = S.Number And L.SignMonth = '$month'
							Left Join $DataPublic.branchdata D On D.Id = M.BranchId
						    Left Join $database.staffgroup E On E.GroupId = M.GroupId
							WHERE S.Month='$month' 
							and (S.Estate = '0' or S.Estate = '3')
							and M.Number not in ('10001','10744')
							and M.Estate = '1'
							and M.JobId != 38
							and M.OffStaffSign = 0
							Order By M.BranchId, M.GroupId, M.Number";
	
	$detailWageListResult = mysql_query($getDetailWageListSql);
	while($detailWageRow = mysql_fetch_assoc($detailWageListResult))
	{
		$id = $detailWageRow["Id"];
		$number = ($detailWageRow["Number"])?$detailWageRow["Number"]:"";
		
		include_once("../../model/subprogram/factoryCheckDate.php");
			if(skipStaff($number))
			{
				continue;
			}

		
		$name = $detailWageRow["Name"];
		$sign = $detailWageRow["Sign"];
		$branch = $detailWageRow["Branch"];
		$group = $detailWageRow["GroupName"];
		$state = ($sign == "")?"no":"yes"; 
		
		$signList[] = array("id"=>"$id", "number"=>"$number", "name"=>"$name", "sign"=>"$sign", "state"=>"$state", "branch"=>"$branch", "group"=>"$group");	
	}
	
	//echo $getDetailWageListSql;
	echo json_encode($signList);

	
	
?>