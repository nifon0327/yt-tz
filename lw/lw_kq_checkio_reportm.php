<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator_lw.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceStatistic.php");

	$workDayOverTimeStarndard = 2;
	$otherDayOverTimeStarndard = 8;

	$toWebPage="lw_kq_checkio_save";
	$nowMonth=date("Y-m");			//现在的月份，默认
	if($CheckMonth==""){			//如果没有填月份，则为默认的现在月份
		$CheckMonth=substr($CheckDate,0,7);

	}

	$FristDay=$CheckMonth."-01";
	$EndDay=date("Y-m-t",strtotime($FristDay));
	if($CheckMonth==$nowMonth){
		$Days=date("d")-1;
	}
	else{
		$Days=date("t",strtotime($FristDay));
	}
	
	$KqSignStr="";	
	if ($KqSign=="" or $KqSign == '1') {
		$KqSignStr= "AND M.KqSign='1' ";
	}else{
		$KqSignStr= "AND M.KqSign='2' ";
	}

	$CheckStaffSql = "SELECT A.Number,A.Name,A.JobName FROM (
							SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId
							FROM $DataIn.lw_staffmain M
							LEFT JOIN $DataIn.lw_checkinout C ON  M.Number=C.Number
							LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
							LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
							WHERE C.CheckTime LIKE '$CheckMonth%' $SearchRows $KqSignStr
							GROUP BY C.Number 
							UNION ALL 
							SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId  
							FROM $DataIn.lw_staffmain M
							LEFT JOIN $DataIn.kqqjsheet Q ON  M.Number=Q.Number
							LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
							LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
							WHERE (Q.StartDate  LIKE '$CheckMonth%'  OR Q.EndDate  LIKE '$CheckMonth%'  OR  (Q.StartDate<'$CheckMonth-01'  AND Q.EndDate>'$CheckMonth-01'))  $SearchRows  $KqSignStr
							GROUP BY Q.Number ) A GROUP BY A.Number 
							ORDER BY A.BranchId,A.JobId,A.Number";
	//echo $CheckStaffSql;
	$CheckStaff= mysql_query($CheckStaffSql,$link_id);


	if($StaffRow = mysql_fetch_array($CheckStaff)){
		$StaffList="<select name='Number' id='Number' onchange='document.form1.submit()'>";
		$k=1;
		do{
			$NumberT = $StaffRow["Number"];

			$isSaveKqSql = "SELECT * From $DataIn.kqdata WHERE Number = '$NumberT' and Month = '$CheckMonth'";
			//echo $isSaveKqSql.'<br>';
			$isSaveResult = mysql_query($isSaveKqSql);
			$saveFlag = mysql_num_rows($isSaveResult)==0?'':'*';

			$NameT=$StaffRow["Name"];
			$JobName=$StaffRow["JobName"];
			$Number=$Number==""?$NumberT:$Number;
			if($Number==$NumberT){
				$StaffList.="<option value='$NumberT' selected>$k $saveFlag $JobName $NameT $NumberT</option>";
			}
			else{
				$StaffList.="<option value='$NumberT' >$k $JobName $saveFlag $NameT $NumberT</option>";
			}
			$k++;
		}while ($StaffRow = mysql_fetch_array($CheckStaff));
		$StaffList.="</select>&nbsp;";
	}
	$SelectCode=$StaffList."
	<input name='CheckMonth' type='text' id='CheckMonth' size='10' maxlength='7' value='$CheckMonth' onchange='javascript:document.form1.submit();'>&nbsp;
	<select name='CountType' id='CountType' onchange='document.form1.submit()'>
	<option value='0' $CountType0>日考勤统计</option>
	<option value='1' $CountType1>月考勤统计</option>
	</select> &nbsp; ";

	$selStr="selFlag" . $KqSign;
	$$selStr="selected";
	$SelectCode=$SelectCode. "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''>
      <option  value='1'  $selFlag1>考勤有效</option>
	   <option value='2' $selFlag2>考勤参考</option>
	  </select>";
	//如果是之前月份检查统计是否存在:如果是当月，则只有离职员工可以保存
	$checkSql = mysql_query("SELECT Id FROM $DataIn.kqdata WHERE 1 and Number=$Number and Month='$CheckMonth' ORDER BY Id LIMIT 1",$link_id);
	if($checkRow = mysql_fetch_array($checkSql)){
		$SaveSTR="NO";
	}
	$checkSql1 = mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE 1 and Number=$Number and Estate=0 ORDER BY Id LIMIT 1",$link_id);
	if($checkRow1 = mysql_fetch_array($checkSql1)){
		$Days=date("t",strtotime($FristDay));
	}
	include "../model/subprogram/add_model_t.php";

	echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'>";
	echo"<tr class='' align='center'>
		<td width='30' rowspan='2' class='A1111'>日期</td>
		<td width='50' rowspan='2' class='A1101'>星期</td>
		<td width='45' rowspan='2' class='A1101'>类别</td>
		<td height='20' colspan='2' class='A1101'>签卡记录</td>
		<td width='45' rowspan='2' class='A1101'>应到<br>工时</td>
		<td width='45' rowspan='2' class='A1101'>实到<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>迟到</td>
		<td width='30' rowspan='2' class='A1101'>早退</td>
		<td colspan='9' class='A1101'>请、休假工时</td>
		<td width='30' rowspan='2' class='A1101'>缺勤<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>旷工<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>夜班</td>
		<td width='30' rowspan='2' class='A1101'>无效<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>有薪<br>工时</td>
 	</tr>
  	<tr class='' align='center'>
		<td width='45' height='20' class='A0101'>签到</td>
		<td width='45' class='A0101'>签退</td>
		
		<td class='A0101' >事</td>
	<td class='A0101' >病</td>		
	<td class='A0101' >无</td>
	<td class='A0101' >年</td>
	<td class='A0101' >补</td>
	<td class='A0101' >婚</td>
	<td class='A0101' >丧</td>
	<td class='A0101' >产</td>
	<td class='A0101' >工</td>
	</tr>";
	//<td width='25' class='A0101'>事(淡)</td>
	$j=0;
	$attendanceStatistic = new AttendanceStatistic();
	$staff = new AttendanceAvatar_lw($Number, $DataIn, $DataPublic, $link_id);

	//标准工时统计
	$statisticWorkdayOtStandard = 0;
	$statisticWeekdayOtStandard = 0;
	$statisticHolidayOtStandard = 0;
	$sumpersonalotherleave = 0;

	for($i=0;$i<$Days;$i++){
		$j=$i+1;
		$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
		$cloneLeaveAvatar = clone $staff;
		$cloneLeaveAvatar->setupAttendanceData($Number, $CheckDate, $DataIn, $DataPublic, $link_id);

		$checkIsOutOfWorkResult=mysql_query("SELECT A.Id,A.ComeIn,B.outDate FROM $DataPublic.staffmain A
						 Left Join $DataPublic.dimissiondata B On B.Number = A.Number 
						 WHERE A.Number='".$cloneLeaveAvatar->getStaffNumber()."' and (A.ComeIn>'$CheckDate' OR A.Number IN(SELECT B.Number FROM $DataPublic.dimissiondata WHERE B.Number='".$cloneLeaveAvatar->getStaffNumber()."' and B.OutDate<'$CheckDate'))",$link_id);
		if($checkDRow = mysql_fetch_array($checkIsOutOfWorkResult)){
			$cloneLeaveAvatar->setOutOfWorkState($DataIn, $DataPublic, $link_id);
		}
		else{
			$cloneLeaveAvatar->attendanceSetup($DataIn, $DataPublic, $link_id);
		}

		$dayAttendanceInfomation =  $cloneLeaveAvatar->getInfomationByTag();
		$attendanceStatistic->statistic($dayAttendanceInfomation);
		echo"<tr align='center'><td class='A0111' $rowBgcolor>$j</td>";
		echo"<td class='A0101'>".$dayAttendanceInfomation["weekDay"]."</td>";
		echo"<td class='A0101'><div $DateTypeColor>".$dayAttendanceInfomation["state"]."</div></td>";
		echo"<td class='A0101'><span $AIcolor>".$dayAttendanceInfomation["startTime"]."</span></td>";
		echo"<td class='A0101'><span $AOcolor>".$dayAttendanceInfomation["endTime"]."</span></td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["defaultWorkHours"])."</td>";

		//劳务工工时计算
		$lw_workHours = $dayAttendanceInfomation["workHours"] + $dayAttendanceInfomation["workOtHours"] + $dayAttendanceInfomation["workZlHours"] + $dayAttendanceInfomation["weekOtTime"] + $dayAttendanceInfomation["weekZlHours"] + $dayAttendanceInfomation["holidayOtHours"] + $dayAttendanceInfomation["holidayZlHours"];


		echo"<td class='A0101'>".letZeroChangeToSpace($lw_workHours)."</td>";
		
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["beLate"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["beEarly"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["personalLeave"])."</td>";
		//echo"<td class='A0101'>".letZeroChangeToSpace($personalotherleave)."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["sickLeave"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["noPayLeave"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["annualLeave"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["bxLeave"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["marrayLeave"])."</td>";//  $test
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["deadLeave"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["birthLeave"])."</td>";// $qjTest
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["hurtLeave"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["lackWorkHours"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["kgHours"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["nightShit"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["noPayHours"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["dkHours"])."</td>";
		echo"</tr>";
	}

	$statisticAttendnce = $attendanceStatistic->getStatisticByTag();
	//统计数据格式处理
	$statisticWorkdayOverOt = ($statisticAttendnce["workOtHours"] - $statisticWorkdayOtStandard) == 0?"0":$statisticAttendnce["workOtHours"] - $statisticWorkdayOtStandard;
	$statisticWorkdayOtStandard = $statisticWorkdayOtStandard== 0?"0":$statisticWorkdayOtStandard;

	$statisticWeekdayOverOt = $statisticAttendnce["weekOtTime"] - $statisticWeekdayOtStandard == 0?"0":$statisticAttendnce["weekOtTime"] - $statisticWeekdayOtStandard;
	$statisticWeekdayOtStandard = $statisticWeekdayOtStandard == 0?"0":$statisticWeekdayOtStandard;

	$statisticHolidayOverOt = $statisticAttendnce["holidayOtHours"] - $statisticHolidayOtStandard == 0?"0":$statisticAttendnce["holidayOtHours"] - $statisticHolidayOtStandard;
	$statisticHolidayOtStandard = $statisticHolidayOtStandard == 0?"0":$statisticHolidayOtStandard;


	$yxTotleQj = $statisticAttendnce["bxLeave"] + $statisticAttendnce["marrayLeave"] + $statisticAttendnce["deadLeave"] + $statisticAttendnce["birthLeave"] + $statisticAttendnce["hurtLeave"] + $statisticAttendnce["annualLeave"];
//<td class='A0101'>".letZeroChangeToSpace($sumpersonalotherleave)."<input name='SJhours1' type='hidden' id='SJhours1' value='".$sumpersonalotherleave."'></td>

	$statisticWorkHours = $statisticAttendnce["workHours"] + $statisticAttendnce["workOtHours"] + $statisticAttendnce["workZlHours"] + $statisticAttendnce["weekOtTime"] + $statisticAttendnce["weekZlHours"] + $statisticAttendnce["holidayOtHours"] + $statisticAttendnce["holidayZlHours"];


	echo"<tr align='center'>
	<td class='A0111' colspan='5' >合计</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["defaultWorkHours"])."<input name='Dhours' type='hidden' id='Dhours' value='".$statisticAttendnce["defaultWorkHours"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticWorkHours)."<input name='Whours' type='hidden' id='Whours' value='".$statisticWorkHours."'>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["beLate"])."<input name='InLates' type='hidden' id='InLates' value='".$statisticAttendnce["beLate"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["beEarly"])."<input name='OutEarlys' type='hidden' id='OutEarlys' value='".$statisticAttendnce["beEarly"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["personalLeave"])."<input name='SJhours' type='hidden' id='SJhours' value='".$statisticAttendnce["personalLeave"]."'><input name='SJhours1' type='hidden' id='SJhours1' value='0'></td>
	
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["sickLeave"])."<input name='BJhours' type='hidden' id='BJhours' value='".$statisticAttendnce["sickLeave"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["noPayLeave"])."<input name='WXJhours' type='hidden' id='WXJhours' value='".$statisticAttendnce["noPayLeave"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["annualLeave"])."<input name='YXJhours' type='hidden' id='YXJhours' value='".$yxTotleQj."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["bxLeave"])."</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["marrayLeave"])."</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["deadLeave"])."</td>		
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["birthLeave"])."</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["hurtLeave"])."</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["lackWorkHours"])."<input name='QQhours' type='hidden' id='QQhours' value='".$statisticAttendnce["lackWorkHours"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["kgHours"])."<input name='KGhours' type='hidden' id='KGhours' value='".$statisticAttendnce["kgHours"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["nightShit"])."<input name='YBs' type='hidden' id='YBs' value='".$statisticAttendnce["nightShit"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["noPayHours"])."<input name='WXhours' type='hidden' id='WXhours' value='".$statisticAttendnce["noPayHours"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["dkHours"])."<input name='dkhours' type='hidden' id='dkhours' value='".$statisticAttendnce["dkHours"]."'></td>
	</tr>";
	echo"<tr class='' align='center'>
	<td rowspan='2' class='A0111'>日期</td>
	<td rowspan='2' class='A0101'>星期</td>
	<td rowspan='2' class='A0101'>类别</td>
	<td height='20' class='A0101'>签到</td>
	<td class='A0101'>签退</td>
	<td rowspan='2' class='A0101'>应到<br>工时</td>
	<td rowspan='2' class='A0101'>实到<br>工时</td>
	<td rowspan='2' class='A0101'>迟到</td>
	<td rowspan='2' class='A0101'>早退</td>
	
	<td class='A0101' >事</td>
	<td class='A0101' >病</td>		
	<td class='A0101' >无</td>
	<td class='A0101' >年</td>
	<td class='A0101' >补</td>
	<td class='A0101' >婚</td>
	<td class='A0101' >丧</td>
	<td class='A0101' >产</td>
	<td class='A0101' >工</td>
	<td rowspan='2' class='A0101'>缺勤<br>工时</td>
	<td rowspan='2' class='A0101'>旷工<br>工时</td>
	<td rowspan='2' class='A0101'>夜班</td>
	<td rowspan='2' class='A0101'>无效<br>工时</td>
	<td rowspan='2' class='A0101'>有薪<br>工时</td>
	</tr>
	<tr class=''  align='center'>
	<td height='20' colspan='2' class='A0101'>签卡记录</td>
	<td colspan='9' class='A1101'>请、休假工时</td>
	</tr></table>";

	//读取加班时薪资料
	$checkResult=mysql_query("SELECT ValueCode,Value FROM $DataPublic.cw3_basevalue WHERE Estate=1",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)){
		do{
			$ValueCode=$checkRow["ValueCode"];
			$TempEstateSTR="HourlyWage".strval($ValueCode); 
			$$TempEstateSTR=$checkRow["Value"];
		}while ($checkRow = mysql_fetch_array($checkResult));
	}
	echo "1.5倍时薪：".$HourlyWage102."<br>";
	echo "&nbsp;&nbsp;&nbsp;2倍时薪：".$HourlyWage103."<br>";
	echo "&nbsp;&nbsp;&nbsp;3倍时薪：".$HourlyWage104."<br>";
	if($CheckMonth<"2013-05"){
		$Amount001=floor(($statisticAttendnce["workOtHours"]+$statisticAttendnce["workZlHours"])*$HourlyWage102);
		$Amount002=floor(($statisticAttendnce["weekOtTime"]+$statisticAttendnce["weekZlHours"])*$HourlyWage103+($statisticAttendnce["holidayOtHours"]+$statisticAttendnce["holidayZlHours"])*$HourlyWage104);
		$Amount003=$Amount001+$Amount002;
		echo "工作日加班费:<input name='Jbf' type='text' id='Jbf' value='$Amount001'><br>";
		echo "节假日加班费:<input name='Jj' type='text' id='Jj' value='$Amount002'><br>";
		echo "费用合计:".$Amount003."<br>";
	}
	else{
		$Amount001=floor(($statisticWorkdayOtStandard)*$HourlyWage102+($statisticWeekdayOtStandard)*$HourlyWage103);
		$Amount002=floor(($statisticWorkdayOverOt+$statisticAttendnce["workZlHours"])*$HourlyWage102+($statisticWeekdayOverOt+$statisticAttendnce["weekZlHours"])*$HourlyWage103+($statisticHolidayOverOt+$statisticAttendnce["holidayZlHours"]+$statisticHolidayOtStandard)*$HourlyWage104);
		$Amount003=$Amount001+$Amount002;
		echo "加班费用:<input name='Jbf' type='text' id='Jbf' value='$Amount001'><br>";
		echo "奖金费用:<input name='Jj' type='text' id='Jj' value='$Amount002'><br>";
		echo "费用合计:".$Amount003."<br>";
	}
	echo "&nbsp;<br>";

	include "../model/subprogram/add_model_b.php";
?>
<?php
	function letZeroChangeToSpace($data){
		return ($data == "" or $data == 0)? "&nbsp;":"$data";
	}
?>