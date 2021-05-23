<?php   
// 行政费用OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=900;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='60' align='center'>请款人</td>
		<td width='70' align='center'>请款日期</td>
		<td width='70' align='center'>请款金额</td>
		<td width='60' align='center'>货币类型</td>
		<td width='300' align='center'>请款说明</td>
		<td width='120' align='center'>分类</td>
		<td width='60' align='center'>票据</td>
		<td width='60' align='center'>状态</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND DATE_FORMAT(S.Date,'%Y-%m')='$MonthTemp'";

$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.Date,S.Estate,S.Locks,S.Operator,T.Name AS Type,C.Symbol AS Currency,E.Name AS AuditStaff
 	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataPublic.adminitype T ON S.TypeId=T.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	LEFt JOIN $DataPublic.admini_audittype D ON D.Id=T.AuditType
	LEFT JOIN $DataPublic.staffmain E ON E.Number=D.Number
	WHERE 1 $SearchRows order by S.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];		
		$Content=$myRow["Content"];
		$AuditStaff=$myRow["AuditStaff"];
		$Type=$myRow["Type"];		
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/cwadminicost/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="H".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
	
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='60' align='center'>$Operator</td>
				<td width='70' align='center'>$Date</td>
				<td width='70' align='center'>$Amount</td>
				<td width='60' align='center'>$Currency</td>
                <td width='300' > $Content</td>
				<td width='120' align='center'>$Type</td>
				<td width='60' align='center'>$Bill</td>
				<td width='60' align='center' >$Estate</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>