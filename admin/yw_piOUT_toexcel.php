<?php   
//电信-zxq 2013-07-11
//include "../basic/chksession.php" ;
//include "../basic/parameter.inc";
//include "../model/modelfunction.php";
/** Include PHPExcel */
//require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
$Ids="";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}
$PI=$Ids;
//$PI="CEL-A PI2013065";
//echo $Ids;
//*********************相应公司信息

$clientResult = mysql_query("SELECT * FROM (
SELECT 
C.Forshort,U.Symbol,C.CompanyId,
I.Company,I.Fax,I.Address,D.SoldTo,D.Address AS ToAddress,D.EndPlace AS EndPlace,S.Nickname,P.ShipTo,C.PriceTerm,
D.SoldFrom,D.FromAddress,
O.OutCompanyName,O.OutAddress,O.OutTel,O.OutFax,O.OutURL,O.OutRequistion,O.OutReqTel,O.OutBeneficiary,O.OutBeneficiaryCode,O.OutSWIFTAddress,O.OutAccountName,O.OutAccountNumber,O.OutBankAddress,O.OutRemark
FROM $DataIn.yw3_pisheet P
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8
LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataIn.ch8_shipoutcompany O ON O.Mid=D.Id
LEFT JOIN $DataIn.staffmain S ON S.Number=P.Operator
WHERE P.PI='$PI' AND  D.PiSign=1  
UNION
SELECT 
C.Forshort,U.Symbol,C.CompanyId,
I.Company,I.Fax,I.Address,D.SoldTo,D.Address AS ToAddress,D.EndPlace AS EndPlace,S.Nickname,P.ShipTo,C.PriceTerm,  
D.SoldFrom,D.FromAddress,
O.OutCompanyName,O.OutAddress,O.OutTel,O.OutFax,O.OutURL,O.OutRequistion,O.OutReqTel,O.OutBeneficiary,O.OutBeneficiaryCode,O.OutSWIFTAddress,O.OutAccountName,O.OutAccountNumber,O.OutBankAddress,O.OutRemark
FROM  $DataIn.yw3_pisheet P
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8 
LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataIn.ch8_shipoutcompany O ON O.Mid=D.Id
LEFT JOIN $DataIn.staffmain S ON S.Number=P.Operator
WHERE P.PI='$PI' ) A WHERE 1 LIMIT 1 ",$link_id);


if($clientRows = mysql_fetch_array($clientResult)){
	$Symbol=$clientRows["Symbol"]=="USD"?"U.S.DOLLARS":$clientRows["Symbol"];
	$Forshort=$clientRows["Forshort"];
	//SOLD TO:  $Company, $Address
	$Company=$clientRows["Company"];
    $CompanyId=$clientRows["CompanyId"];
	include "invoicetopdf/company_info.php";  //相应公司附近加的信息
	$FaxNo=$clientRows["Fax"];
	$Address=$clientRows["Address"];
	//$SoldTo=$clientRows["SoldTo"]==""?$Company:$clientRows["SoldTo"];
	//$ToAddress=$clientRows["ToAddress"]==""?$Address:$clientRows["ToAddress"];
	
	//SHIP TO: $SoldTo, $ToAddress 
	$ShipTo=$clientRows["ShipTo"];
	$Priceterm=$clientRows["Priceterm"];
    $SoldTo=$Company;
	$ToAddress=$Address;
	switch ($CompanyId) {
		case 1090:
		    $SoldTo=$clientRows["EndPlace"];
			$ToAddress=$clientRows["ToAddress"];
			break;
	}	
	$Nickname=$clientRows["Nickname"];
	
	//**********************************
	
	$OutCompanyName=$clientRows["OutCompanyName"];
	$OutAddress=$clientRows["OutAddress"];
	$OutRequistion=$clientRows["OutRequistion"];
	$OutBeneficiary=$clientRows["OutBeneficiary"];
	$OutBeneficiaryCode=$clientRows["OutBeneficiaryCode"];
	$OutSWIFTAddress=$clientRows["OutSWIFTAddress"];
	$OutAccountName=$clientRows["OutAccountName"];
	$OutAccountNumber=$clientRows["OutAccountNumber"];
	$OutBankAddress=$clientRows["OutBankAddress"];
	$OutRemark=$clientRows["OutRemark"];	
	
	$OutTel=$clientRows["OutTel"];
	$OutFax=$clientRows["OutFax"];
	$OutURL=$clientRows["OutURL"];
	$OutReqTel=$clientRows["OutReqTel"];	
	
	$SoldFrom=$clientRows["SoldFrom"];	
	$FromAddress=$clientRows["FromAddress"];	
	$SoldTo=$clientRows["SoldTo"];	
	$Address=$clientRows["ToAddress"];
	
	
}





$CompanyRow =mysql_fetch_array(mysql_query("SELECT P.CompanyId,P.ShipTo,P.Operator,D.Wise  FROM $DataIn.yw3_pisheet P
										   LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
										   LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId
										   WHERE P.PI='$PI' LIMIT 1",$link_id));
$CompanyId=$CompanyRow["CompanyId"];
$ShipTo=$CompanyRow["ShipTo"];
$Operator=$CompanyRow["Operator"];
$Wise=$CompanyRow["Wise"];


$clientResult = mysql_query("
SELECT  C.Forshort,U.Symbol,I.Company,I.Fax,I.Address,S.Nickname
FROM $DataIn.trade_object C
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8
LEFT JOIN $DataPublic.staffmain S ON S.Number='$Operator'
WHERE C.CompanyId='$CompanyId' LIMIT 1",$link_id);  //S.Number=C.Staff_Number

if($clientRows = mysql_fetch_array($clientResult)){
	$Symbol=$clientRows["Symbol"]=="USD"?"U.S.DOLLARS":$clientRows["Symbol"];
	$Forshort=$clientRows["Forshort"];
	$Company=$clientRows["Company"];
	$Priceterm="Price term:FOB HK";
	$FaxNo=$clientRows["Fax"];

	$Operator=$clientRows["Nickname"];

	}
	
//Bank 信息
include "subprogram/mybank_info.php";
$bankResult = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.my2_bankinfo WHERE Id='$BankId' LIMIT 1",$link_id));		 
$Beneficary=$bankResult["Beneficary"];
$Bank=$bankResult["Bank"];
$BankAdd=$bankResult["BankAdd"];
$SwiftID=$bankResult["SwiftID"];
$ACNO=$bankResult["ACNO"];

$sheetResult =mysql_query("SELECT S.OrderPO
   FROM $DataIn.yw1_ordersheet S 
   LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
   LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
   WHERE PI.PI='$PI' ORDER BY PI.Id",$link_id);
   if($sheetRows = mysql_fetch_array($sheetResult)){
	do{
	   $OrderPO=$sheetRows["OrderPO"];
		if($OrderPO!="" && $oldPO!=$OrderPO){
			$OrderPOs=$OrderPOs==""?"PO#".$OrderPO:($OrderPOs.".".$OrderPO);
			$oldPO=$OrderPO;
			}	
	   }while ($sheetRows = mysql_fetch_array($sheetResult));
	 }
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
  
 // Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);

$objPHPExcel->getActiveSheet()->setCellValue('A1', "$OutCompanyName");
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');

$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Proforma Invoice');
$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLACK);
$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setSize(18);
$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('E1:I1');

$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A2', "Factory Add: $OutAddress ");
$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');

$objPHPExcel->getActiveSheet()->mergeCells('E2:F2');

$objPHPExcel->getActiveSheet()->setCellValue('G2', "$PI");
$objPHPExcel->getActiveSheet()->getStyle('G2')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('G2:I2');

$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);

$objPHPExcel->getActiveSheet()->setCellValue('A3', "Tel:    $OutTel    Fax:    $OutFax     URL:   $OutURL ");
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
$objPHPExcel->getActiveSheet()->mergeCells('E3:I3');
$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(40);
$objPHPExcel->getActiveSheet()->setCellValue('A4', "SOLD TO: $SoldFrom  \r\n $FromAddress. ");
$objPHPExcel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->mergeCells('A4:D4');

$objPHPExcel->getActiveSheet()->setCellValue('E4', "SHIP TO: $SoldTo  \r\n  $Address.");
$objPHPExcel->getActiveSheet()->getStyle('E4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->mergeCells('E4:I4');

$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
//$objPHPExcel->getActiveSheet()->setCellValue('A5', 'PO:');
//$objPHPExcel->getActiveSheet()->setCellValue('B5', "$OrderPOs");
$objPHPExcel->getActiveSheet()->setCellValue('A5', 'FORWARDER:');
$objPHPExcel->getActiveSheet()->setCellValue('B5', "$Wise");

$objPHPExcel->getActiveSheet()->mergeCells('B5:C5');
$objPHPExcel->getActiveSheet()->setCellValue('D5', 'PI:');
$objPHPExcel->getActiveSheet()->setCellValue('E5', "$PI");
$objPHPExcel->getActiveSheet()->mergeCells('E5:G5');
$objPHPExcel->getActiveSheet()->setCellValue('H5', 'Requisition by:');
$objPHPExcel->getActiveSheet()->setCellValue('I5', "$OutRequistion");

$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A6', 'Ln.');
$objPHPExcel->getActiveSheet()->setCellValue('B6', 'PO#');
$objPHPExcel->getActiveSheet()->setCellValue('C6', 'Product Code');
$objPHPExcel->getActiveSheet()->setCellValue('D6', 'Unit Price');
$objPHPExcel->getActiveSheet()->setCellValue('E6', 'Qty');
$objPHPExcel->getActiveSheet()->setCellValue('F6', 'Amount');
$objPHPExcel->getActiveSheet()->setCellValue('G6', 'Air/Sea');
$objPHPExcel->getActiveSheet()->setCellValue('H6', 'Leadtime');
$objPHPExcel->getActiveSheet()->setCellValue('I6', 'Remark');
  
 $objPHPExcel->getActiveSheet()->getStyle( 'A5:I6')->applyFromArray($style_center);
 $Rows=7;

$OrderSql=mysql_query("SELECT S.Id,S.OrderPO,S.Qty,P.OutPrice,S.ShipType,P.eCode,P.Description,
   PI.Leadtime,PI.PaymentTerm,PI.Notes,PI.Terms,P.bjRemark,PI.Id AS Pid,PI.Date,PI.Remark
   FROM $DataIn.yw1_ordersheet S 
   LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
   LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
   WHERE PI.PI='$PI' ORDER BY PI.Id",$link_id);
/*
$OrderSql=mysql_query("SELECT S.Id,S.OrderPO,S.Qty,S.Price,S.ShipType,P.eCode,P.Description,
   PI.Leadtime,PI.PaymentTerm,PI.Notes,PI.Terms,P.bjRemark,PI.Id AS Pid,PI.Date,PI.Remark
   FROM $DataIn.yw1_ordersheet S 
   LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
   LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
   WHERE PI.PI='$PI' ORDER BY PI.Id",$link_id);
*/
 if($OrderRow=mysql_fetch_array($OrderSql)){
   $PaymentTerm=$OrderRow["PaymentTerm"];
   $Notes=$OrderRow["Notes"];
  // $Date =$OrderRow["Date"];
   $Date=date("d-M-y",strtotime($OrderRow["Date"]));
   $PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm";
   $i=1; 
   $SumQty=0;
   $SumAmount=0;
   do{
      $Id=$OrderRow["Id"];
      $OrderPO=$OrderRow["OrderPO"];
       $Qty=$OrderRow["Qty"];
	   //$Price=$OrderRow["Price"];
	   $Price=$OrderRow["OutPrice"];
	   
	   $Amount=sprintf("%.2f",$Qty*$Price);
	   $eCode=$OrderRow["eCode"];
	   $ShipType=$OrderRow["ShipType"];
	   if (is_numeric($ShipType)){
		   	$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataIn.ch_shiptype WHERE Id='$ShipType' LIMIT 1",$link_id);
		   	if($shipTypeRow = mysql_fetch_array($shipTypeResult)){
		   	   $ShipType=$shipTypeRow["Name"];
		   	}
	   }
	   
	   $Remark=preg_replace('/\s(?=\s)/', '', $OrderRow["Remark"]); 
	   $bjRemark=$OrderRow["bjRemark"];
	   $bjRemark=preg_replace('/\s(?=\s)/', '', $bjRemark); 
	   $SumQty+=$Qty;
	   $SumAmount+=$Amount;
       $Counts=count($timeArray);
       $Leadtime=$OrderRow["Leadtime"];
       $Leadtime=str_replace("*", "", $Leadtime);
       if($CompanyId == 100359 || $CompanyId ==1004){
            $Leadtime = date("d-m-Y",strtotime($Leadtime));
        }
       /*if ($Leadtime!=""){
			$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek",$link_id));
	        $PIWeek=$dateResult["PIWeek"];
	        $week=substr($PIWeek, 4,2);
		    $Leadtime="Week " . $week;   
        }*/
   
        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$OrderPO");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$eCode");
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Price");
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Qty");
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Amount");
		$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$ShipType");
		$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Leadtime");
		if ($Remark==""){
			 $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "/");
			 $objPHPExcel->getActiveSheet()->getStyle( "I$Rows")->applyFromArray($style_center);
		}
		else{
			 $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Remark");
			 $objPHPExcel->getActiveSheet()->getStyle( "I$Rows")->applyFromArray($style_left);
		}
		$objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		
		$objPHPExcel->getActiveSheet()->getStyle( "A$Rows:B$Rows")->applyFromArray($style_center);
		$objPHPExcel->getActiveSheet()->getStyle( "C$Rows")->applyFromArray($style_left);
		$objPHPExcel->getActiveSheet()->getStyle( "D$Rows:F$Rows")->applyFromArray($style_right);
		$objPHPExcel->getActiveSheet()->getStyle( "G$Rows:H$Rows")->applyFromArray($style_center);
		/*		
        $Rows++;
         $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$bjRemark");
        $objPHPExcel->getActiveSheet()->getStyle( "A$Rows")->applyFromArray($style_right);
        $objPHPExcel->getActiveSheet()->mergeCells("A$Rows:I$Rows");
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getFill()->getStartColor()->setARGB('DDDDDD');
		*/
	   $i++;
	   $Rows++;
      } while($OrderRow=mysql_fetch_array($OrderSql));
      
         $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Total");
         $objPHPExcel->getActiveSheet()->getStyle( "A$Rows")->applyFromArray($style_center);
         
         $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$SumQty");
         $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$SumAmount");
         $objPHPExcel->getActiveSheet()->getStyle( "E$Rows:F$Rows")->applyFromArray($style_right);
         $Rows++;
  }
  
 if($CompanyId == 100359 || $CompanyId ==1004){
	 $Origin  = "Origin of the goods: GOODS MADE IN CHINA";
}else{
	$Origin ="";
} 
  
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(40);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Notes: \r\n $Notes");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->applyFromArray($style_left);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:I$Rows"); 

$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(65);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Terms: \r\n $PaymentTerm \r\n $Priceterm\r\n $Origin");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->applyFromArray($style_left);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:E$Rows"); 

$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "Currency: $Symbol");
$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->applyFromArray($style_right);
$objPHPExcel->getActiveSheet()->mergeCells("F$Rows:I$Rows"); 

$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(100);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Beneficiary:  $OutBeneficiary \r\n Beneficiary bank Code: $OutBeneficiaryCode  \r\n  SWIFT Address: $OutSWIFTAddress  \r\n  Business Vantage Account Name: $OutAccountName  \r\n  Business Vantage Account Number: $OutAccountNumber  \r\n  Beneficiary bank add: $OutBankAddress");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->applyFromArray($style_left);
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:E$Rows"); 

$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "");
$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->applyFromArray($style_right);
$objPHPExcel->getActiveSheet()->mergeCells("F$Rows:I$Rows"); 

$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(25);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Authorised By");
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:B$Rows"); 
$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", " ");
$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "Signature:");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "");
$objPHPExcel->getActiveSheet()->mergeCells("E$Rows:G$Rows"); 
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "Date");
$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Date");
$objPHPExcel->getActiveSheet()->getStyle("A$Rows:I$Rows")->applyFromArray($style_center);
 
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$PI.'.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>