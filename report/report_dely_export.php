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

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(15);//第一列

$objPHPExcel->getActiveSheet()->setCellValue('A1', '项目编号');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '项目名称');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '构件类型');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '构件总层数');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '总方量（m³）');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '完成方量');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '发货放量');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '生产层数');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '发货层数');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '最后更新时间');

$objPHPExcel->getActiveSheet()->getStyle( 'A1:J1')->applyFromArray($style_center);

$mySql="SELECT i.TradeNo, o.Forshort,c.CmptType,c.CmptFloors,c.TotalCube,c.FinishedCube,c.DeliveredCube,
c.BuildFloors,c.DeliveredFloors,c.modified
FROM $DataIn.rep_cube c 
LEFT JOIN $DataIn.trade_info i ON c.TradeId = i.TradeId
LEFT JOIN $DataIn.trade_object o ON o.Id=c.TradeId 
WHERE c.id IN ($Ids) ORDER BY c.Id";

$Rows=2;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $i=1;
    do{
        $TradeNo = $myRow['TradeNo'];
        $Forshort = $myRow['Forshort'];
        $CmptType = $myRow['CmptType'];
        $CmptFloors = $myRow['CmptFloors'];
        $TotalCube = $myRow['TotalCube'];
        $FinishedCube = $myRow['FinishedCube'];
        $DeliveredCube = $myRow['DeliveredCube'];
        $BuildFloors = $myRow['BuildFloors'];
        $DeliveredFloors = $myRow['DeliveredFloors'];
        $modified = $myRow['modified'];

        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", $TradeNo);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $Forshort);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $CmptType);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $CmptFloors);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $TotalCube);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $FinishedCube);
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $DeliveredCube);
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $BuildFloors);
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $DeliveredFloors);
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $modified);

        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $i++;
        $Rows++;
    }while ($myRow = mysql_fetch_array($myResult));
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=report.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>