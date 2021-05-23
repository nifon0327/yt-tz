<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw2_fkdjmain
$DataIn.cw2_fkdjsheet 
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
$upDataMain="$DataIn.cw2_fkdjmain";
ChangeWtitle("$SubCompany 更新预付订金结付资料");
//供应商和货币
$checkSymbol=mysql_query("SELECT P.Forshort,C.Symbol 
FROM $DataIn.cw2_fkdjsheet S 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE S.Mid='$Mid' LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkSymbol)) {
	$Forshort=$checkRow["Forshort"];
	$cashSymbol=$checkRow["Symbol"];
	}
include "subprogram/upmain_model.php";
?>