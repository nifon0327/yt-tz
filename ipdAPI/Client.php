<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/model/modelhead.php");
	include_once("$path/public/kqClass/Kq_pbSet.php");
	include_once("$path/public/kqClass/Kq_dailyItem.php");
	include_once("$path/public/kqClass/Kq_totleItem.php");
	include_once("$path/public/kqClass/Kq_otHourSet.php");
	
	
	$nowWebPage ="kq_checkio_count";
	$fromWebPage="kq_checkio_read";
	$_SESSION["nowWebPage"]=$nowWebPage;
	$Parameter="fromWebPage,$fromWebPage";
	
	$CheckMonth = "2014-06";
	//先读取排班设置
	$pbSet = new KqPbSet();
	//第一步：计算要查询的月份及当月考勤天数
	$nowMonth=date("Y-m");			//现在的月份，默认
	if($CheckMonth=="")
	{			//如果没有填月份，则为默认的现在月份
		$CheckMonth=$nowMonth;
	}
	
	$FristDay=$CheckMonth."-01";
	$EndDay=date("Y-m-t",strtotime($FristDay));
	if($CheckMonth==$nowMonth)
	{
		$Days=date("d")-1;
	}
	else
	{
		$Days=date("t",strtotime($FristDay));
	}
	$KqSignStr="";	
	if ($KqSign!="")
	{
		$KqSignStr=" AND M.KqSign='$KqSign'";
	}

	//sql
	$CheckStaffSql = "SELECT A.Number,A.Name,A.JobName FROM (
	SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataIn.checkinout C ON  M.Number=C.Number
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
	WHERE C.CheckTime LIKE '$CheckMonth%' AND M.GroupId!='517' AND G.Estate=1 AND M.cSign='$Login_cSign' $KqSignStr
	GROUP BY C.Number 
	UNION ALL 
	SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId  
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataPublic.kqqjsheet Q ON  M.Number=Q.Number
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
	WHERE (Q.StartDate  LIKE '$CheckMonth%'  OR Q.EndDate  LIKE '$CheckMonth%'  OR  (Q.StartDate<'$CheckMonth-01'  AND Q.EndDate>'$CheckMonth-01'))  AND M.GroupId!='517' AND G.Estate=1 AND M.cSign='$Login_cSign' AND M.KqSign=1 $KqSignStr
	GROUP BY Q.Number 
	) A GROUP BY A.Number 
	ORDER BY A.BranchId,A.JobId,A.Number";
	
	//
	$CheckStaff = mysql_query($CheckStaffSql);
	while($StaffRow = mysql_fetch_array($CheckStaff))
	{
		$Number=$StaffRow["Number"];
		//$Number = "11562";
		$totleDayItem = new KqTotleItem();
	
		for($i=0;$i<$Days;$i++)
		{
			$j=$i+1;
			$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
			
			$inTime = "";
			$outTime = "";
			$lsbResult = mysql_query("SELECT InTime,OutTime,InLate,OutEarly,TimeType,RestTime FROM $DataIn.kqlspbb WHERE Number=$Number and left(InTime,10)='$CheckDate' order by Id",$link_id);
			if($lsbRow = mysql_fetch_assoc($lsbResult))
			{
				$inTime = $CheckDate." "."07:50:00";
				$ioResultSql = "SELECT CheckTime,CheckType,KrSign 
				FROM $DataIn.checkinout 
				WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1')) and CheckType = 'O'
				order by CheckTime";
				$ioResult = mysql_query($ioResultSql);
				$ioResultRow = mysql_fetch_assoc($ioResult);
				$outTime = $ioResultRow["CheckTime"];
			}
			else
			{
				$ioResultSql = "SELECT CheckTime,CheckType,KrSign 
				FROM $DataIn.checkinout 
				WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1'))
			order by CheckTime";
				$ioResult = mysql_query($ioResultSql,$link_id);
				
				while($ioResultRow = mysql_fetch_assoc($ioResult))
				{
					$checkTime=$ioResultRow["CheckTime"];
					$checkType=$ioResultRow["CheckType"];
					$krSign=$ioResultRow["KrSign"];
			
					switch($checkType)
					{
						case "I":
						{
							$inTime = $checkTime; 
						}
						break;
						case "O":
						{
							$outTime = $checkTime;
						}
						break;
					}	
				}
			}
		
		
			$dayItem = new KqDailyItem($inTime, $outTime, $CheckDate);
			$dayItem->setupDateType($CheckDate, $Number,$DataIn, $DataPublic, $link_id);
			//离职或入职处理
			$outDate = "";
			$comeIn = "";
			$checkD=mysql_query("SELECT A.Id,A.ComeIn,B.outDate FROM $DataPublic.staffmain A LEFT JOIN $DataPublic.dimissiondata B On B.Number = A.Number WHERE A.Number='$Number' and (A.ComeIn>'$CheckDate' OR A.Number IN(SELECT B.Number FROM $DataPublic.dimissiondata WHERE B.Number='$Number' and B.OutDate<'$CheckDate'))",$link_id);
			if($checkDRow = mysql_fetch_array($checkD))
			{
				$outDate = $checkDRow["outDate"];
				$comeIn = $checkDRow["ComeIn"];
			}
			
			
			if(($outDate!="" && $CheckDate > $outDate) || ($comeIn != "" && $CheckDate < $comeIn))
			{	
				$dayItem->dayInfomation = "(离)";
			}
			else
			{	
				//读取当天的设定加班工时
				$otHours = new KqOtHourSet($Number,$CheckDate, $DataIn, $DataPublic, $link_id);
				
				
				
				//开始计算加班时间
				if(($otHours->getOtHours($dayItem->dateType) != 0 && ($dayItem->dateType == "X" || $dayItem->dateType == "Y")) || $dayItem->dateType == "G")
				{
					
					$dayItem->calculateHours($Number, $otHours->getOtHours($dayItem->dateType), $otHours->zlHours, $pbSet, $DataIn, $DataPublic, $link_id);
				}
				else
				{
					if($dayItem->dateType == "X" || $dayItem->dateType == "Y")
					{
						$dayItem->checkInTime = "";
						$dayItem->checkOutTime = "";
					}
				}
			}
		
			/*
if($Number == "10373")
			{
				echo $CheckDate."|".$dayItem->realWorkTime."|".$dayItem->checkInTime."|".$dayItem->checkOutTime."<br>";
			}
*/
			$totleDayItem->summary($dayItem);
		}
		
		$workTime = $totleDayItem->workTime;
		$realWorkTime = $totleDayItem->realWorkTime;
		$jbTime = $totleDayItem->jbTime;
		$sxTime = $totleDayItem->sxTime;
		$jrTime = $totleDayItem->jrTime;
		
		$beLate = $totleDayItem->beLate;
		$leaveEarly = $totleDayItem->leaveEarly;
		
		$privateLeave = $totleDayItem->privateLeave;
		$sickLeave = $totleDayItem->sickLeave;
		$noWageLeave = $totleDayItem->noWageLeave;
		$annualLeave = $totleDayItem->annualLeave;
		$notBusyLeave = $totleDayItem->notBusyLeave;
		$marriageLeave = $totleDayItem->marriageLeave;
		$funeralLeave = $totleDayItem->funeralLeave;
		$maternityLeave = $totleDayItem->maternityLeave;
		$injuryLeave = $totleDayItem->injuryLeave;
		$absenteeismHours = $totleDayItem->absenteeismHours;
		$queQingHours = $totleDayItem->queQingHours;
		$nightShit = $totleDayItem->nightShit;
		$nopayHours = $totleDayItem->nopayHours;
		$payHours = $totleDayItem->payHours;
		
		//echo $Number."|".$realWorkTime."<br>";
		
		$totleKqDataSql = "Insert Into $DataIn.kqdataother (Id, Number, Dhours, Whours, Ghours, Xhours, Fhours, InLates, OutEarlys, SJhours, BJhours	, YXJhours, WXJhours, QQhours, YBs, WXhours, KGhours, dkhours, Month, Locks, Operator, ConfirmSign) Values (NULL, '$Number', '$workTime', '$realWorkTime', '$jbTime', '$sxTime', '$jrTime', '$beLate', '$leaveEarly', '$privateLeave', '$sickLeave', '$annualLeave', '$noWageLeave', '$queQingHours', '$nightShit', '$nopayHours', '$absenteeismHours', '$payHours', '$CheckMonth', '0', '10082', '0')";
		
		//echo $totleKqDataSql."<br>";
		
		if(!mysql_query($totleKqDataSql))
		{
			echo $totleKqDataSql."<br>";
		}
	}
		
	
?>