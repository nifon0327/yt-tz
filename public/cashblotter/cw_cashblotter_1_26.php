<?php
//26	模具退回费用	  OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("
SELECT M.Id,M.Mid,M.Moq,M.ItemName ,M.OutAmount ,M.Remark,M.Operator,M.Date,M.Locks,M.Estate,S.Provider,B.Title  
FROM $DataIn.cw16_modelfee  M 
LEFT JOIN cwdyfsheet S  ON M.Mid=S.Id
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
WHERE M.Id='$Id_Remark' 
",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='40' class='A1111'>序号</td>
<td width='120' class='A1101'>结付银行</td>
<td width='300' class='A1101'>模具项目</td>
<td width='100' class='A1101'>退回金额</td>
<td width='40' class='A1101'>供应商</td>
<td width='300' class='A1101'>备注</td>
<td width='40' class='A1101'>凭证</td>
<td width='70' class='A1101'>更新日期</td>
<td width='60' class='A1101'>请款人</td>
</tr>";
$i=1;
if($checkRow=mysql_fetch_array($checkSql)){
	$Dir=anmaIn("download/modelfee/",$SinkOrder,$motherSTR);
	do{
		$Id=$checkRow["Id"];
		$Title =$checkRow["Title"]==""?"&nbsp;":$checkRow["Title"];	
		$ItemName=$checkRow["ItemName"];
		$OutAmount=$checkRow["OutAmount"];
		$Provider =$checkRow["Provider"];	
		$Remark=$checkRow["Remark"]==""?"&nbsp":$checkRow["Remark"];
		$Bill=$checkRow["Bill"];
		$Dir=anmaIn("download/modelfee/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="M".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		$Date=$checkRow["Date"];
		$Operator=$checkRow["Operator"];
		include"../model/subprogram/staffname.php";
 
		//输出首行前段
		echo"<tr><td class='A0101' align='center' height='20'>$i</td>";	
		echo"<td class='A0101' align='center'>$Title</td>";
		echo"<td class='A0101'>$ItemName</td>";
		echo"<td class='A0101'  align='right'>$OutAmount</td>";
		echo"<td class='A0101'>$Provider</td>";
		echo"<td class='A0101'>$Remark</td>";
		echo"<td class='A0101' align='center'>$Bill</td>";
		echo"<td class='A0101' align='center'>$Date</td>";
		echo"<td  class='A0101' align='center'>$Operator</td>";	
		echo"</tr>";
		$i++;
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
?>