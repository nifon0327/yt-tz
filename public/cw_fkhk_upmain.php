<?php 
/*$DataIn.cw2_hkmain
  $DataIn.cw2_hksheet 
  $DataIn.trade_object
  $DataPublic.currencydata
*/
include "../model/modelhead.php";
$upDataMain="$DataIn.cw2_hkmain";
ChangeWtitle("$SubCompany 更新供应商其它扣款结付资料");
//供应商和货币
$checkSymbol=mysql_query("SELECT P.Forshort,C.Symbol 
FROM $DataIn.cw2_hksheet S 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE S.Mid='$Mid' LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkSymbol)) {
	$Forshort=$checkRow["Forshort"];
	$cashSymbol=$checkRow["Symbol"];
	}
include "subprogram/upmain_model.php";
?>