<?php   
//未结付退税收益OK	
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
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='60' align='center'>国税时间</td>
		<td width='80' align='center'>免抵退税金额</td>
		<td width='90' align='center'>免抵退税发票号</td>
		<td width='100' align='center'>结付银行</td>
		<td width='70' align='center'>收款日期</td>
		<td width='60' align='center'>扫描附件</td>
		<td width='250' align='center'>备注</td>
		<td width='80' align='center'>期末留抵税额</td>
		<td width='60' align='center'>结付凭证</td>
		<td width='50' align='center'>状态</td>
		<td width='60' align='center'>操作人</td>
	</tr></table>";
$SearchRows=" AND M.Estate='3'  AND  DATE_FORMAT(M.Taxdate,'%Y-%m')='$MonthTemp'";

$mySql="select M.Id,M.Taxdate,M.TaxNo,(-M.Taxamount) AS Taxamount,M.Taxgetdate,M.Attached,M.Estate,M.Remark,M.Operator,M.endTax,M.TaxIncome,M.Proof,B.Title
FROM $DataIn.cw14_mdtaxmain M  
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
WHERE 1 $SearchRows ";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
       $m=1;
	   $Id=$myRow["Id"];
	   $TaxNo=$myRow["TaxNo"];
	   $Taxdate=date("Y-m",strtotime($myRow["Taxdate"]));
	   $endTax=$myRow["endTax"];
	   $Taxamount=$myRow["Taxamount"];
	   $BankName=$myRow["Title"];
	   $Taxgetdate=$myRow["Taxgetdate"];
	   if($Taxgetdate=="0000-00-00")$Taxgetdate="&nbsp;";
	   $Attached=$myRow["Attached"];
	   $Proof=$myRow["Proof"];
	   $TaxIncome=$myRow["TaxIncome"];
	   $Dir=anmaIn("download/cwmdtax/",$SinkOrder,$motherSTR);
		if($Attached!=""){
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		if($Proof!=""){
			$Proof=anmaIn($Proof,$SinkOrder,$motherSTR);
			$Proof="<span onClick='OpenOrLoad(\"$Dir\",\"$Proof\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Proof="-";
			}
	   $Estate=$myRow["Estate"];
	        switch($Estate){
			case "3":
			$Estate="<div align='center' class='redB' >税款未收到</div>";
			break;	
			case "0":
			$Estate="<div align='center' class='greenB' >税款已收到</div>";
			break;
			}
		$Remark=$myRow["Remark"];
		if($Remark=="")$Remark="&nbsp;";
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";

         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='60' align='center'>$Taxdate</td>
				<td width='80' align='center'>$Taxamount</td>
				<td width='90' align='center'>$TaxNo</td>
				<td width='100' align='center'>$BankName</td>
                <td width='70' align='center'> $Taxgetdate</td>
				<td width='60' align='center'>$Attached</td>
				<td width='250' align='center'>$Remark</td>
				<td width='80' align='center' >$endTax</td>
			    <td width='60' align='center' >$Estate</td>
			    <td width='50' align='center' >$Proof</td>
			    <td width='60' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>