<?php
//电信-zxq 2012-08-01
//EUR专用模板  //二合一已更新
$TableFontSize=8;
define('FPDF_FONTPATH','../plugins/fpdf/font/');
//include "../plugins/fpdf/pdftable.inc.php";		//更新
require('../plugins/fpdf/chinese-unicode.php');
//$pdf=new PDFTable();
$pdf=new PDF_Unicode();

$pdf->SetAutoPageBreak("on",5);		//分页下边距
$pdf->SetMargins(10,10,10);			//上左右边距
$pdf->Open();
$addSign=0; //是否要重新分页,
$PreY=0;   //上一次的位置
$oldPreY=0; //保存的上一次位置，主要是最后一页使用
$MaxContainY=275;  //每一页主内容的最下面的位置
$CurMargin=8;  //左边距

//$pdftmp=new PDFTable();
$pdftmp=new PDF_Unicode();
$pdftmp->SetAutoPageBreak("off",5);		//分页下边距
$pdftmp->SetMargins(10,10,10);			//上左右边距
$pdftmp->Open();
$pdftmp->AddPage();
$pdftmp->AddUniGBhwFont('uGB');


//空白行
$eurEmptyField="<table  border=1 >
<tr >
<td width=10 align=center height=$RowsHight valign=middle style=bold> </td>	
<td width=90 valign=middle>  </td>
<td width=35 align=center valign=middle style=bold>  </td>
<td width=15 align=center valign=middle style=bold>  </td>			
<td width=25 align=center valign=middle style=bold>  </td>	
<td width=20 align=center valign=middle style=bold> </td></td></tr></table>" ;//$eurTableList;
$pageNo=0;
$DateMDY=date("Y-m-d"); // date("m/d/Y");
//$Date=date("d-M-y");
$pdf->AddUniGBhwFont('uGB');
$Counts=1;
for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //循环输出
	$eurTableNo="eurTableNo".strval($pi);
     if($addSign==0){  //新加一页
		$pdf->AddPage();
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;

		$Com_Pack_PI="报 价 单";
		//include "invoicetopdf/invoicepublicHeaderS.php";  //页头
		$CurH=25;  //当前高度
		$pdf->SetFillColor(180,180,180);
		$pdf->Setxy(6,6);
		$pdf->SetFont('uGB','B',22);
		$pdf->SetTextColor(255,0,0);
		$pdf->Text($CurMargin+3,18,"$S_Company");
		$pdf->Rect(130,10,73,12,"F");  //Order框 15
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(133,13);
        $pdf->Text(133,18,$Com_Pack_PI);
		$pdf->SetTextColor(0,0,255);
		$pdf->SetFont('uGB','B',13); //设定Order的字体大小

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
			$pdf->SetFont('Arial','B',$TableFontSize);
			$tmp_str="To:";
			include "invoicetopdf/invoicepublicText.php";  //英文输出
			$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大小
			$eurSTO="<table border=0 >
			<tr><td width=13 align=left  valign=middle style=bold >&nbsp;</td><td width=76   align=left height=$RowsHight valign=middle >$Company,</td><tr>	
			<tr><td width=13 ></td><td width=76   align=left valign=middle >$Address.</td><tr>
			<tr><td width=13 ></td><td width=76   align=left valign=middle >电话: $Tel </td><tr><tr><td width=13 ></td><td width=76   align=left valign=middle >联系人: $LinkName</td><tr></table>" ;
			$pdf->htmltable($eurSTO);

		}

		$pdf->Rect(103,37,100,$CurH,"D"); //第三个框
		$NexPY=41;
		$pdf->SetFont('uGB','B',TableFontSize); //设回字体大小
		{
			$CurX=105;	//116
			$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
			$pdf->SetFont('Arial','B',$TableFontSize);
			$tmp_str="FROM:";
			//include "invoicetopdf/invoicepublicText.php";  //英文输出
			$pdf->Text($CurX,$NexPY,$tmp_str);
			$pdf->SetFont('uGB','B',$TableFontSize); //设回字体大
			$FromAddress="上海市宝安区西乡镇宝民二路臣田伟信达大厦八楼";
			$eurSHIPTO="<table border=0 >
			<tr><td width=16 align=left style=bold valign=middle >&nbsp;</td><td width=76   align=left height=$RowsHight valign=middle >$FromAddress</td><tr>	
			<tr><td width=16 align=left></td><td width=76  align=left valign=middle >联系人:黎志基</td><tr>	
			<tr><td width=16 align=left></td><td width=76  align=left valign=middle >手机:+86 13823610525</td><tr>		
			<tr><td width=16 align=left></td><td width=76  align=left valign=middle >e-mail:LZJ0525@me.com</td><tr>		
			</table>" ;
			$pdf->htmltable($eurSHIPTO);

		}
		//Head 到此结束

		$pdf->SetXY($CurMargin,65);
		$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY($CurMargin,$pdf->GetY());  //77

		//表标题
		{
			$eurTableField="<table  border=1 >
			<tr bgcolor=#CCCCCC repeat>
			<td width=10 align=center height=$RowsHight valign=middle style=bold></td>
			<td width=90 align=center valign=middle style=bold>产品名称</td>			
			<td width=35 align=center valign=middle style=bold>模具尺寸</td>
			<td width=15 align=center valign=middle style=bold>穴数</td>			
			<td width=25 align=center valign=middle style=bold>模仁材料</td>	
			<td width=20 align=center valign=middle style=bold>模具费用</td></tr></table>" ;//$eurTableList;
			$pdf->htmltable($eurTableField);
			$pdf->Text(9,$pdf->GetY()-1.5,"序号");
		}

		$HeaerBY=$pdf->GetY();//获取固定Y位置;
		$PreY=$HeaerBY;

		$addSign=1;

	}   //增加一页后
	$pdf->SetX($CurMargin);  //
	if(($$eurTableNo)!=""){   //输出表的内容。
	    $pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->htmltable($$eurTableNo);
	}
	$NowY=$pdf->GetY();  //取得当前的Y的位置


	if(($NowY)>($MaxContainY+1)) //>278 //说明已超过了
	{
		$EmptyH=$MaxContainY-$NowY;
		$pdf->SetFillColor(255,255,255);

		for($EveryH=$RowsHight;$EveryH<$EmptyH;$EveryH=$EveryH+$RowsHight)
		{
			$pdf->SetX($CurMargin);
			$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
			$pdf->htmltable($eurEmptyField);
		}

		$addSign=0; //准备重新分页
		$PreY=0; //前一个Y得新置为0。

	}
	else{
		$oldPreY=$PreY;  //用于最后一页
		$PreY=$NowY; //前一个Y位置
	}

	$isSeal=0;
	if($pi==$Counts) //说明最后一条记录情况,有了统计，则要计算统计的高度。
	{
		$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		//$TotalH=$NowY-$oldPreY;  //获取决统计的高度
		$TotalH=55;
		$EmptyH=$NowY-$oldPreY+$TotalH+1;   //连统计一起清除
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($CurMargin-1,$oldPreY,196,$EmptyH,"F");  //
		$pdf->SetXY($CurMargin,$oldPreY);  //重回位置
		$EmptyH=$MaxContainY-$oldPreY-$TotalH;  //留有统计的高度,并加所有空行  $MaxContainY=277
		for($EveryH=$RowsHight;$EveryH<$EmptyH;$EveryH=$EveryH+$RowsHight)
		{
			$pdf->SetX($CurMargin);
			$pdf->htmltable($eurEmptyField);
		}
		$pdf->SetX($CurMargin);  //输出最后一页,也就是统计的
		$$eurTableNo="YES";
		if(($$eurTableNo)!=""){   //输出表的内容。
			//由于中文空格不同，所以要单独放上去

				$NowSY=$pdf->GetY(); //
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
			    $tmp_Total="<table  border=1 > <tr bgcolor='#999999'>
				<td width=175 align='right' valign='top'>Total</td><td  width=20 align='right'></td></tr>
				</table>";
			    $pdf->htmltable($tmp_Total);

			    $NowSY=$pdf->GetY();
				$tmp_str="备注:";
				$pdf->Text($CurMargin+0.4,$NowSY+2.8,$tmp_str);
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
			    $tmp_Notes="<table  border=1 >
						<tr><td width=195  height=12  align='left' valign='top'>&nbsp;<br>$PaymentTerm$Priceterm$Terms</td></tr></table>";
			    $pdf->htmltable($tmp_Notes);

				$NowSY=$pdf->GetY();
				$tmp_str="付款方式:";
                $pdf->Text($CurMargin+0.4,$NowSY+2.8,$tmp_str);
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
			     $tmp_Terms="<table  border=1 >
						<tr><td width=95  height=24  align='left' valign='top'>
						&nbsp;<br>开模前30%定金,确认模具后付70%余款</td></tr></table>";
			$pdf->htmltable($tmp_Terms);

				/*$pdf->SetFont('uGB','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+160,$NowSY+3.5,"币  种 :");
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+175,$NowSY+3.5,"$Symbol"); */

				//$NowSY=$pdf->GetY(); //
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->SetXY($CurMargin+95.4,$NowSY+3.8);
				$tmp_str="BANK:";
				include "invoicetopdf/invoicepublicText.php";  //英文输出


				$pdf->SetXY($CurMargin+95,$NowSY);
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
			    $tmp_BANK="<table  border=1 ><tr>
				<td width=100  align='left' height=24  valign=middle >&nbsp;<br>Beneficiary: $Beneficary<br>Bank      : $Bank<br>Bank Add  : $BankAdd<br>Swift ID  : $SwiftID<br>A/C NO    : $ACNO</td></tr></table>";

			$pdf->htmltable($tmp_BANK);
		}

		$isSeal=1;
		$addSign=0; //输出最后一页的页脚
	}
	if($addSign==0)
	{
		/* $pdf->SetFont('uGB','',TableFontSize); //设回字体大小
		//$eurTableList="<table  border=1 >
		$pdf->SetXY($CurMargin,$MaxContainY);   //页脚位置
		$eurTableFooter="<table border=1 >
		<tr >
		<td width=27  height=13 bgcolor=#CCCCCC align=center valign=middle style=bold>授权</td>
		<td width=49 align=center valign=middle style=bold>Kung-Yi Chen(Fred)</td>
		<td width=20  bgcolor=#CCCCCC align=center valign=middle style=bold>签名</td>
		<td width=59 align=center valign=middle style=bold></td>
		<td width=10  bgcolor=#CCCCCC align=center valign=middle style=bold>日期</td>
		<td width=30 align=center valign=middle style=bold>$DateMDY</td>
		</tr>
		</table>" ;//$eurTableList;


		$pdf->htmltable($eurTableFooter);
		//老板签名
		//$Boss_Logo="../images/BossSignature.jpg";
		//$pdf->Image($Boss_Logo,110,$MaxContainY+1,38,0,"JPG");*/
        $pdf->SetDrawColor(0,0,0);//边框设回黑色
		$pdf->SetLineWidth(0.2);
		$pdf->Rect($CurMargin,$pdf->GetY()+25,60,0.15,"D");
		 $pdf->SetFont('uGB','',12);
		$pdf->Text($CurMargin+2,$pdf->GetY()+30,$S_Company);
		$pdf->Rect($CurMargin+125,$pdf->GetY()+25,60,0.15,"D");
		$pdf->Text($CurMargin+135,$pdf->GetY()+30,$Company);
	}
}

?>