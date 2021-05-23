<?php  
//节日奖金OK 
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=1140;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
	$Th_Col="选项|40|序号|40|奖金项目|120|部门|60|职位|60|员工ID|50|员工姓名|60|入职日期|70|在职时间|70|计算月份|100|离职日期|70|离职原因|70|比率参数|60|总金额|60|结付比率|60|结付金额|60|状态|40|请款月份|70";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='120' align='center'>奖金项目</td>
		<td width='60' align='center'>部门</td>
		<td width='60' align='center'>职位</td>
		<td width='50' align='center'>员工ID</td>
		<td width='60' align='center'>员工姓名</td>
		<td width='70' align='center'>入职日期</td>
		<td width='70' align='center'>在职时间</td>
		<td width='100' align='center'>计算月份</td>
		<td width='70' align='center'>离职日期</td>
		<td width='70' align='center'>离职原因</td>
		<td width='60' align='center'>比率参数</td>
		<td width='60' align='center'>总金额</td>
		<td width='60' align='center'>结付比率</td>
		<td width='60' align='center'>结付金额</td>
		<td width='40' align='center'>状态</td>
		<td width='70' align='center'>请款月份</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND DATE_FORMAT(S.Date,'%Y-%m')='$MonthTemp'";

$mySql="SELECT S.Id,S.ItemName,B.Name AS Branch,W.Name AS Job,S.Number,P.Name,P.Estate AS mEsate,S.Month,S.MonthS,S.MonthE,S.Divisor,S.Rate,S.Amount,S.Estate,S.Locks,S.Date,P.ComeIn,D.Reason,D.outDate,S.JfRate,S.JfTime
FROM $DataIn.cw11_jjsheet S 
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId 
LEFT JOIN $DataPublic.jobdata W ON W.Id=S.JobId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
LEFT JOIN $DataPublic.dimissiondata D ON D.Number=P.Number
WHERE 1 $SearchRows  ORDER BY S.BranchId,S.JobId,P.Number";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemName=$myRow["ItemName"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Number=$myRow["Number"];
		//$Name=$myRow["Name"];
		$Name=$myRow["mEsate"]==1?$myRow["Name"]:"<div class='yellowB'>".$myRow["Name"]."</div>";		
		$Month=$myRow["Month"];
		$MonthS=$myRow["MonthS"];
		$MonthE=$myRow["MonthE"];
		$MonthSTR=$MonthS."~".$MonthE;
		$Divisor=$myRow["Divisor"];
		$Rate=$myRow["Rate"]*100/100;
		$Amount=$myRow["Amount"];
		$ComeIn=$myRow["ComeIn"];
		$JfRate=$myRow["JfRate"];
		$JfTime=$myRow["JfTime"];
		// 取得员工离职资料
		
		$outDate=$myRow["outDate"]==""?"&nbsp;":$myRow["outDate"];
		$Reason=$myRow["Reason"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Reason]' width='18' height='18'>";
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$Locks=1;
		$Date=$myRow["Date"];
		$Operator=($myRow["Operator"]=="")?"&nbsp;":$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		//////////////////////////////////////////////
		//工龄计算
		$Gl_STR="&nbsp;";
		include "../../public/subprogram/staff_model_gl.php";
		$totalResult=mysql_query("SELECT Amount   FROM $DataIn.cw11_jjsheet_frist    
		                            WHERE ItemName='$ItemName' AND Number='$Number'",$link_id);
		$totalAmount =mysql_result($totalResult,0,"Amount");
		$ItemName=$ItemName."--".$JfTime;
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='120' align='center'>$ItemName</td>
				<td width='60' align='center'>$Branch</td>
				<td width='60' align='center'>$Job</td>
				<td width='50' align='center'>$Number</td>
                <td width='60' align='center'> $Name</td>
				<td width='70' align='center'>$ComeIn</td>
				<td width='70' align='center'>$Gl_STR</td>
				<td width='100' align='center' >$MonthSTR</td>
				<td width='70' align='center'>$outDate</td>
				<td width='70' align='center' >$Reason</td>
				<td width='60' align='center'>$Rate%</td>
				<td width='60' align='center' >$totalAmount</td>
				<td width='60' align='center'>$JfRate</td>
				<td width='60' align='center' >$Amount</td>
				<td width='40' align='center'>$Estate</td>
				<td width='70' align='center' >$Month</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>