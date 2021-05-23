<?php 
$pdf->SetFont('Arial','B',10);
	$pdf->Cell(140,6,"Total",1,0,"L");
	$pdf->Cell(15,6,$SUMQty,1,0,"R");
	$pdf->Cell(15,6,"",1,0,"R");
	$pdf->Cell(20,6,$Total,1,1,"R");


$pdf->SetFont('Arial','',10);
$pdf->Cell(0,2,"",0,1,"L");
$pdf->Cell(0,5,"Beneficary: CHEN KUNG YI",0,1,"L");
$pdf->Cell(0,5,"Bank         : The HongKong and Shanghai Banking Corporation Limited",0,1,"L");
$pdf->Cell(0,5,"Bank Add  : no.238 Nathan Road, Kowloon, Hong Kong",0,1,"L");
$pdf->Cell(0,5,"Swift ID     : HSBCHKHHHKH",0,1,"L");
$pdf->Cell(0,5,"A/C NO     : 127-313716-833",0,1,"L");

?>