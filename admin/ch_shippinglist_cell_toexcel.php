<?php
include "../basic/chksession.php";
include "../basic/parameter.inc";

/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
$style_left = array(
    'font' => array(
        'size' => 10
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap' => true
    )
);

$style_center = array(
    'font' => array(
        'size' => 10
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap' => true
    )
);

$style_right = array(
    'font' => array(
        'size' => 10
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap' => true
    )
);

$style_border = array(

    'borders' => array(

        'allborders' => array(

            'style' => PHPExcel_Style_Border::BORDER_THIN,//边框是粗的

        ),

    ),

);

$Ids = $_GET["Ids"];
$CompanyId = $_GET["CompanyId"];
$Building = $_GET["Building"];
$Floor = $_GET["Floor"];

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();


$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('出货单元');

//行高
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);


//字体样式
$objPHPExcel->getDefaultStyle()->getFont()->setName('宋体');
//    $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($style_border);
$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


$objPHPExcel->getActiveSheet()->setCellValue('A1', '序号');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '项目');
//$objPHPExcel->getActiveSheet()->setCellValue('C1', '楼栋');
//$objPHPExcel->getActiveSheet()->setCellValue('D1', '楼层');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '类型');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '构件名称');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '出货单元');


//    $mySql = "SELECT T.Id,T.CompanyId,T.Forshort,D.BuildingNo,D.FloorNo,D.CmptType,D.CmptNo,P.cName
////    FROM productdata P
////	INNER JOIN trade_object T ON P.CompanyId = T.CompanyId
////	INNER JOIN trade_drawing D ON P.drawingId = D.Id
////	WHERE P.Id IN ($Ids)";

$mySql = "SELECT T.Id,T.CompanyId,T.Forshort,D.BuildingNo,D.FloorNo,D.CmptType,D.CmptNo,P.cName 
    FROM productdata P
	INNER JOIN trade_object T ON P.CompanyId = T.CompanyId
	INNER JOIN trade_drawing D ON P.drawingId = D.Id
	WHERE P.CompanyId = '$CompanyId' 
	GROUP BY D.CmptNo ORDER BY D.CmptNo";

$Rows = 2;
$myResult = mysql_query($mySql, $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $company = $myRow['Forshort'];
    $i = 1;
    do {
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $myRow['Forshort']);
//        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $myRow["BuildingNo"]);
//        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $myRow['FloorNo']);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $myRow['CmptType']);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $myRow['CmptNo']);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $myRow[""]);

        $Rows++;
        $i++;
    } while ($myRow = mysql_fetch_array($myResult));
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $company . ' 出货单元设置.xls');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit;

?>