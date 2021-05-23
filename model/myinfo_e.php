<?php 
//电信-ZX  2012-08-01
//$DataIn.my1_companyinfo 二合一已更新
$checkMyCompany=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.my1_companyinfo WHERE cSign =7 ORDER BY Id DESC LIMIT 1",$link_id));
$myCompany=$checkMyCompany["Company"];
$myForshort=$checkMyCompany["Forshort"];
$myTel=$checkMyCompany["Tel"];
$myFax=$checkMyCompany["Fax"];
$myAddress=$checkMyCompany["Address"];
$myZIP=$checkMyCompany["ZIP"];
$myWebSite=$checkMyCompany["WebSite"];
$myLinkMan=$checkMyCompany["LinkMan"];
$myMobile=$checkMyCompany["Mobile"];
$myEmail=$checkMyCompany["Email"];
?>