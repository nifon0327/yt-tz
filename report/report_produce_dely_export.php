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

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(15);//?????????

$objPHPExcel->getActiveSheet()->setCellValue('A1', '??????');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '?????????');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '??????');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '????????????m?????');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '??????????????????m?????');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '????????????%???');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '???????????????H???');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '????????????');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '??????????????????');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '??????????????????');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '?????????????????????');

$objPHPExcel->getActiveSheet()->getStyle( 'A1:K1')->applyFromArray($style_center);

$mySql="SELECT WorkDate, WorkShop, WorkGroup, PlanCube, FinishedCube, AttainmentRate, WorkHours, WorkerNum, DPCEffy, PCHourlyEffy, 
CauseAnalysis
FROM $DataIn.rp_produce
WHERE Id IN ($Ids) ORDER BY Id";

$Rows=2;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $i=1;
    do{
        $WorkDate = $myRow['WorkDate'];
        $WorkShop = $myRow['WorkShop'];
        $WorkGroup = $myRow['WorkGroup'];
        $PlanCube = $myRow['PlanCube'];
        $FinishedCube = $myRow['FinishedCube'];
        $AttainmentRate = $myRow['AttainmentRate'];
        $WorkHours = $myRow['WorkHours'];
        $WorkerNum = $myRow['WorkerNum'];
        $DPCEffy = $myRow['DPCEffy'];
        $PCHourlyEffy = $myRow['PCHourlyEffy'];
        $CauseAnalysis = $myRow['CauseAnalysis'];

        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", $WorkDate);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $WorkShop);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $WorkGroup);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $PlanCube);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $FinishedCube);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $AttainmentRate);
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $WorkHours);
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $WorkerNum);
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $DPCEffy);
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $PCHourlyEffy);
        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $CauseAnalysis);

        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $i++;
        $Rows++;
    }while ($myRow = mysql_fetch_array($myResult));
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=report_produce.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>