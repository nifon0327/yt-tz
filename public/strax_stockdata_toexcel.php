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
	



$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Picture');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Item No.');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Description');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Ash Cloud Code');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Unit Price');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Last Shipped Date');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Total Shipped Qty');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Stock Qty');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Stock Rate');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Stock in USD');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Stock in EUR');
$objPHPExcel->getActiveSheet()->getStyle( 'A1:K1')->applyFromArray($style_center);
$Rows=2;
$mySql="SELECT  D.eCode,D.StockQty,D.StockAmount,P.cName,IFNULL(P.Description,D.Description) AS Description,IFNULL(P.ProductId,0) AS ProductId,P.Price,P.TestStandard
FROM $DataIn.straxdata D 
LEFT JOIN $DataIn.productdata P ON P.eCode = D.eCode
WHERE 1 ORDER BY P.ProductId DESC";
$myResult = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($myResult)){
	$i=1;
	$Page=1;
	do{
	 	$eCode = $myRow["eCode"];
	 	$StockQty= $myRow["StockQty"];
		$StockEUR= $myRow["StockAmount"];
		$cName = $myRow["cName"];
		$Description = $myRow["Description"];
		$ProductId = $myRow["ProductId"];
		$TestStandard = $myRow["TestStandard"];
		$Price = $myRow["Price"];
		
	    $LastMonth ="";  $ShipQty  ="";
	    if($ProductId>0){
		    $CheckLastMonthRow= mysql_fetch_array(mysql_query("SELECT 
		            DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,
		            TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,
		            SUM(Qty) AS ShipQty           
	                FROM $DataIn.ch1_shipmain M 
		            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
	                WHERE  S.ProductId ='$ProductId'",$link_id));
			$LastMonth = $CheckLastMonthRow["LastMonth"];
			$ShipQty  = $CheckLastMonthRow["ShipQty"];
			$LastYear = substr($LastMonth, 0, 4);
	    }
	    $StockQty = str_replace(",", "", $StockQty);
        if($ShipQty>0){
	       $StockRate = round($StockQty/$ShipQty, 4) * 100;
	       $StockRateStr = $StockRate."%";
        }else{
	       $StockRate =0;
	       $StockRateStr ="";
        }
	    $StockEUR = str_replace(",", "", $StockEUR);
        $StockUSA = round($StockEUR/1.17,2);
       
       
        $AppFilePath="../download/productIcon/" .$ProductId.".jpg";
        
        if(file_exists($AppFilePath)){
	        $objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Photo');
			$objDrawing->setDescription('Photo');
			$objDrawing->setPath($AppFilePath);
			$objDrawing->setHeight(80);
			$objDrawing->setWidth(80);
			$objDrawing->setCoordinates("A$Rows");
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		
        $objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(65);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "");
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$eCode");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$Description");
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$cName");
        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Price");
		$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$LastMonth");
		$objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$ShipQty");
	    $objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$StockQty");
		$objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$StockRateStr");
		$objPHPExcel->getActiveSheet()->getStyle("J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$StockUSA");
		$objPHPExcel->getActiveSheet()->getStyle("K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "$StockEUR");
		
		if($StockRate>=10){
			$objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		}
		
		if($LastYear>='2016'){
			$objPHPExcel->getActiveSheet()->getStyle( "B$Rows:K$Rows")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle( "B$Rows:K$Rows")->getFill()->getStartColor()->setARGB('9AFF9A');
		}
		
		$i++; $Rows++;
	}while ($myRow = mysql_fetch_array($myResult));
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=straxdata.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
