<?php 
//电信-ZX  2012-08-01
$nowYear=date("Y");//默认年
$sYear=$sYear==""?$nowYear:$sYear;
$T="<br><table border='0' cellspacing='0' bgcolor='#CCCCCC' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:9px;'>
	<tr>
		<td width='45' height='25' class='A1111' align='center'>序号</td>
		<td width='60' class='A1101' align='center'>部门</td>
		<td width='60' class='A1101' align='center'>职位</td>
		<td width='60' class='A1101' align='center'>员工姓名</td>
		<td width='85' class='A1101' align='center'>请假起始日期</td>
		<td width='85' class='A1101' align='center'>请假结束日期</td>
		<td width='60' class='A1101' align='center'>请假工时</td>
		<td width='70' class='A1101' align='center'>请假分类</td>
		<td width='370' class='A1101' align='center'>请假原因</td>
		<td width='120' class='A1101' align='center'>审核(点击批准)</td>
 	</tr>
	<tr>
	<td colspan='10' height='425' class='A0111' valign='top'><div style='width:1010;height:425;overflow-x:hidden;overflow-y:scroll'><table border='0' Id='RecordList' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:9px;'>
	";
//记录内容
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$checkQJ="
SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.bcType,T.Name AS Type,M.Name,C.Name AS Job,B.Name AS Branch
FROM $DataPublic.kqqjsheet J 
LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
LEFT JOIN $DataPublic.jobdata C ON C.Id=M.JobId
WHERE 1 AND J.Estate=1 AND left(J.StartDate,4)='$sYear' 
ORDER BY J.StartDate";
$QJResult = mysql_query($checkQJ." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($QJResult)){
	$i=1;
	do{
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Job=$myRow["Job"];
		$Branch=$myRow["Branch"];
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"];
		$MonthTemp=substr($EndDate,0,7);
		$Reason=$myRow["Reason"];
		$Type=$myRow["Type"];
		$bcType=$myRow["bcType"];
		
		$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
		$Days=intval($HoursTemp/24);//取整求相隔天数
			$HolidayTemp=0;
			$DateTemp=$StartDate;
			for($n=1;$n<=$Days;$n++){
				$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				$weekDay=date("w",strtotime("$DateTemp"));	 
				if($weekDay==6 || $weekDay==0){
					$HolidayTemp=$HolidayTemp+1;
					}
				else{
					$holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
					if($holiday_Row = mysql_fetch_array($holiday_Result)){
						$HolidayTemp=$HolidayTemp+1;
						}
					}
				}
			//计算请假工时
			$Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
			//如果是临时班，则按实际计算
			if($bcType==0){
				$Hours=$Hours>4?($Hours<=9?$Hours-1:8):$Hours;//相隔小时数的实际工时
				}
			$HourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时
		//权限
		$j=$i-1;
		
		if($PowerS==1){
			if($HourTotal>24){
				$Estate="<div>&nbsp;&nbsp;&nbsp;&nbsp;未批准</div>";
				}
			else{
				$Estate="<div style='color: #FF0000;font-weight: bold;' onClick='javascript:QJSH($j,$Id)'>&nbsp;&nbsp;&nbsp;&nbsp;未批准</div>";
				}
			}
		else{
			if($HourTotal>24){
				$Estate="<div style='color: #FF0000;font-weight: bold;' onClick='javascript:QJSH($j,$Id)'>&nbsp;&nbsp;&nbsp;&nbsp;未批准</div>";
				}
			else{
				$Estate="<div>&nbsp;&nbsp;&nbsp;&nbsp;未批准</div>";
				}
			}
		$T.="<tr>
			<td class='A0101' align='center' width='43' height='30'>$i</td>
			<td class='A0101' align='center' width='60'>$Branch</td>
			<td class='A0101' align='center' width='60'>$Job</td>
			<td class='A0101' align='center' width='60'>$Name</td>
			<td class='A0101' align='center' width='85'>$StartDate</td>
			<td class='A0101' align='center' width='85'>$EndDate</td>
			<td class='A0101' align='center' width='60'>$HourTotal</td>
			<td class='A0101' width='70'>$Type</td>
			<td class='A0101' width='370'>$Reason</td>
			<td class='A0100' width='120'>$Estate</td>
			</tr>";
		$i++;
		}while($myRow = mysql_fetch_array($QJResult));
	}
else{
	$T.="<tr><td class='A0101' align='center' height='30'>没有请假记录</td></tr>";
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$T.="</table></div></td></tr></table>";
?>