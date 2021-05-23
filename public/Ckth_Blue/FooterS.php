<?php



        $LineWidth=$pdf->LineWidth;  //默认线宽
		$LineRealW=0.3;  //实际线的长度
		$LineVirW=0.5;   //简隔长度
		$LineStarX=$CurMargin;  //线的起点
		$LineStarY=$MaxContainY; //往上走一点
		$LineLen=38;  //线的的长度
		$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
		//$pdf->SetDrawColor(0,0,0);
		include "../Admin/invoicetopdf_blue/drawline.php";  //画虚线

		$pdf->SetFont('uGB','B',7,'','true');
		$pdf->SetTextColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
		$pdf->Text($CurMargin,$MaxContainY+2.5,"签收人");  //输出I


		$pdf->SetXY($CurMargin,$MaxContainY+6);
		$tmpTableY=$pdf->GetY()+0.5;

		//画线
		$pdf->SetLineWidth(0.1);
		$pdf->SetDrawColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->Line($CurMargin,$tmpTableY,$CurMargin+184,$tmpTableY); //

		$tmpTableY=$pdf->GetY()+1;
		$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->SetFont('uGB','',$FontSize_Footer-1,'','true');
		$pdf->SetXY($CurMargin-1,$tmpTableY);  //传真，电话，邮件，网址
		$tmptable="<table border=0 >
			<tr>
				<td width=22 align=right>$S_Tel</td>
				<td width=1 align=right></td>
				<td width=24 align=right>$S_Fax</td>
				<td width=1 align=right></td>
				<td width=27 align=right>$S_mail</td>
				<td width=1 align=right></td>
				<td width=25 align=right>$S_WebSite</td>
				<td width=1 align=right></td>
				<td width=80 align=right>$S_Address $S_ZIP</td>
			<tr>	
				
		</table>" ;//$eurTableList;
		$pdf->htmltable($tmptable);

		$pdf->Image("../Admin/invoicetopdf_blue/p_tel.jpg",$CurMargin,$tmpTableY,3,0,"JPG"); //电话
		$pdf->Image("../Admin/invoicetopdf_blue/p_fax.jpg",$CurMargin+25.5,$tmpTableY,3,0,"JPG"); //传真
		$pdf->Image("../Admin/invoicetopdf_blue/p_email.jpg",$CurMargin+54,$tmpTableY,3,0,"JPG"); //邮件
		$pdf->Image("../Admin/invoicetopdf_blue/p_ie.jpg",$CurMargin+78.5,$tmpTableY,3,0,"JPG"); //ie
		$pdf->Image("../Admin/invoicetopdf_blue/p_address.jpg",$CurMargin+131,$tmpTableY,3,0,"JPG"); //ie


?>