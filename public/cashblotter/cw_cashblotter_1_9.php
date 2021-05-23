<?php
//9 开发费用						OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cwdyfmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='80' class='A1101'>项目ID</td>
<td width='100' class='A1101'>费用分类</td>
<td width='120' class='A1101'>供应商</td>
<td width='300' class='A1101'>请款说明</td>
<td width='40' class='A1101'>凭证</td>
<td width='40' class='A1101'>备注</td>
<td width='60' class='A1101'>请款金额</td>
<td width='40' class='A1101'>币别</td>
<td width='60' class='A1101'>请款人</td>
<td width='70' class='A1101'>请款日期</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$d1=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/cwdyf/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT 
							   S.Id,S.Mid,S.ItemId,K.Name as KName,S.Date,S.Amount,C.Name as CName,S.ModelDetail,S.Description,S.Remark,S.Provider,S.Bill,S.Estate,S.Locks,S.Operator
 	FROM $DataIn.cwdyfsheet S
	LEFT JOIN $DataPublic.kftypedata K ON K.ID=S.TypeID
	LEFT JOIN $DataPublic.currencydata C ON C.ID=S.Currency
	WHERE S.Mid='$Mid' ORDER BY S.Date DESC
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
			$Id=$checkSheetRow["Id"];
			$ItemId=$checkSheetRow["ItemId"];		
			$KName=$checkSheetRow["KName"];
			$Description=$checkSheetRow["Description"]==""?"&nbsp":$checkSheetRow["Description"];
			$Amount=$checkSheetRow["Amount"];
			
			$CName=$checkSheetRow["CName"];
			$ModelDetail=$checkSheetRow["ModelDetail"]==""?"&nbsp":$checkSheetRow["ModelDetail"];
			
			$Remark=$checkSheetRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkSheetRow[Remark]' width='16' height='16'>";
			$Provider=$checkSheetRow["Provider"];
			$Date=$checkSheetRow["Date"];
			$Locks=$checkSheetRow["Locks"];
			$Operator=$checkSheetRow["Operator"];
			$Bill=$checkSheetRow["Bill"];
			
			if($Bill==1){
				$Bill="DYF".$Id.".jpg";
				$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
				$Bill="<span onClick='OpenOrLoad(\"$d1\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
				}
			else{
				$Bill="&nbsp;";
				}
			include "../model/subprogram/staffname.php";
			$Amount=sprintf("%.2f",$Amount);
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101' align='center'>$ItemId</td>";
			echo"<td class='A0101'>$KName</td>";
			echo"<td class='A0101'>$Provider</td>";
			echo"<td class='A0101'>$Description</td>";
			echo"<td class='A0101'  align='center'>$Bill</td>";
			echo"<td class='A0101' align='center'>$Remark</td>";
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"<td class='A0101' align='center'>$CName</td>";
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