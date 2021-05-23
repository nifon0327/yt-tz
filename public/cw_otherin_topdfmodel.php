<?php 
$TableFontSize=8;
define('FPDF_FONTPATH','../plugins/fpdf/font/');
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

//*******************************************************************************Bill OF Delivery
//空白行
$eurEmptyField="<table  border=1 >
<tr >
<td width=10 align=center height=$RowsHight vglign=middle style=bolde></td>
<td width=20 align=center valign=middle style=bold></td>
<td width=120 align=center valign=middle style=bold></td>			
<td width=27 align=center valign=middle style=bold></td>	
<td width=18 align=center valign=middle style=bold></td></tr></table>" ;
$pageNo=0;
$DateMDY=date("d-M-y"); 
$pdf->AddUniGBhwFont('uGB');
for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //循环输出
	$eurTableNo="eurTableNo".strval($pi);
     if($addSign==0){  //新加一页
		$pdf->AddPage();		
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;		
		$Com_Pack_PI="收款单";
		include "cw_otherin_topdfHeader.php";  //页头	
		
		{
			$pdf->SetXY($CurMargin,$ConTableBegin_Y);   //第一个表 $ConTableBegin_Y值在 invoicepublicHeader.php     $pdf->SetXY($CurMargin,72);  
			$eurTableHeader="<table border=1 >
			<tr >
			<td width=20 bgcolor=#CCCCCC align=center height=$RowsHight valign=middle style=bold>收款日期</td>	
			<td width=40 align=center valign=middle style=bold>$PayDate</td>
			<td width=20 bgcolor=#CCCCCC align=center valign=middle style=bold>操作人</td>			
			<td width=35 align=center valign=middle style=bold>$Operator</td>	
			<td width=20 bgcolor=#CCCCCC align=center valign=middle style=bold>币种</td>	
			<td width=20 align=center valign=middle style=bold color=#06F>$CurrencyName</td>
			<td width=20 bgcolor=#CCCCCC align=center valign=middle style=bold>页数:</td>
			<td width=20 align=center valign=middle style=bold>$pageNo</td>
			</tr>
			</table>" ;//$eurTableList;
			$pdf->htmltable($eurTableHeader);
			
		}		
		
		$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY($CurMargin,$pdf->GetY());  //77		
		//***************************************************************表标题
		{
			$eurTableField="<table  border=1 >
			<tr bgcolor=#CCCCCC repeat>
			<td width=10 align=center valign=middle style=bold height=$RowsHight>序号</td>
			<td width=20 align=center valign=middle style=bold>登记日期</td>
			<td width=120 align=center valign=middle style=bold>说明</td>			
			<td width=27 align=center valign=middle style=bold>收入类别</td>
			<td width=18 align=center valign=middle style=bold>金额</td>				
			</tr></table>" ;//$eurTableList;
			$pdf->htmltable($eurTableField);
			
		}
		
		$HeaerBY=$pdf->GetY();//获取固定Y位置;
		$PreY=$HeaerBY;
		
		$addSign=1;
	
	}   //增加一页后
	
	$pdf->SetX($CurMargin);  //	
	if(($$eurTableNo)!=""){   //输出表的内容。	
		$pdf->htmltable($$eurTableNo);
	}
	$NowY=$pdf->GetY();  //取得当前的Y的位置
	$tmpH=0; 
	if($pi<$Counts){
		$pdftmp->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		$pdftmp->SetXY(1,1);
		$tmpNo="eurTableNo".strval($pi+1);
		$pdftmp->htmltable($$tmpNo);
		$tmpY=$pdftmp->GetY(); 
		$tmpH=$tmpY-1; //
		
	}	

	if(($NowY+$tmpH)>($MaxContainY+1)) //>278 
	{
		$EmptyH=$MaxContainY-$NowY;
		$pdf->SetFillColor(255,255,255);
		for($EveryH=$RowsHight;$EveryH<$EmptyH;$EveryH=$EveryH+$RowsHight)
		{
			$pdf->SetX($CurMargin);
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
		$TotalH=$NowY-$oldPreY;  //获取决统计的高度		
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
		if(($$eurTableNo)!=""){   //输出表的内容。	

				$NowSY=$pdf->GetY();
				$pdf->SetXY($CurMargin,$NowSY);				
				$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
				//$pdf->Text(0,$CurMargin,"$CurMargin");
			    $pdf->htmltable($$eurTableNo);

		}
		//$pdf->Text($CurMargin+5,$NowSY+15,"$RemarkTableNo"); 
		$sealX=$pdf->GetX();
		$sealY=$pdf->GetY();
 		$isSeal=1;
		$addSign=0; //输出最后一页的页脚
		if($RemarkTableNo!=""){
		 $pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		 $pdf->SetXY($CurMargin+6,$NowSY+12);	
		 $pdf->htmltable($RemarkTableNo);}
	}
	
	if($addSign==0)		
	{
	    //$pdf->SetFont('Arial','',8);
		include "invoicetopdf/invoicepublicFooter.php";  //页尾   	
	}
	if($isSeal==1){
		include "cw_otherin_topdfSeal.php";  //封印	
	 }
}

?>