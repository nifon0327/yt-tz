<?php
// Forward费用OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=800;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
	$Th_Col="选项|40|序号|35|发票日期|70|Forward公司|80|入仓号|100|Forward Invoice|90|件数|35|研砼<br>称重|60|上海<br>称重|60|金额(HKD)|60|ETD/ETA|80|状态|30|备注|30|操作|50|$TypeName|110|出货日期|70";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='70' align='center'>发票日期</td>
		<td width='80' align='center'>Forward公司</td>
		<td width='100' align='center'>入仓号</td>
		<td width='90' align='center'>Forward Invoice</td>
		<td width='35' align='center'>件数</td>
		<td width='60' align='center'>研砼<br>称重</td>
		<td width='60' align='center'>上海<br>称重</td>
		<td width='60' align='center'>金额(HKD)</td>
		<td width='80' align='center'>ETD/ETA</td>
		<td width='30' align='center'>状态</td>
		<td width='30' align='center'>备注</td>
		<td width='50' align='center'>操作</td>
	</tr></table>";
$SearchRows=" AND F.Estate='3' AND DATE_FORMAT(F.Date,'%Y-%m')='$MonthTemp'";

$mySql="SELECT F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.Amount,F.InvoiceDate,F.PayType,
    F.ETD,F.Remark,F.Estate,F.Locks,F.Date,P.Name AS Operator,D.Forshort
    FROM $DataIn.ch3_forward F
    LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
    LEFT JOIN $DataPublic.staffmain P ON P.Number=F.Operator
    WHERE 1 $SearchRows";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$HoldNO=$myRow["HoldNO"]==""?"&nbsp;":$myRow["HoldNO"];
		$ForwardNO=$myRow["ForwardNO"];
		$BoxQty=$myRow["BoxQty"];
		$mcWG=$myRow["mcWG"];
		$forwardWG=$myRow["forwardWG"];
		$Amount=$myRow["Amount"];
		$InvoiceDate=$myRow["InvoiceDate"];
		$ETD=$myRow["ETD"]==""?"&nbsp;":$myRow["ETD"];
		$Operator=$myRow["Operator"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Locks=1;
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='70' align='center'>$InvoiceDate</td>
				<td width='80' align='center'>$Forshort</td>
				<td width='100' align='center'>$HoldNO</td>
                <td width='90' align='center'> $ForwardNO</td>
				<td width='35' align='right'>$BoxQty</td>
			    <td width='60' align='right'>$mcWG</td>
				<td width='60' align='right'>$forwardWG</td>
				<td width='60' align='right' >$Amount</td>
				<td width='80' align='center'>$ETD</td>
				<td width='30' align='center' >$Estate</td>
				<td width='30' align='center'>$Remark</td>
				<td width='50' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>