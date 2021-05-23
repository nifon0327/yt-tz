<?php   
//假日加班费OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=850;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
		$Th_Col="选项|40|序号|40|员工ID|50|员工姓名|60|部门|60|职位|60|月份|60|2倍时薪|60|2倍工时|60|3倍时薪|60|3倍工时|60|加班费|60|状态|40|更新日期|80|操作|60";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='50' align='center'>员工ID</td>
		<td width='60' align='center'>员工姓名</td>
		<td width='60' align='center'>部门</td>
		<td width='60' align='center'>职位</td>
		<td width='60' align='center'>月份</td>
		<td width='60' align='center'>2倍时薪</td>
		<td width='60' align='center'>2倍工时</td>
		<td width='60' align='center'>3倍时薪</td>
		<td width='60' align='center'>3倍工时</td>
		<td width='60' align='center'>加班费</td>
		<td width='40' align='center'>状态</td>
		<td width='80' align='center'>更新日期</td>
		<td width='60' align='center'>操作</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND S.Month='$MonthTemp'";
$mySql="SELECT 
S.Id,S.Number,S.Month,S.xHours,S.xWage,S.fHours,S.fWage,S.Amount,S.Date,S.Estate,S.Locks,S.Operator,M.Name,J.Name AS Job,B.Name AS Branch
FROM $DataIn.hdjbsheet S
LEFT JOIN $DataPublic.staffmain M ON S.Number=M.Number
LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
WHERE 1 $SearchRows ORDER BY M.BranchId,M.JobId,M.Number";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
        $m=1;
		$Id=$myRow["Id"];	
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Month=$myRow["Month"];
		$xHours=$myRow["xHours"];
		$fHours=$myRow["fHours"];
		$xWage=$myRow["xWage"];
		$fWage=$myRow["fWage"];
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		$Locks=1;
		$Estate="<div align='center' class='redB'>未付</div>";
              	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='50' align='center'>$Number</td>
				<td width='60' align='center'>$Name</td>
				<td width='60' align='center'>$Branch</td>
				<td width='60' align='center'>$Job</td>
                <td width='60' align='center'> $Month</td>
				<td width='60' align='center'>$xWage</td>
				<td width='60' align='center'>$xHours</td>
				<td width='60' align='center' >$fWage</td>
				<td width='60' align='center'>$fHours</td>
				<td width='60' align='center' >$Amount</td>
				<td width='40' align='center'>$Estate</td>
				<td width='80' align='center'>$Date</td>
				<td width='60' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>