<?php
defined('IN_COMMON') || include '../basic/common.php';

//电信-zxq 2012-08-01
include "../model/modelhead.php";
$RowsHight=5;			//表格行高
$InvoiceHeadFontSize=8;	//头文字字体大小
$TableFontSize=8;		//表格字体大小
$FromFunPos="CH";
//公司信息//
$CompanyId="1075";
$mySql="SELECT P.Forshort,P.ExpNum,F.Tel,F.Fax,L.Mobile,C.Symbol,F.Company,F.Address,L.Name
FROM $DataIn.trade_object P
LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId AND F.Type=1
LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId AND L.Defaults=0 AND L.Type=1
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE P.CompanyId='$CompanyId'";
$mainResult = mysql_query($mySql,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$Forshort=$mainRows["Forshort"];
    $ExpNum=$mainRows["ExpNum"];
	$Company=$mainRows["Company"];
	$Address=$mainRows["Address"];
	$LinkName=$mainRows["Name"];
	$Tel=$mainRows["Tel"];
	$Fax=$mainRows["Fax"];
	$Mobile=$mainRows["Mobile"];
	$Symbol=$mainRows["Symbol"];
	}
$BankSql=mysql_query("SELECT * FROM $DataPublic.my2_bankinfo WHERE Id='5'");
if($BankRow=mysql_fetch_array($BankSql)){
   $Beneficary=$BankRow["Beneficary"];
   $Bank=$BankRow["Bank"];
   $BankAdd=$BankRow["BankAdd"];
   $SwiftID=$BankRow["SwiftID"];
   $ACNO=$BankRow["ACNO"];
  }

include "../model/subprogram/mycompany_info.php";   //公司信息

$filename="../download/clientmodel/$CompanyId.pdf";
if(file_exists($filename)){unlink($filename);}
include "clientmodel_invoicemodel.php";
$pdf->Output("$filename","F");

?>
