<?php

		$CurH=32;  //当前高度
		$pdf->SetFillColor(180,180,180);
		$pdf->Setxy(6,6);
		$pdf->SetFont('uGB','B',22);
		$pdf->SetTextColor(255,0,0);
		$pdf->Text($CurMargin+3,18,"$S_Company");
		$pdf->Rect(130,5,73,18,"F");  //Order框 15
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(133,13);
		/*$pdf->SetFont('Arial','B',16); //设定Order的字体大小
		$tmp_str=$Com_Pack_PI;
		include "invoicetopdf/invoicepublicText.php";  //英文输出*/
		$pdf->Text(133,13,"$Com_Pack_PI");
		$pdf->SetTextColor(0,0,255);
		$pdf->SetFont('uGB','B',13); //设定Order的字体大小

		{

			$pdf->SetXY(130,18);   //SHIP TO

			$eurSInvoice="<table border=0 >
			<tr><td width=73 align=right color=#FFFFFF  valign=middle style=bold >$Invoice_PI</td><tr>	
			</table>" ;
			$pdf->htmltable($eurSInvoice);

		}


		$pdf->SetFont('uGB','B',$TableFontSize+1); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		$pdf->Text($CurMargin+2,28,"$S_Address $S_ZIP");
		$pdf->Text($CurMargin+2,33,"电话:    $S_Tel    传真:    $S_Fax     网址:   $S_WebSite     FSC NO:");
		$pdf->Rect($CurMargin,37,195,$CurH,"D"); //第二个框
		$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大小
		$NexPY=41;
		$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大小
		{

			$CurX=$CurMargin+1;  //15;
			$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
			/*$pdf->SetFont('Arial','B',$TableFontSize);
			$tmp_str="To:";
			include "invoicetopdf/invoicepublicText.php";  //英文输出
			$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大小*/
			$pdf->Text($CurX,$NexPY-1,"TO:");
			$eurSTO="<table border=0 >
			<tr><td width=16 align=left  valign=middle style=bold >&nbsp;</td><td width=76   align=left height=$RowsHight valign=middle >东莞市卡登仕礼品有限公司</td><tr>	
			<tr><td width=16 ></td><td width=76   align=left valign=middle >中国广东省东莞市横沥镇西城二区B15</td><tr>	
			<tr><td width=16 ></td><td width=76   align=left valign=middle >Tel:(0796)83797008 Fax:(0796)82650825</td><tr>	
			<tr><td width=16 ></td><td width=76   align=left valign=middle >联络:魏明权/孙明霞</td><tr>	
			
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSTO);

		}

		$pdf->Rect(103,37,100,32,"D"); //第三个框

		$pdf->SetFont('uGB','',$TableFontSize); //设回字体大小
		$NexPY=41;
		$pdf->SetFont('uGB','B',TableFontSize); //设回字体大小
		{
			$CurX=105;	//116
			$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
			/*$pdf->SetFont('Arial','B',$TableFontSize);
			$tmp_str="SHIP TO:";
			include "invoicetopdf/invoicepublicText.php";  //英文输出*/
			$pdf->Text($CurX,$NexPY-1,"FROM:");
			$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大

			$eurSHIPTO="<table border=0 >
			<tr><td width=16 align=left style=bold valign=middle >&nbsp;</td><td width=76   align=left height=$RowsHight valign=middle >上海市宝安区西乡镇宝民二路臣田伟信达大厦八楼</td><tr>	
			<tr><td width=16 align=left style=bold ></td><td width=76   align=left valign=middle >联系人：黎志基</td><tr>
			<tr><td width=16 align=left style=bold ></td><td width=76   align=left valign=middle >手机：+86 13823610525</td><tr>		
			<tr><td width=16 align=left style=bold ></td><td width=76   align=left valign=middle > e-mail:LZJ0525@me.com</td><tr>					
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurSHIPTO);

		}
?>