<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_toexcel";
$_SESSION["nowWebPage"]=$nowWebPage;

$mySql1="SELECT M.CompanyId,M.InvoiceNO,M.Wise,M.Notes,M.Terms,M.PaymentTerm,M.Ship,M.Date,U.Symbol,I.Company,I.Fax,I.Address,D.InvoiceModel,D.SoldTo,D.Address AS ToAddress,D.FaxNo,D.SoldFrom,D.FromAddress,D.FromFaxNo,
B.Beneficary,B.Bank,B.BankAdd,B.SwiftID,B.ACNO,S.Nickname,S.Name as ZName,C.PriceTerm
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=2 
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId 
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
LEFT JOIN $DataPublic.staffmain S ON S.Number=C.Staff_Number
WHERE M.Id=$Id LIMIT 1";
//echo $mySql1;
$mainResult = mysql_query($mySql1,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$CompanyId=$mainRows["CompanyId"];
     $Ship=$mainRows["Ship"];
	include "invoicetopdf/company_info.php";  //相应公司附近加的信息
	$InvoiceNO=$mainRows["InvoiceNO"];
	$Wise=$mainRows["Wise"];
	
	$Notes=$mainRows["Notes"];
	$Terms=$mainRows["Terms"];
	
	$Date=date("d-M-y",strtotime($mainRows["Date"]));
	$Symbol=$mainRows["Symbol"]=="USD"?"U.S.DOLLARS":$mainRows["Symbol"];
	$Company=$mainRows["Company"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$InvoiceModel=$mainRows["InvoiceModel"];

	$SoldTo=$mainRows["SoldTo"]==""?$Company:$mainRows["SoldTo"];
	$ToAddress=$mainRows["ToAddress"]==""?$Address:$mainRows["ToAddress"];
	$FaxNo=$mainRows["FaxNo"]==""?$Fax:$mainRows["FaxNo"];	
	$Company=$mainRows["SoldFrom"]==""?$Company:$mainRows["SoldFrom"];
	$Address=$mainRows["FromAddress"]==""?$Address:$mainRows["FromAddress"];
	$Fax=$mainRows["FromFaxNo"]==""?$Fax:$mainRows["FromFaxNo"];	
	
	$Beneficary=$mainRows["Beneficary"];
	$Bank=$mainRows["Bank"];
	$BankAdd=$mainRows["BankAdd"];
	$SwiftID=$mainRows["SwiftID"];
	$ACNO=$mainRows["ACNO"];
	$Nickname=$mainRows["Nickname"];
	$ZName=$mainRows["ZName"];
	
	
	}

require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
 // Create new PHPExcel object
$objPHPExcel = new PHPExcel();
 $style_left= array( 
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
  
$style_center = array( 
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
  
  $style_right = array( 
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

$Rows=1;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(30);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Ash Cloud Co.,Ltd.Shenzhen");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:D$Rows");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$InvoiceNO");
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("E$Rows:H$Rows");

$Rows++;

$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(35);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Building 48,Bao-Tian Industrial Zone,Qian-Jin 2Rd,XiXiang,Baoan,Shenzhen,China 518102\n Tel:+86-755-6113-9580 Fax: +86-755-6113-9585  URL: www.middlecloud.com FSN NO:");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:H$Rows");



$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(50);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "SOLD TO:$Company  \r\n $Address");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:D$Rows");


$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "SHIP TO :$SoldTo  \r\n  $ToAddress");
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->mergeCells("E$Rows:H$Rows");


$Rows++;

$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "INVOICE NO:$InvoiceNO");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:C$Rows");

$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "Forwarder:");
$objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("D$Rows:E$Rows");

$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "Requisition by:$Nickname");
$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("F$Rows:H$Rows");





$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Ln.");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "PO#");
$objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "Product Code");
$objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "Description");
$objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "H.S.");
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "Qty");
$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "Unit Price");
$objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "Amount");
$objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$Rows++;
$mySql="SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,P.Weight AS Weight,M.Date,P.Description,PI.PaymentTerm
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
     LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=O.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'
UNION ALL
	SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,O.Weight AS Weight,M.Date,O.Description,'' AS PaymentTerm
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$Id' AND S.Type='2'
UNION ALL
	SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,'0' AS Weight ,M.Date,O.Description,'' AS PaymentTerm
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$Id' AND S.Type='3'";
$result = mysql_query($mySql,$link_id);
 if($myrow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SumAmount=0;
         $PaymentTerm=$myrow["PaymentTerm"];
	      $PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm";  //放在Terms里
	do{
		$eCode=$myrow["eCode"];
		$Description=$myrow["Description"];
	  	$Qty=$myrow["Qty"];
       $OrderPO=$myrow["OrderPO"];
		$Price=$myrow["Price"];
		$cName=$myrow["cName"];
		$char_change = array(
                    '<'=>'&lt;',
                    '>'=>'&gt;'
                    );
        $cName=strtr($cName,$char_change);  
        $Amount=$Qty*$Price;
     $SumAmount+=$Amount;
        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$OrderPO");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$eCode");
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Description");
        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "");
        $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Qty");
        $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$Price");
        $objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Amount");

		$i++; $Rows++;
		}while ($myrow = mysql_fetch_array($result));
	}

$TempRow1=$Rows;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Notes: \n$Notes");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:D$Rows");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "SUBTOTAL");
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("E$Rows:G$Rows");
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$SumAmount");
$objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$Rows++;

$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:D$Rows");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "DELIVERY COST");
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("E$Rows:G$Rows");
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "");
$objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


$Rows++;

$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:D$Rows");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "VAT");
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("E$Rows:G$Rows");
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "");
$objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



$Rows++;

$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", " ");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:D$Rows");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "TOTAL");
$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("E$Rows:G$Rows");
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$SumAmount");
$objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$TempRow2=$Rows;
//$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow("A","$TempRow1","D","$TempRow2");
$objPHPExcel->getActiveSheet()->mergeCells("A$TempRow1:A$TempRow2");



$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(40);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Terms: \n$PaymentTerm \n$Priceterm \n$Terms");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:H$Rows");


$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(85);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "BANK:\n Beneficiary: $Beneficary \n Bank         : $Bank \n Bank Add : $BankAdd \n Swift ID    : $SwiftID \n A/C NO    : $ACNO");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:H$Rows");


$Rows++;

$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(60);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Authorised By:Kung-Yi Chen(Fred)");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:C$Rows");

$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "Signature:");
$objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("D$Rows:E$Rows");

$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "Date:$Date");
$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("F$Rows:H$Rows");

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=InvoiceNO.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>