<?php   
$clientResult = mysql_query("SELECT BankId  FROM $DataIn.trade_object WHERE CompanyId=$CompanyId  LIMIT 1",$link_id);  
if($clientRows = mysql_fetch_array($clientResult)){
	 $BankId=$clientRows["BankId"];
}
$BankId=$BankId==""?5:$BankId;
?>