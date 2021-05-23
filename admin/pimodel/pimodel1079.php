<?php   
$TableFontSize=8;
define('FPDF_FONTPATH','../plugins/fpdf/font/');
include "../plugins/fpdf/pdftable.inc.php";		//更新
$pdf=new PDFTable(); 
$pdf->SetAutoPageBreak("off",5);		//分页下边距
$pdf->SetMargins(10,10,10);			//上左右边距
$pdf->Open(); 
$addSign=0; //是否要重新分页,
$PreY=0;   //上一次的位置
$oldPreY=0; //保存的上一次位置，主要是最后一页使用
$MaxContainY=277;  //每一页主内容的最下面的位置
$CurMargin=8;  //左边距
//空白行

$pdftmp=new PDFTable(); 
$pdftmp->SetAutoPageBreak("off",5);		//分页下边距
$pdftmp->SetMargins(10,10,10);			//上左右边距
$pdftmp->Open(); 
$pdftmp->AddPage();

$pageNo=0;
$DateMDY=date("d-M-y"); // date("m/d/Y");
for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //循环输出
	$eurTableNo="eurTableNo".strval($pi);
	$RemarkTableNo="RemarkTableNo".strval($pi);
     if($addSign==0){  //新加一页
		$pdf->AddPage();		
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;		
		$Invoice_PI="$PI";
		$Com_Pack_PI="Proforma Invoice";
		include "pimodel/pimodelHeader1079.php";  //页头
		
		{
			$pieces = explode("#", $OrderPOs);  //获取所有PO
			$pdf->SetXY($CurMargin,$ConTableBegin_Y);   //第一个9 $ConTableBegin_Y值在 invoicepublicHeader.php     $pdf->SetXY($CurMargin,72); 
			$eurTableHeader="<table border=1 >
			<tr >
			<td width=15 bgcolor=#CCCCCC align=center height=$RowsHight valign=middle style=bold>PO:</td>	
			<td width=46 align=left valign=middle style=bold>$pieces[1]</td>
			<td width=15 bgcolor=#CCCCCC align=center valign=middle style=bold>PI:</td>			
			<td width=37 align=left valign=middle  style=bold>$PI</td>	
			<td width=32  bgcolor=#CCCCCC align=center valign=middle style=bold>Requisition by:</td>	
			<td width=22 align=center valign=middle style=bold>$Nickname</td>
			<td width=16  bgcolor=#CCCCCC align=center valign=middle style=bold>Page NO.:</td>
			<td width=12 align=center valign=middle style=bold>$pageNo</td>
			</tr>
			</table>" ;//$eurTableList;color=#55BFFF
			$pdf->htmltable($eurTableHeader);
			
		}	
		
		$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
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
	
	$pdf->SetX($CurMargin);  //
	if(($$eurTableNo)!=""){
	     $pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
         $pdf->htmltable($$eurTableNo);}
    $NowY=$pdf->GetY();  //取得当前的Y的位置
	
	$pdf->SetX($CurMargin); 
	if(($$RemarkTableNo)!=""){
		 $pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		 $pdf->htmltable($$RemarkTableNo);
		// $pdf->Text(0,$NowY,"$CompanyId");
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
		//$pdf->Text(200,$NowY,"$tmpH");  //输出当前值	
	   }
	
	if(($NowY+$tmpH)>($MaxContainY+1)) //>278 //说明已超过了，1、那得把此行去掉.2、用空白行275-$PreY 画一空表，并标示continue，3、同时记录退一
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
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小				
			    $pdf->htmltable($tmp_Total);
			    $addsplit=0;
				if($CompanyId==1058){
					$NowSY=$pdf->GetY(); //
					$pdf->SetXY($CurMargin,$NowSY);
					$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小				
					$pdf->htmltable($tmp_1058);	
					
					$NowSY=$pdf->GetY(); //
					$pdf->SetXY($CurMargin,$NowSY);
					$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
					 $addsplit=2;	
				}

				$NowSY=$pdf->GetY()+$addsplit;
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+3.5,"Notes:"); 
				$pdf->SetXY($CurMargin,$NowSY);				
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小 
			        $pdf->htmltable($tmp_Notes);
                                if ($OtherNotes!=""){
                                    $pdf->Text($CurMargin+155,$NowSY+3.5,"$OtherNotes");
                                }

				$NowSY=$pdf->GetY();
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+3.5,"Terms:"); 
				$pdf->SetXY($CurMargin,$NowSY);				
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
			$pdf->htmltable($tmp_Terms);
			  
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+155,$NowSY+3.5,"Currency:"); 
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+170,$NowSY+3.5,"$Symbol"); 
				
				$NowSY=$pdf->GetY(); // 
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+6,"BANK:"); 
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
			$pdf->htmltable($tmp_BANK);					
			$sealX=$pdf->GetX();
			$sealY=$pdf->GetY(); 			
		 }
       		
		$addSign=0; //输出最后一页的页脚
	}
	
	
	if($addSign==0)		
	   {
		include "invoicetopdf/invoicepublicFooter.php";  //页尾  	
	   }		
}

include "invoicetopdf/invoicepublicSeal.php";  //封印  

?>