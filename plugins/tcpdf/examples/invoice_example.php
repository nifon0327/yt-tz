<?php
/**
*  Invoice Example
*  @author Aitch.Zung(aitch.zung@icloud.com)
*  @version 1.0
*/

define(AUTHOR, 'Aitch Chung (aitch.zung@icloud.com)');
define(TITLE, 'PI PDF');
define(SUBJECT, 'Create PI PDF');
define(DEFAULT_FONT_SIZE, 22);
define(PDF_MARGIN_TOP, 5);
define(PDF_MARGIN_LEFT, 10);
define(PDF_MARGIN_RIGHT, 10);
define(PDF_MARGIN_HEADER, 0);

require_once('../tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// --- Setup ---
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(AUTHOR);
$pdf->SetTitle(TITLE);
$pdf->SetSubject(SUBJECT);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->setPrintHeader(false);

// set font
$fontname = $pdf->addTTFfont(dirname(dirname(__FILE__)).'/fonts/msyh.ttf', 'TrueTypeUnicode', '', DEFAULT_FONT_SIZE);
$pdf->AddFont($fontname, '', DEFAULT_FONT_SIZE);

// add a page
$pdf->AddPage();
$pdf->setCellHeightRatio(0.7);

// --- EO Setup ---

// --- Data models ---
$fscNo = 'no';
$innvoiceName = 'AI-multi 99';
$note = nl2br('中文備註在這裡....');
$currency = 'RMB';
$terms = nl2br('Payment term:Pay every 5th for last month
Price term:FOB HK');
$forwarder = '';
$requistionBy = 'du';
$pageNo = 1;
$records = array(
	array(
		'po' => 29.009,
		'product_code' => 'MUUSC0007',
		'description' => 'Cable USB (Carga-SincronizaciA^3n) Apple IPhone/iPad/iPod',
		'hs' => '',
		'qty' => 1000,
		'unit_price' => 0.6000,
		'amount' => '600.00'
	),
	array(
		'po' => 29.009,
		'product_code' => 'MUUSC0007',
		'description' => 'Cable USB 中文型號名稱 Apple IPhone/iPad/iPod',
		'hs' => '',
		'qty' => 1000,
		'unit_price' => 0.6000,
		'amount' => '600.00'
	)
);
$bankInfo = nl2br('Beneficary: Ash Cloud Co.,Ltd.Shenzhen
Bank : AGRICULTURAL BANK OF CHINA SHENZHEN BAOAN SUB-BRANCH 
Bank Add : NO 50 1st Jian An Road Bao An Dist Shenzhen China
Swift ID : ABOCCNBJ410
A/C NO : 41-021700040055832');
$authorizedBy = 'Kung-Yi Chen(Fred)';
$signatureImg = 'signature.png';
$ashcloudLogoImg = 'ashcloud_log.png';
$date = date('d-M y');
$subtotal = 0;
$deliveryCost = 0;
$vat = 0;
$total = 0;
$filename = 'invoice.pdf';

// --- EO Data Models --

// --- Template --- 

$html = <<<EOD
	<!-- HEADER -->
	<h1 color="red">Ash Cloud Co.,Ltd. Shenzhen</h1>
	<span style="font-size: 8px; ">Building 48,Bao-Tian Industrial Zone,Qian-Jin 2Rd,XiXiang,Baoan,Shenzhen,China 518102</span><br/>
	<span style="font-size: 8px; ">Tel: +86-755-6113 9580 Fax: +86-755-6113 9585 URL: www.middlecloud.com FSC NO: $fscNo</span><br/>
EOD;

// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);

$invoiceNameHtml = <<<EOD
	<div style="font-size: 10px; line-height: 16px; padding: 10px;">
		<span style="font-weight: bold;">COMMERCIAL INVOICE</span><br/>
		<span style="text-align: right; color: white;">$innvoiceName.</span>
	</div>
EOD;

$pdf->SetFillColor(200,200,200);
$pdf->writeHTMLCell($w=70, $h=10, $x=130, $y=5, $invoiceNameHtml, $border=0, $ln=1, $fill=1, $reseth=true, $align='L', $autopadding=true);

$soldToContent = nl2br('Ascendeo Iberia S.L., Les Planas,2-4-Poligono Fontsanta 08970 Sant Joan Despi-Barcelona Spain.');//model
$soldToHtml = <<<EOD
	<table border="0" style="font-size: 8px; line-height: 12px;">
		<tr>
			<th style="font-weight: bold;" width="50">SOLD TO:</th>
			<td width="150" style="font-family: '$fontname'">$soldToContent</td>
		</tr>
	</table>
EOD;

$pdf->SetFillColor(255,255,255);
$pdf->writeHTMLCell($w=95, $h=20, $x=10, $y=23, $soldToHtml, $border=1, $ln=1, $fill=1, $reseth=true, $align='L', $autopadding=true);

$shipToContent = nl2br('Ascendeo Iberia S.L., Les Planas,2-4-Poligono Fontsanta 08970 Sant Joan Despi-Barcelona Spain. 中文字在這裡');//model
$shipToHtml = <<<EOD
	<table border="0" style="font-size: 8px; line-height: 12px;">
		<tr>
			<th style="font-weight: bold;" width="50">SHIP TO:</th>
			<td width="150" style="font-family: '$fontname'">$shipToContent</td>
		</tr>
	</table>
EOD;

$pdf->SetFillColor(255,255,255);
$pdf->writeHTMLCell($w=95, $h=20, $x=105, $y=23, $shipToHtml, $border=1, $ln=1, $fill=1, $reseth=true, $align='L', $autopadding=true);

//Table itself
$tableHtml = <<<EOD
	<table border="1" style="font-size: 8px; line-height: 12px;">
		<tr align="center">
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="60">Invoice NO.:</th>
			<td width="80" style="font-family: '$fontname'; text-align: center;">$innvoiceName</td>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="60">Forwarder:</th>
			<td width="80" style="font-family: '$fontname'; text-align: center;">$forwarder</td>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="70">Requisition by:</th>
			<td width="100" style="font-family: '$fontname'; text-align: center;">$requistionBy</td>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="50">Page No.:</th>
			<td width="39" style="font-family: '$fontname'; text-align: center;">$pageNo</td>
		</tr>
	</table>
	<table border="1" style="font-size: 8px; line-height: 12px;">
		<tr align="center">
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="20">Ln.</th>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="50">PO#</th>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="100">Product Code</th>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="169">Description</th>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="50">H.S.</th>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="50">Qty</th>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="50">Unit Price</th>
			<th style="font-weight: bold; background-color: rgb(200, 200, 200);" width="50">Amount</th>
		</tr>
EOD;

for($idx = 0 ; $idx <= 31 ; $idx++){
	$record = $records[$idx];
	$tableHtml .= '
		<tr>
			<td align="center;" style="height: 13px;">'.($idx+1).'</td>
			<td style="height: 13px;">'.$record['po'].'</td>
			<td style="height: 13px;">'.$record['product_code'].'</td>
			<td style="font-family: \''.$fontname.'\'; height: 13px;">'.$record['description'].'</td>
			<td style="height: 13px;">'.$record['hs'].'</td>
			<td style="height: 13px;">'.$record['qty'].'</td>
			<td style="height: 13px;">'.$record['unit_price'].'</td>
			<td style="height: 13px;">'.$record['amount'].'</td>
		</tr>';
	
}

$tableHtml .= '</table>

    <table border="1" style="font-size: 8px; line-height: 12px;" cellspacing="0" cellpadding="0">
    	<tr>
    		<td width="539">
    		<table border="0" style="font-size: 8px; line-height: 12px;" cellspacing="0" cellpadding="0">
	    		<tr>
		    		<td width="330" style="height:48px;">
		    			<strong>Notes:</strong><span style="font-family: \''.$fontname.'\';">'.$note.'</span>
		    		</td>
		    		<td width="200" style="height:48px;">
		    			<table border="1" style="font-size: 8px; line-height: 12px;" cellspacing="0" cellpadding="2">
							<tr>
								<th width="149" style="font-weight: bold; background-color: rgb(200, 200, 200);">SUBTOTAL</th> 
								<td width="51">'.$subtotal.'</td>
							</tr>
							<tr>
								<th width="149" style="font-weight: bold; background-color: rgb(200, 200, 200);">DELIVERY COST</th> 
								<td width="51">'.$deliveryCost.'</td>
							</tr>
							<tr>
								<th width="149" style="font-weight: bold; background-color: rgb(200, 200, 200);">VAT</th> 
								<td width="51">'.$vat.'</td>
							</tr>
							<tr>
								<th width="149" style="font-weight: bold; background-color: rgb(200, 200, 200);">TOTAL</th> 
								<td width="51">'.$total.'</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
    		</td>
    	</tr>
    </table>

    <table border="1" style="font-size: 8px; line-height: 10px;" cellspacing="0" cellpadding="2">
    	<tr>
    		<td width="539" style="height:48px;" colspan="4">
    			<table border="0">
    				<tr>
    					<th width="439" style="font-weight: bold;">Terms:</th>
    					<th width="139"><strong>Currency:</strong>'.$currency.'</th>
    				</tr>
    				<tr>
    					<td colspan="2" width="439">'.$terms.'</td>
    				</tr>
    			</table>	
    		</td>
    	</tr>
    </table>
    <table border="1" style="font-size: 8px; line-height: 10px;" cellspacing="0" cellpadding="2">
    	<tr>
    		<td width="539" style="height:70px;" colspan="4">
    			<table border="0">
    				<tr>
    					<th width="339" style="font-weight: bold;">BANK:</th>
    					<th width="239"></th>
    				</tr>
    				<tr>
    					<td colspan="2" width="339">'.$bankInfo.'</td>
    					<td><img border="0" src="'.$ashcloudLogoImg.'" width="160" height="51" /></td>
    				</tr>
    			</table>	
    		</td>
    	</tr>
    </table>
    <table border="1" style="font-size: 8px; line-height: 10px; padding-top: 15px; font-weight: bold;" cellspacing="0">
    	<tr>
    		<th width="90" height="30" style="background-color: rgb(200, 200, 200);" align="center">Authorised By</th>
    		<td width="120" height="30" align="center">'.$authorizedBy.'</td>
    		<th width="80" height="30" style="background-color: rgb(200, 200, 200);" align="center">Signature</th>
    		<td width="130" height="30" align="center"><img src="'.$signatureImg.'" width="100" height="25" /></td>
    		<th width="30" height="30" style="background-color: rgb(200, 200, 200);" align="center">Date</th>
    		<td width="89" height="30" align="center">'.$date.'</td>
    	</tr>
    </table>';

$pdf->SetFillColor(255,255,255);
$pdf->writeHTMLCell($w=200, $h=10, $x=9, $y=44, $tableHtml, $border=0, $ln=1, $fill=1, $reseth=true, $align='L', $autopadding=false);

//Close and output PDF document
$pdf->Output($filename, 'I');
