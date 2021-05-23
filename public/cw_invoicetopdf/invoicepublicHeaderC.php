<?php 

		$CurH=32;  //当前高度
		//$pdf->Rect($CurMargin,5,195,$CurH,"D");  //第一框
		$pdf->SetFillColor(180,180,180);
		
		$pdf->Setxy(6,6);
		//$mc_Logo="../images/ASH.jpg";
		//$pdf->Image($mc_Logo,8,15,50,4,"JPG"); 
		//$pdf->SetFont('Arial','B',22); //设回字体大小
		$pdf->SetFont('uGB','B',22); 
		$pdf->SetTextColor(255,0,0);
		//$pdf->Text($CurMargin+3,17,"Ash Cloud Co, Ltd. Shenzhen");  
		$pdf->Text($CurMargin+3,18,"$S_Company");  
		
	
		$pdf->Rect(130,5,73,18,"F");  //Order框 15
		$pdf->SetTextColor(0,0,0);
		//$pdf->SetFont('Arial','B',16); //设定Order的字体大小
		$pdf->SetFont('uGB','B',16); //设定Order的字体大小		
		/*
		if($PackList=="Packing List"){
			$pdf->Text(133,15,"Packing List"); 
		}
		else{		
			$pdf->Text(133,15,"COMMERCIAL INVOICE");
		}
		*/
		//$pdf->Text(133,13,"$Com_Pack_PI");
		$pdf->SetXY(133,13);
		$tmp_str=$Com_Pack_PI;
		include "invoicetopdf/invoicepublicText.php";  //英文输出		
		//$pdf->Rect(130,20,73,8,"D");  //Invoice NO框
		$pdf->SetTextColor(0,0,255);
		$pdf->SetFont('uGB','B',13); //设定Order的字体大小		
		//$pdf->Text(133,25,"Invoice NO.:$InvoiceNO"); 
		
		{
						
			$pdf->SetXY(130,18);   //SHIP TO
			
			$eurSInvoice="<table border=0 >
			<tr><td width=73 align=right color=#FFFFFF  valign=middle style=bold >$Invoice_PI</td><tr>	
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSInvoice);  //<tr><td width=8 valign=middle>Fax:</td><td width=80   align=left valign=middle >$FaxNo</td><tr>
		
		}			


		$pdf->SetFont('uGB','B',$TableFontSize+1); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		$pdf->Text($CurMargin+2,28,"$S_Address $S_ZIP");  //7F,Chen Tian Dongfang Dasha,Bao-Ming 2Rd,XiXiang,Baoan,Shenzhen,China 518102
		
		$pdf->Text($CurMargin+2,33,"電話:    $S_Tel    傳真:    $S_Fax    網址:   $S_WebSite");	
		
		$pdf->Rect($CurMargin,37,195,$CurH,"D"); //第二个框
		
		$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大小
		$NexPY=41;
		//$pdf->Text(7,$NexPY,"TO:");
		$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大小
		{
			$CurX=$CurMargin+1;  //15;			
			$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
			$pdf->SetFont('Arial','B',$TableFontSize);
			$tmp_str="To:";
			include "invoicetopdf/invoicepublicText.php";  //英文输出
			$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大小
			$eurSTO="<table border=0 >
			<tr><td width=16 align=left  valign=middle style=bold >&nbsp;</td><td width=76   align=left height=$RowsHight valign=middle >$Company,</td><tr>	
			<tr><td width=16></td><td width=76   align=left valign=middle >$Address.</td><tr>	
			
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSTO);  //<tr><td width=8 valign=middle>Fax:</td><td width=80   align=left valign=middle >$FaxNo</td><tr>
		
		}			
		
		$pdf->Rect(103,37,100,32,"D"); //第三个框
		
		$pdf->SetFont('uGB','',$TableFontSize); //设回字体大小
		$NexPY=41;
		//$pdf->Text(102,$NexPY,"SHIP TO:");
		$pdf->SetFont('uGB','B',TableFontSize); //设回字体大小
		//SHIP TO
		//SHIP TO
		{
			$CurX=105;	//116		
			$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
			$pdf->SetFont('Arial','B',$TableFontSize);
			$tmp_str="SHIP TO:";
			include "invoicetopdf/invoicepublicText.php";  //英文输出
			$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大
			$eurSHIPTO="<table border=0 >
			<tr><td width=16 align=left style=bold valign=middle >&nbsp;</td><td width=76   align=left height=$RowsHight valign=middle >$SoldTo,</td><tr>	
			<tr><td width=16 align=left style=bold ></td><td width=76   align=left valign=middle >$ToAddress.</td><tr>			
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSHIPTO);
		
		}
		//Hearde 到外结束	
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