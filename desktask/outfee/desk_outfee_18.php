<?php
// 报关/商检费用OK
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
		$Th_Col="选项|40|序号|30|货运公司|80|目的地|80|提单号码|80|件数|40|公司称重|60|上海称重|60|单价(元/KG)|60|运费<br>(RMB)|60|入仓费<br>(HKD)|60|报关费用<br>(RMB)|60|商检费用<br>(RMB)|60|状态|40|备注|40|操作|50|物流对账日期|80|$TypeName|110|出货日期|70";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='80' align='center'>货运公司</td>
		<td width='100' align='center'>目的地</td>
		<td width='80' align='center'>提单号码</td>
		<td width='40' align='center'>件数</td>
		<td width='60' align='center'>研砼<br>称重</td>
		<td width='60' align='center'>上海<br>称重</td>
		<td width='60' align='center'>单价(元/KG</td>
		<td width='60' align='center'>运费<br>(RMB)</td>
		<td width='60' align='center'>入仓费<br>(HKD)</td>
		<td width='60' align='center'>报关费用<br>(RMB)</td>
		<td width='60' align='center'>商检费用<br>(RMB)</td>
		<td width='40' align='center'>状态</td>
		<td width='40' align='center'>备注</td>
		<td width='50' align='center'>操作</td>
	</tr></table>";
$SearchRows=" AND F.Estate='3' AND DATE_FORMAT(F.Date,'%Y-%m')='$MonthTemp'";

$mySql="SELECT   F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,
       F.depotCharge,F.declarationCharge,F.checkCharge,F.Remark,F.Estate,F.Locks,F.Date AS  fDate,F.Operator,D.Forshort
       FROM $DataIn.ch4_freight_declaration F
       LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId
       WHERE 1 $SearchRows ORDER BY F.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Forshort=$myRow["Forshort"];
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		$Id=$myRow["Id"];
		$Termini=$myRow["Termini"]==""?"&nbsp;":$myRow["Termini"];
		$ExpressNO=$myRow["ExpressNO"];
		$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
		$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";

		$BoxQty=$myRow["BoxQty"];
		$mcWG=$myRow["mcWG"];
		$forwardWG=$myRow["forwardWG"]==""?"&nbsp;":$myRow["forwardWG"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$mcWG*$Price);
		$depotCharge=$myRow["depotCharge"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myrow[Remark]' width='18' height='18'>";
		$Locks=1;
		$Estate=$myRow["Estate"];
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				break;
			case "0":
				$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
				break;
			}
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";

		$declarationCharge=$myRow["declarationCharge"]==""?"&nbsp;":$myRow["declarationCharge"];
		$checkCharge=$myRow["checkCharge"]==""?"&nbsp;":$myRow["checkCharge"];

         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='80' align='center'>$Forshort</td>
			    <td width='100' align='center'>$Termini</td>
                <td width='80' align='center'> $ExpressNO</td>
				<td width='40' align='right'>$BoxQty</td>
			    <td width='60' align='right'>$mcWG</td>
				<td width='60' align='right'>$forwardWG</td>
				<td width='60' align='right'>$Price</td>
				<td width='60' align='right' >$Amount</td>
				<td width='60' align='center'>$depotCharge</td>
				<td width='60' align='center'>$declarationCharge</td>
				<td width='60' align='center'>$checkCharge</td>
				<td width='40' align='center' >$Estate</td>
				<td width='40' align='center'>$Remark</td>
				<td width='50' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>