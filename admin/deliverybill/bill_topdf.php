<?php   
$TableFontSize=8;
define('FPDF_FONTPATH','../plugins/fpdf/font/');
require('../plugins/fpdf/chinese-unicode.php'); 
$pdf=new PDF_Unicode(); 
$pdf->SetAutoPageBreak("on",5);		//分页下边距
$pdf->SetMargins(10,10,10);			//上左右边距
$pdf->Open(); 
$addSign=0; //是否要重新分页,
$PreY=0;   //上一次的位置
$oldPreY=0; //保存的上一次位置，主要是最后一页使用
$MaxContainY=150;  //每一页主内容的最下面的位置
$CurMargin=8;  //左边距
$pdftmp=new PDF_Unicode(); 
$pdftmp->SetAutoPageBreak("off",5);		//分页下边距
$pdftmp->SetMargins(10,10,10);			//上左右边距
$pdftmp->Open(); 
$pdftmp->AddPage();
$pdftmp->AddUniGBhwFont('uGB'); 


//空白行
$eurEmptyField="<table  border=1 >
<tr >
<td width=8 align=center height=$RowsHight valign=middle style=bold> </td>	
<td width=20 valign=middle>  </td>
<td width=30 align=center valign=middle style=bold>  </td>
<td width=60 align=center valign=middle style=bold>  </td>			
<td width=12 align=center valign=middle style=bold>  </td>	
<td width=65 align=center valign=middle style=bold> </td></tr></table>" ;//$eurTableList;
$pageNo=0;
$DateMDY=date("Y-m-d"); 
$pdf->AddUniGBhwFont('uGB'); 
for ($pi=0;$pi<=$Counts;$pi=$pi+1){  //循环输出
	$eurTableNo="eurTableNo".strval($pi);
     if($addSign==0){  //新加一页
		$pdf->AddPage();
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;		
		$Com_Pack_PI="送 货 单";
       $Invoice_PI=$InvoiceNO;
	    include "bill_header.php";  //页头
		$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY($CurMargin,$CurH);  
		{
			$eurTableField="<table  border=1 >
			<tr bgcolor=#CCCCCC repeat>
			<td width=8 align=center height=$RowsHight valign=middle style=bold></td>
			<td width=20 align=center valign=middle style=bold>PO#</td>			
			<td width=30 align=center valign=middle style=bold>产品代码</td>
			<td width=60 align=center valign=middle style=bold>中文名称</td>			
			<td width=12 align=center valign=middle style=bold>数量</td>	
			<td width=65  valign=middle style=bold>备注</td></tr></table>" ;
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
	
	$tmpH=0; 
	if($pi<$Counts){
		$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
		$pdftmp->SetXY(1,1);
		$tmpNo="eurTableNo".strval($pi+1);
		$pdftmp->htmltable($$tmpNo);
		$tmpY=$pdftmp->GetY(); 
		$tmpH=$tmpY-1; //
		
	}	
	
	if(($NowY+$tmpH)>($MaxContainY+1)) //>278 //说明已超过了,那得把此行去掉,用空白行275-$PreY 画一空表，并标示continue,同时记录退
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
	    $isSeal=1;
		$addSign=0; //输出最后一页的页脚*/
	}
}
   $NowSY=$pdf->GetY();
    $pdf->Text($CurMargin+0.4,$NowSY+2.6,"备注:"); 
    $pdf->SetXY($CurMargin,$NowSY);				
	$pdf->SetFont('uGB','',$InvoiceHeadFontSize); //设回字体大小
	$tmp_Terms="<table  border=1 ><tr><td  width='195'  height=25  align='left' valign='top'><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$Remark</td></tr></table>";	
	$pdf->htmltable($tmp_Terms);
    $pdf->Text($CurMargin+20,$NowSY+36,"收货人:"); 
    $pdf->Text($CurMargin+130,$NowSY+36,"送货人:刘明洪"); 
?>