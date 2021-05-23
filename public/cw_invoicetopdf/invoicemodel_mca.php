<?php
defined('IN_COMMON') || include '../../basic/common.php';
//EUR专用模板  //二合一已更新
$filename="../download/invoice/mca/$InvoiceNO.pdf";
if(file_exists($filename)){unlink($filename);}

$TableFontSize=8;
//define('FPDF_FONTPATH','fpdf/font/');
//include "fpdf/pdftable.inc.php";		//更新
$pdf=new PDFTable();
$pdf->SetAutoPageBreak("on",5);		//分页下边距
$pdf->SetMargins(10,10,10);			//上左右边距
$pdf->Open();
$addSign=0; //是否要重新分页,
$PreY=0;   //上一次的位置
$oldPreY=0; //保存的上一次位置，主要是最后一页使用
$MaxContainY=277;  //每一页主内容的最下面的位置
$CurMargin=8;  //左边距

$pdftmp=new PDFTable();
$pdftmp->SetAutoPageBreak("off",5);		//分页下边距
$pdftmp->SetMargins(10,10,10);			//上左右边距
$pdftmp->Open();
$pdftmp->AddPage();



//空白行
$eurEmptyField="<table  border=1 >
<tr >
<td width=8 align=center height=$RowsHight valign=middle style=bold> </td>	
<td width=25 valign=middle>  </td>
<td width=35 align=center valign=middle style=bold>  </td>
<td width=108 align=center valign=middle style=bold>  </td>			
<td width=19 align=center valign=middle style=bold> </td></tr></table>" ;//$eurTableList;
$pageNo=0;
$DateMDY=date("d-M-y"); // date("m/d/Y");
//$Date=date("d-M-y");
for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //循环输出
	$eurTableNo="mcaTableNo".strval($pi);
     if($addSign==0){  //新加一页
		$pdf->AddPage();
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;

		$Com_Pack_PI="COMMERCIAL INVOICE";
		include "invoicetopdf/invoicepublicHeader.php";  //页头

		{
			//$eurTableList="<table  border=1 >
			$pdf->SetXY($CurMargin,$ConTableBegin_Y);   //第一个表modify by zx 2011-0129 $ConTableBegin_Y值在 invoicepublicHeader.php     $pdf->SetXY($CurMargin,72);
			$eurTableHeader="<table border=1 >
			<tr >
			<td width=20 bgcolor=#CCCCCC align=center height=$RowsHight valign=middle style=bold>Invoice NO.:</td>	
			<td width=30 align=center valign=middle style=bold>$InvoiceNO</td>
			<td width=20  bgcolor=#CCCCCC align=center valign=middle style=bold>Forwarder:</td>			
			<td width=30 align=center valign=middle style=bold>$Wise</td>	
			<td width=24  bgcolor=#CCCCCC align=center valign=middle style=bold>Requisition by:</td>	
			<td width=39 align=center valign=middle style=bold color=#06F>$Nickname</td>
			<td width=16  bgcolor=#CCCCCC align=center valign=middle style=bold>Page NO.:</td>
			<td width=16 align=center valign=middle style=bold>$pageNo</td>
			</tr>
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurTableHeader);

		}
		$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY($CurMargin,$pdf->GetY());  //77

		//表标题
		{
			$eurTableField="<table  border=1 >
			<tr bgcolor=#CCCCCC repeat>
			<td width=8 align=center height=$RowsHight valign=middle style=bold>Ln.</td>
			<td width=25 align=center valign=middle style=bold>PO#</td>			
			<td width=35 align=center valign=middle style=bold>Product Code</td>
			<td width=108 align=center valign=middle style=bold>Description</td>			
			<td width=19 align=center valign=middle style=bold>Quantity</td>	
			</tr></table>" ;//$eurTableList;
			$pdf->htmltable($eurTableField);

		}

		$HeaerBY=$pdf->GetY();//获取固定Y位置;
		$PreY=$HeaerBY;

		$addSign=1;

	}   //增加一页后

	//if($eurTableList!=""){
	$pdf->SetX($CurMargin);  //
	if(($$eurTableNo)!=""){   //输出表的内容。
		$pdf->htmltable($$eurTableNo);
	}
	$NowY=$pdf->GetY();  //取得当前的Y的位置

	//$pdf->Text(0,$NowY,"$NowY");  //输出当前值
	$tmpH=0;
	if($pi<$Counts){
		$pdftmp->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		$pdftmp->SetXY(1,1);
		$tmpNo="mcaTableNo".strval($pi+1);
		$pdftmp->htmltable($$tmpNo);
		$tmpY=$pdftmp->GetY();
		$tmpH=$tmpY-1; //
		//$pdf->Text(200,$NowY,"$tmpH");  //输出当前值

	}

	/*
	if($NowY>($MaxContainY+1)) //>278 //说明已超过了，1、那得把此行去掉.2、用空白行275-$PreY 画一空表，并标示continue，3、同时记录退一
	{
		$EmptyH=$NowY-$PreY+1;   //如果刚好到了275-277之间,则满行了,选择不换页
		//$pdf->SetX($PreY);
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($CurMargin-1,$PreY,196,$EmptyH,"F");  //第一框
		$pdf->SetXY($CurMargin,$PreY);  //画空白格补充，如果超过一行的话
		$EmptyH=$MaxContainY-$PreY;   //并加所有空行  $MaxContainY=277
		for($EveryH=$RowsHight;$EveryH<$EmptyH;$EveryH=$EveryH+$RowsHight)
		{
			$pdf->SetX($CurMargin);
			$pdf->htmltable($eurEmptyField);
		}
		$pi=$pi-1; //此记录得输出，回到上一条
		$addSign=0; //准备重新分页
		$PreY=0; //前一个Y得新置为0。
	}
	else{
		$oldPreY=$PreY;  //用于最后一页
		$PreY=$NowY; //前一个Y位置
	}
	*/
	if(($NowY+$tmpH)>($MaxContainY+1)) //>278 //说明已超过了，1、那得把此行去掉.2、用空白行275-$PreY 画一空表，并标示continue，3、同时记录退一
	{
		//$EmptyH=$NowY-$PreY+1;   //如果刚好到了275-277之间,则满行了,选择不换页
		$EmptyH=$MaxContainY-$NowY;
		//$pdf->SetX($PreY);
		$pdf->SetFillColor(255,255,255);
		//$pdf->Rect($CurMargin-1,$PreY,196,$EmptyH,"F");  //第一框
		//$pdf->SetXY($CurMargin,$PreY);  //画空白格补充，如果超过一行的话
		//$EmptyH=$MaxContainY-$PreY;   //并加所有空行  $MaxContainY=277
		for($EveryH=$RowsHight;$EveryH<$EmptyH;$EveryH=$EveryH+$RowsHight)
		{
			$pdf->SetX($CurMargin);
			$pdf->htmltable($eurEmptyField);
		}
		/*
		$eurEmpty="<table  border=1 ><tr >
		<td width=195 align=center height=$EmptyH valign=middle style=bold>Continue...</td>
		</tr></table>" ;//$eurTableList;
		$pdf->htmltable($eurEmpty);
		*/
		//$pi=$pi-1; //此记录得输出，回到上一条

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
		$TotalH=$NowY-$oldPreY;  //获取决统计的高度

		$EmptyH=$NowY-$oldPreY+$TotalH+1;   //连统计一起清除
		//$pdf->SetX($PreY);
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
		if(($$eurTableNo)!=""){   //输出表的内容。

				$NowSY=$pdf->GetY(); //
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+3.5,"Notes:");

				if ($special_Pic!=""){
					$pdf->Image($special_Pic,$CurMargin+1,$NowSY+4,131,10,"JPG");

				}

				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小

			$tmp_Total="<table  border=1 >
				 <tr>
				<td  width=138 rowspan='4' align='left' valign='top'>&nbsp;<br>$Commoditycode$StableNote$Notes </td>
				<td  width=38 bgcolor='#999999' >SUBTOTAL</td>
				<td  width=19 align='right'>$Total</td>
			  </tr>
			  <tr>
				<td width=38 bgcolor='#999999'>DELIVERY COST</td>
				<td width=19 align='right'></td>
			  </tr>
			  <tr>
				<td width=38 bgcolor='#999999'>VAT</td>
				<td width=19 align='right'></td>
			  </tr>
			  <tr>
				<td  width=38 bgcolor='#999999'>TOTAL</td>
				<td  width=19 align='right'>$Total</td>
			  </tr>
			</table>";
			$pdf->htmltable($tmp_Total);

				$NowSY=$pdf->GetY();
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+3.5,"Terms:");
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
			$tmp_Terms="<table  border=1 >
			<tr>
				<td $colspan  $width  height=17  align='left' valign='top'>&nbsp;      <br>$PaymentTerm$Priceterm$Terms  </td>
			  </tr>
			</table>";
			$pdf->htmltable($tmp_Terms);
			 /*
				$NowSY=$pdf->GetY();
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+5,"$N_Currency");
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小

			$tmp_Currency="<table  border=1 >
				<tr  >
			  <td  $colspan  $width  height=8 align='left' valign=middle >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$Symbol</td>
			  </tr>
			</table>";
			$pdf->htmltable($tmp_Currency);
			*/
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+160,$NowSY+3.5,"$N_Currency");
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+175,$NowSY+3.5,"$Symbol");

				$NowSY=$pdf->GetY(); //
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+6,"BANK:");
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小

			$tmp_BANK="<table  border=1 >
			  <tr>
				 <td $colspan  $width  height=30  align='left' valign=middle >&nbsp;     <br>Beneficary: $Beneficary<br>Bank         : $Bank<br>Bank Add : $BankAdd<br>Swift ID    : $SwiftID<br>A/C NO    : $ACNO</td>
			  </tr>
			</table>";
			$pdf->htmltable($tmp_BANK);



			//$pdf->htmltable($$eurTableNo);
		}
		$sealX=$pdf->GetX();
		$sealY=$pdf->GetY();
		$isSeal=1;
		$addSign=0; //输出最后一页的页脚
	}


	if($addSign==0)
	{
		include "invoicetopdf/invoicepublicFooter.php";  //页尾


	}
	if($isSeal==1){
		//$sealX=$pdf->GetX()+130;
		//$sealY=$pdf->GetY()-36;   //34
		include "invoicetopdf/invoicepublicSeal.php";  //封印
	 }
}


/*
$mc_officialseal="../images/officialseal$E_SealType.jpg";
$pdf->Image($mc_officialseal,$pdf->GetX()+130,$pdf->GetY()-54,40,40,"JPG");
*/
/*
//packinglist
if($eurplList!=""){
	$pdf->AddPage();
	$pdf->FloorSign=0;
$pdf->SetFont('Arial','',35);
$pdf->Cell(0,20,"Packing List",0,1,"C");
$pdf->Setxy(10,25);
//$mc_Logo="../images/mclogo.jpg";
//$pdf->Image($mc_Logo,20,5,28,44,"JPG");
$pdf->SetY(50);

$pdf->SetFont('Arial','',$InvoiceHeadFontSize);
	$pdf->Cell(0,$RowsHight,"Seller        : $E_Company",0,1,"L");
	$pdf->Cell(0,$RowsHight,"Fax           : $E_Fax",0,1,"L");
	$pdf->Cell(62,$RowsHight,"Sold To     : $SoldTo",0,0,"L");
	$pdf->SetTextColor(0,0,255);
	$pdf->Cell(0,$RowsHight,"",0,1,"C");
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(0,$RowsHight,"Address    : $ToAddress",0,1,"L");
	$pdf->Cell(143,$RowsHight,"Fax NO     : $FaxNo",0,0,"L");
	$pdf->SetTextColor(0,0,255);
	$pdf->Cell(80,$RowsHight,"Invoice# : $InvoiceNO",0,1,"L");
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(143,$RowsHight,"Delivery    :",0,0,"L");
		$pdf->Cell(80,$RowsHight,"Date       : $Date",0,1,"L");
		$pdf->Cell(20,$RowsHight,"Price term : ",0,0,"L");
		$pdf->SetTextColor(255,0,0);
		$pdf->Cell(123,$RowsHight,$Wise,0,0,"C");
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(60,$RowsHight,"Currency U.S.DOLLARS",0,1,"L");
$pdf->SetFont('Arial','B',$TableFontSize);
		$eurplList="<table border=1 ><tr bgcolor=#CCCCCC repeat>
			<td width=20 align=center height=$RowsHight valign=middle style=bold>No.</td>
			<td width=90 align=center valign=middle style=bold>Item</td>
		<td width=20 align=center valign=middle style=bold>Unit/<br>carton</td>
		<td width=21 align=center valign=middle style=bold>Quantity</td>
		<td width=20 align=center valign=middle style=bold>NW(KG)</td>
		<td width=20 align=center valign=middle style=bold>GW(KG)</td>
		</tr></table>".$eurplList;
	$pdf->htmltable($eurplList);
$pdf->SetFont('Arial','',$InvoiceHeadFontSize);
$pdf->Cell(0,2,"",0,1,"L");
	$pdf->Cell(0,4,"Beneficary: $Beneficary",0,1,"L");
	$pdf->Cell(0,4,"Bank         : $Bank",0,1,"L");
	$pdf->Cell(0,4,"Bank Add : $BankAdd",0,1,"L");
	$pdf->Cell(0,4,"Swift ID    : $SwiftID",0,1,"L");
	$pdf->Cell(0,4,"A/C NO    : $ACNO",0,1,"L");
//$mc_officialseal="../images/officialseal.jpg";
//$pdf->Image($mc_officialseal,$pdf->GetX()+120,$pdf->GetY()-20,40,40,"JPG");
	}
*/
//空白行

$eurEmptyField="<table  border=1 ><tr>
<td width=16  height=$RowsHight valign=middle align=center ></td>
<td width=25 valign=middle></td>
<td width=35 valign=middle></td>
<td width=38 valign=middle></td>
<td width=15 valign=middle align=right></td>
<td width=25 valign=middle align=right></td>
<td width=15 valign=middle align=right ></td>
<td width=13 valign=middle align=right ></td>
<td width=13 valign=middle align=right ></td>
</tr></table>" ;//$eurTableList;


$pageNo=0;
for ($pi=1;$pi<=$plCounts;$pi=$pi+1){  //循环输出
	//$eurplNo="eurplNo".strval($pi);   //每一条记录都是一个表格
	$eurplNo="plTableNo".strval($pi);
     if($addSign==0){  //新加一页
		$pdf->AddPage();
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;
		//$CurMargin=5;  //左边距
		//$PackList="Packing List";
		$Com_Pack_PI="Packing List";
		include "invoicetopdf/invoicepublicHeader.php";  //页头

		{
			//$eurTableList="<table  border=1 >
			$pdf->SetXY($CurMargin,$ConTableBegin_Y);   //第一个表modify by zx 2011-0129 $ConTableBegin_Y值在 invoicepublicHeader.php     $pdf->SetXY($CurMargin,72);
			$eurTableHeader="<table border=1 >
			<tr >
			<td width=20 bgcolor=#CCCCCC align=center height=$RowsHight valign=middle style=bold>Invoice NO.:</td>	
			<td width=30 align=center valign=middle style=bold>$InvoiceNO</td>
			<td width=20  bgcolor=#CCCCCC align=center valign=middle style=bold>Forwarder:</td>			
			<td width=30 align=center valign=middle style=bold>$Wise</td>	
			<td width=24  bgcolor=#CCCCCC align=center valign=middle style=bold>Requisition by:</td>	
			<td width=39 align=center valign=middle style=bold>$Nickname</td>
			<td width=16  bgcolor=#CCCCCC align=center valign=middle style=bold>Page NO.:</td>
			<td width=16 align=center valign=middle style=bold>$pageNo</td>
			</tr>
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurTableHeader);

		}


		$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY($CurMargin,$pdf->GetY());  //77

		//表标题
		{
			$eurTableField="<table border=1 ><tr bgcolor=#CCCCCC repeat> 
			<td width=16 align=center height=$RowsHight valign=middle style=bold>No.</td>
			<td width=25 align=center valign=middle style=bold>PO#</td>
			<td width=35 align=center valign=middle style=bold>Product Code</td>
			<td width=38 align=center valign=middle style=bold>Product Description</td>
			<td width=15 align=center valign=middle style=bold>Unit / Carton</td>	
			<td width=25 align=center valign=middle style=bold>Carton Size (CM)</td>
			<td width=15 align=center valign=middle style=bold>Quantity</td>
			<td width=13 align=center valign=middle style=bold> NW (KG)</td>
			<td width=13 align=center valign=middle style=bold> GW (KG)</td></tr></table>" ;//$eurTableList;
			$pdf->htmltable($eurTableField);

		}

		$HeaerBY=$pdf->GetY();//获取固定Y位置;
		$PreY=$HeaerBY;
		$addSign=1;
	 }
		//if($eurTableList!=""){
	$pdf->SetX($CurMargin);  //
	if(($$eurplNo)!=""){   //输出表的内容。
		$pdf->htmltable($$eurplNo);
	}
	$NowY=$pdf->GetY();  //取得当前的Y的位置

	//$pdf->Text(0,$NowY,"$NowY");  //输出当前值
	$tmpH=0;
	if($pi<$plCounts){
		$pdftmp->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		$pdftmp->SetXY(1,1);
		$tmpNo="plTableNo".strval($pi+1);
		$pdftmp->htmltable($$tmpNo);
		$tmpY=$pdftmp->GetY();
		$tmpH=$tmpY-1; //
		//$pdf->Text(200,$NowY,"$tmpH");  //输出当前值

	}




	/*
	if($NowY>($MaxContainY+1)) //>278 //说明已超过了，1、那得把此行去掉.2、用空白行275-$PreY 画一空表，并标示continue，3、同时记录退一
	{
		$EmptyH=$NowY-$PreY+1;   //如果刚好到了275-277之间,则满行了,选择不换页
		//$pdf->SetX($PreY);
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($CurMargin-1,$PreY,196,$EmptyH,"F");  //第一框
		$pdf->SetXY($CurMargin,$PreY);  //画空白格补充，如果超过一行的话
		$EmptyH=$MaxContainY-$PreY;   //并加所有空行  $MaxContainY=277
		for($EveryH=$RowsHight;$EveryH<$EmptyH;$EveryH=$EveryH+$RowsHight)
		{
			$pdf->SetX($CurMargin);
			$pdf->htmltable($eurEmptyField);
		}

		$pi=$pi-1; //此记录得输出，回到上一条
		$addSign=0; //准备重新分页
		$PreY=0; //前一个Y得新置为0。
	}
	else{
		$oldPreY=$PreY;  //用于最后一页
		$PreY=$NowY; //前一个Y位置
	}
	*/

	if(($NowY+$tmpH)>($MaxContainY+1)) //>278 //说明已超过了，1、那得把此行去掉.2、用空白行275-$PreY 画一空表，并标示continue，3、同时记录退一
	{
		//$EmptyH=$NowY-$PreY+1;   //如果刚好到了275-277之间,则满行了,选择不换页
		$EmptyH=$MaxContainY-$NowY;
		//$pdf->SetX($PreY);
		$pdf->SetFillColor(255,255,255);
		//$pdf->Rect($CurMargin-1,$PreY,196,$EmptyH,"F");  //第一框
		//$pdf->SetXY($CurMargin,$PreY);  //画空白格补充，如果超过一行的话
		//$EmptyH=$MaxContainY-$PreY;   //并加所有空行  $MaxContainY=277
		for($EveryH=$RowsHight;$EveryH<$EmptyH;$EveryH=$EveryH+$RowsHight)
		{
			$pdf->SetX($CurMargin);
			$pdf->htmltable($eurEmptyField);
		}
		/*
		$eurEmpty="<table  border=1 ><tr >
		<td width=195 align=center height=$EmptyH valign=middle style=bold>Continue...</td>
		</tr></table>" ;//$eurTableList;
		$pdf->htmltable($eurEmpty);
		*/
		//$pi=$pi-1; //此记录得输出，回到上一条

		$addSign=0; //准备重新分页
		$PreY=0; //前一个Y得新置为0。

	}
	else{
		$oldPreY=$PreY;  //用于最后一页
		$PreY=$NowY; //前一个Y位置
	}
	$isSeal=0;
	if($pi==$plCounts) //说明最后一条记录情况,有了统计，则要计算统计的高度。
	{
		$TotalH=$NowY-$oldPreY;  //获取决统计的高度

		$EmptyH=$NowY-$oldPreY+$TotalH+1;   //连统计一起清除
		//$pdf->SetX($PreY);
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
		if(($$eurTableNo)!=""){   //输出表的内容。
			$pdf->htmltable($$eurplNo);
		}
		$sealX=$pdf->GetX();
		$sealY=$pdf->GetY();
		$isSeal=1;
		$addSign=0; //输出最后一页的页脚
	}


	if($addSign==0)
	{
		include "invoicetopdf/invoicepublicFooter.php";  //页尾

	}
	/*
	$pdf->SetFont('Arial','',$InvoiceHeadFontSize);
	$pdf->Cell(0,2,"",0,1,"L");
		$pdf->Cell(0,4,"Beneficary: $Beneficary",0,1,"L");
		$pdf->Cell(0,4,"Bank         : $Bank",0,1,"L");
		$pdf->Cell(0,4,"Bank Add : $BankAdd",0,1,"L");
		$pdf->Cell(0,4,"Swift ID    : $SwiftID",0,1,"L");
		$pdf->Cell(0,4,"A/C NO    : $ACNO",0,1,"L");
	*/
	if($isSeal==1){
		//$sealX=$pdf->GetX()+130;
		//$sealY=$pdf->GetY()-36;
		include "invoicetopdf/invoicepublicSeal.php";  //封印
	}
}

/*
$mc_officialseal="../images/officialseal$E_SealType.jpg";  //Type:S简体E英C繁体
$pdf->Image($mc_officialseal,$pdf->GetX()+130,$pdf->GetY()-52,40,40,"JPG");
*/
$pdf->Output("$filename","F");
?>