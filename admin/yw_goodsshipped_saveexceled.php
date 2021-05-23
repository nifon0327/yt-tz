<?php   
//电信-zxq 2013-07-11

include "../basic/chksession.php" ;
include "../basic/parameter.inc";

/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';

$eDate=$eDate==""?date("Y-m-d"):$eDate;
$SearchRows=$CompanyId==""?"":" AND M.CompanyId='$CompanyId'";
$SearchRows.=$InvoiceNO==""?"":" AND M.InvoiceNO LIKE '$InvoiceNO%'";
$SearchRows.=" AND M.Date>='$sDate' AND M.Date<='$eDate' ";
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
  
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'NO');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Invoice No');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Amount');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Shipping Date');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Accounting Date');
$objPHPExcel->getActiveSheet()->getStyle( 'A1:E1')->applyFromArray($style_right);
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->getStartColor()->setARGB('AAAAAA');
 $Rows=2;$i=1;
$OrderSql=mysql_query("SELECT M.Id,M.CompanyId,M.InvoiceNO,M.Date,M.Sign FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows   ORDER BY M.InvoiceNO",$link_id);
 if($OrderRow=mysql_fetch_array($OrderSql)){
   $SumAmount=0;
   do{
      $Id=$OrderRow["Id"];
       $InvoiceNO=$OrderRow["InvoiceNO"];
	   $Date=$OrderRow["Date"];
	   $Sign=$OrderRow["Sign"];
	   $CompanyId=$OrderRow["CompanyId"];
	   
	  //订单出货金额
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount  FROM $DataIn.ch1_shipsheet WHERE Mid='$Id' AND (Type=1 OR Type=3)",$link_id));
		$Amount=round(($checkAmount["Amount"])*$Sign,2);
		
		 //其它金额                 
         $checkAmount2=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id' AND Type=2",$link_id));
          $Amount2=round(($checkAmount2["Amount"])*$Sign,2);
          
         $Amount+=$Amount2;
         $SumAmount+=$Amount;
		 $AmountSTR=number_format($Amount,2);

       $checkShipAmount=mysql_query("SELECT M.PayDate,SUM(S.Amount) AS ShipAmount
		FROM $DataIn.cw6_orderinsheet S 
		LEFT JOIN $DataIn.cw6_orderinmain M ON M.Id=S.Mid
		WHERE S.chId='$Id' GROUP BY S.chId",$link_id);
		if($ShipRow=mysql_fetch_array($checkShipAmount)){
		    $PayDate=$ShipRow["PayDate"];
		    $PayAmount=round($ShipRow["ShipAmount"],2);
		}
		else{
			$PayDate="";
			$PayAmount=0;
		}
        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$InvoiceNO");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$AmountSTR");
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Date");
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$PayDate");
		if (abs($PayAmount-$Amount)>0){
				$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		}
	   $i++;
	   $Rows++;
      } while($OrderRow=mysql_fetch_array($OrderSql));
      $objPHPExcel->getActiveSheet()->getStyle( "A2:E$Rows")->applyFromArray($style_right);
      
        $SumAmount=number_format($SumAmount,2);
         $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Total");
         $objPHPExcel->getActiveSheet()->getStyle( "A$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$SumAmount");
         $objPHPExcel->getActiveSheet()->getStyle( "A$Rows:E$Rows")->applyFromArray($style_right);
         $objPHPExcel->getActiveSheet()->getStyle("A$Rows:E$Rows")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
         $objPHPExcel->getActiveSheet()->getStyle("A$Rows:E$Rows")->getFill()->getStartColor()->setARGB('AAAAAA');
         $Rows++;
  }
 
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$CompanyId.'.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>