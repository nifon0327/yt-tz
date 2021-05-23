<?php
//电信-ZX  2012-08-01
/*
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.companyinfo
二合一已更新
*/
$clientResult = mysql_query("SELECT 
C.Forshort,U.Symbol,
I.Company,I.Fax,I.Address
FROM $DataIn.trade_object C
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=1 
WHERE C.CompanyId=$CompanyId LIMIT 1",$link_id);
if($clientRows = mysql_fetch_array($clientResult)){
	$Symbol=$clientRows["Symbol"]=="USD"?"U.S.DOLLARS":$clientRows["Symbol"];
	$Forshort=$clientRows["Forshort"];
	$Company=$clientRows["Company"];
	$SoldTo=$Company;
	$FaxNo=$clientRows["Fax"];
	$ToAddress=$clientRows["Address"];
	}
$Date=date("d-M-y");
$TableFontSize=8;
define('FPDF_FONTPATH','../plugins/fpdf/font/');
require('../plugins/fpdf/chinese-unicode.php');
$pdf=new PDF_Unicode();
$pdf->SetAutoPageBreak("on",10);
$pdf->SetMargins(10,10,10);
$pdf->Open();
$pdf->AddPage();
$pdf->FloorSign=0;

$pdf->Setxy(10,25);
$Logo="../download/images/logo.jpg";
if(file_exists($Logo)){
	$pdf->Image($Logo,20,5,28,44,"JPG");
	}
$pdf->Setxy(10,25);
$PiTitle="../download/images/pi-title.jpg";
if(file_exists($PiTitle)){
	$pdf->Image($PiTitle,70,10,81,8,"JPG");
	}
$pdf->SetY(50);
$pdf->AddUniGBhwFont('uGB');
$pdf->SetFont('uGB','',$InvoiceHeadFontSize);
	$pdf->Cell(0,$RowsHight,"供 应 商: $S_Company",0,1,"L");
	$pdf->Cell(0,$RowsHight,"传    真: $S_Fax  电 话: $S_Tel",0,1,"L");
	$pdf->Cell(123,$RowsHight,"客    户: 吉恩杰饰品(上海)有限公司",0,0,"L");
	$pdf->SetTextColor(0,0,255);
	$pdf->Cell(0,$RowsHight,"PO#".$OrderPOs,0,1,"C");
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(0,$RowsHight,"地    址: 上海市宝安区西乡镇银田共和工业路西发工业区A区2栋6楼",0,1,"L");
	$pdf->Cell(143,$RowsHight,"传    真: +86-755-27572482  电 话: +86-755-27572700",0,0,"L");
	$pdf->SetTextColor(0,0,255);
	$pdf->Cell(80,$RowsHight,"$PI",0,1,"L");
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(143,$RowsHight,"交 货 期: $Leadtime",0,0,"L");
		$pdf->Cell(80,$RowsHight,"日    期: $Date",0,1,"L");
		$pdf->Cell(20,$RowsHight,"付款条件: $Paymentterm",0,0,"L");
		$pdf->SetTextColor(255,0,0);
		$pdf->Cell(123,$RowsHight,"",0,0,"C");
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(60,$RowsHight,"货    币:人民币",0,1,"L");
$pdf->SetFont('uGB','',$InvoiceHeadFontSize);
	if($iTableList!=""){
		$iTableList="<table  border=1 >
		<tr bgcolor=#CCCCCC repeat>
			<td width=10 align=center height=$RowsHight valign=middle style=bold>NO.</td>
			<td width=74 align=center valign=middle style=bold>Item</td>
			<td width=60 align=center valign=middle style=bold>Description</td>
			<td width=12 align=center valign=middle style=bold>Q'ty</td>
			<td width=15 align=center valign=middle style=bold>Unit</td>
			<td width=18 align=center valign=middle style=bold>Amount</td>
		</tr>".$iTableList."
		<tr bgcolor=#CCCCCC>
		<td height=$RowsHight valign=middle style=bold>Total</td>
		<td></td>
		<td></td>
		<td align=right valign=middle style=bold>$QtySUM</td>
		<td></td>
		<td align=right valign=middle style=bold>$AmountSUM</td>
		</tr></table>";
			;
		}
$pdf->htmltable($iTableList);
$pdf->SetFont('uGB','',$InvoiceHeadFontSize);
$pdf->Cell(0,2,"",0,1,"L");

	$pdf->Cell(0,4,"开 户 行:中国工商银行福永支行",0,1,"L");
	$pdf->Cell(0,4,"开 户 名:刘道香",0,1,"L");
	$pdf->Cell(0,4,"银行帐号:9558884000001498657",0,1,"L");
$Officialseal="../download/images/officialseal.jpg";
if(file_exists($Officialseal)){
	$pdf->Image($Officialseal,$pdf->GetX()+120,$pdf->GetY()-10,40,40,"JPG");
	}
?>