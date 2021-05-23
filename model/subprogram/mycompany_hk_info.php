<?php
//上海公司信息
$E_Tel     = "+852-2408 2870";
$E_Fax     = "+852-2408 2870";
$E_mail    = "sale@ashcloud.com";
$E_WebSite = "www.ashcloud.com";
$E_Address = "Room 05, 14F, Lucida Industrial Bldg.,43-47 Wang Lung Street, Tsuen Wan, NT,Hong Kong";
$E_ZIP     = "";

$Company =  "Offwire DBA Brightstar";
$Address = "13573 lynam Drive, 68138, Omaha,Ne.";

$bankResult = mysql_query("SELECT B.Beneficary,B.Bank,B.BankAdd,B.SwiftID,B.ACNO FROM my2_bankinfo B WHERE Id='5' ",$link_id);
if($bankRow = mysql_fetch_array($bankResult)){
	    $Beneficary=$bankRow["Beneficary"];
		$Bank=$bankRow["Bank"];
		$BankAdd=$bankRow["BankAdd"];
		$SwiftID=$bankRow["SwiftID"];
		$ACNO=$bankRow["ACNO"];
}

?>