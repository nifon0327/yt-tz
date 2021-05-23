<?php 
//货币汇率
//电信-EWEN
//代码、数据库共享-EWEN
$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE 1 AND Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."Rate";
		$$TempRate=sprintf("%.4f",$rateRow["Rate"]);
		}while($rateRow = mysql_fetch_array($rateResult));
	}
?>