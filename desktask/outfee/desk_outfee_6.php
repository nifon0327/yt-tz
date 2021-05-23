<?php   
//供应商税款OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=1000;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
		$Th_Col="选项|40|序号|40|请款日期|75|供应商|80|货币|40|税款金额|60|说明|300|发票号|80|状态|40|请款人|50";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='70' align='center'>请款日期</td>
		<td width='80' align='center'>供应商</td>
		<td width='60' align='center'>税款金额</td>
		<td width='40' align='center'>货币</td>
		<td width='500' align='center'>请款说明</td>
		<td width='80' align='center'>发票号</td>
		<td width='40' align='center'>状态</td>
		<td width='60' align='center'>请款人</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND DATE_FORMAT(S.Date,'%Y-%m')='$MonthTemp'";

$mySql="SELECT S.Id,S.Mid,S.Forshort,S.InvoiceNUM,S.InvoiceFile,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol
 	FROM $DataIn.cw2_gyssksheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 $SearchRows";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$InvoiceNUM=$myRow["InvoiceNUM"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
 		$Estate=$myRow["Estate"];
		switch($Estate){
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				break;
			default:
				$Estate="<div align='center' class='redB' title='状态错误'>×</div>";
				$LockRemark="状态错误";
				$Locks=0;
				break;
			}
		if($InvoiceFile==1){
			$InvoiceFile="S".$Id;
			$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
			$InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
		    $InvoiceNUM="<a href=\"../../public/openorload.php?d=$Dir&f=$InvoiceFile&Type=&Action=7\" target=\"download\">$InvoiceNUM</a>";
			}
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='70' align='center'>$Date</td>
				<td width='80' align='center'>$Forshort</td>
				<td width='60' align='right'>$Amount</td>
				<td width='40' align='center'>$Symbol</td>
                <td width='500' > $Remark</td>
				<td width='80' align='center'>$InvoiceNUM</td>
				<td width='40' align='center'>$Estate</td>
				<td width='60' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>