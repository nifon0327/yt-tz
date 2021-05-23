<?php
defined('IN_COMMON') || include '../basic/common.php';

//电信-zxq 2012-08-01
$RowsHight=5;			//表格行高
$InvoiceHeadFontSize=9;	//头文字字体大小
$TableFontSize=8;		//表格字体大小
//公司信息
$filename="../download/test.pdf";
if(file_exists($filename)){unlink($filename);}
$TableFontSize=8;
define('FPDF_FONTPATH','../plugins/fpdf/font/');
include "../plugins/fpdf/pdftable.inc.php";		//更新
$pdf=new PDFTable();
$pdf->SetAutoPageBreak("on",10);		//分页下边距
$pdf->SetMargins(10,10,10);			//上左右边距
$pdf->Open();
$pdf->AddPage();
$pdf->FloorSign=0;
$pdf->SetFont('Arial','',35);
$pdf->Cell(0,20,"Invoice",0,1,"C");
$pdf->Setxy(10,25);
$pdf->SetFont('Arial','',$InvoiceHeadFontSize);
	$pdf->Cell(0,$RowsHight,"Seller        :",0,1,"L");
	$pdf->Cell(0,$RowsHight,"Fax           :",0,1,"L");
	$pdf->Cell(62,$RowsHight,"Sold To     : $",0,0,"L");
$pdf->Output("$filename","D");
?>
