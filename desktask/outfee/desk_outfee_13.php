<?php   
//社保费用OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=750;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='60' align='center'>员工姓名</td>
		<td width='60' align='center'>部门</td>
		<td width='60' align='center'>职位</td>
		<td width='70' align='center'>缴费月份</td>
		<td width='60' align='center'>个人缴费</td>
		<td width='60' align='center'>公司缴费</td>
		<td width='60' align='center'>小计</td>
		<td width='80' align='center'>结付</td>
		<td width='100' align='center'>登记日期</td>
		<td width='80' align='center'>操作员</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND S.Month='$MonthTemp'";

$mySql="SELECT S.Id,S.BranchId,S.JobId,S.Number,S.Month,S.mAmount,S.cAmount,S.Locks,S.Date,S.Operator,S.Estate,
	P.Name
	 FROM $DataIn.sbpaysheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
	WHERE 1 $SearchRows AND S.TypeId=1 ORDER BY S.Month DESC,S.BranchId,S.JobId,P.Number";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];			
		$Name=$myRow["Name"];
		$Month =$myRow["Month"];
		$mAmount =$myRow["mAmount"];
		$cAmount =$myRow["cAmount"];
		$Amount=sprintf("%.2f",$mAmount +$cAmount);
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		$Estate="<div align='center' class='yellowB'>未结付</div>";
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='60' align='center'>$Name</td>
				<td width='60' align='center'>$Branch</td>
				<td width='60' align='center'>$Job</td>
				<td width='70' align='center'>$Month</td>
                <td width='60' align='center'> $mAmount</td>
				<td width='60' align='center'>$cAmount</td>
				<td width='60' align='center'>$Amount</td>
				<td width='80' align='center' >$Estate</td>
				<td width='100' align='center' >$Date</td>
				<td width='80' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>