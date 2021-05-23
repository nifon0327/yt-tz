<?php
	
	include "../../basic/parameter.inc";
	include "../../model/modelfunction.php";
	include("getStaffNumber.php");
	
	$num = $_POST["number"];
	//$num = "11710";
	if(strlen($num) != 5)
	{
		$num = getStaffNumber($num, $DataPublic);
	}
	
	$month = $_POST["month"];
	//$month = "2013-05";
	
	$personalAttendanceSql = "SELECT K.Id,K.Month,K.Number,K.Dhours,K.Whours,K.Ghours,K.GOverTime,K.GDropTime,K.Xhours,K.XOverTime,K.XDropTime,K.Fhours,K.FOverTime,K.FDropTime,K.InLates,K.OutEarlys,K.SJhours,K.BJhours,K.YXJhours,K.WXJhours,K.QQhours,K.WXhours,K.KGhours,K.dkhours,K.YBs,K.Locks,M.Name,M.Estate,B.Name AS Branch,J.Name AS Job
							  FROM $DataIn.kqdata K 
							  LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
							  LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
							  LEFt JOIN $DataPublic.jobdata J ON J.Id=M.JobId
							  WHERE
							  M.Number = $num
							  And K.Month='$month'
							  ORDER BY K.Month DESC,M.Estate DESC,M.BranchId,M.JobId,K.Number";
							  
	$personalAttendanceResult = mysql_query($personalAttendanceSql);
	$myRow = mysql_fetch_assoc($personalAttendanceResult);
	
	$Month=$myRow["Month"];
	$Number=$myRow["Number"];
	$Name=$myRow["Name"];
	$Branch=$myRow["Branch"];		
	$Job=$myRow["Job"];		
    
	$Dhours= ($myRow["Dhours"]=="")?"0":$myRow["Dhours"];		//应到工时
	$Whours= ($myRow["Whours"]=="")?"0":$myRow["Whours"];		//实到工时
	
	$Ghours = $myRow["Ghours"] + $myRow["GOverTime"] + $myRow["GDropTime"];
	$Ghours = ($Ghours == "")?"0":$Ghours;
		
	//2倍加班工时
	if($Month<"2013-04")
	{//4月份之前按原来的方式去计算
			//2倍加班工时
		$XhoursResult=mysql_fetch_array(mysql_query("SELECT xHours FROM $DataIn.hdjbsheet WHERE Number=$num and Month='$Month'",$link_id));
		$Xhours=$XhoursResult["xHours"];
	}
	else
	{
		$Xhours = $myRow["Xhours"] + $myRow["XOverTime"] + $myRow["XDropTime"];
		$Xhours = ($Xhours == "")?"0":$Xhours;
	}
	
	//3倍工时
	$Fhours = $myRow["Fhours"] + $myRow["FOverTime"] + $myRow["FDropTime"];
	$Fhours = ($Fhours == "")?"0":$Fhours;
	
	$InLates= ($myRow["InLates"]=="")?"0":$myRow["InLates"];	//迟到次数
	$OutEarlys= ($myRow["OutEarlys"]=="")?"0":$myRow["OutEarlys"];//早退次数
	$SJhours= ($myRow["SJhours"]=="")?"0":$myRow["SJhours"];	//事假工时
	$BJhours= ($myRow["BJhours"]=="")?"0":$myRow["BJhours"];	//病假工时
	$BXhours= ($myRow["BXhours"]=="")?"0":$myRow["BXhours"];	//补休工时 
	$YXJhours= ($myRow["YXJhours"]=="")?"0":$myRow["YXJhours"];	//有薪假工时:婚、丧等有薪假
	$WXJhours= ($myRow["WXJhours"]=="")?"0":$myRow["WXJhours"];	//无薪假工时
	$QQhours= ($myRow["QQhours"]=="")?"0":$myRow["QQhours"];	//缺勤工时
	$WXhours= ($myRow["WXhours"]=="")?"0":$myRow["WXhours"];	//无效工时
	$KGhours= ($myRow["KGhours"]=="")?"0":$myRow["KGhours"];	//旷工工时
	$dkhours= ($myRow["dkhours"]=="")?"0":$myRow["dkhours"];	//有薪工时
	$YBs= ($myRow["YBs"]=="")?"0":$myRow["YBs"];			//夜班次数
	$Estate=$myRow["Estate"];
	$Locks=$myRow["Locks"];
		
	$attendance = array("$Dhours:应到工时", "$Whours:实到工时", "$Ghours:1.5倍工时", "$Xhours:2倍工时", "$Fhours:3倍工时", "$InLates:迟到次数", "$OutEarlys:早退次数", "$SJhours:事假工时", "$BJhours:病假工时", "$BXhours:补休工时", "$YXJhours:有薪假工时", "$WXJhours:无薪假工时", "$QQhours:缺勤工时", "$WXhours:无效工时", "$KGhours:旷工工时", "$dkhours:有薪工时", "$YBs:夜班次数");
	
	echo json_encode($attendance);
	
?>