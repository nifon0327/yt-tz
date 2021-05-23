<?php 
//电信-ZX  2012-08-01
include "../model/kq_YearHolday.php";
//正式工薪资查询
$nowYear=date("Y");//默认年
$sYear=$sYear==""?$nowYear:$sYear;
$T="<br><table border='0' cellspacing='0' bgcolor='#CCCCCC' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:9px;'>
	<tr>
		<td width='45' height='25' class='A1111' align='center'>序号</td>
		<td width='95' class='A1101' align='center'>请假起始日期</td>
		<td width='95' class='A1101' align='center'>请假结束日期</td>
		<td width='65' class='A1101' align='center'>请假工时</td>
		<td width='75' class='A1101' align='center'>请假分类</td>
		<td width='410' class='A1101' align='center'>请假原因</td>
		<td width='100' class='A1101' align='center'>状态</td>
 	</tr>
	<tr>
	<td colspan='7' height='425' class='A0111' valign='top'><div style='width:880;height:425;overflow-x:hidden;overflow-y:scroll'><table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:9px;'>
	";
//记录内容
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$checkQJ="
SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.bcType,J.Estate,T.Name AS Type
 FROM $DataPublic.kqqjsheet J 
	LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
	WHERE 1 AND J.Number='$Number' AND left(J.StartDate,4)='$sYear' ORDER BY J.StartDate";
$QJResult = mysql_query($checkQJ." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($QJResult)){
	$i=1;
	do{
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"];
		$MonthTemp=substr($EndDate,0,7);
		$Reason=$myRow["Reason"];
		$Type=$myRow["Type"];
		$bcType=$myRow["bcType"];
		$Estate=$myRow["Estate"]==1?"<div style='color: #FF0000;font-weight: bold;'>未批准</div>":"<div style='color: #009900;font-weight: bold;'>已批准</div>";
		/*$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
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
                 */
                $HourTotal=GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);
		$T.="<tr>
			<td class='A0101' align='center' width='43' height='30'>$i</td>
			<td class='A0101' align='center' width='95'>$StartDate</td>
			<td class='A0101' align='center' width='95'>$EndDate</td>
			<td class='A0101' align='center' width='65'>$HourTotal</td>
			<td class='A0101' width='75'>$Type</td>
			<td class='A0101' width='410'>$Reason</td>
			<td class='A0100' width='100'>$Estate</td>
			</tr>";
		$i++;
		}while($myRow = mysql_fetch_array($QJResult));
	}
else{
	$T.="<tr><td class='A0101' align='center'>没有请假记录</td></tr>";
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$T.="</table></div></td></tr></table>";
?>