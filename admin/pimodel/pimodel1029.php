<?php   
//电信-ZX  2012-08-01
/*
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.companyinfo
$DataPublic.my2_bankinfo
二合一已更新
*/
$clientResult = mysql_query("SELECT C.Forshort,U.Symbol,I.Company,I.Fax,I.Address
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
include "../plugins/fpdf/pdftable.inc.php";
$pdf=new PDFTable(); 
$pdf->SetAutoPageBreak("on",10);
$pdf->SetMargins(10,10,10);
$pdf->Open(); 
$pdf->AddPage(); 
$pdf->FloorSign=0;
$pdf->SetFont('Arial','',35);
$pdf->Cell(0,20,"Proforma Invoice",0,1,"C");
$pdf->Setxy(10,25);
$Logo="../download/images/logo.jpg";
if(file_exists($Logo)){
	$pdf->Image($Logo,20,5,28,44,"JPG"); 
	}
$pdf->SetY(50);
$pdf->SetFont('Arial','',$InvoiceHeadFontSize);
	$pdf->Cell(0,$RowsHight,"Seller        : $E_Company",0,1,"L");
	$pdf->Cell(0,$RowsHight,"Fax           : $E_Fax",0,1,"L");
	$pdf->Cell(123,$RowsHight,"Sold To     : $SoldTo",0,0,"L");
	$pdf->SetTextColor(0,0,255);
	$pdf->Cell(0,$RowsHight,"PO# 450000".$OrderPOs,0,1,"C");
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(0,$RowsHight,"Address    : $ToAddress",0,1,"L");
	$pdf->Cell(143,$RowsHight,"Fax NO     : $FaxNo",0,0,"L");
	$pdf->SetTextColor(0,0,255);
	$pdf->Cell(80,$RowsHight,"$PI",0,1,"L");
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(143,$RowsHight,"Leadtime   : $Leadtime",0,0,"L");
		$pdf->Cell(80,$RowsHight,"Date       : $Date",0,1,"L");	
		$pdf->Cell(20,$RowsHight,"Payment term : $Paymentterm",0,0,"L");
		$pdf->SetTextColor(255,0,0);
		$pdf->Cell(123,$RowsHight,"",0,0,"C");
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(60,$RowsHight,"Currency: $Symbol",0,1,"L");
$pdf->SetFont('Arial','B',$TableFontSize);
	if($iTableList!=""){
		$iTableList="<table  border=1 >
		<tr bgcolor=#CCCCCC repeat>
			<td width=10 align=center height=$RowsHight valign=middle style=bold>NO.</td>
			<td width=134 align=center valign=middle style=bold>Description</td>
			<td width=12 align=center valign=middle style=bold>Q'ty</td>
			<td width=15 align=center valign=middle style=bold>Unit</td>
			<td width=18 align=center valign=middle style=bold>Amount</td>
		</tr>".$iTableList."
		<tr bgcolor=#CCCCCC>
		<td height=$RowsHight valign=middle style=bold>Total</td>
		<td></td>
		<td align=right valign=middle style=bold>$QtySUM</td>
		<td></td>
		<td align=right valign=middle style=bold>$AmountSUM</td>
		</tr></table>";
			;
		}
$pdf->htmltable($iTableList);
$pdf->SetFont('Arial','',$InvoiceHeadFontSize);
$pdf->Cell(0,2,"",0,1,"L");
$bankResult = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.my2_bankinfo WHERE Id=1 LIMIT 1",$link_id));		 
$Beneficary=$bankResult["Beneficary"];
$Bank=$bankResult["Bank"];
$BankAdd=$bankResult["BankAdd"];
$SwiftID=$bankResult["SwiftID"];
$ACNO=$bankResult["ACNO"];
	$pdf->Cell(0,4,"Beneficary: $Beneficary",0,1,"L");
	$pdf->Cell(0,4,"Bank         : $Bank",0,1,"L");
	$pdf->Cell(0,4,"Bank Add : $BankAdd",0,1,"L");
	$pdf->Cell(0,4,"Swift ID    : $SwiftID",0,1,"L");
	$pdf->Cell(0,4,"A/C NO    : $ACNO",0,1,"L");
$Officialseal="../download/images/officialseal.jpg";
if(file_exists($Officialseal)){
	$pdf->Image($Officialseal,$pdf->GetX()+120,$pdf->GetY()-20,40,40,"JPG");
	}
?>