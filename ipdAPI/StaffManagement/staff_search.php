<?php

	include "../../basic/parameter.inc";
	$filter = $_POST["filter"];
	//$filter = "6";
	$Login_cSign = $_POST["cSign"];
	//$Login_cSign = "7";
	$Month = date("Y-m");

	switch($filter){
	case"0"://来自新增社保资料，需过滤已加入社保的记录OKM.cSign='$Login_cSign' and
		$NumberSTR="  AND  M.Estate=1 and M.Number NOT IN(SELECT Number FROM $DataPublic.sbdata ORDER BY Number)".$BranchIdSTR;
		break;
	case"1"://来自于登录帐户ok  不过滤楼层
		$NumberSTR=" and M.Estate=1 and M.Number NOT IN(SELECT Number FROM $DataIn.usertable ORDER BY Number)";
	break;
	case "2"://来自员工等级设定OKM.cSign='$Login_cSign'  and
		$NumberSTR=" AND M.Estate=1 ".$BranchIdSTR.$JobIdSTR;
	break;
	case "3"://来自考勤:过滤 1:考勤有效		2：考勤无效		3：无须考勤(过滤)OKM.cSign='$Login_cSign' and
		//$NumberSTR="  AND M.Estate=1 and M.KqSign!=3 ".$BranchIdSTR.$JobIdSTR;
		$NumberSTR="  AND M.Estate=1  ".$BranchIdSTR.$JobIdSTR;
	break;
	case "4"://来自部门界定OKM.cSign='$Login_cSign'  and
		$NumberSTR=" AND M.Estate=1 ".$BranchIdSTR;
	break;
	case "5"://来自职位设定OKM.cSign='$Login_cSign' and
		$NumberSTR="  AND M.Estate=1 ".$JobIdSTR;
	break;
	case "6"://社保有效 且当月没有缴费的     来自社保缴费记录 OK" ".
		$MonthSTR=$Month==""?"":" and M.Number NOT IN(SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$Month' ORDER BY Number)";
		$NumberSTR="  AND M.cSign='$Login_cSign' and M.Number IN(SELECT Number FROM $DataPublic.sbdata WHERE eMonth='' OR eMonth>'$Month' OR eMonth='$Month' ORDER BY Number) ".$MonthSTR.$BranchIdSTR;
	break;
	case "7"://来自考勤调动M.cSign='$Login_cSign' and
		$NumberSTR=" AND M.Estate=1 ".$KqSignSTR;
	break;
	case "8"://M.cSign='7' and
		$NumberSTR="  AND M.Estate=1 and M.KqSign=3";
	break;
	case "9":
	    $NumberSTR=" AND M.Estate=1 AND M.JobId>10 AND M.GroupId=0 ".$JobIdSTR;
		//$NumberSTR=" AND M.Estate=1 AND M.BranchId=5 AND M.JobId>10 ".$JobIdSTR." AND M.Number NOT IN(SELECT Number FROM $DataIn.sc1_member WHERE 1 ORDER BY Number)";
		break;
	case "10"://来自员工等级设定OKM.cSign='$Login_cSign'  and
		$NumberSTR=" AND M.Estate=1 ".$BranchIdSTR.$JobIdSTR;
	break;
	}

	$staffList = array();

	$mySql="SELECT 
	M.Id,M.Number,M.Name,M.ComeIn,B.Name AS BranchName,J.Name AS JobName,G.GroupName
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataPublic.staffsheet S ON M.Number=S.Number
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	LEFT JOIN  $DataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	WHERE 1 $NumberSTR  ORDER BY M.BranchId,M.GroupId,M.Number";

	$mySqlResult = mysql_query($mySql);
	while($myRow =  mysql_fetch_assoc($mySqlResult))
	{

		$id = $myRow["Id"];
		$number = $myRow["Number"];
		$name = $myRow["Name"];
		$branch = $myRow["BranchName"];
		$job = $myRow["JobName"];
		$group = $myRow["GroupName"];
		$comeIn = $myRow["ComeIn"];

		$staffList[] = array($name,$number,$branch,$job,$group,$comeIn,$id);
	}

	//print_r($staffList);
	echo json_encode($staffList);

?>