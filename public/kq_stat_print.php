<?php 
//内部模式:OK ewen2013-07-30
include "../model/modelhead.php";
?>
<table border='0' cellspacing='0' bgcolor='#FFFFFF'>
<tr class='' align='center'>
		<td width='30' rowspan='2' class='A1111'>日期</td>
		<td width='50' rowspan='2' class='A1101'>星期</td>
		<td width='45' rowspan='2' class='A1101'>类别</td>
		<td height='20' colspan='2' class='A1101'>签卡记录</td>
		<td width='45' rowspan='2' class='A1101'>应到<br>工时</td>
		<td width='45' rowspan='2' class='A1101'>实到<br>工时</td>
		<td colspan='3' class='A1101'>1.5倍薪工时</td>
		<td colspan='3' class='A1101'>2倍薪工时</td>
		<td colspan='3' class='A1101'>3倍薪工时</td>
		<td width='30' rowspan='2' class='A1101'>迟到</td>
		<td width='30' rowspan='2' class='A1101'>早退</td>
		<td colspan='4' class='A1101'>请、休假工时</td>
		<td width='30' rowspan='2' class='A1101'>缺勤<br />工时</td>
        <td width='30' rowspan='2' class='A1101'>无效<br />工时</td>
		<td width='30' rowspan='2' class='A1101'>旷工<br />工时</td>
		<td width='30' rowspan='2' class='A1101'>夜班<br />次数</td>
		
 	</tr>
  	<tr class='' align='center'>
		<td width='45' height='20' class='A0101'>签到</td>
		<td width='45' class='A0101'>签退</td>
		<td width='25' class='A0101'>标</td>
		<td width='25' class='A0101' >超</td>
		<td width='25' class='A0101' >直</td>
		<td width='25' class='A0101'>标</td>
		<td width='25' class='A0101' >超</td>
		<td width='25' class='A0101' >直</td>
		<td width='25' class='A0101'>标</td>
		<td width='25' class='A0101' >超</td>
		<td width='25' class='A0101' >直</td>
		<td width='50' class='A0101'>事假</td>
		<td width='50' class='A0101'>病假</td>		
		<td width='50' class='A0101'>有薪假</td>
		<td width='50' class='A0101'>无薪假</td>

	</tr>
<?php 
$i=1;
$mySql = "SELECT K.Id,K.Month,K.Number,K.Dhours,K.Whours,K.Ghours,K.InLates,K.OutEarlys,K.SJhours,K.BJhours,K.YXJhours,K.WXJhours,K.QQhours,
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
		$Job=$myRow["Job"];		
		$Dhours=zerotospace($myRow["Dhours"]);		//应到工时
		$Whours=zerotospace($myRow["Whours"]);		//实到工时
		$Ghours=zerotospace($myRow["Ghours"]);		//加点工时
		$GOverTime=zerotospace($myRow["GOverTime"]);
		$GDropTime=zerotospace($myRow["GDropTime"]);
		
		$Xhours=zerotospace($myRow["Xhours"]);		//休息日加班工时
		$XOverTime=zerotospace($myRow["XOverTime"]);
		$XDropTime=zerotospace($myRow["XDropTime"]);
		
		$Fhours=zerotospace($myRow["Fhours"]);		//法定假日加班工时
		$FOverTime=zerotospace($myRow["FOverTime"]);
		$FDropTime=zerotospace($myRow["FDropTime"]);
		
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
		echo"<td class='A0101' align='center'>$GOverTime</td>";
		echo"<td class='A0101' align='center'>$GDropTime</td>";
		
		echo"<td class='A0101' align='center'>$Xhours</td>";
		echo"<td class='A0101' align='center'>$XOverTime</td>";
		echo"<td class='A0101' align='center'>$XDropTime</td>";
		
		echo"<td class='A0101' align='center'>$Fhours</td>";
		echo"<td class='A0101' align='center'>$FOverTime</td>";
		echo"<td class='A0101' align='center'>$FDropTime</td>";
		
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
<tr class='' align='center'>
	<td rowspan='2' class='A0111'>日期</td>
	<td rowspan='2' class='A0101'>星期</td>
	<td rowspan='2' class='A0101'>类别</td>
	<td height='20' class='A0101'>签到</td>
	<td class='A0101'>签退</td>
	<td rowspan='2' class='A0101'>应到<br>工时</td>
	<td rowspan='2' class='A0101'>实到<br>工时</td>
	<td class='A0101'>标</td>
	<td class='A0101' >超</td>
	<td class='A0101' >直</td>
	<td class='A0101' >标</td>
	<td class='A0101' >超</td>
	<td class='A0101' >直</td>
	<td class='A0101' >标</td>
	<td class='A0101' >超</td>
	<td class='A0101' >直</td>
	<td rowspan='2' class='A0101'>迟到</td>
	<td rowspan='2' class='A0101'>早退</td>
	
	<td class='A0101' >事假</td>
	<td class='A0101' >病假</td>		
	<td class='A0101' >有薪假</td>
	<td class='A0101' >无薪假</td>
	<td rowspan='2' class='A0101'>缺勤<br />工时</td>
    <td rowspan='2' class='A0101'>无效<br />工时</td>
	<td rowspan='2' class='A0101'>旷工<br />工时</td>
	<td rowspan='2' class='A0101'>夜班<br />次数</td>
	
	</tr>
	<tr class=''  align='center'>
	<td height='20' colspan='2' class='A0101'>签卡记录</td>
	<td colspan='3' class='A0101'>1.5倍薪工时</td>
	<td colspan='3' class='A0101'>2倍薪工时</td>
	<td colspan='3' class='A0101'>3倍薪工时</td>
	<td colspan='4' class='A0101'>请、休假工时</td>
	</tr></table>
