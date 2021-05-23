<?php
defined('IN_COMMON') || include '../basic/common.php';

//电信-zxq 2012-08-01
/* 只英文模板
$DataIn.yw4_quotationsheet
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.companyinfo
$DataPublic.staffmain
二合一已更新
*/
$mainResult = mysql_query("SELECT Q.Number,Q.ProductCode,Q.Price,Q.Rate,Q.Moq,Q.Priceterm, 
Q.Paymentterm,Q.Leadtime,Q.Remark,Q.Image1,Q.Image2,Q.Image3,Q.Model,
P.Nickname AS Sales,Q.Date,C.Forshort,I.Company,I.Tel,I.Fax,I.Address,U.Symbol,P.Mail
FROM $DataIn.yw4_quotationsheet Q
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=Q.CompanyId 
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=1
LEFT JOIN $DataPublic.staffmain P ON P.Number=Q.Sales 
WHERE Q.Id=$Id LIMIT 1",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$Number=$mainRows["Number"];
	$ProductCode=$mainRows["ProductCode"];
	$CompanyId=$mainRows["CompanyId"];
	$Forshort=$mainRows["Forshort"];
	$Company=$mainRows["Company"];
	$Tel=$mainRows["Tel"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$Symbol=$mainRows["Symbol"]=="USD"?"U.S.DOLLARS":$mainRows["Symbol"];
	$Image1temp=$mainRows["Image1"];
	$Image2temp=$mainRows["Image2"];
	$Image3temp=$mainRows["Image3"];
	$ProductCode=$mainRows["ProductCode"];
	$Price=$mainRows["Price"];
	$Symbol=$mainRows["Symbol"];
	$Rate=$mainRows["Rate"];
	$Priceterm=$mainRows["Priceterm"];
	$Moq=$mainRows["Moq"];
	$Leadtime=$mainRows["Leadtime"];
	$Paymentterm=$mainRows["Paymentterm"];
	$Date=date("d-M-y",strtotime($mainRows["Date"]));
	$Remrak=$mainRows["Remark"];
	$Sales=$mainRows["Sales"];
	$Mail=$mainRows["Mail"];
	$ApprovedBy=$mainRows["ApprovedBy"]==0?"Approved by: FRED CHEN (fredchentw@hotmail.com)":"&nbsp;";
	$ModelTemp=$mainRows["Model"];
	}
//
//扣款单另行处理
$RowsHight=8;			//表格行高
$InvoiceHeadFontSize=9;	//头文字字体大小
$TableFontSize=10;		//表格字体大小
include "../model/subprogram/mycompany_info.php";

$TableList3="
<table >
	<tr><td width=95 valign=middle height=$RowsHight border=0></td><td width=95 valign=middle align=right border=0>NO.$Number</td></tr>
	<tr><td valign=middle height=$RowsHight bgcolor=#CCCCCC>FROM: $E_Company</td><td valign=middle bgcolor=#CCCCCC>TO: $Company</td></tr>
	<tr><td height=15 bgcolor=#CCCCCC>Address: 7F,chen tian dongfang dasha,bao-ming rd,xixiang,baoan,shenzhen,china</td><td bgcolor=#CCCCCC>Address: $Address</td></tr>
	<tr><td colspan=2 border=0></td></tr>
</table>
<table border=1>
	<tr><td width=120 valign=middle  rowspan='2'></td><td width=70 valign=middle height=70></td></tr>
	<tr><td height=70></td></tr>
	<tr><td colspan=2 border=0></td></tr>
</table>
<table border=1>
	<tr><td width=190 valign=middle height=$RowsHight colspan=2>Product Code: $ProductCode</td></tr>
	<tr><td width=95 valign=middle height=$RowsHight>Unit Price: $Price $Smbol</td><td width=95 valign=middle>Exchange Rate: $Rate RMB= 1 USD</td></tr>
	<tr><td height=$RowsHight valign=middle>Price term: $Priceterm</td><td rowspan=5 valign=top>Remark: $Remark</td></tr>
	<tr><td height=$RowsHight valign=middle>MOQ:$Moq</td></tr>
	<tr><td height=$RowsHight valign=middle>Lead Time: $Leadtime</td></tr>
	<tr><td height=$RowsHight valign=middle>Payment term: $Paymentterm</td></tr>
	<tr><td height=$RowsHight valign=middle>Date: $Date</td></tr>
	<tr><td height=$RowsHight valign=middle bgcolor=#CCCCCC>Sales assistant: $Sales ($Mail)</td><td valign=middle bgcolor=#CCCCCC>$ApprovedBy</td></tr>
</table>";
//输出Quotation sheet
$filename="../download/quotation/$Number.pdf";
if(file_exists($filename)){unlink($filename);}
define('FPDF_FONTPATH','../plugins/fpdf/font/');
if($ModelTemp==0){
	include "../plugins/fpdf/pdftable.inc.php";
	$pdf=new PDFTable();
	}
else{
	require('../plugins/fpdf/chinese-unicode.php');
	$pdf=new PDF_Unicode();
	}
$pdf->SetAutoPageBreak("on",10);
$pdf->SetMargins(10,10,10);
$pdf->Open();
$pdf->AddPage();
$pdf->FloorSign=0;
$pdf->SetFont('Arial','',25);
$pdf->Cell(0,20,"Quotation Sheet",0,1,"C");
if($Image1temp==1){
	$ImageFile1="../download/quotation/$Number-01.jpg";
	$pdf->Image($ImageFile1,12,67,116,136,"JPG");
	}
if($Image2temp==1){
	$ImageFile2="../download/quotation/$Number-02.jpg";
	$pdf->Image($ImageFile2,132,67,66,66,"JPG");
	}
if($Image3temp==1){
	$ImageFile3="../download/quotation/$Number-03.jpg";
	$pdf->Image($ImageFile3,132,138,66,66,"JPG");
	}
if($ModelTemp==0){
	//画线
	$pdf->Line(72,24,140,24);
	$pdf->Line(72,25,140,25);
	$pdf->SetFont('Arial','B',$TableFontSize);
	}
else{
	$pdf->AddUniGBhwFont('uGB');
	$pdf->SetFont('uGB','',$TableFontSize); //中文
	}
$pdf->htmltable($TableList3);
//画矩形
$pdf->Setxy(10,38);
$pdf->Cell(190,23,'',1,1,'C');

$pdf->Output("$filename","F");
$Log.="<br>The quotation Sheet has completed!";
?>
