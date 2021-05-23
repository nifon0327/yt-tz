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
		$pdf->Image($Boss_Logo,$CurMargin,$MaxContainY,70,0,"JPG");



		if($CompanyId ==1064){

			$Company_Logo="invoicetopdf_blue/1064.jpg";
			$pdf->Image($Company_Logo,$CurMargin+145,$MaxContainY,40,0,"JPG");
		}





		$LineWidth=$pdf->LineWidth;  //默认线宽
		$LineRealW=0.3;  //实际线的长度
		$LineVirW=0.5;   //简隔长度
		$LineStarX=$CurMargin;  //线的起点
		$LineStarY=$MaxContainY+16; //往上走一点
		$LineLen=38;  //线的的长度
		$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
		//$pdf->SetDrawColor(0,0,0);
		include "invoicetopdf_blue/drawline.php";  //画虚线

		$pdf->SetFont('Arial','',7);
		$pdf->SetTextColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
		$pdf->Text($CurMargin,$MaxContainY+19.5,"SIGNATURE OF SELLER");  //输出I


		$LineStarX=$CurMargin+184-$LineLen;  //线的起点
		$LineStarY=$MaxContainY+16; //往上走一点
		//$pdf->SetDrawColor(0,0,0);
		include "invoicetopdf_blue/drawline.php";  //画虚线

		$pdf->Text($CurMargin+184-27,$MaxContainY+19.5,"SIGNATURE OF BUYER");  //输出I

		if($isPackListSign!=1  && $isLastPage==1){ //如果不是packlist
		//银行账号
			$pdf->SetFont('Arial','',$FontSize_Footer);
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin-1,$MaxContainY+22);
			$pdf->transhtmltable($BankTable,0,1);
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
		$pdf->SetFont('Arial','',$FontSize_Footer-1);
		$pdf->SetXY($CurMargin-2,$tmpTableY);  //传真，电话，邮件，网址
		$tmptable="<table border=0 >
			<tr>
				<td width=22 align=right>$E_Tel</td>
				<td width=1 align=right></td>
				<td width=23 align=right>$E_Fax</td>
				<td width=1 align=right></td>
				<td width=26 align=right>$E_mail</td>
				<td width=1 align=right></td>
				<td width=26 align=right>$E_WebSite</td>
				<td width=1 align=right></td>
				<td width=83 align=right>$E_Address $E_ZIP</td>
			<tr>	
				
		</table>" ;//$eurTableList;
		$pdf->htmltable($tmptable);

		$pdf->Image("invoicetopdf_blue/p_tel.jpg",$CurMargin,$tmpTableY,3,0,"JPG"); //电话
		$pdf->Image("invoicetopdf_blue/p_fax.jpg",$CurMargin+25,$tmpTableY,3,0,"JPG"); //传真
		$pdf->Image("invoicetopdf_blue/p_email.jpg",$CurMargin+50,$tmpTableY,3,0,"JPG"); //邮件
		$pdf->Image("invoicetopdf_blue/p_ie.jpg",$CurMargin+78,$tmpTableY,3,0,"JPG"); //ie
		$pdf->Image("invoicetopdf_blue/p_address.jpg",$CurMargin+109,$tmpTableY,3,0,"JPG"); //ie

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