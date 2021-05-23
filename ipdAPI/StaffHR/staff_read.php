<?php 
	
	include "../../basic/parameter.inc";
	
	$cSign = $_POST["cSign"];
	$staffInfo = array();
	
	$staffReadSql = "SELECT 
	M.Id,M.Number,M.Name,M.Grade,M.ComeIn,M.Introducer,M.FormalSign,M.Estate,M.Locks,M.Date,M.Operator,
	S.Sex,S.Rpr,S.Idcard,M.KqSign,B.Name AS Branch,J.Name AS Job,G.GroupName
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId 
	WHERE 1 AND M.Estate=1 and M.cSign = '$cSign' ORDER BY M.BranchId,M.GroupId,M.JobId,M.ComeIn,M.Number";
	
	
	
	$i=1;
	$staffReadResult = mysql_query($staffReadSql);
	while($staffRow = mysql_fetch_assoc($staffReadResult))
	{
		$name = $staffRow["Name"];
		$number = $staffRow["Number"];
		$sex = ($staffRow["Sex"]==1)?"男":"女";
		$rpr = $staffRow["Rpr"];
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id='$rpr' order by Id",$link_id);
		if ($rResult )
		{
			if($rRow = mysql_fetch_array($rResult))
			{
				$rpr=$rRow["Name"];
			}
		}

		
		$idCard = $staffRow["Idcard"];
		$branch = $staffRow["Branch"];
		$job = $staffRow["Job"];
		$group = $staffRow["GroupName"];
		$kqSign = $staffRow["KqSign"];
		$kqSign = ($kqSign == 1)?"√":"";
		$ComeIn = $staffRow["ComeIn"];
		$glPad = "";
		include "../../public/subprogram/staff_model_gl.php"; //$glPad
		$introducer = $staffRow["Introducer"];
		if ($introducer){
			$iResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$introducer order by Id",$link_id);
			if($iRow = mysql_fetch_array($iResult)){
				   $introducer=$iRow["Name"];
			}
		}
		else
		{
			$introducer="";
		}
		//不显示，但需要的数据
		$id = $staffRow["Id"];
		$grade = $staffRow["Grade"];
		$formalSign = $staffRow["FormalSign"];
		$estate = $staffRow["Estate"];
		
		$staffInfo[] = array("$i","$name", "$number", "$sex", "$rpr", "$idCard", "$branch", "$job", "$group", "$kqSign", "$ComeIn", "$glPad", "$introducer", "$id", "$grade", "$formalSign", "$estate");
		$i++;
	}
	
	echo json_encode($staffInfo);
	
?>