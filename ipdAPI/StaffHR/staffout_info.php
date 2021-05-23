<?php 
	
	include "../../basic/parameter.inc";
	
	$staffOutArray = array();
	
	$startPosition = $_POST["position"];
	//$startPosition = 0;
	
	$getVolnum = $_POST["volnum"];
	//$getVolnum = 100;
	
	$login_csgin = $_POST["cSign"];
	$login_csgin = "7";

	$staffOutSql = "SELECT D.Id,D.Number,D.Reason,D.outDate,D.Locks,P.Id as MId,P.Name,P.BranchId,P.JobId,P.ComeIn,P.Introducer,S.Sex,S.Rpr,S.Birthday,S.Mobile,S.Idcard,DT.Name AS dName	
	FROM $DataPublic.staffmain P,$DataPublic.staffsheet S,$DataPublic.dimissiondata D,$DataPublic.dimissiontype DT  
	WHERE P.cSign='$login_csgin' 
	AND P.Number=S.Number 
	AND D.Number=P.Number 
	AND DT.Id=D.Type 
	AND P.Estate=0  
	ORDER BY D.outDate DESC,P.BranchId,P.JobId,P.Number 
	Limit $startPosition,$getVolnum";
	
	$no = $startPosition;
	$staffOutResult = mysql_query($staffOutSql);
	while($myRow = mysql_fetch_assoc($staffOutResult))
	{
		$no++;
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Number=$myRow["Number"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$dName=$myRow["dName"];
		$ComeIn=$myRow["ComeIn"];
		$outDate=$myRow["outDate"];
	
		//计算在职时间
		$ThisDay=$outDate;
		$ThisEndDay=$Month."-".date("t",strtotime($ThisDay));		
		$Years=date("Y",strtotime($ThisDay))-date("Y",strtotime($ComeIn));
		$ThisMonth=date("m",strtotime($ThisDay));
		$CominMonth=date("m",strtotime($ComeIn));
		//年计算
		if($ThisMonth<$CominMonth)
		{//计薪月份少于进公司月份
			$Years=($Years-1);
			$MonthSTR=$ThisMonth+12-$CominMonth;
			$gl_STR=$Years<=0?" ":$Years."年";
		}
		else
		{
			$MonthSTR=$ThisMonth-$CominMonth;
			$gl_STR=$Years<=0?" ":$Years."年";
		}

		//月计算
		//如果是当月，如果入职日期是3号之前，则当整月，否则不足月
		if(date("d",strtotime($ComeIn))<4)
		{
			$MonthSTR=$MonthSTR+1;
		}
		$MonthSTR=$MonthSTR>0?$MonthSTR."个月":"";
		$gl_STR=$gl_STR.$MonthSTR;
		$Locks=$myRow["Locks"];
		
		$MId=$myRow["MId"];
		$Age = date('Y', time()) - date('Y', strtotime($Birthday)) - 1;
		if (date('m', time()) == date('m', strtotime($Birthday)))
		{
			if (date('d', time()) > date('d', strtotime($Birthday)))
			{
				$Age++;
			}
		}
		else
		{
			if (date('m', time()) > date('m', strtotime($Birthday)))
			{
				$Age++;
			}
		}
		
		$staffOutArray[] = array("$no","$Name","$Branch","$Job","$dName","$ComeIn","$outDate","$gl_STR","$Id","$Number");
	}
	
	echo json_encode($staffOutArray);
	
?>