<?php
//8	预收客户货款						OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("
SELECT S.Id,S.Mid,S.CompanyId,S.Amount,S.Remark,S.PayDate,S.Locks,S.Operator,P.Forshort,C.Symbol,C.Rate,B.Title,S.Attached
FROM $DataIn.cw6_advancesreceived S 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=S.BankId
WHERE S.Id='$Id_Remark' order by S.PayDate DESC

",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='40' class='A1111'>序号</td>
<td width='80' class='A1101'>客户</td>
<td width='300' class='A1101'>预收说明</td>
<td width='100' class='A1101'>预收金额</td>
<td width='40' class='A1101'>币别</td>
<td width='120' class='A1101'>结付银行</td>
<td width='100' class='A1101'>转RMB</td>
<td width='40' class='A1101'>凭证</td>
<td width='60' class='A1101'>抵付状态</td>
<td width='70' class='A1101'>收款日期</td>
<td width='60' class='A1101'>操作员</td>
</tr>";
$i=1;
if($checkRow=mysql_fetch_array($checkSql)){
	$d1=anmaIn("download/cwadvance/",$SinkOrder,$motherSTR);
	do{
		$Id=$checkRow["Id"];
		$Forshort=$checkRow["Forshort"];
		$Remark=$checkRow["Remark"]==""?"&nbsp":$checkRow["Remark"];
		$Symbol=$checkRow["Symbol"];
		$Amount=$checkRow["Amount"];
		$BankName=$checkRow["Title"];
		$Rate=$checkRow["Rate"];
		$RmbAmount=sprintf("%.2f",$Amount*$Rate);
		$Operator=$checkRow["Operator"];
		include "../model/subprogram/staffname.php";
		$PayDate=$checkRow["PayDate"];
		$Locks=$checkRow["Locks"];		
		$Mid=$checkRow["Mid"];			
		if($Mid==0){
			$Estate="<div align='center' class='redB' title='未抵付'>×</div>";
			$LockRemark="";
			}
		else{
			$Estate="<div align='center' class='greenB' title='已抵付'>√</div>";
			$LockRemark="记录已经抵付，强制锁定！";
			$Locks=0;
			}
        $Attached=$checkRow["Attached"];	
        if($Attached!=""){
	        $f1=anmaIn($Attached,$SinkOrder,$motherSTR);
		    $PictureView="<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\">view</a>";  
          }
        else $PictureView="&nbsp;";
		//输出首行前段
		echo"<tr><td class='A0101' align='center' height='20'>$i</td>";	
		echo"<td class='A0101' align='center'>$Forshort</td>";
		echo"<td class='A0101'>$Remark</td>";
		echo"<td class='A0101'>$Amount</td>";
		echo"<td class='A0101'>$Symbol</td>";
		echo"<td class='A0101'  align='center'>$BankName</td>";
		echo"<td class='A0101' align='center'>$RmbAmount</td>";
		echo"<td class='A0101' align='right'>$PictureView</td>";
		echo"<td class='A0101' align='center'>$Estate</td>";
		echo"<td  class='A0101' align='center'>$PayDate</td>";
		echo"<td  class='A0101' align='center'>$Operator</td>";	
		echo"</tr>";
		$i++;
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
?>