<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$ClientSTR="and M.CompanyId=\"$myCompanyId\"";
$ClientSTR.=$Ids==""?"":" AND SP.Id IN ($Ids)";

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



$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PO#');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Ready date');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Item Code');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Ln');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'pcs');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Air/Sea');
 $objPHPExcel->getActiveSheet()->getStyle( 'A1:F1')->applyFromArray($style_center);
$Rows=2;

$mySql="SELECT M.OrderNumber,M.CompanyId,M.OrderDate,SP.Id,SP.Qty AS thisQty,SP.ShipType,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,
P.cName,P.eCode,P.TestStandard,SP.Estate,SP.OrderSign,S.dcRemark,C.Forshort
	FROM $DataIn.ch1_shipsplit   SP  
     LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
    WHERE  1 $ClientSTR ";
$result = mysql_query($mySql,$link_id);
 if($myrow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	do{
	 	$OrderPO=$myrow["OrderPO"];
		$OrderDate=$myrow["OrderDate"];
		$eCode=$myrow["eCode"];
		$Description=$myrow["Description"];
		$ProductId=$myrow["ProductId"];
	  	$Qty=$myrow["Qty"];
		$ShipType=$myrow["ShipType"];
         $Forshort=$myrow["Forshort"];
	   if (strlen(trim($ShipType))>0){
	   	    $shipTypeResult = mysql_fetch_array(mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Id=$ShipType",$link_id));
       	    $ShipTypeStr=$shipTypeResult["Name"];
          }
       else  $ShipTypeStr="";

		$SUMQTY=$SUMQTY+$Qty;

     
        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$OrderPO");
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$OrderDate");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$eCode");
		$objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Forshort");
        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Qty");
        $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$ShipTypeStr");
		$i++; $Rows++;
		}while ($myrow = mysql_fetch_array($result));
	}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=Orders_split.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
