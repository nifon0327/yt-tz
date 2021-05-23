<?php 

	include "../../basic/parameter.inc";

	$login_csgin = $_GET["csgin"];
	$login_csgin = 7;
	$staffOutArray = array();

	$staffOutSql = "SELECT 
	D.Id,D.Number,D.Reason,D.outDate,D.Locks,P.Id as MId,P.Name,P.BranchId,P.JobId,P.ComeIn,P.Introducer,S.Sex,S.Rpr,S.Birthday,S.Mobile,S.Idcard,DT.Name AS dName	FROM $DataPublic.staffmain P,$DataPublic.staffsheet S,$DataPublic.dimissiondata D,$DataPublic.dimissiontype DT  WHERE P.cSign='$login_csgin' AND P.Number=S.Number AND D.Number=P.Number AND DT.Id=D.Type AND P.Estate=0  ORDER BY D.outDate DESC,P.BranchId,P.JobId,P.Number ";
	
	$no = 0;
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
		$Mobile=$myRow["Mobile"]==""?"":$myRow["Mobile"];
		$dName=$myRow["dName"];
		$Reason=$myRow["Reason"]==""?"":$myRow["Reason"];
		$ComeIn=$myRow["ComeIn"];
		$outDate=$myRow["outDate"];
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Birthday=$myRow["Birthday"];
		$Rpr=$myRow["Rpr"];
		//$Idcard=$myRow["Idcard"];
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if($rRow = mysql_fetch_array($rResult))
		{
			$Rpr=$rRow["Name"];
		}
		$Idcard=$myRow["Idcard"]==""?"":$myRow["Idcard"];
		$sbResult = mysql_query("SELECT Id FROM $DataPublic.sbdata WHERE Number=$Number order by Id LIMIT 1",$link_id);
		/*
		if($sbRow = mysql_fetch_array($sbResult))
		{
			$Sb="<a href='staff_sbview.php?Number=$Number' target='_blank'>查看</a>";
		}
		else
		{
			$Sb="&nbsp;";
		}
		*/
		$Introducer=$myRow["Introducer"];
		if($Introducer != "")
		{
			$iResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Introducer order by Id",$link_id);
			if($iRow = mysql_fetch_array($iResult))
			{
				$Introducer=$iRow["Name"];
			}
		}		
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
		
		$staffOutArray[] = array("$no","$Name","$Number","$Idcard","$Branch","$Job","$Mobile","$dName","$Reason","$ComeIn","$outDate","$gl_STR","$Sex","$Rpr","","$Introducer","$Id");
	}
	
	echo json_encode($staffOutArray);

?>