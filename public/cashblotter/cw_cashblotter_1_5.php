<?php
//5	 非BOM采购预付订金
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.nonbom11_djmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='80' class='A1101'>供应商</td>
<td width='60' class='A1101'>采购单号</td>
<td width='400' class='A1101'>预付说明</td>
<td width='40' class='A1101'>货币</td>
<td width='60' class='A1101'>预付金额</td>
<td width='40' class='A1101'>状态</td>
<td width='60' class='A1101'>请款人</td>
<td width='70' class='A1101'>请款日期</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	//$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/nonbomdj/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";

	$checkSheetSql=mysql_query("SELECT 
	A.Id,A.Mid,A.Did,A.CompanyId,A.PurchaseID,A.Amount,A.Remark,A.Date,A.Estate,A.Locks,A.Operator,
	C.Forshort,D.Symbol AS Currency,E.Id AS cgMid
 	FROM $DataIn.nonbom11_djsheet A
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	LEFT JOIN $DataIn.nonbom6_cgmain E ON E.PurchaseID=A.PurchaseID
	WHERE  A.Mid='$Mid' ORDER BY A.Date DESC
	",$link_id);
	if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
		//计算子记录数量
		$Rowspan=mysql_num_rows($checkSheetSql);
		//输出首行前段
		echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$PayDate</td>";	//结付日期
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Payee</td>";							//凭证
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";					//结付备注
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";						//结付总额
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";						//结付银行	
		$j=1;
		do{
			//结付明细数据
			$Forshort=$checkSheetRow["Forshort"];
			$CompanyId=$checkSheetRow["CompanyId"];
			$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);	
			$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
			
			$PurchaseID=$checkSheetRow["PurchaseID"];
			$cgMid=$checkSheetRow["cgMid"];
			$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
			$PurchaseID="<a href='../public/nonbom6_view.php?f=$cgMidSTR' target='_blank'>$PurchaseID</a>";
			
			$Remark=$checkSheetRow["Remark"]==""?"&nbsp;":$checkSheetRow["Remark"];
			$Currency=$checkSheetRow["Currency"];
			$Amount=$checkSheetRow["Amount"];	
			
			//查货款结付日期
			$Did=$checkSheetRow["Did"];
			$dfPayDate=$checkSheetRow["dfPayDate"]==""?"&nbsp;":$checkSheetRow["dfPayDate"];
			if($Did==0){
				$Did="<div class='redB'>未抵付</div>";
				$LockRemark="";
				}
			else{
				$Did="<a href='../public/nonbom6_cwview.php?d=$Did' target='_blank'><span class='greenB'>已抵付-$Did</span></a>";
				$LockRemark="记录已经抵付，锁定操作！";
				}
			$Operator=$checkSheetRow["Operator"];
			include "../model/subprogram/staffname.php";
			$Date=$checkSheetRow["Date"];
	
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101'>$Forshort</td>";
			echo"<td class='A0101' align='center'>$PurchaseID</td>";
			echo"<td class='A0101' >$Remark</td>";
			echo"<td class='A0101' align='center'>$Currency</td>";	
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"<td  class='A0101' align='center'>$Did</td>";
			echo"<td  class='A0101' align='center'>$Operator</td>";
			echo"<td  class='A0101' align='center'>$Date</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>