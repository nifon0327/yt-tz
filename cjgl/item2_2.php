<?php
//电信-zxq 2012-08-01
$Th_Col="序号|30|申请人|50|请假起始时间|120|请假结束日期|120|请假工时|50|请假类型|50|请假原因|330|登记日期|70|批准|50|不批准|50";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$nowInfo="当前:请假审核";
//步骤5：
echo"<table id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='6' height='40px' class=''>$ClientList</td>
	<td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";


$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.Proof,J.bcType,J.Date,
M.Number,M.Name,T.Name AS Type
 FROM $DataPublic.kqqjsheet J 
	LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
	LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
	WHERE  J.Estate=1 AND G.GroupLeader='$Login_P_Number'  AND M.cSign='7' order by J.StartDate DESC";
//	echo $mySql;1 $SearchRows AND
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"];
		$MonthTemp=substr($EndDate,0,7);
		$Reason=$myRow["Reason"]==""?"&nbsp;":$myRow["Reason"];
		$Type=$myRow["Type"];

		$Number=$myRow["Number"];

		$Date=substr($myRow["Date"],0,10);
		$Proof=$myRow["Proof"];

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

			$bcType=$bcType==0?"标准":"<div class=yellowB>临时</div>";
			$checkMonth=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Month='$MonthTemp' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
			if($checkRow = mysql_fetch_array($checkMonth)){
				$LockRemark="该月考勤统计已生成,禁止修改.";
				}
			if($SubAction==31){//有权限
				$UpdateIMG1="<img src='../images/Pass.png' width='30' height='30'";
				$UpdateClick1="onclick='qjAuditing(1,$Id)'";
				$UpdateIMG2="<img src='../images/unPass.png' width='30' height='30'";
				$UpdateClick2="onclick='qjAuditing(2,$Id)'";
				}
			else{
				$UpdateIMG1="<img src='../images/PassNo.png' width='30' height='30'";
				$UpdateClick1="";
				$UpdateIMG2="<img src='../images/unPassNo.png' width='30' height='30'";
				$UpdateClick2="";
				}
			echo"<tr>
				 <td class='A0111' align='center' height='25'>$i</td>";
			echo"<td class='A0101' align='center'>$Name</td>";
			echo"<td class='A0101' align='center'>$StartDate</td>";
			echo"<td class='A0101' align='center'>$EndDate</td>";
			echo"<td class='A0101' align='center'>$HourTotal</td>";
			echo"<td class='A0101' align='center'>$Type</td>";
			echo"<td class='A0101'>$Reason</td>";
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center' $UpdateClick1>$UpdateIMG1</td>";
			echo"<td class='A0101' align='center' $UpdateClick2>$UpdateIMG2</td>";
			echo"</tr>";
			$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='12' align='center' height='30' class='A0111' style='background-color: #fff;'><div class='redB'>你的小组没有申请中的请假记录.</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
</html>
<script>
function qjAuditing(Action,Id){
	var url="item2_2_ajax.php?Id="+Id+"&Action="+Action;
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			var BackData=ajax.responseText;
			//更新该单元格底色和内容
			if(Action==1){
				ListTable.rows[2].cells[8].innerHTML=BackData;
				ListTable.rows[2].cells[9].innerHTML="&nbsp;";
				ListTable.rows[2].cells[8].onclick="";
				ListTable.rows[2].cells[9].onclick="";
				}
			else{
				ListTable.rows[2].cells[8].innerHTML="&nbsp;";
				ListTable.rows[2].cells[9].innerHTML=BackData;
				ListTable.rows[2].cells[8].onclick="";
				ListTable.rows[2].cells[9].onclick="";
				}
			}
		}
　	ajax.send(null);
	}
</script>