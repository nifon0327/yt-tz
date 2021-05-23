<?php   

		$CurH=21;  //当前高度  modify by zx 2011-0129   $CurH=32;
		$CurH2=20;  //第二个框的高度  add by zx  2011-0129
		$ConTableBegin_Y=$CurH+$CurH2+5+1; //主内容的第一个表的Y位置  add by  by zx 2011-0129 
		$pdf->SetFillColor(180,180,180);
		
		$pdf->Setxy(6,6);
		$pdf->SetFont('Arial','B',18); //设回字体大小 modify by zx 2011-0129  ///////////////////////////////$pdf->SetFont('Arial','B',22);
		$pdf->SetTextColor(255,0,0);
		$pdf->Text($CurMargin+3,13,"$E_Company");  // modify by zx 2011-0129  $pdf->Text($CurMargin+3,18,"$E_Company");  
		
		$pdf->Rect(130,5,73,13,"F");  //Order框 15   modify by zx 2011-0129  $pdf->Rect(130,5,73,18,"FD");
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','B',12); //设定Order的字体大小	 modify by zx 2011-0129  $pdf->SetFont('Arial','B',16);

		$pdf->Text(133,11,"$Com_Pack_PI"); //  modify by zx 2011-0129  $pdf->Text(133,13,"$Com_Pack_PI");  
		$pdf->SetTextColor(0,0,255);
		$pdf->SetFont('Arial','B',10); //设定Order的字体大小	  modify by zx 2011-0129 	$pdf->SetFont('Arial','B',13);
		
		{
						
			$pdf->SetXY(130,13);   //SHIP TO   modify by zx 2011-0129  $pdf->SetXY(130,18);
			
			$eurSInvoice="<table border=0 >
			<tr><td width=73 align=right color=#FFFFFF  valign=middle style=bold >$Invoice_PI</td><tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSInvoice);  //<tr><td width=8 valign=middle>Fax:</td><td width=80   align=left valign=middle >$FaxNo</td><tr>
		
		}			


		$pdf->SetFont('Arial','',$TableFontSize); //设回字体大小  //modify by zx 2011-0129  $pdf->SetFont('Arial','',$TableFontSize+1); 
		$pdf->SetTextColor(0,0,0);
		$pdf->Text($CurMargin+2,20,"$E_Address $E_ZIP");  //  /modify by zx 2011-0129    $pdf->Text($CurMargin+2,29,"$E_Address $E_ZIP");    7F,Chen Tian Dongfang Dasha
		
		$pdf->Text($CurMargin+2,24,"Tel:    $E_Tel    Fax:    $E_Fax     URL:   $E_WebSite     FSC NO:");	//  /modify by zx 2011-0129$pdf->Text($CurMargin
		$pdf->Rect($CurMargin,$CurH+5,195,$CurH2,"D"); //第二个框  第三个框modify by zx 2011-0129  ///////////////////////////////
		
		$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
		$NexPY=30;    //modify by zx 2011-0129    $NexPY=41
		//$pdf->Text(7,$NexPY,"TO:");
		$pdf->SetFont('Arial','',$TableFontSize); //设回字体大小
		{
			$CurX=$CurMargin+1;  //15;			
			$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
			$eurSTO="<table border=0 >
			<tr><td width=16 align=left  valign=middle style=bold ></td><td width=76   align=left height=$RowsHight valign=middle ></td><tr>	
			<tr><td width=16></td><td width=76   align=left valign=middle ></td><tr>	
			
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSTO); 
		
		}			
		
		$pdf->Rect(103,$CurH+5,100,$CurH2,"D"); 
		
		$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
		$NexPY=30;    //modify by zx 2011-0129    $NexPY=41
		$pdf->SetFont('Arial','',TableFontSize); //设回字体大小

		{
			$CurX=105;	//116		
			$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
			$eurSHIPTO="<table border=0 >
			<tr><td width=16 align=left style=bold valign=middle ></td><td width=76   align=left height=$RowsHight valign=middle ></td><tr>	
			<tr><td width=16 align=left style=bold ></td><td width=76   align=left valign=middle ></td><tr>			
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSHIPTO);
		
		}

		$sealX=$CurMargin+1;
        $sealY=26;
        $ozkai_sold="../images/".$CompanyId."_sold.png";
        $ozkai_ship="../images/".$CompanyId."_ship.png";
        $pdf->Image($ozkai_sold,$sealX,$sealY,95,21,"png");	
	    $pdf->Image($ozkai_ship,$sealX+98,$sealY,95,15,"png");	

?>