<?php 

if ($tmp_str=="COMMERCIAL INVOICE"){ 
		$tmp_CX=133;
		$tmp_CY=13;
		$pdf->SetFont('Arial','B',16); //设定Order的字体大小		
		$pdf->Text($tmp_CX-0.5,$tmp_CY,"C");
		$pdf->Text($tmp_CX+3.5,$tmp_CY,"O");
		$pdf->Text($tmp_CX+8,$tmp_CY,"M");
		$pdf->Text($tmp_CX+13,$tmp_CY,"M");
		$pdf->Text($tmp_CX+18,$tmp_CY,"E");		
		$pdf->Text($tmp_CX+22,$tmp_CY,"R");
		$pdf->Text($tmp_CX+26,$tmp_CY,"C");
		$pdf->Text($tmp_CX+30,$tmp_CY,"I");
		$pdf->Text($tmp_CX+32,$tmp_CY,"A");
		$pdf->Text($tmp_CX+36,$tmp_CY,"L");
		$pdf->Text($tmp_CX+41,$tmp_CY,"I");
		$pdf->Text($tmp_CX+43,$tmp_CY,"N");		
		$pdf->Text($tmp_CX+47,$tmp_CY,"V");
		$pdf->Text($tmp_CX+51,$tmp_CY,"O");		
		$pdf->Text($tmp_CX+55.5,$tmp_CY,"I");
		$pdf->Text($tmp_CX+57,$tmp_CY,"C");
		$pdf->Text($tmp_CX+61,$tmp_CY,"E");		
		
}

if ($tmp_str=="CREDIT NOTE"){ 
		$tmp_CX=133;
		$tmp_CY=13;
		$pdf->SetFont('Arial','B',16); //设定Order的字体大小		
		$pdf->Text($tmp_CX-0.5,$tmp_CY,"C");
		$pdf->Text($tmp_CX+3.5,$tmp_CY,"R");
		$pdf->Text($tmp_CX+8,$tmp_CY,"E");
		$pdf->Text($tmp_CX+12,$tmp_CY,"D");
		$pdf->Text($tmp_CX+16.5,$tmp_CY,"I");		
		$pdf->Text($tmp_CX+18.5,$tmp_CY,"T");
		$pdf->Text($tmp_CX+26,$tmp_CY,"N");
		$pdf->Text($tmp_CX+30.5,$tmp_CY,"O");
		$pdf->Text($tmp_CX+35,$tmp_CY,"T");
		$pdf->Text($tmp_CX+39,$tmp_CY,"E");


}


if ($tmp_str=="DEBIT NOTE"){ 
		$tmp_CX=133;
		$tmp_CY=13;
		$pdf->SetFont('Arial','B',16); //设定Order的字体大小		
		$pdf->Text($tmp_CX-0.5,$tmp_CY,"D");
		$pdf->Text($tmp_CX+3.5,$tmp_CY,"E");
		$pdf->Text($tmp_CX+8,$tmp_CY,"B");
		$pdf->Text($tmp_CX+12.5,$tmp_CY,"I");		
		$pdf->Text($tmp_CX+14.5,$tmp_CY,"T");
		$pdf->Text($tmp_CX+22,$tmp_CY,"N");
		$pdf->Text($tmp_CX+26.5,$tmp_CY,"O");
		$pdf->Text($tmp_CX+31,$tmp_CY,"T");
		$pdf->Text($tmp_CX+35,$tmp_CY,"E");


}

if ($tmp_str=="Packing List"){ 
		$tmp_CX=133;
		$tmp_CY=13;
		$pdf->SetFont('Arial','B',16); //设定Order的字体大小		
		$pdf->Text($tmp_CX-0.5,$tmp_CY,"P");
		$pdf->Text($tmp_CX+3.5,$tmp_CY,"a");
		$pdf->Text($tmp_CX+7,$tmp_CY,"c");
		$pdf->Text($tmp_CX+10.5,$tmp_CY,"k");
		$pdf->Text($tmp_CX+14,$tmp_CY,"i");		
		$pdf->Text($tmp_CX+16,$tmp_CY,"n");
		$pdf->Text($tmp_CX+19.5,$tmp_CY,"g");
		$pdf->Text($tmp_CX+24,$tmp_CY,"L");
		$pdf->Text($tmp_CX+28,$tmp_CY,"i");
		$pdf->Text($tmp_CX+30,$tmp_CY,"s");
		$pdf->Text($tmp_CX+33.5,$tmp_CY,"t");		
		
}

if ($tmp_str=="SHIP TO:"){ 
	    $tmp_CX=$CurX-0.5;
		$tmp_CY=$NexPY;
		$pdf->Text($tmp_CX+0.3,$tmp_CY,"S");
		$pdf->Text($tmp_CX+2.5,$tmp_CY,"H");
		$pdf->Text($tmp_CX+4.5,$tmp_CY,"I");
		$pdf->Text($tmp_CX+5.5,$tmp_CY,"P");
		$pdf->Text($tmp_CX+8,$tmp_CY,"T");
		$pdf->Text($tmp_CX+9.8,$tmp_CY,"O");
		$pdf->Text($tmp_CX+12.4,$tmp_CY,":");
			
		
}

switch($tmp_str){
	case "To:" : 
	    $tmp_CX=$CurX-0.5;
		$tmp_CY=$NexPY;
		$pdf->Text($tmp_CX+0.3,$tmp_CY,"T");
		$pdf->Text($tmp_CX+2,$tmp_CY,"o");
		$pdf->Text($tmp_CX+4,$tmp_CY,":");
		
		break;
	
	case "BANK:" : 
		$tmp_len=strlen($tmp_str);
		for($tmp_i=0; $tmp_i<$tmp_len;$tmp_i++){
			$pdf->Cell(2.1,0,substr($tmp_str,$tmp_i,1),0,0,C);
		}
	break;	
	case "Notes:" : 
		$pdf->Text($CurMargin+0.3,$NowSY+3.5,"N");
		$pdf->Text($CurMargin+2.5,$NowSY+3.5,"o");
		$pdf->Text($CurMargin+4.5,$NowSY+3.5,"t");
		$pdf->Text($CurMargin+5.6,$NowSY+3.5,"e");
		$pdf->Text($CurMargin+7.3,$NowSY+3.5,"s");
		$pdf->Text($CurMargin+9,$NowSY+3.5,":");
		
		break;		
	case "Terms:" : 
		$pdf->Text($CurMargin+0.3,$NowSY+3.5,"T");
		$pdf->Text($CurMargin+2.3,$NowSY+3.5,"e");
		$pdf->Text($CurMargin+4.1,$NowSY+3.5,"r");
		$pdf->Text($CurMargin+5.3,$NowSY+3.5,"m");
		$pdf->Text($CurMargin+8.1,$NowSY+3.5,"s");
		$pdf->Text($CurMargin+9.8,$NowSY+3.5,":");
		
		break;		
	/*
	default :
		$tmp_len=strlen($tmp_str);
		for($tmp_i=0; $tmp_i<$tmp_len;$tmp_i++){
			$tmp_char=substr($tmp_str,$tmp_i,1);
			if($tmp_char>="A" && $tmp_char<="Z"){
				$pdf->Cell(2,0,$tmp_char,0,0,C);
			}
			else{
				$pdf->Cell(1.7,0,$tmp_char,0,0,C);
			}
			
		}
	*/	
	beak;
}
		
	 

?>