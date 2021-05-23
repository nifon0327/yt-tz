<?php   
//快递费用
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=950;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
		$Th_Col="选项|50|序号|40|寄件日期|80|快递公司|80|提单号码|100|件数|40|重量|50|金额|60|寄/到付|60|经手人|60|备注|250|状态|40|操作|60";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='80' align='center'>寄件日期</td>
		<td width='80' align='center'>快递公司</td>
		<td width='100' align='center'>提单号码</td>
		<td width='40' align='center'>件数</td>
		<td width='50' align='center'>重量</td>
		<td width='60' align='center'>金额</td>
		<td width='60' align='center'>寄/到付</td>
		<td width='60' align='center'>经手人</td>
		<td width='250' align='center'>备注</td>
		<td width='40' align='center'>状态</td>
		<td width='60' align='center'>操作</td>
	</tr></table>";
$SearchRows=" AND E.Estate='3' AND DATE_FORMAT(E.Date,'%Y-%m')='$MonthTemp'";

$mySql="SELECT 
E.Id,E.Mid,E.Date,E.ExpressNO,E.BoxQty,E.Weight,E.Amount,E.Type,E.Operator,E.Remark,
E.Estate,E.Locks,P.Name AS HandledBy,D.Forshort 
FROM $DataIn.ch9_expsheet E
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=E.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=E.HandledBy
WHERE 1 $SearchRows
ORDER BY E.Date DESC,E.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Forshort=$myRow["Forshort"];
		$ExpressNO=$myRow["ExpressNO"];
		$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
		$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
		$BoxQty=$myRow["BoxQty"];
		$Weight=$myRow["Weight"];
		$Amount=$myRow["Amount"];
		$Type=$myRow["Type"]==1?"到付":"寄付";		
		$HandledBy=$myRow["HandledBy"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='80' align='center'>$Date</td>
				<td width='80' align='center'>$Forshort</td>
				<td width='100' align='center'>$ExpressNO</td>
				<td width='40' align='center'>$BoxQty</td>
                <td width='50' > $Weight</td>
				<td width='60' align='center'>$Amount</td>
				<td width='60' align='center'>$Type</td>
				<td width='60' align='center' >$HandledBy</td>
				<td width='250' align='center'>$Remark</td>
				<td width='40' align='center'>$Estate</td>
				<td width='60' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>