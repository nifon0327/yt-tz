<?php 
//电信-ZX  2012-08-01
//二合一已更新
$checkMyCompany=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.my1_companyinfo WHERE TYPE='S' AND cSign =7 LIMIT 1",$link_id));
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

$checkECompany=mysql_fetch_array(mysql_query("SELECT Company FROM $DataIn.my1_companyinfo WHERE TYPE='E' AND cSign=7 LIMIT 1",$link_id));
$eCompany=$checkECompany["Company"];
?>