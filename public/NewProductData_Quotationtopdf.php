<?php
defined('IN_COMMON') || include '../basic/common.php';
//电信-ZX  2012-08-01
/*
$DataIn.productdata
$DataIn.trade_object
$DataIn.ch8_shipmodel
$DataIn.pands
$DataIn.stuffdata
$DataPublic.staffmain
已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<link rel='stylesheet' href='../model/css/sharing.css'>";
//读取客户，产品ID，产品中文名，产品英文名，包装方式
$myRow=mysql_fetch_array(mysql_query("
SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.CompanyId,P.Description,P.Remark,P.pRemark,
	P.TestStandard,P.Img_H,P.Img_L,P.Date,P.PackingUnit,P.Estate,P.Locks,P.Code,P.Operator,T.TypeName,C.Forshort,D.Rate,D.Symbol
	FROM $DataIn.newproductdata P
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	where  P.Id=$Id",$link_id));

$ProductId=$myRow["ProductId"];
$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
$Price=$myRow["Price"];
$Moq=$myRow["Moq"]==0?"&nbsp;":$myRow["Moq"];
$TestStandard=$myRow["TestStandard"];
//$JPGFile ="../download/teststandard/T".$ProductId.".jpg";

$filename="Quotation_model.pdf";
if(file_exists($filename)){unlink($filename);}
define('FPDF_FONTPATH','../plugins/fpdf/font/');
require('../plugins/fpdf/chinese-unicode.php');
$pdf=new PDF_Unicode();
$pdf->SetAutoPageBreak("on",10);
$pdf->SetMargins(10,10,10);
$pdf->Open();
$pdf->AddPage();
$pdf->FloorSign=0;
$pdf->SetFillColor(255,0,51);
$pdf->SetDrawColor(255,0,51);
//$pdf->Setxy(10,25);
$pdf->Setxy(10,25);


$pdf->AddUniGBhwFont('uGB');
$pdf->SetFont('uGB','B',20);
//$Title="检 验 标 准 图";
$Title="Quotation";

//$pdf->Rect(82,5,51,11,"F"); //标题底色
$TitleL=$pdf->GetStringWidth($Title);
//$pdf->SetTextColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->Text(80,13,$Title);
$Date=date("d-M-y");
//$Date=date("d-M-y",strtotime($Date));
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('uGB','',8);
//$pdf->Text(160,20,"制图: $StaffName");
//$pdf->Text(182,20,"日期：$Date");


$pdf->Text(13,25,"7F,Chen Tian Dongfang Dasha,Bao-Ming 2Rd,XiXiang,Baoan,Shenzhen,China 518102");
$pdf->Text(13,30,"TEL: +86-755-61139580");
$pdf->Text(13,35,"FAX: +86-755-61139585");

$pdf->Rect(10,40,190,19,""); //标题底色

$pdf->Text(13,45,"To: $To");
$pdf->Text(100,45,"Date: $Date");

$pdf->Text(13,50,"Attn: $Attn");
$pdf->Text(100,50,"PINo: $PINo");

$pdf->Text(13,55,"Address: $Address");
$pdf->Text(100,55,"Co.: $Co");

$pdf->Rect(10,60,190,190,"");

if($TestStandard>0)   //存在图片
{
	$JPGFile ="../download/newproductdata/T".$ProductId.".jpg";
	$pdf->Image("$JPGFile",10,65); //增加一张图片，文件名为sight.jpg
	//$pdf->Image('../images/PDFMCA.jpg',5,100+52,16.8,4.8); //增加一张图片，文件名为sight.jpg
	//$pdf->Image('../download/newproductdata/T10001.jpg',5,100+52,16.8,4.8); //增加一张图片，文件名为sight.jpg

}


$pdf->Rect(10,250,190,24,""); //标题底色

$pdf->Text(13,255,"Product Code: $eCode");
$pdf->Text(100,255,"Exchange Rate: ");

$pdf->Text(13,260,"Unit Price: $Price");
$pdf->Text(13,265,"Price term: ");
$pdf->Text(13,270,"MOQ: $Moq");



$pdf->Output("$filename","F");
$Log="<p><a href='Quotation_model.pdf' target='_blank'>$eCode 的报价单已生成，点击下载</a>";
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
