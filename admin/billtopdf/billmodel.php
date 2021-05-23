<?php   
//电信-joseph
$TableFontSize=8;
define('FPDF_FONTPATH','../plugins/fpdf/font/');
include "../plugins/fpdf/pdftable.inc.php";		//更新
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

//*******************************************************************************Bill OF Delivery
//空白行
$eurEmptyField="<table  border=1 >
<tr >
<td width=8 align=center height=$RowsHight vglign=middle style=bolde></td>
<td width=18 align=center valign=middle style=bold> </td>	
<td width=18 valign=middle>  </td>
<td width=32 align=center valign=middle style=bold></td>
<td width=60 align=center valign=middle style=bold></td>			
<td width=20 align=center valign=middle style=bold></td>	
<td width=12 align=center valign=middle style=bold></td>
<td width=12 align=center valign=middle style=bold></td>
<td width=15 align=center valign=middle style=bold></td>
</tr></table>" ;//$eurTableList;
$pageNo=0;
$DateMDY=date("d-M-y"); 
for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //循环输出
	$eurTableNo="eurTableNo".strval($pi);
     if($addSign==0){  //新加一页
		$pdf->AddPage();		
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;		
		$Com_Pack_PI="Bill Of Delivery";
		include "billpublicHeader.php";  //页头	
		$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->SetTextColor(0,0,0);
		$DDDD=$pdf->GetY();
		$pdf->SetXY($CurMargin,$pdf->GetY()+2);  //77		
		//***************************************************************表标题
		{
			$eurTableField="<table  border=1 >
			<tr bgcolor=#CCCCCC repeat>
			<td width=8 align=center valign=middle style=bold height=$RowsHight>Ln.</td>
			<td width=18 align=center valign=middle style=bold>InvoiceNo</td>
			<td width=18 align=center valign=middle style=bold>PO#</td>			
			<td width=32 align=center valign=middle style=bold>Product Code</td>
			<td width=60 align=center valign=middle style=bold>Description</td>				
			<td width=20 align=center valign=middle style=bold>H.S.</td>	
			<td width=12 align=center valign=middle style=bold>Qty</td>
			<td width=12 align=center valign=middle style=bold>Price</td>
			<td width=15 align=center valign=middle style=bold>Amount</td>
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
		$pdftmp->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		$pdftmp->SetXY(1,1);
		$tmpNo="eurTableNo".strval($pi+1);
		$pdftmp->htmltable($$tmpNo);
		$tmpY=$pdftmp->GetY(); 
		$tmpH=$tmpY-1; //
		
	}	

	if(($NowY+$tmpH)>($MaxContainY+1)) //>278 说明已超过1、那得把此行去掉.2、用空白行275-$PreY 画一空表，并标示continue，3、同时记录退一
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
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
				//$pdf->Text(0,$CurMargin,"$CurMargin");
			    $pdf->htmltable($$eurTableNo);

		}
		
		$sealX=$pdf->GetX();
		$sealY=$pdf->GetY();
 		$isSeal=1;
		$addSign=0; //输出最后一页的页脚
	}
	
	if($addSign==0)		
	{
		include "billpublicFooter.php";  //页尾  	
	}
	if($isSeal==1){
		include "invoicetopdf/invoicepublicSeal.php";  //封印	
	 }
}


//*******************************************************************************PackingList

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
</tr></table>" ;

$pageNo=0;
for ($pi=1;$pi<=$plCounts;$pi=$pi+1){  //循环输出
	$eurplNo="plTableNo".strval($pi); 
     if($addSign==0){  //新加一页
		$pdf->AddPage();		
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;
		$Com_Pack_PI="Packing List";
		include "invoicetopdf/invoicepublicHeader.php";  //页头

		{
			$pdf->SetXY($CurMargin,$ConTableBegin_Y);   //第一个表$ConTableBegin_Y值在 invoicepublicHeader.php     $pdf->SetXY($CurMargin,72);  
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
	$pdf->SetX($CurMargin);  //	
	if(($$eurplNo)!=""){   //输出表的内容。	
		$pdf->htmltable($$eurplNo);
	}
	$NowY=$pdf->GetY();  //取得当前的Y的位置
	$tmpH=0; 
	if($pi<$plCounts){
		$pdftmp->SetXY(1,1);
		$tmpNo="plTableNo".strval($pi+1);
		$pdftmp->htmltable($$tmpNo);
		$tmpY=$pdftmp->GetY(); 
		$tmpH=$tmpY-1; //	
	}
	

	if(($NowY+$tmpH)>($MaxContainY+1)) //>278 说明已超过1、那得把此行去掉.2、用空白行275-$PreY 画一空表，并标示continue，3、同时记录退一
	{
		//$EmptyH=$NowY-$PreY+1;   //如果刚好到了275-277之间,则满行了,选择不换页
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
	if($pi==$plCounts) //说明最后一条记录情况,有了统计，则要计算统计的高度。
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
		if(($$eurplNo)!=""){   //输出表的内容。
		    //$pdf->Text(0,$CurMargin,"$CurMargin");
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
	if($isSeal==1){
		include "invoicetopdf/invoicepublicSeal.php";  //封印	
	}
}


?>