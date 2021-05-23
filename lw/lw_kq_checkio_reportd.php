<?php

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator_lw.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceStatistic.php");

	$workDayOverTimeStarndard = 2;
	$otherDayOverTimeStarndard = 8;

	$ActioToS="";
	if($CheckDate==""){
		$CheckDate=date("Y-m-d");
	}
	$CheckMonth=substr($CheckDate,0,7);
	$SelectCode="<input name='CheckDate' type='text' id='CheckDate' size='10' maxlength='10' value='$CheckDate' onchange='javascript:document.form1.submit();'>&nbsp;
		<select name='CountType' id='CountType' onchange='document.form1.submit()'><option value='0' $CountType0>日考勤统计</option><option value='1' $CountType1>月考勤统计</option></select>";
	$selStr="selFlag" . $KqSign;
	$$selStr="selected";
	$SelectCode=$SelectCode. "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''>
      <option  value='1' $selFlag1>考勤有效</option>
	   <option value='2' $selFlag2>考勤参考</option>
	  </select>";
	$KqSignStr="";	
	if ($KqSign=="") {
		$KqSignStr=" AND M.KqSign='1'";
	}

	$SaveFun="<span onClick='SaveDaytj()' $onClickCSS>保存</span>&nbsp;";
	$CustomFun="<a href='kq_checkio_print.php?CheckDate=$CheckDate' target='_blank' $onClickCSS>列印</a>&nbsp;&nbsp;";
	$SaveSTR="NO";
	include "../model/subprogram/add_model_t.php";

	//步骤4：星期处理
	$ToDay=$CheckDate;//计算当天
	$NowToDay=date("Y-m-d");//
	//2		计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
	$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
	$weekDay=date("w",strtotime($CheckDate));	 
	$weekInfo="星期".$Darray[$weekDay];
	$DateTypeTemp=($weekDay==6 || $weekDay==0)?"X":"G";
	$jbTimes=0;
	$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataIn.kqholiday WHERE Date='$CheckDate'",$link_id);
	if($holidayRow = mysql_fetch_array($holidayResult)){
		$jbTimes=$holidayRow["jbTimes"];
		switch($holidayRow["Type"]){
			case 0:		$DateTypeTemp="W";		break;
			case 1:		$DateTypeTemp="Y";		break;
			case 2:		$DateTypeTemp="F";		break;
		}
	}

	echo"<input name='kqList' type='hidden' id='kqList'>";
	echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF' id='kqTable'>
<tr><td colspan='27' class='A0011'>当天是：$weekInfo (直落工时的薪酬计算与加班工时一致)<span style='color:#ff0033'>(Number号红色为数据已作修改,黄色为数据未保存)</span></td></tr>";
	echo"<tr class=''>
		<td width='30' rowspan='2' class='A1111'><div align='center'>序号</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>编号</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>姓名</div></td>
		<td width='100' rowspan='2' class='A1101'><div align='center'>小组</div></td>
		<td width='60' rowspan='2' class='A1101'><div align='center'>现职</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>日期<br>类别</div></td>
		<td height='19' width='80' colspan='2' class='A1101'><div align='center'>签卡记录</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>应到<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>实到<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>迟到</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>早退</div></td>
		<td width='160' colspan='9' class='A1101'><div align='center'>请、休假工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>缺勤<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>旷工<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>夜班</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>有薪工时</div></td>
 	</tr>
  	<tr class=''>
		<td width='40' heigh38t='20'  align='center' class='A0101'>签到</td>
		<td width='40' class='A0101' align='center'>签退</td>
		<td width='20' class='A0101' align='center'>事</td>
		<td width='20' class='A0101' align='center'>病</td>		
		<td width='20' class='A0101' align='center'>无</td>
		<td width='20' class='A0101' align='center'>年</td>
		<td width='20' class='A0101' align='center'>补</td>
		<td width='20' class='A0101' align='center'>婚</td>
		<td width='20' class='A0101' align='center'>丧</td>
		<td width='20' class='A0101' align='center'>产</td>
		<td width='20' class='A0101' align='center'>工</td>
	</tr>";
	$i=1;
	

	if($KqSign == 1 || $KqSign == ''){
		$SearchRows = "AND (M.KqSign='1' or (M.KqSign='2' AND (M.BranchId IN ( 6, 7, 8 ) OR M.JobId IN ( 10, 28 )))) ";
	}else{
		$SearchRows = "AND M.KqSign='2' AND M.Number not in (select Number From staffmain where M.KqSign='2' AND (M.BranchId IN ( 6, 7, 8 ) OR M.JobId IN ( 10, 28 ))) ";
	}


	$mySql="SELECT M.Number,M.Name,J.Name AS Job,G.GroupName FROM $DataIn.lw_staffmain M LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId 
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE 1 $KqSignStr AND
((M.Number NOT IN (SELECT K.Number FROM $DataIn.redeployk K GROUP BY K.Number ORDER BY K.Id) and M.KqSign<3)
OR(
	M.Number IN(
		SELECT K.Number FROM $DataIn.redeployk K 
			INNER JOIN(
				SELECT Number,max(Month) as Month FROM $DataIn.redeployk group by Number) k2 ON K.Number=k2.Number and K.Month=k2.Month 	
			WHERE K.ActionIn<3 and K.Month<='$CheckMonth'))
OR(
	M.Number IN(
		SELECT Ka.Number FROM $DataIn.redeployk Ka 
			INNER JOIN(
				SELECT Number,min(Month) as Month FROM $DataIn.redeployk WHERE Month>'$CheckMonth' group by Number) k2a ON Ka.Number=k2a.Number and Ka.Month=k2a.Month 
			WHERE Ka.ActionOut<3)))
and left(M.ComeIn,7) <='$CheckMonth' 
and M.Number NOT IN (SELECT D.Number FROM $DataIn.dimissiondata D WHERE D.Number=M.Number and  D.outDate<'$CheckDate')
$SearchRows 
ORDER BY M.BranchId,M.GroupId,M.Number";
//echo $mySql;
	$result = mysql_query($mySql,$link_id);
	while($sqlRows = mysql_fetch_assoc($result)){
		$number = $sqlRows["Number"];

		$staff = new AttendanceAvatar_lw($number, $DataIn, $DataIn, $link_id);
		$staff->setupAttendanceData($number, $CheckDate, $DataIn, $DataIn, $link_id);
		$staff->attendanceSetup($DataIn, $DataIn, $link_id);
		$dayAttendanceInfomation =  $staff->getInfomationByTag();
		echo"<tr><td class='A0111' align='center'>".$i++."</td>";
		echo"<td class='A0101' align='center' $NumberColor >". $staff->getStaffNumber() ."</td>";
		echo"<td class='A0101' align='center'>". $staff->getStaffName() ."</td>";
		echo"<td class='A0101' align='center'>". $staff->getStaffGroupName() ."</td>";
		echo"<td class='A0101' align='center'>". $staff->getStaffJobName() ."</td>";
		echo"<td class='A0101' align='center'><div $DateTypeColor>". $dayAttendanceInfomation["state"] ."</div></td>";
		echo"<td class='A0101' align='center'><span $AIcolor>". $dayAttendanceInfomation["startTime"] ."</span></td>";
		echo"<td class='A0101' align='center'><span $AOcolor>". $dayAttendanceInfomation["endTime"] ."</span></td>";
		echo"<td class='A0101' align='center'>". letZeroChangeToSpace($dayAttendanceInfomation["defaultWorkHours"]) ."</td>";
		//劳务工工时计算
		$lw_workHours = $dayAttendanceInfomation["workHours"] + $dayAttendanceInfomation["workOtHours"] + $dayAttendanceInfomation["workZlHours"] + $dayAttendanceInfomation["weekOtTime"] + $dayAttendanceInfomation["weekZlHours"] + $dayAttendanceInfomation["holidayOtHours"] + $dayAttendanceInfomation["holidayZlHours"];

		echo"<td class='A0101' align='center'>". letZeroChangeToSpace($lw_workHours) ."</td>";

		echo"<td class='A0101' align='center'>". letZeroChangeToSpace($dayAttendanceInfomation["beLate"]) ."</td>";
		echo"<td class='A0101' align='center'>". letZeroChangeToSpace($dayAttendanceInfomation["beEarly"]) ."</td>";
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
		echo"<td class='A0101'>".letZeroChangeToSpace($dayAttendanceInfomation["nightShit"])."</td>";
		echo"<td class='A0101' align='center'>".letZeroChangeToSpace($dayAttendanceInfomation["dkHours"])."</td>";
	}


	echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'><input name='TempValue' type='hidden' value='0'>";
		echo"
			<tr class=''>
				<td rowspan='2' class='A0111'><div align='center'>序号</div></td>
				<td rowspan='2' class='A0101'><div align='center'>编号</div></td>
				<td rowspan='2' class='A0101'><div align='center'>姓名</div></td>
				<td rowspan='2' class='A0101'><div align='center'>小组</div></td>
				<td rowspan='2' class='A0101'><div align='center'>现职</div></td>
				<td rowspan='2' class='A0101'><div align='center'>日期<br>类别</div></td>
				<td height='19' colspan='2' class='A0101'><div align='center'>签卡记录</div></td>
				<td rowspan='2' class='A0101'><div align='center'>应到<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>实到<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>迟到</div></td>
				<td rowspan='2' class='A0101'><div align='center'>早退</div></td>
				<td colspan='9' class='A0101'><div align='center'>请、休假工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>缺勤<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>旷工<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>夜班</div></td>
				<td rowspan='2' class='A0101'><div align='center'>有薪工时</div></td>
			</tr>
			<tr class=''>
				<td heigh38t='20'  align='center' class='A0101'>签到</td>
				<td class='A0101' align='center'>签退</td>
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
	//步骤5：
	include "../model/subprogram/add_model_b.php";
	echo"<br>";
	//include "../model/subprogram/read_model_menu.php";
?>
<?php
	function letZeroChangeToSpace($data){
		return ($data == "" or $data == 0)? "&nbsp;":"$data";
	}
?>

	<input name="CheckNote" type="hidden" id="CheckNote" value="<?php  echo $CheckNote?>"/>
	<script language="javascript" type="text/javascript">
	function SaveDaytj(){
        var message=confirm("保存之前请确认相关数据是否正确，点取消可以返回修改。");
		if (message){
		   var kqList="";
		   for(var m=3; m<kqTable.rows.length-2; m++){
		         for(var n=9;n<13;n++){
				 var s=kqTable.rows[m].cells[n].innerText;
				      s=s.replace(/(^\s*)/g,"");
				   if(s.length>0){
		               kqList=kqList+","+
				      kqTable.rows[m].cells[1].innerHTML+"^^"+
					  kqTable.rows[m].cells[9].innerHTML+"^^"+
					  kqTable.rows[m].cells[10].innerHTML+"^^"+
					  kqTable.rows[m].cells[11].innerHTML+"^^"+
					  kqTable.rows[m].cells[12].innerHTML;
					  break;
					  }
				    }
                }	
		    //alert(kqList);
			document.getElementById("kqList").value=kqList;
			document.form1.action="kq_checkio_updated.php?ActionId=kq";
			document.form1.submit();
		}
		else{
			return false;
		}
	}
</script>


