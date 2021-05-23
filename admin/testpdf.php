<?php
defined('IN_COMMON') || include '../basic/common.php';

//EUR专用模板  //二合一已更新
$TableFontSize=8;
define('FPDF_FONTPATH','../plugins/fpdf/font/');
include "../plugins/fpdf/pdftable.inc.php";		//更新
$pdf=new PDFTable();
$pdf->SetAutoPageBreak("on",5);		//分页下边距
$pdf->SetMargins(10,10,10);			//上左右边距
$pdf->Open();
$addSign=0; //是否要重新分页,
$PreY=0;   //上一次的位置
$oldPreY=0; //保存的上一次位置，主要是最后一页使用
$MaxContainY=277;  //每一页主内容的最下面的位置
$CurMargin=8;  //左边距
			$CurX=10;	//116
			$NexPY=20;
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',18); //设回字体大小 modify by zx 2011-0129  ///////////////////////////////$pdf->SetFont('Arial','B',22);
			$pdf->SetTextColor(255,0,0);
			$pdf->Text($CurMargin+3,13,"1111111sdsdsdsd111111111111111");
			$eurSHIPTO="<table border=0 >
			<tr><td width=16 align=left style=bold valign=middle >SHIP TO:</td><td width=76   align=left height=10 valign=middle >1234 </td><tr>	
			<tr><td width=16 align=left style=bold ></td><td width=76   align=left valign=middle >7890</td><tr>			
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSHIPTO);
$pdf->Output("123.pdf","F");

?>