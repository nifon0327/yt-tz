<?php
//3	BOM采购预付订金		OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cw2_fkdjmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='80' class='A1101'>供应商</td>
<td width='400' class='A1101'>预付说明</td>
<td width='60' class='A1101'>预付金额</td>
<td width='60' class='A1101'>分类</td>
<td width='60' class='A1101'>抵付状态</td>
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
	$ImgDir="download/cwfkdj/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";

	$checkSheetSql=mysql_query("SELECT 
	A.Id,A.Mid,A.Did,A.TypeId,A.CompanyId,A.Amount,A.Remark,A.Date,A.Estate,A.Locks,A.Operator,P.Forshort,P.Currency,K.PayDate AS dfPayDate,C.Symbol
 	FROM $DataIn.cw2_fkdjsheet A
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	LEFT JOIN $DataIn.cw1_fkoutmain K ON K.Id=A.Did 
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
	
			$Remark=$checkSheetRow["Remark"]==""?"&nbsp;":$checkSheetRow["Remark"];
			$Amount=$checkSheetRow["Amount"];	
			$TypeId=$checkSheetRow["TypeId"];
			$Type=$TypeId==1?"订金":($TypeId==2?"多付平衡帐":"少付平衡帐");
			//查货款结付日期
			$Did=$checkSheetRow["Did"];
			$dfPayDate=$checkSheetRow["dfPayDate"]==""?"&nbsp;":$checkSheetRow["dfPayDate"];
			$Did=$Did>0?"<div class='greenB'>已抵货款</div>":"<div class='redB'>未抵付</div>";
			
			$Operator=$checkSheetRow["Operator"];
			include "../model/subprogram/staffname.php";
			$Date=$checkSheetRow["Date"];
	
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101'>$Forshort</td>";
			echo"<td class='A0101' >$Remark</td>";
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"<td class='A0101'>$Type</td>";
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