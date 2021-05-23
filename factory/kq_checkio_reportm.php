<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator_Factory.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceStatistic.php");

	$workDayOverTimeStarndard = 2;
	$otherDayOverTimeStarndard = 8;

	$toWebPage="kq_checkio_save";
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
	if ($KqSign!="") {
		$KqSignStr=" AND M.KqSign='$KqSign'";
	}
	
	$SearchRows=$KqSign==1?"":"  AND (M.BranchId IN ( 6, 7, 8 ) OR M.JobId IN ( 10, 38 )) ";
	if (true) {
		//$SearchRows=$KqSign==1?"":"  AND (M.BranchId IN ( 6, 7, 8 ) OR M.JobId IN ( 10)) AND M.WorkAdd!=6 AND M.Number != 10744";
		$SearchRows=" AND M.JobId!=38 AND M.WorkAdd!=6 AND M.Number != '10744'  AND (M.BranchId IN ( 6, 7, 8 ) OR M.JobId IN (10))";
	}


	$CheckStaffSql = "SELECT A.Number,A.Name,A.JobName FROM (
							SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId
							FROM $DataPublic.staffmain M
							LEFT JOIN $DataIn.checkinout C ON  M.Number=C.Number
							LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
							LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
							WHERE C.CheckTime LIKE '$CheckMonth%' AND G.Estate=1  $SearchRows AND  M.cSign='$Login_cSign' $KqSignStr
							GROUP BY C.Number 
							UNION ALL 
							SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId  
							FROM $DataPublic.staffmain M
							LEFT JOIN $DataPublic.kqqjsheet Q ON  M.Number=Q.Number
							LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
							LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
							WHERE (Q.StartDate  LIKE '$CheckMonth%'  OR Q.EndDate  LIKE '$CheckMonth%'  OR  (Q.StartDate<'$CheckMonth-01'  AND Q.EndDate>'$CheckMonth-01'))  AND G.Estate=1 AND M.cSign='$Login_cSign' $SearchRows  $KqSignStr
							GROUP BY Q.Number ) A GROUP BY A.Number 
							ORDER BY A.BranchId,A.JobId,A.Number";
	//echo $CheckStaffSql;
	$CheckStaff= mysql_query($CheckStaffSql,$link_id);

	$ifNonNumber = '';
	$noCompare = true;
	if($StaffRow = mysql_fetch_array($CheckStaff)){
		$StaffList="<select name='Number' id='Number' onchange='document.form1.submit()'>";
		$k=1;
		do{
			$NumberT = $StaffRow["Number"];

			if($ifNonNumber == ''){
				$ifNonNumber = $StaffRow["Number"];
			}

			$isSaveKqSql = "SELECT * From $DataIn.kqdata WHERE Number = '$NumberT' and Month = '$CheckMonth'";
			//echo $isSaveKqSql.'<br>';
			$isSaveResult = mysql_query($isSaveKqSql);
			$saveFlag = mysql_num_rows($isSaveResult)==0?'':'*';

			$NameT=$StaffRow["Name"];
			$JobName=$StaffRow["JobName"];
			$Number=$Number==""?$NumberT:$Number;
			if($Number==$NumberT){
				$StaffList.="<option value='$NumberT' selected>$k $saveFlag $JobName $NameT $NumberT</option>";
				$noCompare = false;
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
      <option  value=''   $selFlag>--全部--</option> 
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
		<td class='A1101' rowspan='2'>1.5倍薪<br>工时</td>
		<td class='A1101' rowspan='2'>2倍薪<br>工时</td>
		<td class='A1101' rowspan='2'>3倍薪<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>迟到</td>
		<td width='30' rowspan='2' class='A1101'>早退</td>
		<td colspan='9' class='A1101'>请、休假工时</td>
		<td width='30' rowspan='2' class='A1101'>缺勤<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>旷工<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>无效<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>有薪<br>工时</td>
 	</tr>
  	<tr class='' align='center'>
		<td width='45' height='20' class='A0101'>签到</td>
		<td width='45' class='A0101'>签退</td>
		<td width='25' class='A0101'>事</td>
		<td width='25' class='A0101'>病</td>		
		<td width='25' class='A0101'>无</td>
		<td width='25' class='A0101'>年</td>
		<td width='25' class='A0101'>补</td>
		<td width='25' class='A0101'>婚</td>
		<td width='25' class='A0101'>丧</td>
		<td width='25' class='A0101'>产</td>
		<td width='25' class='A0101'>工</td>
	</tr>";
	$j=0;

	if($noCompare){
		$Number = $ifNonNumber;
	}


	$attendanceStatistic = new AttendanceStatistic();
	$staff = new AttendanceAvatar_Factory($Number, $DataIn, $DataPublic, $link_id);

	//标准工时统计
	$statisticWorkdayOtStandard = 0;
	$statisticWeekdayOtStandard = 0;
	$statisticHolidayOtStandard = 0;

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


		$hasZlStateG = '';
		$hasZlStateX = '';
		$hasZlStateH = '';
		//echo $staff->getStaffName.'   '.$staff->hasZlhours.'<br>';
		// if($dayAttendanceInfomation["workZlHours"] > 0 || $dayAttendanceInfomation["weekZlHours"] > 0 || $dayAttendanceInfomation["holidayZlHours"] > 0 ){
		// 	switch ($dayAttendanceInfomation["state"]) {
		// 		case 'G':
		// 			$ZL_Hours = $dayAttendanceInfomation["workZlHours"];
		// 			$hasZlStateG = "<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>";
		// 		break;
		// 		case 'X';
		// 			$ZL_Hours = $dayAttendanceInfomation["weekZlHours"];
		// 			$hasZlStateX = "<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>";
		// 		break;
		// 		case 'F':
		// 			$ZL_Hours = $dayAttendanceInfomation["holidayZlHours"];
		// 			$hasZlStateH = "<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>";
		// 		break;
		// 	}
		// }

		echo"<tr align='center'><td class='A0111' $rowBgcolor>$j</td>";
		echo"<td class='A0101'>".$dayAttendanceInfomation["weekDay"]."</td>";
		echo"<td class='A0101'><div $DateTypeColor>".$dayAttendanceInfomation["state"]."</div></td>";
		echo"<td class='A0101'><span $AIcolor>".$dayAttendanceInfomation["startTime"]."</span></td>";
		echo"<td class='A0101'><span $AOcolor>".$dayAttendanceInfomation["endTime"]."</span></td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["defaultWorkHours"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["workHours"])."</td>";
		//工作日超时计算
		$workdayStarndardOtTime = $dayAttendanceInfomation["workOtHours"]>$workDayOverTimeStarndard?$workDayOverTimeStarndard:$dayAttendanceInfomation["workOtHours"];
		$statisticWorkdayOtStandard += ($workdayStarndardOtTime);
		echo"<td class='A0101'>$hasZlStateG".letZeroChangeToSpace($workdayStarndardOtTime)."</td>";
		$workdayOverOtTime = $dayAttendanceInfomation["workOtHours"]-$workDayOverTimeStarndard>0?$dayAttendanceInfomation["workOtHours"]-$workDayOverTimeStarndard:"&nbsp;";
		//echo"<td class='A0101' bgcolor='#CCCCCC'>".letZeroChangeToSpace($workdayOverOtTime)."</td>";
		//echo"<td class='A0101' bgcolor='#CCCCCC'>".letZeroChangeToSpace($dayAttendanceInfomation["workZlHours"])."</td>";
		//双休日超时计算
		$weekdayStarndardOtTime = $dayAttendanceInfomation["weekOtTime"]>$otherDayOverTimeStarndard?$otherDayOverTimeStarndard:$dayAttendanceInfomation["weekOtTime"];
		$statisticWeekdayOtStandard += ($dayAttendanceInfomation["weekOtTime"]);
		echo"<td class='A0101'>$hasZlStateX".letZeroChangeToSpace($dayAttendanceInfomation["weekOtTime"])."</td>";
		$weekdayOverOtTime = $dayAttendanceInfomation["weekOtTime"]-$otherDayOverTimeStarndard>0?$dayAttendanceInfomation["weekOtTime"]-$otherDayOverTimeStarndard:"&nbsp;";
		//echo"<td class='A0101' bgcolor='#CCCCCC'>".letZeroChangeToSpace($weekdayOverOtTime)."</td>";
		//echo"<td class='A0101' bgcolor='#CCCCCC'>".letZeroChangeToSpace($dayAttendanceInfomation["weekZlHours"])."</td>";
		//节假日超时计算

		if(substr($CheckDate, 5,5) == '03-08'){
			$holidayStarndardOtTime = $dayAttendanceInfomation["holidayOtHours"];
			$statisticHolidayOtStandard += $holidayStarndardOtTime;
			echo"<td class='A0101'>".letZeroChangeToSpace("")."</td>";
		}else{
			$holidayStarndardOtTime = $dayAttendanceInfomation["holidayOtHours"]>$otherDayOverTimeStarndard?$otherDayOverTimeStarndard:$dayAttendanceInfomation["holidayOtHours"];
			$statisticHolidayOtStandard += 0;
			echo"<td class='A0101'>".letZeroChangeToSpace("")."</td>";
			$holidayOverOtTime = $dayAttendanceInfomation["holidayOtHours"]-$otherDayOverTimeStarndard>0?$dayAttendanceInfomation["holidayOtHours"]-$otherDayOverTimeStarndard:"&nbsp;";
		}
		//echo"<td class='A0101' bgcolor='#CCCCCC'>".letZeroChangeToSpace($holidayOverOtTime)."</td>";
		//echo"<td class='A0101' bgcolor='#CCCCCC'>".letZeroChangeToSpace($dayAttendanceInfomation["holidayZlHours"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["beLate"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["beEarly"])."</td>";
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["personalLeave"])."</td>";
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

	echo"<tr align='center'>
	<td class='A0111' colspan='5' >合计</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["defaultWorkHours"])."<input name='Dhours' type='hidden' id='Dhours' value='".$statisticAttendnce["defaultWorkHours"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["workHours"])."<input name='Whours' type='hidden' id='Whours' value='".$statisticAttendnce["workHours"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticWorkdayOtStandard)."<input name='Ghours' type='hidden' id='Ghours' value='".$statisticWorkdayOtStandard."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticWeekdayOtStandard)."<input name='Xhours' type='hidden' id='Xhours' value='".$statisticWeekdayOtStandard."'></td>
	<td class='A0101'>".letZeroChangeToSpace("")."<input name='Fhours' type='hidden' id='Fhours' value=''></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["beLate"])."<input name='InLates' type='hidden' id='InLates' value='".$statisticAttendnce["beLate"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["beEarly"])."<input name='OutEarlys' type='hidden' id='OutEarlys' value='".$statisticAttendnce["beEarly"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["personalLeave"])."<input name='SJhours' type='hidden' id='SJhours' value='".$statisticAttendnce["personalLeave"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["sickLeave"])."<input name='BJhours' type='hidden' id='BJhours' value='".$statisticAttendnce["sickLeave"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["noPayLeave"])."<input name='WXJhours' type='hidden' id='WXJhours' value='".$statisticAttendnce["noPayLeave"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["annualLeave"])."<input name='YXJhours' type='hidden' id='YXJhours' value='".$statisticAttendnce["annualLeave"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["bxLeave"])."</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["marrayLeave"])."</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["deadLeave"])."</td>		
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["birthLeave"])."</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["hurtLeave"])."</td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["lackWorkHours"])."<input name='QQhours' type='hidden' id='QQhours' value='".$statisticAttendnce["lackWorkHours"]."'></td>
	<td class='A0101'>".letZeroChangeToSpace($statisticAttendnce["kgHours"])."<input name='KGhours' type='hidden' id='KGhours' value='".$statisticAttendnce["kgHours"]."'></td>
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
	<td width=60 class='A0101' rowspan='2'>1.5倍薪<br>工时</td>
	<td width=60 class='A0101' rowspan='2'>2倍薪<br>工时</td>
	<td width=60 class='A0101' rowspan='2'>3倍薪<br>工时</td>
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
	<td rowspan='2' class='A0101'>无效<br>工时</td>
	<td rowspan='2' class='A0101'>有薪<br>工时</td>
	</tr>
	<tr class=''  align='center'>
	<td height='20' colspan='2' class='A0101'>签卡记录</td>
	<td colspan='9' class='A0101'>请、休假工时</td>
	</tr></table>";

	

	include "../model/subprogram/add_model_b.php";
?>
<?php
	function letZeroChangeToSpace($data){
		return ($data == "" or $data == 0)? "&nbsp;":"$data";
	}
?>
