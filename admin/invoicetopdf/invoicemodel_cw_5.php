<?php   
//EUR专用模板  //二合一已更新
$TableFontSize=8;
//define('FPDF_FONTPATH','../plugins/fpdf/font/');
//include "../plugins/fpdf/pdftable.inc.php";		//更新
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
<td width=70 align=center valign=middle style=bold>  </td>			
<td width=19 align=center valign=middle style=bold>  </td>	
<td width=19 align=center valign=middle style=bold> </td>	
<td width=19 align=center valign=middle style=bold> </td></tr></table>" ;//$eurTableList;
$pageNo=0;
$DateMDY=date("d-M-y"); // date("m/d/Y");
//$Date=date("d-M-y");
for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //循环输出
	$eurTableNo="eurTableNo".strval($pi);
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
			<td width=70 align=center valign=middle style=bold>Description</td>			
			<td width=19 align=center valign=middle style=bold>Quantity</td>	
			<td width=19 align=center valign=middle style=bold>Unit Price</td>	
			<td width=19 align=center valign=middle style=bold>Amount</td></tr></table>" ;//$eurTableList;
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
		$tmpNo="eurTableNo".strval($pi+1);
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
				
				if (($special_Pic!="") && ($Notes=="")){
					$pdf->Image($special_Pic,$CurMargin+1,$NowSY+4,131,10,"JPG");	
					
				}	
				
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小				
			$pdf->htmltable($tmp_Total);
				
				$NowSY=$pdf->GetY();
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+3.5,"Terms:"); 
				$pdf->SetXY($CurMargin,$NowSY);				
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
			$pdf->htmltable($tmp_Terms);
			    /*
				$NowSY=$pdf->GetY(); 
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+5,"$N_Currency"); 
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
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
		//$sealY=$pdf->GetY()-36;
		//$pdf->Text(0,$sealY,"1:$sealY");  //输出当前值		
		include "invoicetopdf/invoicepublicSeal.php";  //封印
	 }
}
?>