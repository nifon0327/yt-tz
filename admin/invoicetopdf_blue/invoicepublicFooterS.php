<?php

		$pdf->SetXY($CurMargin,$MaxContainY);   //页脚位置
		if ($CompanyId==1104){
			$BossName="LEO LU";
			$Boss_Logo="invoicetopdf_blue/BossSignature_lu.jpg";

		}
		else{
			if ($CheckSign=='HK'){
			      $BossName="Kung-Yi Chen(Fred)";
			      $Boss_Logo="invoicetopdf_blue/BossSignature_hk.jpg";
			  }else{
				   $BossName="Kung-Yi Chen(Fred)";
				   $Boss_Logo="invoicetopdf_blue/BossSignature.jpg";
			  }

		}
		//老板签名
		if ($isLastPage!=1){
			//$pdf->Image($Boss_Logo,$CurMargin,$MaxContainY,70,0,"JPG");
			//$pdf->Image($Boss_Logo,$CurMargin-5,$tmpTableY-2,199,0,"JPG");
		}






$MaxContainY=260;
		$LineWidth=$pdf->LineWidth;  //默认线宽
		$LineRealW=0.3;  //实际线的长度
		$LineVirW=0.5;   //简隔长度
		$LineStarX=$CurMargin;  //线的起点
		$LineStarY=$MaxContainY+10; //往上走一点
		$LineLen=184;  //线的的长度
		$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
		//$pdf->SetDrawColor(0,0,0);
		include "invoicetopdf_blue/drawline.php";  //画虚线

		$pdf->SetFont('uGB','',10);
		$pdf->SetTextColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
		//$pdf->Text($CurMargin,$MaxContainY+19.5,"卖方签名");  //输出I


//		$LineStarX=$CurMargin+184-$LineLen;  //线的起点
//		$LineStarY=$MaxContainY+16; //往上走一点
//		//$pdf->SetDrawColor(0,0,0);
//		include "invoicetopdf_blue/drawline.php";  //画虚线

		//$pdf->Text($CurMargin+184-10,$MaxContainY+19.5,"买方签名");  //输出I

		if($isPackListSign!=1  && $isLastPage==1){ //如果不是packlist
		//银行账号
			$pdf->SetFont('uGB','',$FontSize_Footer);
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$MaxContainY+12);
			$pdf->transhtmltable($ChinaBankTable,0,1);
			$tmpTableY=$pdf->GetY()+0.5;
		}
		else{

			$pdf->SetXY($CurMargin,$MaxContainY+22);
			$tmpTableY=$pdf->GetY()+0.5;
		}

		//画线
		$pdf->SetLineWidth(0.1);
		$pdf->SetDrawColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->Line($CurMargin,$tmpTableY,$CurMargin+184,$tmpTableY); //

		$tmpTableY=$pdf->GetY()+1;
		$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->SetFont('uGB','',$FontSize_Footer);
		$pdf->SetXY(-30,$tmpTableY);  //传真，电话，邮件，网址
		$tmptable="<table border=0 >
			<tr>
				<td align=right>TZ-QF-077-102</td>
			<tr>	
				
		</table>" ;//$eurTableList;
		$pdf->htmltable($tmptable);

//		$pdf->Image("invoicetopdf_blue/p_tel.jpg",$CurMargin,$tmpTableY,3,0,"JPG"); //电话
//		//if($isPackListSign!=1  && $isLastPage==1){ //如果不是packlist
//		if(($isPackListSign!=1  && $isLastPage==1) || $isLastPage!=1){ //如果不是packlist
//			$pdf->Image("invoicetopdf_blue/p_fax.jpg",$CurMargin+25.5,$tmpTableY,3,0,"JPG"); //传真
//		}
//		else {
//			$pdf->Image("invoicetopdf_blue/p_fax.jpg",$CurMargin+25.5,$tmpTableY,3,0,"JPG"); //传真
//		}
//		$pdf->Image("invoicetopdf_blue/p_email.jpg",$CurMargin+51,$tmpTableY,3,0,"JPG"); //邮件
//		$pdf->Image("invoicetopdf_blue/p_ie.jpg",$CurMargin+78.5,$tmpTableY,3,0,"JPG"); //ie
//		$pdf->Image("invoicetopdf_blue/p_address.jpg",$CurMargin+131,$tmpTableY,3,0,"JPG"); //ie

		/*
		$pdf->Image("invoicetopdf_blue/p_address.jpg",$CurMargin+96,$MaxContainY+23,3,0,"JPG"); //地址
		$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
		$pdf->SetFont('Arial','',$FontSize_Footer);
		$pdf->SetXY($CurMargin+80,$MaxContainY+23);
		$tmptable="<table border=0 >
			<tr>
				<td width=106 align=right>$E_Address $E_ZIP</td>
			<tr>

		</table>" ;//$eurTableList;
		$pdf->htmltable($tmptable);


		$pdf->SetXY($CurMargin+75,$MaxContainY+26);  //传真，电话，邮件，网址
		$tmptable="<table border=0 >
			<tr>
				<td width=25 align=right>$E_Tel</td>
				<td width=2 align=right></td>
				<td width=25 align=right>$E_Fax</td>
				<td width=2 align=right></td>
				<td width=28 align=right>$E_mail</td>
				<td width=2 align=right></td>
				<td width=27 align=right>$E_WebSite</td>
			<tr>

		</table>" ;//$eurTableList;
		$pdf->htmltable($tmptable);
		$pdf->Image("invoicetopdf_blue/p_tel.jpg",$CurMargin+76,$MaxContainY+26,3,0,"JPG"); //电话
		$pdf->Image("invoicetopdf_blue/p_fax.jpg",$CurMargin+76+27,$MaxContainY+26,3,0,"JPG"); //传真
		$pdf->Image("invoicetopdf_blue/p_email.jpg",$CurMargin+76+27+27,$MaxContainY+26,3,0,"JPG"); //邮件
		$pdf->Image("invoicetopdf_blue/p_ie.jpg",$CurMargin+76+27+27+29.5,$MaxContainY+26,3,0,"JPG"); //ie

		$LineWidth=$pdf->LineWidth;  //默认线宽
		$LineRealW=0.1;  //实际线的长度
		$LineVirW=0.2;   //简隔长度
		$LineStarX=$CurMargin+76+24.7;  //线的起点
		$LineStarY=$MaxContainY+26.7; //往上走一点
		$LineLen=2;
		$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
		//$pdf->SetDrawColor(0,0,0);
		include "invoicetopdf_blue/drawlineV.php";  //画虚线
		$LineStarX=$CurMargin+76+24.7+27;  //线的起点
		include "invoicetopdf_blue/drawlineV.php";  //画虚线
		$LineStarX=$CurMargin+76+24.7+27+30;  //线的起点
		include "invoicetopdf_blue/drawlineV.php";  //画虚线
		*/

?>