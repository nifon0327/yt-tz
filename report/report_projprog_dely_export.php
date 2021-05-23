<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

$Ids = $_REQUEST["Ids"];

/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$style_left= array(
        'font'    => array (
                'size'      => 10
        ),
        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true
        )
);

$style_center = array(
        'font'    => array (
                'size'      => 10
        ),
        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true
        )
);

$style_right = array(
        'font'    => array (
                'size'      => 10
        ),
        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true
        )
);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(15);//第一列

$objPHPExcel->getActiveSheet()->setCellValue('A1', '项目编号');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '项目名称');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '构件类型');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '吊装总层数');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '施工楼层');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '总方量（m³）');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '构件总数量');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '生产完成方量（m³）');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '发货方量（m³）');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '首批构件供货时间');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '构件生产结束时间');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '跟新时间');

$objPHPExcel->getActiveSheet()->getStyle( 'A1:L1')->applyFromArray($style_center);

$mySql="SELECT TradeNo, TradeName, CmptType, HoistFloors, WorkFloor, TotalCube, CmptTotal, FinishedCube, DeliveredCube,
FCmptDTime, CmptETime, UpdatedTime
FROM $DataIn.rp_projectprogress 
WHERE id IN ($Ids) ORDER BY Id";

$Rows=2;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $i=1;
    do{
        $TradeNo = $myRow['TradeNo'];
        $TradeName = $myRow['TradeName'];
        $CmptType = $myRow['CmptType'];
        $HoistFloors = $myRow['HoistFloors'];
        $WorkFloor = $myRow['WorkFloor'];
        $TotalCube = $myRow['TotalCube'];
        $CmptTotal = $myRow['CmptTotal'];
        $FinishedCube = $myRow['FinishedCube'];
        $DeliveredCube = $myRow['DeliveredCube'];
        $FCmptDTime = $myRow['FCmptDTime'];
        $CmptETime = $myRow['CmptETime'];
        $UpdatedTime = $myRow['UpdatedTime'];

        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", $TradeNo);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $TradeName);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $CmptType);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $HoistFloors);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $WorkFloor);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $TotalCube);
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $CmptTotal);
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $FinishedCube);
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $DeliveredCube);
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $FCmptDTime);
        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $CmptETime);
        $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $UpdatedTime);

        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $i++;
        $Rows++;
    }while ($myRow = mysql_fetch_array($myResult));
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=report_projprog.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>