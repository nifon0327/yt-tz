<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("../model/modelhead.php");
	include_once("$path/public/kqClass/Kq_pbSet.php");
	include_once("$path/public/kqClass/Kq_dailyItem.php");
	include_once("$path/public/kqClass/Kq_totleItem.php");
	include_once("$path/public/kqClass/Kq_otHourSet.php");
	
	$nowWebPage ="kq_checkio_count";
	$fromWebPage="kq_checkio_read";
	$_SESSION["nowWebPage"]=$nowWebPage;
	$Parameter="fromWebPage,$fromWebPage";
	
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
	if($StaffRow = mysql_fetch_array($CheckStaff))
	{
		$StaffList="<select name='Number' id='Number' onchange='document.form1.submit()'>";
		$n=1;
		do
		{
			$NumberT=$StaffRow["Number"];
			include_once("../model/subprogram/factoryCheckDate.php");
			if(skipStaff($NumberT))
			{
				continue;
			}
			
			$Number=$Number==""?$NumberT:$Number;
			$NameT=$StaffRow["Name"];
			$JobName=$StaffRow["JobName"];
			if($Number==$NumberT)
			{
				$StaffList.="<option value='$NumberT' selected>$n $JobName $NameT $NumberT</option>";
				//$Number = $NumberT;
			}
			else
			{
				$StaffList.="<option value='$NumberT' >$n $JobName $NameT $NumberT</option>";
			}
			$n++;
		}while ($StaffRow = mysql_fetch_array($CheckStaff));
		$StaffList.="</select>&nbsp;";
	}
	$SelectCode=$StaffList."<input name='CheckMonth' type='text' id='CheckMonth' size='10' maxlength='7' value='$CheckMonth' onchange='javascript:document.form1.submit();'>&nbsp;<select name='CountType' id='CountType' onchange='document.form1.submit()'><option value='0' $CountType0>日考勤统计</option><option value='1' $CountType1>月考勤统计</option></select> &nbsp; ";
	$selStr="selFlag" . $KqSign;
	$selStr="selected";
	$SelectCode=$SelectCode. "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''><option  value=''   $selFlag>--全部--</option><option  value='1'  $selFlag1>考勤有效</option><option value='2' $selFlag2>考勤参考</option></select>";
//如果是之前月份检查统计是否存在:如果是当月，则只有离职员工可以保存
	$checkSql = mysql_query("SELECT Id FROM $DataIn.kqdata WHERE 1 and Number=$Number and Month='$CheckMonth' ORDER BY Id LIMIT 1",$link_id);
	if($checkRow = mysql_fetch_array($checkSql))
	{
		$SaveSTR="NO";
	}
	$checkSql1 = mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE 1 and Number=$Number and Estate=0 ORDER BY Id LIMIT 1",$link_id);
	if($checkRow1 = mysql_fetch_array($checkSql1))
	{
		$Days=date("t",strtotime($FristDay));
	}
	include "../model/subprogram/add_model_t.php";
	
	echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'>";
	echo"<tr class='' align='center'>
		<td width='30' rowspan='2' class='A1111'>日期</td>
		<td width='50' rowspan='2' class='A1101'>星期</td>
		<td width='60' rowspan='2' class='A1101'>日期类别</td>
		<td height='19' width='80' colspan='2' class='A1101'>签卡记录</td>
		<td width='30' rowspan='2' class='A1101'>应到<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>实到<br>工时</td>
		<td width='120' colspan='3' class='A1101'>加点加班工时(+直落)</td>
		<td width='30' rowspan='2' class='A1101'>迟到</td>
		<td width='30' rowspan='2' class='A1101'>早退</td>
		<td width='160' colspan='9' class='A1101'>请、休假工时</td>
		<td width='30' rowspan='2' class='A1101'>缺勤<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>旷工<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>夜班</td>
		<td width='30' rowspan='2' class='A1101'>无效<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>有薪<br>工时</td>
 	</tr>
  	<tr class=''  align='center'>
		<td width='40' heigh38t='20' class='A0101'>签到</td>
		<td width='40' class='A0101'>签退</td>
		<td width='38' class='A0101'>1.5倍</td>
		<td width='38' class='A0101'>2倍</td>
		<td width='38' class='A0101'>3倍</td>
		<td width='20' class='A0101'>事</td>
		<td width='20' class='A0101'>病</td>		
		<td width='20' class='A0101'>无</td>
		<td width='20' class='A0101'>年</td>
		<td width='20' class='A0101'>补</td>
		<td width='20' class='A0101'>婚</td>
		<td width='20' class='A0101'>丧</td>
		<td width='20' class='A0101'>产</td>
		<td width='20' class='A0101'>工</td>
	</tr>";
	
	$totleDayItem = new KqTotleItem();
	
	for($i=0;$i<$Days;$i++)
	{
		$j=$i+1;
		$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
		
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
			$inTime = "";
			$outTime = "";
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
		
		if(($outDate!="" && $CheckDate > $outDate) || $comeIn != "" && $CheckDate < $comeIn)
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
		
		echo"<tr align='center'><td class='A0111' $rowBgcolor>$j</td>";
		echo"<td class='A0101'>".$dayItem->dayOfWeek."</td>";
		echo"<td class='A0101'><div $DateTypeColor>".$dayItem->dateType.$dayItem->dayInfomation."</div></td>";
		echo"<td class='A0101'><span $AIcolor>".$dayItem->returnCheckInTime()." </span></td>";
		echo"<td class='A0101'><span $AOcolor>".$dayItem->returnCheckOutTime()." </span></td>";
		echo"<td class='A0101'>".zerotospace($dayItem->workTime)." </td>";
		echo"<td class='A0101'>".zerotospace($dayItem->realWorkTime)." </td>";
		echo"<td class='A0101'>".zerotospace($dayItem->jbTime)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->sxTime)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->jrTime)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->beLate)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->leaveEarly)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->privateLeave)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->sickLeave)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->noWageLeave)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->annualLeave)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->notBusyLeave)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->marriageLeave)."</td>";//  $test
		echo"<td class='A0101'>".zerotospace($dayItem->funeralLeave)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->maternityLeave)."</td>";// $qjTest
		echo"<td class='A0101'>".zerotospace($dayItem->injuryLeave)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->absenteeismHours)."</td>";
		echo "<td class='A0101'>".zerotospace($dayItem->queQingHours)."</td>";	
		echo"<td class='A0101'>".zerotospace($dayItem->nightShit)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->nopayHours)."</td>";
		echo"<td class='A0101'>".zerotospace($dayItem->payHours)."</td>";
		echo"</tr>";
		
		$totleDayItem->summary($dayItem);
		
	}
	
	echo"<tr align='center'>
	<td class='A0111' colspan='5' >合计</td>
	<td class='A0101'>".zerotospace($totleDayItem->workTime)."<input name='Dhours' type='hidden' id='Dhours' value='$sumGTime'></td>
	<td class='A0101'>".zerotospace($totleDayItem->realWorkTime)."<input name='Whours' type='hidden' id='Whours' value='$sumWorkTime'></td>
	<td class='A0101'>".zerotospace($totleDayItem->jbTime)."<input name='Ghours' type='hidden' id='Ghours' value='$sumGJTime'></td>
	<td class='A0101'>".zerotospace($totleDayItem->sxTime)."<input name='Xhours' type='hidden' id='Xhours' value='$sumXJTime'></td>
	<td class='A0101'>".zerotospace($totleDayItem->jrTime)."<input name='Fhours' type='hidden' id='Fhours' value='$sumFJTime'></td>
	<td class='A0101'>".zerotospace($totleDayItem->beLate)."<input name='InLates' type='hidden' id='InLates' value='$sumInLates'></td>
	<td class='A0101'>".zerotospace($totleDayItem->leaveEarly)."<input name='OutEarlys' type='hidden' id='OutEarlys' value='$sumOutEarlys'></td>
	<td class='A0101'>".zerotospace($totleDayItem->privateLeave)."<input name='SJhours' type='hidden' id='SJhours' value='$sumQjTime1'></td>
	<td class='A0101'>".zerotospace($totleDayItem->sickLeave)."<input name='BJhours' type='hidden' id='BJhours' value='$sumQjTime2'></td>
	<td class='A0101'>".zerotospace($totleDayItem->noWageLeave)."<input name='WXJhours' type='hidden' id='WXJhours' value='$sumQjTime3'></td>
	<td class='A0101'>".zerotospace($totleDayItem->annualLeave)."<input name='YXJhours' type='hidden' id='YXJhours' value='$sumYXJall'></td>
	<td class='A0101'>".zerotospace($totleDayItem->notBusyLeave)."</td>
	<td class='A0101'>".zerotospace($totleDayItem->marriageLeave)."</td>
	<td class='A0101'>".zerotospace($totleDayItem->funeralLeave)."</td>		
	<td class='A0101'>".zerotospace($totleDayItem->maternityLeave)."</td>
	<td class='A0101'>".zerotospace($totleDayItem->injuryLeave)."</td>
	<td class='A0101'>".zerotospace($totleDayItem->absenteeismHours)."<input name='QQhours' type='hidden' id='QQhours' value='$sumQQTime'></td>
	<td class='A0101'>".zerotospace($totleDayItem->queQingHours)."<input name='KGhours' type='hidden' id='KGhours' value='$sumKGTime'></td>
	<td class='A0101'>".zerotospace($totleDayItem->nightShit)."<input name='YBs' type='hidden' id='YBs' value='$sumYBs'></td>
	<td class='A0101'>".zerotospace($totleDayItem->nopayHours)."<input name='WXhours' type='hidden' id='WXhours' value='$sumWXTime'></td>
	<td class='A0101'>".zerotospace($totleDayItem->payHours)."<input name='dkhours' type='hidden' id='dkhours' value='$sumdkHour'></td>
	</tr>";
	
	echo"<tr class='' align='center'>
	<td rowspan='2' class='A0111'>日期</td>
	<td rowspan='2' class='A0101'>星期</td>
	<td rowspan='2' class='A0101'>日期类别</td>
	<td height='19' colspan='2' class='A0101'>签卡记录</td>
	<td rowspan='2' class='A0101'>应到<br>工时</td>
	<td rowspan='2' class='A0101'>实到<br>工时</td>
	<td colspan='3' class='A0101'>加点加班工时(+直落)</td>
	<td rowspan='2' class='A0101'>迟到</td>
	<td rowspan='2' class='A0101'>早退</td>
	<td colspan='9' class='A0101'>请、休假工时</td>
	<td rowspan='2' class='A0101'>缺勤<br>工时</td>
	<td rowspan='2' class='A0101'>旷工<br>工时</td>
	<td rowspan='2' class='A0101'>夜班</td>
	<td rowspan='2' class='A0101'>无效<br>工时</td>
	<td rowspan='2' class='A1101'>有薪<br>工时</td>
	</tr>
	<tr class=''>
	<td heigh38t='20'  align='center' class='A0101'>签到</td>
	<td class='A0101' align='center'>签退</td>
	<td class='A0101' align='center'>1.5倍</td>
	<td class='A0101' align='center'>2倍</td>
	<td class='A0101' align='center'>3倍</td>
	<td class='A0101' align='center'>事</td>
	<td class='A0101' align='center'>病</td>		
	<td class='A0101' align='center'>无</td>
	<td class='A0101' align='center'>年</td>
	<td class='A0101' align='center'>补</td>
	<td class='A0101' align='center'>婚</td>
	<td class='A0101' align='center'>丧</td>
	<td class='A0101' align='center'>产</td>
	<td class='A0101' align='center'>工</td>
	</tr></table>";

	
	
?>