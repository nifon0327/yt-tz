<?php
//电信-zxq 2013-07-11
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';

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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);


$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->mergeCells("A1:O1"); 
$objPHPExcel->getActiveSheet()->setCellValue("A1", "社保".date("Y-m")."资料表");
 $objPHPExcel->getActiveSheet()->getStyle( 'A1:O1')->applyFromArray($style_center);
$objPHPExcel->getActiveSheet()->getStyle('A1:O1')->getFont()->setSize(18);

$Rows=2;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "姓名");
$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "月份");
$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "个人缴费");
$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "公司缴费");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "小计");

$objPHPExcel->getActiveSheet()->getStyle( "A$Rows:E$Rows")->applyFromArray($style_center);
$Rows=3;
$currentMonth = date("Y-m");
$mySql="SELECT A.TypeId,A.Month,A.mAmount,A.cAmount, B.Name
        FROM $DataIn.sbpaysheet A
        INNER JOIN $DataIn.staffmain B On A.Number = B.Number
        WHERE A.Month='$currentMonth' AND A.TypeId=1";

$staffResult = mysql_query($mySql);
while($staffRow = mysql_fetch_assoc($staffResult)){

    $name = $staffRow['Name'];
    $Month = $staffRow['Month'];
    $mAmount = $staffRow['mAmount'];
    $cAmount = $staffRow['cAmount'];
    $amount = $cAmount+$mAmount;

    $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$name");
    
    $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$Month");
    
    $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$mAmount");
            
    $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$cAmount");
    
    $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$amount");
    
    
    $i++; 
    $Rows++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=社保'.$currentMonth.'.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>