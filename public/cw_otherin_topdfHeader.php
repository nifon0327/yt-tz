<?php 
//电信-zxq 2012-08-01
		$CurH=21;  //当前高度   $CurH=32;
		$ConTableBegin_Y=$CurH+5+1; //主内容的第一个表的Y位置  
		$pdf->SetFillColor(180,180,180);
		$pdf->Setxy(6,6);
		$pdf->SetFont('uGB','B',22);  //设回字体大小/;
		$pdf->SetTextColor(255,0,0);
		//$pdf->Text($CurMargin+3,17,"Ash Cloud Co.,Ltd. Shenzhen");  
		$pdf->Text($CurMargin+3,13,"$S_Company");  
		$pdf->Rect(130,5,73,13,"F");  
		 

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('uGB','B',12); //设定Order的字体大小 $pdf->SetFont('Arial','B',16);
		$pdf->Text(133,10,"$Com_Pack_PI"); 
		$pdf->SetTextColor(0,0,255);
		
		$pdf->SetFont('uGB','B',10); //设定Order的字体大小 	$pdf->SetFont('Arial','B',13);
		{				
			$pdf->SetXY(130,13);   
			$eurSInvoice="<table border=0 >
			<tr><td width=73 align=right color=#FFFFFF  valign=middle style=bold >$getmoneyNO</td><tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSInvoice);  
		}			


		$pdf->SetFont('uGB','',$TableFontSize); //设回字体大小  $pdf->SetFont('Arial','',$TableFontSize+1); 
		$pdf->SetTextColor(0,0,0);
		$pdf->Text($CurMargin+2,20,"$S_Address $S_ZIP");  
		$pdf->Text($CurMargin+2,24,"电话:    $S_Tel    传真:    $S_Fax     网址:   $S_WebSite");	//  
		

?>