<?php         
//电信-joseph
		$CurH=30;  //当前高度  
		$CurH2=60;  //第二个框的高度  
		$ConTableBegin_Y=$CurH+$CurH2+5+1; //主内容的第一个表的Y位置
		
		/************************************************************************///标题 
		$pdf->SetFillColor(180,180,180);
		$pdf->Setxy(6,6);
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','B',22); //设定Order的字体大小 $pdf->SetFont('Arial','B',16);
		$pdf->Text(73,13,"$Com_Pack_PI"); // $pdf->Text(133,13,"$Com_Pack_PI");  
		
		$pdf->SetFont('Arial','B',18); //设回字体大小   //$pdf->SetFont('Arial','B',22);
		$pdf->SetTextColor(255,0,0);
		$pdf->Text($CurMargin+3,24,"$E_Company");  // $pdf->Text($CurMargin+3,18,"$E_Company");  
		//***************************$E_Company
		//$pdf->Rect(130,5,73,13,"F");  //Order框 15    $pdf->Rect(130,5,73,18,"FD");
	
		
		//$pdf->SetTextColor(0,0,255);
		//$pdf->SetFont('Arial','B',10); //设定Order的字体大小	$pdf->SetFont('Arial','B',13);
		//$pdf->Text(133,25,"DeliveryNumber.:$DeliveryNumber"); 
		
		/*{			
			$pdf->SetXY(130,13);   
			$eurSInvoice="<table border=0 >
			<tr><td width=73 align=right color=#FFFFFF  valign=middle style=bold >$DeliveryNumber</td><tr>	
			</table>" ;
			$pdf->htmltable($eurSInvoice); 
		}*/			

		$pdf->SetFont('Arial','',$TableFontSize); //设回字体大小  $pdf->SetFont('Arial','',$TableFontSize+1); 
		$pdf->SetTextColor(0,0,0);
		$pdf->Text($CurMargin+2,29,"$E_Address $E_ZIP");  
		$pdf->Text($CurMargin+2,33,"Tel:    $E_Tel    Fax:    $E_Fax     URL:   $E_WebSite");
		
		/***************************************************************************///第二个框
		$FristWidth=90;
		$HeadHight=5;
		$TextHight=15;
		$pdf->Rect($CurMargin,$CurH+5,$FristWidth,$CurH2,"D"); //第二个框 
		$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
		$NexPY=39;    //  
		$pdf->SetFont('Arial','',$TableFontSize); //设回字体大小
		{
			$CurX=$CurMargin;  //15;			
			//$pdf->SetXY($CurX,$NexPY-3.5);   //SOLD TO
			$pdf->SetXY($CurX,$NexPY-4);
			$eurSTO="<table border=1 ><tr bgcolor=#CCCCCC>
			<td width=$FristWidth align=left valign=middle style=bold height=$HeadHight >Shipper/Exporter</td></tr>
			<tr>
			<td width=$FristWidth align=left  height=$TextHight>$Company<br>$Address</td></tr>
			<tr bgcolor=#CCCCCC>
			<td width=$FristWidth align=left valign=middle style=bold height=$HeadHight >SHIP TO:</td></tr>	
			<tr>
			<td width=$FristWidth align=left  height=$TextHight>$SoldTo<br>$ToAddress</td></tr>
			<tr bgcolor=#CCCCCC>
			<td width=$FristWidth align=left valign=middle style=bold height=$HeadHight >Forwader:</td></tr>	
			<tr> 
			<td width=$FristWidth align=left  height=$TextHight>$ForwarderAddress</td></tr>
			</table>" ;
			$pdf->htmltable($eurSTO); 
		}			
		
		/********************************************************************///第三个框
		$CurH3=13;//第三个框的高度
		//$pdf->Rect(128,21,75,$CurH3,"D"); 
		$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
		$NexPY=24;    //  
		$pdf->SetFont('Arial','',$TableFontSize); //设回字体大小
		{
		    $CurX=128;			
			$pdf->SetXY($CurX,$NexPY-4);
		    $InforTable="<table border=1>
			<tr bgcolor=#CCCCCC><td width=18 align=center style=bold valign=middle height=6>Date</td>
			    <td width=43 align=center style=bold valign=middle >Bill Of Delivery</td>
				<td width=14 align=center style=bold valign=middle>PageNO:</td>
			</tr>
			<tr><td width=18 align=center  valign=middle height='8'>$DeliveryDate</td>
			    <td width=43 align=center  valign=middle >$DeliveryNumber</td>
				<td width=14 align=center  valign=middle>$pageNo</td
			</tr>
			</table>";
			$pdf->htmltable($InforTable);
		
		}
		/*********************************************************************///第四个框
		$FourWidth=103;//宽度
		$FourHight=5;
		$pdf->Rect(100,$CurH+5,$FourWidth,$CurH2,"D"); //第四个框 $pdf->Rect(103,37,100,32,"D");
		
		$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
		$NexPY=39;    //   $NexPY=41
		$pdf->SetFont('Arial','',$TableFontSize); //设回字体大小
		{
			$CurX=100;	//116		
			$pdf->SetXY($CurX,$NexPY-4); 
			$eurSHIPTO="<table border=1>
			<tr><td width=$FourWidth align=left style=bold valign=middle height=$FourHight>Customer PO Number</td></tr>
			<tr><td width=$FourWidth align=left valign=middle height=10>$AllPO</td></tr>
			<tr><td width=$FourWidth align=left style=bold valign=middle height=$FourHight>Commercial Invoice#</td></tr>
			<tr><td width=$FourWidth align=left valign=middle height=$FourHight></td>$AllInvoice</tr>
			<tr><td width=$FourWidth align=left style=bold valign=middle height=$FourHight>Country of Origin</td></tr>
			<tr><td width=$FourWidth align=left valign=middle height=$FourHight>China</td></tr>
			<tr><td width=$FourWidth align=left style=bold valign=middle height=$FourHight>Final Destination</td></tr>
			<tr><td width=$FourWidth align=left valign=middle height=$FourHight></td></tr>
			<tr><td width=$FourWidth align=left style=bold valign=middle height=$FourHight>Notes</td></tr>
			<tr><td width=$FourWidth align=left valign=top height=10>$Remark</td></tr>
			</table>" ;
			$pdf->htmltable($eurSHIPTO);
		
		}
		/*************************************************************************/
	
?>