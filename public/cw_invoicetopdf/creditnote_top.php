<?php 
//电信-zxq 2012-08-01
require('../plugins/fpdf/fpdf.php');
$pdf=new FPDF;
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','',35);
$pdf->Cell(0,20,"CREDIT NOTE",0,1,"C");
//读取客户资料

$ClientSQL = mysql_query("SELECT * FROM $DataIn.trade_object WHERE CompanyId=$CompanyId order by CompanyId LIMIT 1",$link_id);
if($ClientRow = mysql_fetch_array($ClientSQL)){
	$ClientName=$ClientRow["Company"];
	$ClientAdress=$ClientRow["Address"];
	$ClientFax=$ClientRow["Fax"];
	}
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,"Seller         : Middle Cloud Trading Ltd",0,1,"L");
$pdf->Cell(0,5,"Fax            : +86-755-61139585",0,1,"L");
$pdf->Cell(62,5,"Sold To      : $ClientName",0,0,"L");
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell(0,5,"",0,1,"C");
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(0,5,"Address     : $ClientAdress",0,1,"L");
		$pdf->Cell(130,5,"Fax NO      : $ClientFax",0,0,"L");
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell(80,5,"Invoice# : ".$InvoiceNO,0,1,"L");
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(130,5,"",0,0,"L");
		$pdf->Cell(80,5,"Date       :".$Date,0,1,"L");
		
		$pdf->Cell(20,5," ",0,0,"L");
		$pdf->SetTextColor(255,0,0);
		$pdf->Cell(110,5,$Wise,0,0,"C");
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(60,5,"Currency: USD",0,1,"L");
		$pdf->SetFont('Arial','B',10);
		
		$pdf->Cell(15,6,"NO.",1,0,"C");
		$pdf->Cell(25,6,"PO#.",1,0,"C");
		$pdf->Cell(100,6,"Product Description",1,0,"C");
		$pdf->Cell(15,6,"Q'ty",1,0,"C");
		$pdf->Cell(15,6,"Unit",1,0,"C");
		$pdf->Cell(20,6,"Amount",1,1,"C");
		$pdf->SetFont('Arial','',10);
?>