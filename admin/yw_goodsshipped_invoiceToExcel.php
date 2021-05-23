<?php
	 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

/** Include PHPExcel */
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

  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
 
  $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
  $objPHPExcel->getActiveSheet()->setCellValue('A1', 'order');
  $objPHPExcel->getActiveSheet()->setCellValue('B1', 'item');
  $objPHPExcel->getActiveSheet()->setCellValue('C1', 'quantity');
  $objPHPExcel->getActiveSheet()->setCellValue('D1', 'delivery date');
  $objPHPExcel->getActiveSheet()->setCellValue('E1', 'shippig way');
    
  $objPHPExcel->getActiveSheet()->getStyle( 'A1:H1')->applyFromArray($style_center);
  $Rows=2;

  $mySql="SELECT S.Id,S.POrderId,O.OrderPO,P.eCode,S.Qty,A.Name as ShipName,M.Date,M.InvoiceNO
    	  FROM $DataIn.ch1_shipsheet S
    	  Left Join $DataIn.ch1_shipmain M On M.Id = S.Mid
  		  LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
  		  LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=O.Id 
  		  LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
  		  Left Join $DataPublic.ch_shiptype A On A.Id = M.Ship
  		  WHERE S.Mid='$Id' AND S.Type='1'
  		  UNION ALL 
  		  SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS eCode,S.Qty,A.Name as ShipName,M.Date,M.InvoiceNO
  		  FROM $DataIn.ch1_shipsheet S 
  		  Left Join $DataIn.ch1_shipmain M On M.Id = S.Mid
  		  LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId 
  		  Left Join $DataPublic.ch_shiptype A On A.Id = M.Ship
  		  WHERE S.Mid='$Id' AND S.Type='2' AND O.Type='1'";
  	  
  $myResult = mysql_query($mySql);
  while($myRow = mysql_fetch_assoc($myResult))
  {
	  $OrderPO = $myRow["OrderPO"];
	  $eCode = $myRow["eCode"];
	  $qty = $myRow["Qty"];
	  $date = $myRow["Date"];
	  $ship = $myRow["ShipName"];
	  $invoiceNo = $myRow["InvoiceNO"];
	  
	  $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
	  $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$OrderPO");
	  $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$eCode");
	  $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$qty");
	  $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$date");
	  $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$ship");
	  $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  
	  $i++; 
	  $Rows++;
	  
  }

  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header("Content-Disposition: attachment;filename=$invoiceNo.xlsx");
  header('Cache-Control: max-age=0');

  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  $objWriter->save('php://output');
  exit;

?>