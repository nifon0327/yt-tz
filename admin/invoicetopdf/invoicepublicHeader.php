<?php   

		$CurH=21;  //当前高度  modify by zx 2011-0129   $CurH=32;
		$CurH2=20;  //第二个框的高度  add by zx  2011-0129
		$ConTableBegin_Y=$CurH+$CurH2+5+1; //主内容的第一个表的Y位置  add by  by zx 2011-0129 
		//$pdf->Rect($CurMargin,5,195,$CurH,"D");  //第一框
		$pdf->SetFillColor(180,180,180);
		
		$pdf->Setxy(6,6);
		//$mc_Logo="../images/ASH.jpg";
		//$pdf->Image($mc_Logo,8,15,50,4,"JPG"); 
		$pdf->SetFont('Arial','B',18); //设回字体大小 modify by zx 2011-0129  ///////////////////////////////$pdf->SetFont('Arial','B',22);
		$pdf->SetTextColor(255,0,0);
		//$pdf->Text($CurMargin+3,17,"Ash Cloud Co.,Ltd. Shenzhen");  
		$pdf->Text($CurMargin+3,13,"$E_Company");  // modify by zx 2011-0129  $pdf->Text($CurMargin+3,18,"$E_Company");  
		
	    //$pdf->LineColor(180,180,180);
		$pdf->Rect(130,5,73,13,"F");  //Order框 15   modify by zx 2011-0129  $pdf->Rect(130,5,73,18,"FD");
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','B',12); //设定Order的字体大小	 modify by zx 2011-0129  $pdf->SetFont('Arial','B',16);
		/*
		if($PackList=="Packing List"){
			$pdf->Text(133,15,"Packing List"); 
		}
		else{		
			$pdf->Text(133,15,"COMMERCIAL INVOICE");
		}
		*/
		$pdf->Text(133,11,"$Com_Pack_PI"); //  modify by zx 2011-0129  $pdf->Text(133,13,"$Com_Pack_PI");  
		//$pdf->Rect(130,20,73,8,"D");  //Invoice NO框
		$pdf->SetTextColor(0,0,255);
		$pdf->SetFont('Arial','B',10); //设定Order的字体大小	  modify by zx 2011-0129 	$pdf->SetFont('Arial','B',13);
		//$pdf->Text(133,25,"Invoice NO.:$InvoiceNO"); 
		
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
		
		$pdf->Text($CurMargin+2,24,"Tel:    $E_Tel    Fax:    $E_Fax     URL:   $E_WebSite     FSC NO:");	//  /modify by zx 2011-0129$pdf->Text($CurMargin+2,33,"Tel:    $E_Tel    Fax:    $E_Fax     URL:   $E_WebSite");
		
		//$CurH=20;   // 当前框高度add by zx 2011-0129  /////////////////////////////
		
		$pdf->Rect($CurMargin,$CurH+5,195,$CurH2,"D"); //第二个框  第三个框modify by zx 2011-0129  ///////////////////////////////
		
		$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
		$NexPY=30;    //modify by zx 2011-0129    $NexPY=41
		//$pdf->Text(7,$NexPY,"TO:");
		$pdf->SetFont('Arial','',$TableFontSize); //设回字体大小
		{
			$CurX=$CurMargin+1;  //15;			
			$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
			$eurSTO="<table border=0 >
			<tr><td width=16 align=left  valign=middle style=bold >SOLD TO:</td><td width=76   align=left height=$RowsHight valign=middle >$Company,</td><tr>	
			<tr><td width=16></td><td width=76   align=left valign=middle >$Address.</td><tr>	
			
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSTO);  //<tr><td width=8 valign=middle>Fax:</td><td width=80   align=left valign=middle >$FaxNo</td><tr>
		
		}			
		
		
		$pdf->Rect(103,$CurH+5,100,$CurH2,"D"); //第三个框modify by zx 2011-0129 $pdf->Rect(103,37,100,32,"D"); //第三个框
		
		$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
		$NexPY=30;    //modify by zx 2011-0129    $NexPY=41
		//$pdf->Text(102,$NexPY,"SHIP TO:");
		$pdf->SetFont('Arial','',TableFontSize); //设回字体大小
		//SHIP TO
		//SHIP TO
		{
			$CurX=105;	//116		
			$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
			if ($ShipTo!="" && ($CompanyId==1088 || $CompanyId==1036 || $CompanyId==1046)){
			     $eurSHIPTO="<table border=0 >
				<tr><td width=16 align=left style=bold valign=middle >SHIP TO:</td><td width=76   align=left height=$RowsHight valign=middle >$ShipTo.</td></tr>			
				</table>" ;
           	}
          else{
			$eurSHIPTO="<table border=0 >
			<tr><td width=16 align=left style=bold valign=middle >SHIP TO:</td><td width=76   align=left height=$RowsHight valign=middle >$SoldTo,</td><tr>	
			<tr><td width=16 align=left style=bold ></td><td width=76   align=left valign=middle >$ToAddress.</td><tr>			
			</table>" ;//$eurTableList;
		}
			$pdf->htmltable($eurSHIPTO);
		
		}
		/*
		//Currency 
		{
			$CurX=105;	//116		
			$pdf->SetX($CurX);   //SHIP TO
			$eurcurrency="<table border=0 >
			<tr><td width=16 align=left style=bold valign=middle >Currency:</td> <td width=70   align=left height=$RowsHight valign=middle >$Symbol</td><tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurcurrency);
		
		}		
				
		*/
		
	 

?>