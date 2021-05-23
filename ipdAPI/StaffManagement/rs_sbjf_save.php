<?php 

	include "../../basic/parameter.inc";
	
	$newMonth = $_POST["mon"];
	$Operator = $_POST["opration"];
	$Login_cSign = $_POST["cSign"];
	$branch = $_POST["branch"];
	$ids = $_POST["ids"];
	$Date=date("Y-m-d");
	if($ids != "")
	{
		$BranchIdSTR="and M.Number IN ($ids)";
	}
	else
	{
		if($branch != "全部")
		{
			$tmpBranchHolder = explode("-",$branch);
			$branch = $tmpBranchHolder[0];
			$BranchIdSTR = "and M.BranchId='$branch'";
		}
		else
		{
			$BranchIdSTR = "";
		}
	}
	
	$succee = array();
	
if($DataIn=="ac"){
	  $inRecode = "INSERT INTO $DataIn.sbpaysheet SELECT NULL,M.BranchId,M.JobId,M.Number,'$newMonth',T.mAmount,T.cAmount,'$Date','1','0','$Operator','0','0',null,null, null,null 
 FROM $DataPublic.staffmain M,$DataPublic.sbdata S,$DataPublic.rs_sbtype T WHERE 1 $BranchIdSTR AND S.Number=M.Number AND T.Id=S.Type AND M.cSign='$Login_cSign' AND M.Number IN (SELECT sbdata.Number FROM $DataPublic.sbdata WHERE sbdata.sMonth<='$newMonth' AND sbdata.Estate='1') AND M.Number NOT IN (SELECT sbpaysheet.Number FROM $DataIn.sbpaysheet WHERE Month='$newMonth')";
}else{
	  $inRecode = "INSERT INTO $DataIn.sbpaysheet SELECT NULL,M.BranchId,M.JobId,M.Number,'$newMonth',T.mAmount,T.cAmount,'$Date','1','0','$Operator','0' 
 FROM $DataPublic.staffmain M,$DataPublic.sbdata S,$DataPublic.rs_sbtype T WHERE 1 $BranchIdSTR AND S.Number=M.Number AND T.Id=S.Type AND M.cSign='$Login_cSign' AND M.Number IN (SELECT sbdata.Number FROM $DataPublic.sbdata WHERE sbdata.sMonth<='$newMonth' AND sbdata.Estate='1') AND M.Number NOT IN (SELECT sbpaysheet.Number FROM $DataIn.sbpaysheet WHERE Month='$newMonth')";
}
			
	if($inResult=@mysql_query($inRecode))
	{
		$succee[] = array("新增缴费记录成功!");
	}
	else
	{
		$succee[] = array("新增缴费记录失败!");
	}
	
	echo json_encode($succee);
	
?>