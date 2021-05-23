<?php 
//mc 验厂文件 OK ewen 2013-08-03
/*
2013-05前：2、3倍薪工时在假日统计表中读取
2013-05后：2、3倍薪工时都在员工月考勤统计表中取
*/
include "../model/modelhead.php";
?>
<table border='0' width='<?php  echo $tableWidth?>' cellpadding='0' cellspacing='0'>
	<tr class=''>
		<td width='30' rowspan='2' class='A1101' align='center'>序号</td>
		<td width='50' rowspan='2' class='A1101' align='center'>部门</td>
		<td width='50' rowspan='2' class='A1101' align='center'>职位</td>
		<td width='50' rowspan='2' class='A1101' align='center'>员工ID</td>
		<td width='50' rowspan='2' class='A1101' align='center'>姓名</td>		
		<td width='38' rowspan='2' class='A1101' align='center'>应到<br>工时</td>
	  <td width='38' rowspan='2' class='A1101' align='center'>实到<br>工时</td>
		<td width='114' colspan='3' class='A1101' align='center'>加点加班工时</td>
		<td width='38' rowspan='2' class='A1101' align='center'>迟到<br>次数</td>
		<td width='38' rowspan='2' class='A1101' align='center'>早退<br>次数</td>
		<td width='166' colspan='4' class='A1101' align='center'>请、休假工时</td>
		<td width='38' rowspan='2' class='A1101' align='center'>缺勤<br>工时</td>
		<td width='38' rowspan='2' class='A1101' align='center'>无效<br>工时</td>
		<td width='38' rowspan='2' class='A1101' align='center'>旷工<br>工时</td>
		<td width='38' rowspan='2' class='A1101' align='center'>夜班<br>次数</td>
  	</tr>
	<tr class=''>
		<td width='38' class='A0101' align='center'>G</td>
		<td width='38' class='A0101' align='center'>X</td>
		<td width='38' class='A0101' align='center'>F</td>
		<td width='38' class='A0101' align='center'>事假</td>
		<td width='38' class='A0101' align='center'>病假</td>
		<td width='45' class='A0101' align='center'>有薪假</td>
		<td width='45' class='A0101' align='center'>无薪假</td>
	</tr>
<?php 
$i=1;
$mySql = "SELECT K.Id,K.Month,K.Number,K.Dhours,K.Whours,K.Ghours,K.Xhours,K.Fhours,K.InLates,K.OutEarlys,K.SJhours,K.BJhours,K.YXJhours,K.WXJhours,K.QQhours,
K.WXhours,K.KGhours,K.YBs,K.Locks,M.Name,M.Estate,B.Name AS Branch,J.Name AS Job
FROM $DataIn.kqdata K 
LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
LEFt JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE 1 AND K.Month='$chooseMonth' ORDER BY K.Month DESC,M.Estate DESC,M.BranchId,M.JobId,K.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$Month=$myRow["Month"];
		$Job=$myRow["Job"];		
				//2倍加班工时
        if($Month<"2013-05"){
			$XhoursResult=mysql_fetch_array(mysql_query("SELECT xHours,fHours FROM $DataIn.hdjbsheet WHERE Number=$Number and Month='$Month'",$link_id));
        	$Xhours=$XhoursResult["xHours"];
			$FHours=$XhoursResult["fHours"];
			}
		else{
			$Xhours=$myRow["Xhours"];
			$Fhours=0;//全部为超点
			}

		$Dhours=zerotospace($myRow["Dhours"]);		//应到工时
		$Whours=zerotospace($myRow["Whours"]);		//实到工时
		$Ghours=zerotospace($myRow["Ghours"]);		//加点工时
		$Xhours=zerotospace($Xhours);		//休息日加班工时
		$Fhours=zerotospace(Fhours);		//法定假日加班工时
		$InLates=zerotospace($myRow["InLates"]);	//迟到次数
		$OutEarlys=zerotospace($myRow["OutEarlys"]);//早退次数
		$SJhours=zerotospace($myRow["SJhours"]);	//事假工时
		$BJhours=zerotospace($myRow["BJhours"]);	//病假工时
		$BXhours=zerotospace($myRow["BXhours"]);	//补休工时 
		$WXJhours=zerotospace($myRow["WXJhours"]);	//无薪假工时
		$YXJhours=zerotospace($myRow["YXJhours"]);	//无薪假工时
		$QQhours=zerotospace($myRow["QQhours"]);	//缺勤工时
		$WXhours=zerotospace($myRow["WXhours"]);	//无薪工时
		$KGhours=zerotospace($myRow["KGhours"]);	//旷工工时
		$YBs=zerotospace($myRow["YBs"]);			//夜班次数

		echo"<tr>";
		echo"<td align='center' class='A0111'>$i</td>";
		echo"<td class='A0101' align='center'>$Branch</td>";
		echo"<td class='A0101' align='center'>$Job</td>";
		echo"<td class='A0101' align='center'>$Number</td>";
		echo"<td class='A0101' align='center'>$Name</td>";
		echo"<td class='A0101' align='center'>$Dhours</td>";
		echo"<td class='A0101' align='center'>$Whours</td>";
		echo"<td class='A0101' align='center'>$Ghours</td>";
		echo"<td class='A0101' align='center'>$Xhours</td>";
		echo"<td class='A0101' align='center'>$Fhours</td>";
		echo"<td class='A0101' align='center'>$InLates</td>";
		echo"<td class='A0101' align='center'>$OutEarlys</td>";
		echo"<td class='A0101' align='center'>$SJhours</td>";
		echo"<td class='A0101' align='center'>$BJhours</td>";
		echo"<td class='A0101' align='center'>$YXJhours</td>";
		echo"<td class='A0101' align='center'>$WXJhours</td>";
		echo"<td class='A0101' align='center'>$QQhours</td>";
		echo"<td class='A0101' align='center'>$WXhours</td>";
		echo"<td class='A0101' align='center'>$KGhours</td>";
		echo"<td class='A0101' align='center'>$YBs</td>";
		echo"</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
		//合计
		//echo"<tr height='30' bgcolor='#FFCC99'><td colspan='15' class='A0111'>注：F为法定假日；X为休息日；G为工作日</td></tr>";
	}
?>
</table>
<table border='0' width='<?php  echo $tableWidth?>' cellpadding='0' cellspacing='0' class=''>
<tr>
    <td width='30' rowspan='2' class='A0101' align='center'>序号</td>
	<td width='50' rowspan='2' class='A0101' align='center'>部门</td>
	<td width='50' rowspan='2' class='A0101' align='center'>职位</td>
	<td width='50' rowspan='2' class='A0101' align='center'>员工ID</td>
	<td width='50' rowspan='2' class='A0101' align='center'>姓名</td>	
	<td width='38' rowspan='2' class='A0101' align='center'>应到<br>工时</td>
    <td width='38' rowspan='2' class='A0101' align='center'>实到<br>工时</td>
    <td width='114' colspan='3' class='A0101' align='center'>加点加班工时</td>
    <td width='38' rowspan='2' class='A0101' align='center'>迟到<br>次数</td>
    <td width='38' rowspan='2' class='A0101' align='center'>早退<br>次数</td>
    <td width='166' colspan='4' class='A0101' align='center'>请、休假工时</td>
    <td width='38' rowspan='2' class='A0101' align='center'>缺勤<br>工时</td>
    <td width='38' rowspan='2' class='A0101' align='center'>无效<br>工时</td>
    <td width='38' rowspan='2' class='A0101' align='center'>旷工<br>工时</td>
	<td width='38' rowspan='2' class='A0101' align='center'>夜班<br>次数</td>
  </tr>
  <tr>
	<td width='38' class='A0101' align='center'>G</td>
	<td width='38' class='A0101' align='center'>X</td>
	<td width='38' class='A0101' align='center'>F</td>
	<td width='38' class='A0101' align='center'>事假</td>
	<td width='38' class='A0101' align='center'>病假</td>
	<td width='45' class='A0101' align='center'>有薪假</td>
	<td width='45' class='A0101' align='center'>无薪假</td>
  </tr></table>