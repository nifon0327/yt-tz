<?php 
//$DataIn.电信---yang 20120801
$checkCurrency=mysql_query("SELECT Symbol,Rate FROM $DataPublic.currencydata WHERE Estate=1 AND Id>1 ORDER BY Id",$link_id);
if($checkCurrencyRow=mysql_fetch_array($checkCurrency)){
	echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>
		<td class='A0011'>汇率：";
	do{
		echo $checkCurrencyRow["Symbol"].":".sprintf("%.2f",$checkCurrencyRow["Rate"])."&nbsp;&nbsp;&nbsp;";
		$TempRate=strval($checkCurrencyRow["Symbol"])."Rate"; 
		$$TempRate=$checkCurrencyRow["Rate"];	
		}while ($checkCurrencyRow=mysql_fetch_array($checkCurrency));
	echo" </td></tr></table>";
	}
?>