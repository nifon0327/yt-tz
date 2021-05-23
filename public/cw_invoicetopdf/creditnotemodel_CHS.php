<?php 
$TableFontSize=8;
define('FPDF_FONTPATH','fpdf/font/');
//include "fpdf/pdftable.inc.php";		//更新
require('fpdf/chinese-unicode.php'); 
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

$DateMDY=date("Y-m-d");
//$pdftmp->AddUniGBhwFont('uGB'); 
for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //循环输出
	$eurTableNo="eurTableNo".strval($pi);
     if($addSign==0){  //新加一页
		$pdf->AddPage();		
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;		
		
		$Invoice_PI="$InvoiceNO";
		$Com_Pack_PI="DEBIT NOTE";
		if($ShipType=="credit"){   //分为DEBIT 和 CREDIT
			$Com_Pack_PI="CREDIT NOTE";
		}
		
		include "invoicetopdf/invoicepublicHeaderS.php";  //页头
		{
			//$eurTableList="<table  border=1 >
			//$pdf->SetXY($CurMargin,72);   //第一个表
			$pdf->SetXY($CurMargin,$ConTableBegin_Y);   //第一个表modify by zx 2011-0129 $ConTableBegin_Y值在 invoicepublicHeader.php     $pdf->SetXY($CurMargin,72);  
			$eurTableHeader="<table border=1 >
			<tr >
			<td width=20 bgcolor=#CCCCCC align=center height=$RowsHight valign=middle style=bold>NO.:</td>	
			<td width=30 align=center valign=middle style=bold>$InvoiceNO</td>
			<td width=20  bgcolor=#CCCCCC align=center valign=middle style=bold>出货方式:</td>			
			<td width=30 align=center valign=middle style=bold>$Wise</td>	
			<td width=24  bgcolor=#CCCCCC align=center valign=middle style=bold>联系人:</td>	
			<td width=39 align=center valign=middle style=bold color=#06F>$Nickname</td>
			<td width=16  bgcolor=#CCCCCC align=center valign=middle style=bold>页号:</td>
			<td width=16 align=center valign=middle style=bold>$pageNo</td>
			</tr>
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurTableHeader);
			
		}		
		$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($CurMargin,$pdf->GetY());  //77
		
		//表标题
		{
			
			$pdf->htmltable($eurTableField);
			
		}
		$HeaerBY=$pdf->GetY();//获取固定Y位置;
		$PreY=$HeaerBY;
		$addSign=1;
	
	}   //增加一页后
	
	//if($eurTableList!=""){
	$pdf->SetX($CurMargin);  //	
	if(($$eurTableNo)!=""){   //输出表的内容。	
		$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->htmltable($$eurTableNo);
	}
	$NowY=$pdf->GetY();  //取得当前的Y的位置
	//$pdf->Text(0,$NowY,"$NowY");  //输出当前值
	//取得下一个的高度，
	$tmpH=0; 
	if($pi<$Counts){
		$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		$pdftmp->SetXY(1,1);
		$tmpNo="eurTableNo".strval($pi+1);
		$pdftmp->htmltable($$tmpNo);
		$tmpY=$pdftmp->GetY(); 
		$tmpH=$tmpY-1; //
		//$pdf->Text(200,$NowY,"$tmpH");  //输出当前值
		
	}
	
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
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小	
			$tmp_Total="<table  border=1 >
				<tr bgcolor=#CCCCCC>
				<td width=15 height=$RowsHight valign=middle style=bold>合计</td>
				<td width=30></td>
				<td width=93></td>
				<td width=19 align=right valign=middle style=bold>$chSUMQty</td>
				<td width=19></td>
				<td width=19 align=right valign=middle style=bold>$Total</td>
				</tr>
			</table>";				
			$pdf->htmltable($tmp_Total);

				$NowSY=$pdf->GetY();
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+3.5,"Notes:"); 
				$pdf->SetXY($CurMargin,$NowSY);				
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
			$colspan="";
			$width="width=195";	
			$tmp_Notes="<table  border=1 >	
			<tr>
			<td $colspan  $width  height=12  align='left' valign='top'>&nbsp;<br>$Commoditycode$StableNote$Notes</td>
			</tr>
			</table>";				
			$pdf->htmltable($tmp_Notes);


				$NowSY=$pdf->GetY();
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+3.5,"Terms:"); 
				$pdf->SetXY($CurMargin,$NowSY);				
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
			$tmp_Terms="<table  border=1 >
			<tr>
				<td $colspan  $width  height=17  align='left' valign='top'>&nbsp;      <br>$PaymentTerm$Priceterm$Terms  </td>
			  </tr>
			</table>";				
			$pdf->htmltable($tmp_Terms);
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+160,$NowSY+3.5,"币  种:"); 
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+175,$NowSY+3.5,"$Symbol"); 
				
				$NowSY=$pdf->GetY(); // 
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+6,"BANK:"); 
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
			$tmp_BANK="<table  border=1 >
			  <tr>
				 <td $colspan  $width  height=30  align='left' valign=middle >&nbsp;     <br>Beneficary: $Beneficary<br>Bank         : $Bank<br>Bank Add : $BankAdd<br>Swift ID    : $SwiftID<br>A/C NO    : $ACNO</td>
			  </tr>
			</table>";					
			$pdf->htmltable($tmp_BANK);					
			$sealX=$pdf->GetX();
			$sealY=$pdf->GetY(); 			
			//$pdf->htmltable($$eurTableNo);
		}
       
		
		$addSign=0; //输出最后一页的页脚
	}
	
	
	if($addSign==0)		
	{
		include "invoicetopdf/invoicepublicFooterS.php";  //页尾  		
	}		
}

include "invoicetopdf/invoicepublicSeal.php";  //封印  

?>