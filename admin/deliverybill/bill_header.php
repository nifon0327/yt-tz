<?php   
		$CurH=40;  //当前高度
		$pdf->SetFillColor(180,180,180);
		$pdf->Setxy(6,6);
		$pdf->SetFont('uGB','B',22); 
		$pdf->SetTextColor(255,0,0);
		$pdf->Text($CurMargin+3,18,"$S_Company");  
		$pdf->Rect(130,5,73,18,"F");  //Order框 15
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('uGB','B',16); 	
		$pdf->Text(133,13,"$Com_Pack_PI"); 
		$pdf->SetTextColor(0,0,255);
		$pdf->SetFont('uGB','B',13); //设定Order的字体大小		
		
		{					
			$pdf->SetXY(130,18);   //SHIP TO
			$eurSInvoice="<table border=0 >
			<tr><td width=73 align=right color=#FFFFFF  valign=middle style=bold >$Invoice_PI</td><tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSInvoice); 	
		}			

		$pdf->SetFont('uGB','B',$TableFontSize+1); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		$pdf->Text($CurMargin+2,28,"$S_Address $S_ZIP");  
		$pdf->Text($CurMargin+2,33,"电话:    $S_Tel    传真:    $S_Fax     网址:   $S_WebSite  日期:    $Today");	
		
	 

?>