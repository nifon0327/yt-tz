<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
$upDataMain="$DataIn.nonbom11_djmain";
ChangeWtitle("$SubCompany 更新非BOM采购预付订金结付资料");
//供应商和货币
$checkSymbol=mysql_query("SELECT B.Forshort,C.Symbol 
FROM $DataIn.nonbom11_djsheet A 
LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=A.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency WHERE A.Mid='$Mid' LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkSymbol)) {
	$Forshort=$checkRow["Forshort"];
	$cashSymbol=$checkRow["Symbol"];
	}
include "subprogram/upmain_model.php";
?>