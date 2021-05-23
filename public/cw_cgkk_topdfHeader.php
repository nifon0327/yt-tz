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
		 
		/*if($Com_Pack_PI=="DebitNote"){
		    $tmp_CX=130;
		    $tmp_CY=11;
			$pdf->SetTextColor(0,0,0);
		    $pdf->SetFont('uGB','B',14);	
		    $pdf->Text($tmp_CX,$tmp_CY,"D");
		    $pdf->Text($tmp_CX+4,$tmp_CY,"e");
		    $pdf->Text($tmp_CX+8,$tmp_CY,"i");
		    $pdf->Text($tmp_CX+10,$tmp_CY,"t");
			$pdf->Text($tmp_CX+13,$tmp_CY,"N");
			$pdf->Text($tmp_CX+17,$tmp_CY,"o");
			$pdf->Text($tmp_CX+21,$tmp_CY,"t");
			$pdf->Text($tmp_CX+23,$tmp_CY,"e");

		  }*/
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('uGB','B',12); //设定Order的字体大小 $pdf->SetFont('Arial','B',16);
		$pdf->Text(133,10,"$Com_Pack_PI"); 
		$pdf->SetTextColor(0,0,255);
		
		$pdf->SetFont('uGB','B',10); //设定Order的字体大小 	$pdf->SetFont('Arial','B',13);
		{				
			$pdf->SetXY(130,13);   
			$eurSInvoice="<table border=0 >
			<tr><td width=73 align=right color=#FFFFFF  valign=middle style=bold >$BillNumber</td><tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSInvoice);  
		}			


		$pdf->SetFont('uGB','',$TableFontSize); //设回字体大小  $pdf->SetFont('Arial','',$TableFontSize+1); 
		$pdf->SetTextColor(0,0,0);
		$pdf->Text($CurMargin+2,20,"$S_Address $S_ZIP");  
		$pdf->Text($CurMargin+2,24,"电话:    $S_Tel    传真:    $S_Fax     网址:   $S_WebSite");	//  
		/*if(1){
		    $tmp_CX=101.5;
		    $tmp_CY=24;
			$pdf->Text($tmp_CX+2,$tmp_CY,"w");
		    $pdf->Text($tmp_CX+4.5,$tmp_CY,"w");
			$pdf->Text($tmp_CX+6.5,$tmp_CY,"w");
			$pdf->Text($tmp_CX+8.5,$tmp_CY,".");
			$pdf->Text($tmp_CX+10,$tmp_CY,"m");
			$pdf->Text($tmp_CX+13,$tmp_CY,"i");
			$pdf->Text($tmp_CX+14,$tmp_CY,"d");
			$pdf->Text($tmp_CX+16,$tmp_CY,"d");
			$pdf->Text($tmp_CX+18,$tmp_CY,"l");
			$pdf->Text($tmp_CX+19,$tmp_CY,"e");
			$pdf->Text($tmp_CX+21,$tmp_CY,"c");
			$pdf->Text($tmp_CX+23,$tmp_CY,"l");
			$pdf->Text($tmp_CX+24,$tmp_CY,"o");
			$pdf->Text($tmp_CX+26,$tmp_CY,"u");
			$pdf->Text($tmp_CX+28,$tmp_CY,"d");
			$pdf->Text($tmp_CX+30,$tmp_CY,".");
			$pdf->Text($tmp_CX+31,$tmp_CY,"c");
			$pdf->Text($tmp_CX+33,$tmp_CY,"o");
			$pdf->Text($tmp_CX+35,$tmp_CY,"m");
		    }*/
		
		

?>