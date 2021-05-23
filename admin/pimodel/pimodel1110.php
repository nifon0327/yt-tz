<?php   
/*
已更新
*/



/*
$Date=date("d-M-y");
$TableFontSize=8;
define('FPDF_FONTPATH','../plugins/fpdf/font/');
include "../plugins/fpdf/pdftable.inc.php";
$pdf=new PDFTable(); 
$pdf->SetAutoPageBreak("on",10);
$pdf->SetMargins(10,10,10);
$pdf->Open(); 
$pdf->AddPage(); 
$pdf->FloorSign=0;

$CurMargin=8;  //左边距
$CurH=32;  //当前高度
$pdf->Rect($CurMargin,5,195,$CurH,"D");  //第一框
$pdf->SetFillColor(180,180,180);

$pdf->Setxy(6,6);
//$mc_Logo="../images/ASH.jpg";
//$pdf->Image($mc_Logo,8,15,50,4,"JPG"); 
$pdf->SetFont('Arial','B',22); //设回字体大小
$pdf->SetTextColor(255,0,0);
$pdf->Text($CurMargin+3,15,"Ash Cloud Co, Ltd. Shenzhen");  


$pdf->Rect(133,5,70,15,"FD");  //Order框
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','B',16); //设定Order的字体大小		
$pdf->Text(140,15,"Proforma Invoice");  

		$pdf->Rect(133,20,70,8,"D");  //Invoice NO框
		$pdf->SetTextColor(0,0,255);
		$pdf->SetFont('Arial','B',10); //设定Order的字体大小		
		$pdf->Text(136,25,"PI.:$PI"); 


$pdf->SetFont('Arial','',$TableFontSize); //设回字体大小
$pdf->SetTextColor(0,0,0);
$pdf->Text($CurMargin+2,26,"$E_Address $E_ZIP");  //7F,Chen Tian Dongfang Dasha,Bao-Ming 2Rd,XiXiang,Baoan,Shenzhen,China 518102

$pdf->Text($CurMargin+2,33,"Tel:    $E_Tel    fax:    $E_Fax    email:    $E_Email");	

$pdf->Rect($CurMargin,37,195,$CurH,"D"); //第二个框

$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
$NexPY=41;
//$pdf->Text(7,$NexPY,"TO:");
$pdf->SetFont('Arial','',$TableFontSize); //设回字体大小

{
	$CurX=$CurMargin+1;  //15;			
	$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
	$eurSTO="<table border=0 >
	<tr><td width=8 align=left  valign=middle style=bold >To:</td><td width=80   align=left height=$RowsHight valign=middle >$Company,</td><tr>	
	<tr><td width=8></td><td width=80   align=left valign=middle >$Address.</td><tr>		
	</table>" ;//$eurTableList;
	$pdf->htmltable($eurSTO); //<tr><td width=8 valign=middle>Fax:</td><td width=80   align=left valign=middle >$FaxNo</td><tr>

}		

$pdf->Rect(103,37,100,32,"D"); //第三个框

$pdf->SetFont('Arial','B',$TableFontSize); //设回字体大小
$NexPY=41;
//$pdf->Text(102,$NexPY,"SHIP TO:");
$pdf->SetFont('Arial','',TableFontSize); //设回字体大小

//SHIP TO
{
	$CurX=105;	//116		
	$pdf->SetXY($CurX,$NexPY-3.5);   //SHIP TO
	$eurSHIPTO="<table border=0 >
	<tr><td width=15 align=left style=bold valign=middle >SHIP TO:</td><td width=80   align=left height=$RowsHight valign=middle >$SoldTo,</td><tr>	
	<tr><td width=15 align=left style=bold ></td><td width=80   align=left valign=middle >$ToAddress.</td><tr>			
	</table>" ;//$eurTableList;
	$pdf->htmltable($eurSHIPTO);

}



{
	//$eurTableList="<table  border=1 >
	
	$pieces = explode("#", $OrderPOs);  //获取所有PO
	
	$pdf->SetXY($CurMargin,72);   //第一个表
	$eurTableHeader="<table border=1 >
	<tr >
	<td width=15 bgcolor=#CCCCCC align=center height=$RowsHight valign=middle style=bold>PO:</td>	
	<td width=46 align=left valign=middle style=bold>$pieces[1]</td>
	<td width=15 bgcolor=#CCCCCC align=center valign=middle style=bold>PI:</td>			
	<td width=33 align=left valign=middle  style=bold>$PI</td>	
	<td width=36  bgcolor=#CCCCCC align=center valign=middle style=bold>Requisition by:</td>	
	<td width=20 align=center valign=middle style=bold>$Nickname</td>
	<td width=12  bgcolor=#CCCCCC align=center valign=middle style=bold>Date:</td>
	<td width=18 align=center valign=middle style=bold>$Date</td>
	</tr>
	</table>" ;//$eurTableList;color=#55BFFF
	$pdf->htmltable($eurTableHeader);
	
}		

		
$pdf->htmltable($hTableList);
$pdf->SetFont('Arial','B',$TableFontSize);
if($iTableList!=""){
	if($CompanyId==1018){
		$iTableList="<table  border=1 >
		<tr bgcolor=#CCCCCC repeat>
			<td width=15 align=center height=$RowsHight valign=middle style=bold>NO.</td>
			<td width=30 align=center valign=middle style=bold>Product Code</td>
			<td width=71 align=center valign=middle style=bold>Description</td>
			<td width=15 align=center valign=middle style=bold>Q'ty</td>
			<td width=15 align=center valign=middle style=bold>Unit</td>
			<td width=19 align=center valign=middle style=bold>Amount</td>
			<td width=30 align=center valign=middle style=bold>Leadtime</td>
		</tr>".$iTableList."
		<tr bgcolor=#CCCCCC>
		<td height=$RowsHight valign=middle style=bold>Total</td>
		<td></td><td></td>
		<td align=right valign=middle style=bold>$QtySUM</td>
		<td></td>
		<td align=right valign=middle style=bold>$AmountSUM</td>
		<td></td>
		</tr></table>";
		}
	else{
		$iTableList="<table  border=1 >
		<tr bgcolor=#CCCCCC repeat>
			<td width=10 align=center height=$RowsHight valign=middle style=bold>Ln.</td>
			<td width=25 valign=middle>PO</td>
			<td width=44 align=center valign=middle style=bold>Product Code</td>
			<td width=19 align=center valign=middle style=bold>Quantity</td>	
			<td width=19 align=center valign=middle style=bold>Unit Price</td>	
			<td width=19 align=center valign=middle style=bold>Amout</td>
			<td width=39 align=center valign=middle style=bold>How to ship</td>
			<td width=20 align=center valign=middle style=bold>Leadtime</td>
		</tr>".$iTableList."
		<tr bgcolor=#CCCCCC>
		<td height=$RowsHight valign=middle style=bold>Total</td>
		<td></td>
		<td></td>
		<td align=right valign=middle style=bold>$QtySUM</td>
		<td></td>
		<td align=right valign=middle style=bold>$AmountSUM</td>
		<td></td><td></td>
		</tr></table>";
		}
	}
$pdf->SetX($CurMargin);	
$pdf->htmltable($iTableList);
*/
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
//$Date=date("d-M-y");
for ($pi=1;$pi<=$Counts;$pi=$pi+1){  //循环输出
	$eurTableNo="eurTableNo".strval($pi);
     if($addSign==0){  //新加一页
		$pdf->AddPage();		
		$pageNo=$pageNo+1;
		$pdf->FloorSign=0;		
		
		$Invoice_PI="$PI";
		$Com_Pack_PI="Proforma Invoice";
		include "invoicetopdf/invoicepublicHeader.php";  //页头
		
		{
			//$eurTableList="<table  border=1 >
			
			$pieces = explode("#", $OrderPOs);  //获取所有PO
			
			//$pdf->SetXY($CurMargin,72);   //第一个表
			$pdf->SetXY($CurMargin,$ConTableBegin_Y);   //第一个表modify by zx 2011-0129 $ConTableBegin_Y值在 invoicepublicHeader.php     $pdf->SetXY($CurMargin,72); 
			$eurTableHeader="<table border=1 >
			<tr >
			<td width=15 bgcolor=#CCCCCC align=center height=$RowsHight valign=middle style=bold>PO:</td>	
			<td width=46 align=left valign=middle style=bold>$pieces[1]</td>
			<td width=15 bgcolor=#CCCCCC align=center valign=middle style=bold>PI:</td>			
			<td width=33 align=left valign=middle  style=bold>$PI</td>	
			<td width=36  bgcolor=#CCCCCC align=center valign=middle style=bold>Requisition by:</td>	
			<td width=20 align=center valign=middle style=bold>$Nickname</td>
			<td width=16  bgcolor=#CCCCCC align=center valign=middle style=bold>Page NO.:</td>
			<td width=14 align=center valign=middle style=bold>$pageNo</td>
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
	
	//if($eurTableList!=""){
	$pdf->SetX($CurMargin);  //	
	if(($$eurTableNo)!=""){   //输出表的内容。	
		$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
		$pdf->htmltable($$eurTableNo);
	}
	$NowY=$pdf->GetY();  //取得当前的Y的位置
	//$pdf->Text(0,$NowY,"$NowY");  //输出当前值
	//取得下一个的高度，
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
				//$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				//$pdf->Text($CurMargin+1,$NowSY+3.5,"Notes:"); 
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小				
			$pdf->htmltable($tmp_Total);
			    $addsplit=0;
				if($CompanyId==1058){
					$NowSY=$pdf->GetY(); //
					//$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
					//$pdf->Text($CurMargin+1,$NowSY+3.5,"Notes:"); 
					$pdf->SetXY($CurMargin,$NowSY);
					$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小				
					$pdf->htmltable($tmp_1058);	
					
					$NowSY=$pdf->GetY(); //
					//$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
					//$pdf->Text($CurMargin+1,$NowSY+3.5,"Notes:"); 
					$pdf->SetXY($CurMargin,$NowSY);
					$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
					/*
					$tmp_split="<table  border=0 >	
						<tr>
							<td   width=136  height=1  align='left' valign='middle'></td>
						</tr>	
					</table>";
					$pdf->htmltable($tmp_split);	
					*/
					 $addsplit=2;
					
				}

				$NowSY=$pdf->GetY()+$addsplit;
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+3.5,"Notes:"); 
				$pdf->SetXY($CurMargin,$NowSY);				
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
			$pdf->htmltable($tmp_Notes);


				$NowSY=$pdf->GetY();
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+3.5,"Terms:"); 
				$pdf->SetXY($CurMargin,$NowSY);				
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
			$pdf->htmltable($tmp_Terms);
			    /*
				$NowSY=$pdf->GetY(); 
				$pdf->SetFont('Arial','B',$InvoiceHeadFontSize); //设回字体大小
				$pdf->Text($CurMargin+1,$NowSY+5,"Currency  :"); 
				$pdf->SetXY($CurMargin,$NowSY);
				$pdf->SetFont('Arial','',$InvoiceHeadFontSize); //设回字体大小
			$pdf->htmltable($tmp_Currency);
			    */
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
			//$pdf->htmltable($$eurTableNo);
		}
       
		
		$addSign=0; //输出最后一页的页脚
	}
	
	
	if($addSign==0)		
	{
		include "invoicetopdf/invoicepublicFooter.php";  //页尾  

			
	}		
}







/*
$pdf->SetFont('Arial','',$InvoiceHeadFontSize);
$pdf->Cell(0,2,"",0,1,"L");

switch($CompanyId){
	case 1024:$BankId=3;break;//KON使用阿香帐号
	case 1050:$BankId=2;break;//PGD使用台北帐号
	default:$BankId=1;break;
	}
$bankResult = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.my2_bankinfo WHERE Id='$BankId' LIMIT 1",$link_id));		 
$Beneficary=$bankResult["Beneficary"];
$Bank=$bankResult["Bank"];
$BankAdd=$bankResult["BankAdd"];
$SwiftID=$bankResult["SwiftID"];
$ACNO=$bankResult["ACNO"];

{
	//$eurTableList="<table  border=1 >
	$pdf->SetX($CurMargin);   //银行
	$BankTableHeader="<table border=0 >
	<tr><td width=180 align=left>&nbsp;</td></tr>
	<tr><td width=180 align=left>Payment term:$Paymentterm</td></tr>
	<tr><td width=180 align=left>Currency  :$Symbol</td></tr>
	<tr><td width=180 align=left>&nbsp;</td></tr>
	<tr><td width=180 align=left>&nbsp;</td></tr>
	<tr><td width=180 align=left>Beneficary: $Beneficary</td></tr>
	<tr><td width=180 align=left>Bank         : $Bank</td></tr>
	<tr><td width=180 align=left>Bank Add : $BankAdd</td></tr>			
	<tr><td width=180 align=left>Swift ID    : $SwiftID</td></tr>	
	<tr><td width=180 align=left>A/C NO    : $ACNO</td></tr>
	</table>" ;//$eurTableList;
	$pdf->htmltable($BankTableHeader);
	
}  
*/

/*
$pdf->SetX(5);	
	$pdf->Cell(0,4,"Beneficary: $Beneficary",0,1,"L");
$pdf->SetX(5);		
	$pdf->Cell(0,4,"Bank         : $Bank",0,1,"L");
$pdf->SetX(5);		
	$pdf->Cell(0,4,"Bank Add : $BankAdd",0,1,"L");
$pdf->SetX(5);		
	$pdf->Cell(0,4,"Swift ID    : $SwiftID",0,1,"L");
$pdf->SetX(5);		
	$pdf->Cell(0,4,"A/C NO    : $ACNO",0,1,"L");

$Officialseal="../download/images/officialseal.jpg";
if(file_exists($Officialseal)){
	$pdf->Image($Officialseal,$pdf->GetX()+120,$pdf->GetY()-20,40,40,"JPG");
	}
*/
//$sealX=$pdf->GetX()+130;
//$sealY=$pdf->GetY()-38;
include "invoicetopdf/invoicepublicSeal.php";  //封印  
//$mc_officialseal="../images/officialseal$E_SealType.jpg";
//$pdf->Image($mc_officialseal,$pdf->GetX()+130,$pdf->GetY()-54,40,40,"JPG");	
?>