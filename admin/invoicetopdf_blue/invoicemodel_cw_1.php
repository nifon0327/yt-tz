<?php
//一页w:210*297:210mm×297mm

define('FPDF_FONTPATH','../plugins/fpdf/font/');
require_once("../plugins/fpdf/pdftable.inc.php");		//更新

$pdf=new PDFTable();
//$msyhfont = $pdf->AddFont('msyh', 'B', '');
$pdf->SetAutoPageBreak("off",5);		//分页下边距
$pdf->SetMargins(10,10,10);			//上左右边距
$pdf->Open();
$addSign=0; //是否要重新分页,
$PreY=0;   //上一次的位置
$oldPreY=0; //保存的上一次位置，主要是最后一页使用
$MaxContainY=254;  //每一页主内容的最下面的位置
$isPackListSign=0;
$isLastPage=0;

$CurMargin=14;  //左边距
//空白行
$pdftmp=new PDFTable();
$pdftmp->SetAutoPageBreak("off",5);		//分页下边距
$pdftmp->SetMargins(10,10,10);			//上左右边距
$pdftmp->Open();
$pdftmp->AddPage();

if($InvoiceModel!=2){
$eurTableField="<table  border=0 >
<tr bgcolor=#E8F5FC repeat>
<td width=10 align=left valign=middle height=$FiledHight></td>	
<td width=8  align=left  valign=middle >LN</td>
<td width=20 align=left valign=middle >PO#</td>			
<td width=30 align=left valign=middle >PRODUCT CODE</td>
<td width=52 align=left valign=middle >DESCRIPTION</td>	
<td width=19 align=left valign=middle >H.S.</td>			
<td width=14 align=right valign=middle >QTY</td>	
<td width=14 align=right valign=middle >U/P</td>	
<td width=17 align=right valign=middle >AMOUNT</td></tr></table>" ;
}
else{
$eurTableField="<table  border=0 >
<tr bgcolor=#E8F5FC repeat>
<td width=10 align=left valign=middle height=$FiledHight></td>	
<td width=8  align=left  valign=middle >LN</td>
<td width=20 align=left valign=middle >PO#</td>			
<td width=30 align=left valign=middle >PRODUCT CODE</td>
<td width=71 align=left valign=middle >DESCRIPTION</td>				
<td width=14 align=right valign=middle >QTY</td>	
<td width=14 align=right valign=middle >U/P</td>	
<td width=17 align=right valign=middle >AMOUNT</td></tr></table>" ;
}
//空白行
if($InvoiceModel!=2){
$eurEmptyField="<table  border=0 >
<tr >
<td width=10 align=left valign=middle height=$FiledHight></td>	
<td width=8 align=center  valign=middle style=bold> </td>	
<td width=20 valign=middle>  </td>
<td width=30 align=center valign=middle style=bold>  </td>
<td width=52 align=center valign=middle style=bold>  </td>	
<td width=19 align=center valign=middle style=bold>  </td>		
<td width=14 align=center valign=middle style=bold>  </td>	
<td width=14 align=center valign=middle style=bold> </td>	
<td width=17 align=center valign=middle style=bold> </td></tr></table>" ;
}
else{

$eurEmptyField="<table  border=0 >
<tr >
<td width=10 align=left valign=middle height=$FiledHight></td>	
<td width=8 align=center  valign=middle style=bold> </td>	
<td width=20 valign=middle>  </td>
<td width=30 align=center valign=middle style=bold>  </td>
<td width=71 align=center valign=middle style=bold>  </td>	
<td width=14 align=center valign=middle style=bold>  </td>	
<td width=14 align=center valign=middle style=bold> </td>	
<td width=17 align=center valign=middle style=bold> </td></tr></table>" ;
}


$pageNo=0;
$DateMDY=date("d-M-y"); // date("m/d/Y");
//$Date=date("d-M-y");
for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //1.循环输出
	$eurTableNo="eurTableNo".strval($pi);
	 $eurAppFileNo="eurAppFileNo".strval($pi);
     if($addSign==0){  //2.新加一页
		$pdf->AddPage();
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;

		$Invoice_PI="$InvoiceNO";
		//$Com_Pack_PI="invoicetopdf_new/INVOICE.jpg";
		include "invoicetopdf_blue/invoicepublicHeader.php";  //页头


		$pdf->SetXY($CurMargin,$ConTableBegin_Y);
		$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->SetTextColor(0,0,0);

		$pdf->SetLineWidth(0.2);
		$pdf->SetXY($CurMargin,$pdf->GetY());  //77
		$tmpTableY=$pdf->GetY();
		//表标题
		{

			$pdf->SetFont('Arial','',$TiTleFontSize);
			$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin,$tmpTableY);  //77
			$pdf->htmltable($eurTableField);

			$recH=$pdf->GetY()-$tmpTableY;
			//$pdf->Rec($CurMargin,$tmpTableY,19,$recH,'');
			$pdf->Rect($CurMargin,$tmpTableY,184,$recH,"");
			$pdf->SetLineWidth(0.5);
			$pdf->Line($CurMargin,$pdf->GetY(),$CurMargin+184,$pdf->GetY()); //

			$recH=$pdf->GetY()-$tmpTableY+1;
			//$pdf->SetLineWidth(0.2);
			$pdf->SetDrawColor(255,255,255);
			$pdf->Line($CurMargin-0.1,$tmpTableY,$CurMargin-0.1,$tmpTableY+$recH);
			$pdf->Line($CurMargin+184,$tmpTableY,$CurMargin+184,$tmpTableY+$recH);

			//$pdf->Text($CurMargin,$pdf->GetY(),$pdf->GetY());

		}
		$pdf->SetLineWidth(0.2);
		$pdf->SetXY($CurMargin,$pdf->GetY()+0.2);  //往下一点，以免庶住画的框

		$HeaerBY=$pdf->GetY();//获取固定Y位置;
		$PreY=$HeaerBY;
		$addSign=1;

		//加入水印图象
		$tmpTableY=$pdf->GetY();
		//$WaterSignEndPng="invoicetopdf_blue/ashcloud.jpg";
		//$pdf->Image($WaterSignEndPng,$CurMargin,$tmpTableY,184,$MaxContainY-$tmpTableY,"jpg");
		$ashcloudStartX=$CurMargin-1;
		$ashcloudStartY=$tmpTableY+1;
		$ashcloudWidth=184; //
		$ashcloudHeight=$MaxContainY-$tmpTableY;
		$pdf->SetFont('Arial','B',7); //设回字体大小
		$pdf->SetTextColor(220,241,251); //220 241 251
		include "../Admin/invoicetopdf_blue/ashcloud.php";  //画虚线

	}  //if($addSign==0){  //2.新加一页

	$tmpTableY=$pdf->GetY();
	$pdf->SetX($CurMargin);  //
    $appFileY=$tmpTableY;

	$pdf->SetXY($CurMargin,$tmpTableY+1);  //恢复到原来的地方

	if(($$eurTableNo)!=""){   //输出表的内容。

		if($pi<$Counts){
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial','',$FontSize_Table); //设回字体大小
			$pdf->SetDrawColor(255,255,255);
			//$pdf->SetDrawColor(0,0,0);
			//$pdf->htmltable($$eurTableNo);
			$pdf->transhtmltable($$eurTableNo,0,0);
			$tmpTableY=$pdf->GetY()+$RowDisc;

			$LineWidth=$pdf->LineWidth;  //默认线宽
			$LineRealW=1;  //实际线的长度
			$LineVirW=1;   //简隔长度
			$LineStarX=$CurMargin;  //线的起点
			$LineStarY=$tmpTableY-1.2; //往上走一点
			$LineLen=184;  //线的的长度
			$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
			include "invoicetopdf_blue/drawline.php";  //画虚线
		}
		else{ //最后一页

			$pdf->SetTextColor(255,255,255);
			$pdf->SetFont('Arial','',$FontSize_Table); //设回字体大小
			$pdf->SetDrawColor(255,255,255);
			//$pdf->SetDrawColor(0,0,0);
			//$pdf->htmltable($$eurTableNo);
			$pdf->SetX(0);  //恢复到原来的地方
			$tmptable="<table  border=1 >
						<tr  repeat><td width=29  align=left height=$RowsHight valign=middle >1</td></tr>
						<tr ><td width=29  align=left height=14 valign=bottom ></td>2</tr>
						</table>";
			//$pdf->transhtmltable($$eurTableNo,0,0);
			$pdf->transhtmltable($tmptable,0,0);

			$tmpTableY=$pdf->GetY()+$RowDisc;

		}

		$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地方
	}

	if($$eurAppFileNo!=""){
			$pdf->Image($$eurAppFileNo,$CurMargin+1,$appFileY,9.5,9.5,"jpg");
		}

	$NowY=$pdf->GetY();  //取得当前的Y的位置
	//$pdf->Text(0,$NowY,"$NowY");  //输出当前值
	//取得下一个的高度，
	$tmpH=0;
	if($pi<$Counts){
		$pdftmp->SetFont('Arial','',$FontSize_Table); //设回字体大小
		$pdftmp->SetXY($RowDisc,1);
		$tmpNo="eurTableNo".strval($pi+1);
		$pdftmp->htmltable($$tmpNo);
		$tmpY=$pdftmp->GetY();
		$tmpH=$tmpY; //$tmpH=$tmpY-1; mody by zx 2014-09-19

		//$pdf->Text(200,$NowY,"$tmpH");  //输出当前值

	}
	//$MaxContainY+1是因为pdftmp起点为1，1
	if(($NowY+$tmpH)>($MaxContainY+$RowDisc/2)) //>278 //说明已超过了，1、那得把此行去掉.2、用空白行275-$PreY 画一空表，并标示continue，3、同时记录退一
	{
		$EmptyH=$MaxContainY-$NowY;
		$WaterSignBegin=$pdf->GetY();  //水印开始
		//$pdf->SetFillColor(255,255,255);
		$pdf->SetDrawColor(255,255,255);
		$isEmpPostion=$pdf->GetY();
		for($EveryH=$emptyHight;$EveryH<$EmptyH;$EveryH=$EveryH+$emptyHight)
		{
			$pdf->SetX($CurMargin);
			//$pdf->htmltable($eurEmptyField);
			$pdf->transhtmltable($eurEmptyField,0,0);
			$tmpTableY=$pdf->GetY();
			$LineWidth=$pdf->LineWidth;  //默认线宽
			$LineRealW=1;  //实际线的长度
			$LineVirW=1;   //简隔长度
			$LineStarX=$CurMargin;  //线的起点
			$LineStarY=$tmpTableY-0.2; //往上走一点
			$LineLen=184;  //线的的长度
			$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
			//$pdf->SetDrawColor(0,0,0);
			include "invoicetopdf_blue/drawline.php";  //画虚线
			$isEmpPostion=$pdf->GetY();  //表示有空行，要去掉原多余的水印
			$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地
			$WaterSignEnd=$tmpTableY; //水印结束

		}
		$tmpTableY=$pdf->GetY();
		//$pdf->Text($CurMargin-3,$isEmpPostion,"$isEmpPostion----");  //
		if($isEmpPostion>0){  //画个框，把多余的水印去掉
			$pdf->SetFillColor(255,255,255);
			$pdf->Rect($CurMargin-1,$isEmpPostion,190,$MaxContainY-$isEmptyLinePostion,"F");  //
		}


		//$tmpTableY=$pdf->GetY();
		// 空白行加入水印开始***********************************
		//空白行加入水印结束**********************************************
		$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地

		$addSign=0; //准备重新分页
		$PreY=0; //前一个Y得新置为0。
	}
	else{
		$oldPreY=$PreY;  //用于最后一页
		$PreY=$NowY; //前一个Y位置
	}



	if($pi==$Counts) //说明最后一条记录情况,有了统计，则要计算统计的高度。
	{
		$NewMaxContainY=240; //最后一页高度,新的高度
		//***$ReserverBankH=$MaxContainY-$NewMaxContainY; //($MaxContainY-$MaxContainY:254-240)=14,预留给银行的高度
		$isLastPage=1;

		$TotalH=$NowY-$oldPreY;  //获取决统计的高度

		$EmptyH=$NowY-$oldPreY+$TotalH+1;   //连统计一起清除
		//$pdf->SetX($PreY);
		$pdf->SetFillColor(255,255,255);
		//$pdf->Rect($CurMargin-1,$oldPreY,196,$EmptyH,"F");  //
		$pdf->SetXY($CurMargin,$oldPreY);  //重回位置

		//!!!!!!!!!!!!!!!!!!!
		//$EmptyH=$MaxContainY-$oldPreY-$TotalH+$RowDisc+$ReserverBankH;  //留有统计的高度,并加所有空行  $MaxContainY=277
		//***$EmptyH=$MaxContainY-$oldPreY+$RowDisc-$ReserverBankH;  //留有统计的高度,并加所有空行  $MaxContainY=277
		$EmptyH=$MaxContainY-$oldPreY-$TotalH+$RowDisc;
		$WaterSignBegin=$pdf->GetY();  //水印开始
		for($EveryH=$emptyHight;$EveryH<$EmptyH;$EveryH=$EveryH+$emptyHight)
		{
			$pdf->SetX($CurMargin);
			//$pdf->htmltable($eurEmptyField);
			$pdf->transhtmltable($eurEmptyField,0,0);
			$tmpTableY=$pdf->GetY();
			$LineWidth=$pdf->LineWidth;  //默认线宽
			$LineRealW=1;  //实际线的长度
			$LineVirW=1;   //简隔长度
			$LineStarX=$CurMargin;  //线的起点
			$LineStarY=$tmpTableY-0.2; //往上走一点
			$LineLen=184;  //线的的长度
			$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
			//$pdf->SetDrawColor(0,0,0);
			include "invoicetopdf_blue/drawline.php";  //画虚线
			$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地
			$WaterSignEnd=$tmpTableY; //水印结束

		}
		//****$pdf->SetXY($CurMargin,$MaxContainY-$TotalH+$RowDisc);  //返回到最后高度前
		$pdf->SetXY($CurMargin,$NewMaxContainY-5);
		$tmpTableY=$pdf->GetY();
		// 空白行加入水印开始***********************************
		//空白行加入水印结束**********************************************

		$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地

		//$pdf->SetX($CurMargin);  //输出最后一页,也就是统计的

		if(($$eurTableNo)!=""){   //输出表的内容。
		    $tmpY=$pdf->GetY();
			$pdf->SetXY($CurMargin,$tmpY+1);
			$tmpTableY=$pdf->GetY();
			//,Total
			{

				$pdf->SetDrawColor(255,255,255);
				if($tmpTableY<$MaxContainY){  //画个框，把多余的水印去掉
					$pdf->SetFillColor(255,255,255);
					$pdf->Rect($CurMargin-1,$tmpTableY,190,$MaxContainY-$tmpTableY,"F");
				}

				$pdf->SetFont('Arial','',$TiTleFontSize);
				$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
				$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);

				//$pdf->SetXY($CurMargin,$tmpTableY);  //倒退银行预留度
				//***$tmpTableY=$NewMaxContainY-$TotalH+$ReserverBankH; //倒退银行预留度
				//$tmpTableY=$MaxContainY-$TotalH;
				$pdf->SetXY($CurMargin,$tmpTableY);  //倒退银行预留度

				//$pdf->transhtmltable($$eurTableNo,0,1);  //统计 eurTableNoTotal
				$pdf->transhtmltable($eurTableNoTotal,0,1);

				$pdf->SetTextColor(0,0,0);  //要把统计字变黑
				//$pdf->SetDrawColor(0,0,0);
				//$pdf->Text($CurMargin+30,$pdf->GetY()-1.5,$Total);  //输出I
				$T_Total=number_format( $Total ,2, '.' , ',' );
				$pdf->SetXY($CurMargin+29,$tmpTableY);
				$tmptable="<table border=0 >
							<tr>
								<td width=35 align=left valign=middle style=bold  height=$RowsHight>$T_Total</td>
							<tr>	
						</table>";
				$pdf->htmltable($tmptable);  //统计

				//$pdf->Text($CurMargin+177,$pdf->GetY()-1.5,$Total);  //输出I
				$pdf->SetXY($CurMargin+167,$tmpTableY);
				$tmptable="<table border=0 >
							<tr >
								<td width=17 align=right valign=middle style=bold  height=$RowsHight >$T_Total</td>
							<tr>	
						</table>";
				$pdf->htmltable($tmptable);  //统计

				$recH=$pdf->GetY()-$tmpTableY;
				//$pdf->Rec($CurMargin,$tmpTableY,19,$recH,'');
				$pdf->Rect($CurMargin,$tmpTableY,184,$recH,"");
				$pdf->SetLineWidth(0.3);
				$pdf->Line($CurMargin,$tmpTableY,$CurMargin+184,$tmpTableY); //

				$pdf->SetDrawColor(255,255,255);
				$pdf->Line($CurMargin,$tmpTableY,$CurMargin,$tmpTableY+$recH); //去掉左边
				$pdf->Line($CurMargin+184,$tmpTableY,$CurMargin+184,$tmpTableY+$recH); //去掉右边
				$pdf->Line($CurMargin,$tmpTableY+$recH,$CurMargin+184,$tmpTableY+$recH); //去掉下边
				//$pdf->Text($CurMargin,$pdf->GetY(),$pdf->GetY());


				$MaxContainY=$NewMaxContainY; //最后一页的高度

			}

		   //***************************************


		   $NoteY=$pdf->GetY(); //给BANK定位的值


		}


		$addSign=0; //输出最后一页的页脚
	}

	//换页，得把页脚输出来
	if($addSign==0)
	{
	    if($pi==$Counts){ //最后一页
			include "invoicetopdf_blue/invoicepublicFooter.php";  //最后固定页脚
		}
		else {
			include "invoicetopdf_blue/invoicepublicFooter.php";  //最后固定页脚
		}


	}


} //for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //1.循环输出


//Packing List----- *************************************************************************************************

 //184
/*
$isPackListSign=1;
$isLastPage=0;

$eurTableField="
<table border=0 ><tr bgcolor=#E8F5FC repeat>
<td width=13 align=left height=$FiledHight valign=middle >No.</td>
<td width=21 align=left valign=middle >PO#</td>
<td width=35 align=left valign=middle >PRODUCT CODE</td>
<td width=42 align=left valign=middle >DESCRIPTION</td>
<td width=11 align=center valign=middle >Unit/<br> Carton</td>
<td width=23 align=center valign=middle >CARTON SIZE<br>(CM)</td>
<td width=13 align=center valign=middle >QTY</td>
<td width=13 align=center valign=middle >NW<br>(KG)</td>
<td width=13 align=center valign=middle >GW<br>(KG)</td>
</tr></table>" ;//$eurTableList;
//空白行
$eurEmptyField="<table  border=0 >
<tr >
<td width=10 align=center height=$emptyHight valign=middle style=bold> </td>
<td width=21 valign=middle>  </td>
<td width=35 align=center valign=middle style=bold>  </td>
<td width=42 align=center valign=middle style=bold>  </td>
<td width=12 align=center valign=middle style=bold>  </td>
<td width=25 align=center valign=middle style=bold>  </td>
<td width=13 align=center valign=middle style=bold> </td>
<td width=13 align=center valign=middle style=bold>  </td>
<td width=13 align=center valign=middle style=bold> </td>
</tr></table>" ;//$eurTableList;

$addSign=0; //是否要重新分页,
$PreY=0;   //上一次的位置
$oldPreY=0; //保存的上一次位置，主要是最后一页使用
$MaxContainY=254;  //每一页主内容的最下面的位

$tmpCounts=$plCounts-1;
$pageNo=0;

$pageNo=0;
$DateMDY=date("d-M-y"); // date("m/d/Y");
//$Date=date("d-M-y");
for ($pi=1;$pi<=$tmpCounts;$pi=$pi+1){  //1.循环输出
	$eurTableNo="plTableNo".strval($pi);
     if($addSign==0){  //2.新加一页
		$pdf->AddPage();
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;

		$Invoice_PI="$InvoiceNO";
		//$Com_Pack_PI="invoicetopdf_new/INVOICE.jpg";
		include "invoicetopdf_blue/packListpublicHeader.php";  //页头


		$pdf->SetXY($CurMargin,$ConTableBegin_Y);
		$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->SetTextColor(0,0,0);

		$pdf->SetLineWidth(0.2);
		$pdf->SetXY($CurMargin,$pdf->GetY());  //77
		$tmpTableY=$pdf->GetY();
		//表标题
		{

			$pdf->SetFont('Arial','',$TiTleFontSize);
			$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
			$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);
			$pdf->SetXY($CurMargin,$tmpTableY);  //77
			$pdf->htmltable($eurTableField);

			$recH=$pdf->GetY()-$tmpTableY;
			//$pdf->Rec($CurMargin,$tmpTableY,19,$recH,'');
			$pdf->Rect($CurMargin,$tmpTableY,184,$recH,"");
			$pdf->SetLineWidth(0.5);
			$pdf->Line($CurMargin,$pdf->GetY(),$CurMargin+184,$pdf->GetY()); //

			$recH=$pdf->GetY()-$tmpTableY+1;
			//$pdf->SetLineWidth(0.2);
			$pdf->SetDrawColor(255,255,255);
			$pdf->Line($CurMargin-0.1,$tmpTableY,$CurMargin-0.1,$tmpTableY+$recH);
			$pdf->Line($CurMargin+184,$tmpTableY,$CurMargin+184,$tmpTableY+$recH);

			//$pdf->Text($CurMargin,$pdf->GetY(),$pdf->GetY());

		}
		$pdf->SetLineWidth(0.2);
		$pdf->SetXY($CurMargin,$pdf->GetY()+0.2);  //往下一点，以免庶住画的框

		$HeaerBY=$pdf->GetY();//获取固定Y位置;
		$PreY=$HeaerBY;
		$addSign=1;

		//加入水印图象
		$tmpTableY=$pdf->GetY();
		//$WaterSignEndPng="invoicetopdf_blue/ashcloud.jpg";
		//$pdf->Image($WaterSignEndPng,$CurMargin,$tmpTableY,184,$MaxContainY-$tmpTableY,"jpg");
		$ashcloudStartX=$CurMargin-1;
		$ashcloudStartY=$tmpTableY+1;

		$ashcloudWidth=184; //
		$ashcloudHeight=$MaxContainY-$tmpTableY;
		$pdf->SetFont('Arial','B',7); //设回字体大小
		$pdf->SetTextColor(220,241,251); //220 241 251
		include "../Admin/invoicetopdf_blue/ashcloud.php";  //画虚线

	}  //if($addSign==0){  //2.新加一页

	$tmpTableY=$pdf->GetY();
	$pdf->SetX($CurMargin);  //


	$pdf->SetXY($CurMargin,$tmpTableY+1);  //恢复到原来的地方

	if(($$eurTableNo)!=""){   //输出表的内容。

		if($pi<$tmpCounts){
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial','',$FontSize_Table); //设回字体大小
			$pdf->SetDrawColor(255,255,255);
			//$pdf->htmltable($$eurTableNo);
			$pdf->transhtmltable($$eurTableNo,0,0);
			$tmpTableY=$pdf->GetY()+$RowDisc;
			$LineWidth=$pdf->LineWidth;  //默认线宽
			$LineRealW=1;  //实际线的长度
			$LineVirW=1;   //简隔长度
			$LineStarX=$CurMargin;  //线的起点
			$LineStarY=$tmpTableY-1.2; //往上走一点
			$LineLen=184;  //线的的长度
			$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
			include "invoicetopdf_blue/drawline.php";  //画虚线

		}
		else { //最后一页

			$pdf->SetTextColor(255,255,255);
			$pdf->SetFont('Arial','',$FontSize_Table); //设回字体大小
			$pdf->SetDrawColor(255,255,255);
			//$pdf->htmltable($$eurTableNo);
			$pdf->SetX(0);  //恢复到原来的地方
			$tmptable="<table border=0 >
				<tr >
					<td width=150 align=left valign=middle height=$RowsHight >1</td>
				<tr>
			</table>";
			//$pdf->transhtmltable($$eurTableNo,0,0);
			$pdf->transhtmltable($tmptable,0,0);
			$tmpTableY=$pdf->GetY()+$RowDisc;

		}


		$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地方
	}

	$NowY=$pdf->GetY();  //取得当前的Y的位置
	//$pdf->Text(0,$NowY,"$NowY");  //输出当前值
	//取得下一个的高度，
	$tmpH=0;
	if($pi<$tmpCounts){
		$pdftmp->SetFont('Arial','',$FontSize_Table); //设回字体大小
		$pdftmp->SetXY($RowDisc,1);
		$tmpNo="plTableNo".strval($pi+1);
		$pdftmp->htmltable($$tmpNo);
		$tmpY=$pdftmp->GetY();
		$tmpH=$tmpY; //$tmpH=$tmpY-1; mody by zx 2014-09-19

		//$pdf->Text(200,$NowY,"$tmpH");  //输出当前值

	}
	//$MaxContainY+1是因为pdftmp起点为1，1
	if(($NowY+$tmpH)>($MaxContainY+$RowDisc/2)) //>278 //说明已超过了，1、那得把此行去掉.2、用空白行275-$PreY 画一空表，并标示continue，3、同时记录退一
	{
		$EmptyH=$MaxContainY-$NowY;
		$WaterSignBegin=$pdf->GetY();  //水印开始
		//$pdf->SetFillColor(255,255,255);
		$pdf->SetDrawColor(255,255,255);
		$isEmpPostion=$pdf->GetY();
		for($EveryH=$emptyHight;$EveryH<$EmptyH;$EveryH=$EveryH+$emptyHight)
		{
			$pdf->SetX($CurMargin);
			//$pdf->htmltable($eurEmptyField);
			$pdf->transhtmltable($eurEmptyField,0,0);
			$tmpTableY=$pdf->GetY();
			$LineWidth=$pdf->LineWidth;  //默认线宽
			$LineRealW=1;  //实际线的长度
			$LineVirW=1;   //简隔长度
			$LineStarX=$CurMargin;  //线的起点
			$LineStarY=$tmpTableY-0.2; //往上走一点
			$LineLen=184;  //线的的长度
			$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
			//$pdf->SetDrawColor(0,0,0);
			include "invoicetopdf_blue/drawline.php";  //画虚线
			$isEmpPostion=$pdf->GetY();  //表示有空行，要去掉原多余的水印
			$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地
			$WaterSignEnd=$tmpTableY; //水印结束

		}
		$tmpTableY=$pdf->GetY();
		//$pdf->Text($CurMargin-3,$isEmpPostion,"$isEmpPostion----");  //
		if($isEmpPostion>0){  //画个框，把多余的水印去掉
			$pdf->SetFillColor(255,255,255);
			$pdf->Rect($CurMargin-1,$isEmpPostion,190,$MaxContainY-$isEmptyLinePostion,"F");  //
		}


		//$tmpTableY=$pdf->GetY();
		// 空白行加入水印开始***********************************
		//空白行加入水印结束**********************************************
		$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地

		$addSign=0; //准备重新分页
		$PreY=0; //前一个Y得新置为0。
	}
	else{
		$oldPreY=$PreY;  //用于最后一页
		$PreY=$NowY; //前一个Y位置
	}


	if($pi==$tmpCounts) //说明最后一条记录情况,有了统计，则要计算统计的高度。
	{
		$TotalH=$NowY-$oldPreY;  //获取决统计的高度

		$EmptyH=$NowY-$oldPreY+$TotalH+1;   //连统计一起清除
		//$pdf->SetX($PreY);
		$pdf->SetFillColor(255,255,255);
		//$pdf->Rect($CurMargin-1,$oldPreY,196,$EmptyH,"F");  //
		$pdf->SetXY($CurMargin,$oldPreY);  //重回位置

		//!!!!!!!!!!!!!!!!!!!
		$EmptyH=$MaxContainY-$oldPreY-$TotalH+$RowDisc;  //留有统计的高度,并加所有空行  $MaxContainY=277
		$WaterSignBegin=$pdf->GetY();  //水印开始
		$isExistingEmpty=0;
		for($EveryH=$emptyHight;$EveryH<$EmptyH;$EveryH=$EveryH+$emptyHight)
		{
			$pdf->SetX($CurMargin);
			//$pdf->htmltable($eurEmptyField);
			$pdf->transhtmltable($eurEmptyField,0,0);
			$tmpTableY=$pdf->GetY();
			$LineWidth=$pdf->LineWidth;  //默认线宽
			$LineRealW=1;  //实际线的长度
			$LineVirW=1;   //简隔长度
			$LineStarX=$CurMargin;  //线的起点
			$LineStarY=$tmpTableY-0.2; //往上走一点
			$LineLen=184;  //线的的长度
			$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
			//$pdf->SetDrawColor(0,0,0);
			include "invoicetopdf_blue/drawline.php";  //画虚线
			$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地
			$WaterSignEnd=$tmpTableY; //水印结束
			$isExistingEmpty=1;

		}
		if($isExistingEmpty==1){
			$pdf->SetXY($CurMargin,$MaxContainY-$TotalH+$RowDisc);  //返回到最后高度前
		}
		else{
			$pdf->SetXY($CurMargin,$oldPreY);
		}

		$tmpTableY=$pdf->GetY();
		// 空白行加入水印开始***********************************
		//空白行加入水印结束**********************************************

		$pdf->SetXY($CurMargin,$tmpTableY);  //画完复位到原来的地

		//$pdf->SetX($CurMargin);  //输出最后一页,也就是统计的


		if(($$eurTableNo)!=""){   //输出表的内容。
		    $tmpY=$pdf->GetY();
			$pdf->SetXY($CurMargin,$tmpY+1);
			$tmpTableY=$pdf->GetY();
			//,Total
			{

				$pdf->SetDrawColor(255,255,255);
				if($tmpTableY<$MaxContainY){  //画个框，把多余的水印去掉
					$pdf->SetFillColor(255,255,255);
					$pdf->Rect($CurMargin-1,$tmpTableY,190,$MaxContainY-$tmpTableY,"F");  //
				}

				$pdf->SetFont('Arial','',$TiTleFontSize);
				$pdf->SetDrawColor($Color_TableHeaderLineA['r'],$Color_TableHeaderLineA['g'],$Color_TableHeaderLineA['b']);
				$pdf->SetTextColor($Color_TitleA['r'],$Color_TitleA['g'],$Color_TitleA['b']);

				$pdf->SetXY($CurMargin,$tmpTableY);  //77
				$pdf->transhtmltable($plTableNoTotal,0,1);  //输出统计

				$pdf->SetTextColor(0,0,0);  //要把统计字变黑
				//$pdf->SetDrawColor(0,0,0);
				//$pdf->Text($CurMargin+30,$pdf->GetY()-1.5,$Total);  //输出I
				$pdf->SetXY($CurMargin,$tmpTableY);
				$tmptable="<table border=0 >
							<tr>
								<td width=35 align=left valign=middle  height=$RowsHight>CUBE $CubeSUM (M3)</td>
							<tr>
						</table>";
				$pdf->htmltable($tmptable);  //统计CURBO

				//$pdf->Text($CurMargin+177,$pdf->GetY()-1.5,$Total);  //输出I
				$pdf->SetXY($CurMargin+145,$tmpTableY);
				$tmptable="<table border=0 >
							<tr >
								<td width=13 valign=middle align=right height=$RowsHight>$packingSUMQty </td>
								<td width=13 valign=middle align=right >$NgSUM</td>
								<td width=13 valign=middle align=right >$WgSUM</td>
							<tr>
						</table>";
				$pdf->htmltable($tmptable);  //统计

				$recH=$pdf->GetY()-$tmpTableY;

				$pdf->Rect($CurMargin,$tmpTableY,184,$recH,"");
				$pdf->SetLineWidth(0.3);
				$pdf->Line($CurMargin,$tmpTableY,$CurMargin+184,$tmpTableY); //

				$pdf->SetDrawColor(255,255,255);
				$pdf->Line($CurMargin,$tmpTableY,$CurMargin,$tmpTableY+$recH); //去掉左边
				$pdf->Line($CurMargin+184,$tmpTableY,$CurMargin+184,$tmpTableY+$recH); //去掉右边
				$pdf->Line($CurMargin,$tmpTableY+$recH,$CurMargin+184,$tmpTableY+$recH); //去掉下边
				//$pdf->Text($CurMargin,$pdf->GetY(),$pdf->GetY());

			}

		   //***************************************


		   $NoteY=$pdf->GetY(); //给BANK定位的值


		}


		$addSign=0; //输出最后一页的页脚
	}

	//换页，得把页脚输出来
	if($addSign==0)
	{
	    if($pi==$tmpCounts){ //最后一页
			include "invoicetopdf_blue/invoicepublicFooter.php";  //最后固定页脚
		}
		else {
			include "invoicetopdf_blue/invoicepublicFooter.php";  //最后固定页脚
		}


	}


} //for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //1.循环输出

*/

?>