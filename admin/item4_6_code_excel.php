<?php
include "../basic/chksession.php";
include "../basic/parameter.inc";

$POrderIds = explode('|', $_REQUEST['selectsPOrderIds']);
$workShop = $_REQUEST["workShop"];
$Forshort = $_REQUEST["Forshort"];
$OrderPO = $_REQUEST["OrderPO"];
$created = $_REQUEST["created"];
/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
$row = 3;
foreach ($POrderIds as $k => $v) {
    // 获取构件名称
    $cName = $CStr = $liningno = ' ';
    $mySql = "SELECT DISTINCT P.ProductId,P.cName,P.eCode,S.OrderPO,DATE_FORMAT(SC.scDate,'%Y-%m-%d') as scDate,
             IFNULL(P.CStr,TD.CStr) AS CStr ,S.liningno FROM $DataIn.yw1_scsheet SC
          INNER JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
          INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
          LEFT JOIN $DataIn.trade_drawing TD ON TD.Id=P.drawingId 
          WHERE 1 and S.Estate>0 AND SC.POrderId=$v";
       
    $myResult = mysql_query($mySql . " $PageSTR", $link_id);
    if ($myRow = mysql_fetch_array($myResult)) {
        $cName = $myRow['cName'];
        $eCode = $myRow['eCode'];
        $OrderPO = $myRow['OrderPO'];
        $scDate = $myRow['scDate'];
        $CStr = $myRow['CStr'];
        $liningno = $myRow['liningno'];
        $ProductId = $myRow["ProductId"];
    }

    // 插入二维码
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    // 本地图片文件路径
    $objDrawing->setPath("./QRCode/$v.png");
    // 图片高
    $objDrawing->setHeight(77);
    // 图片宽
    $objDrawing->setWidth(77);
    // 单元格
    $objDrawing->setCoordinates("C$row");
    // 图片偏移距离
    $objDrawing->setOffsetX(3);
    $objDrawing->setOffsetY(2);
    $objDrawing->setName('二维码');
    $objDrawing->setDescription('二维码');

    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

//设置列宽
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('21.8');
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('16.8');

   // 设置对齐
    //全局竖直居中
    $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $tempArr = explode('-', $cName);
    $tempNo = count($tempArr) - 1;
    $buildFloors = $tempArr[0] . '-' . $tempArr[1] . '-';
    $buildFloor = $tempArr[0] . '# ' . $tempArr[1] . 'F';
    $PcName = str_replace('-' . $tempArr[$tempNo], '', str_replace($buildFloors, '', $cName));
    $PcNames = str_replace('-' . $tempArr[$tempNo], '', str_replace($buildFloors, '', $cName)) . ' / ' . $CStr;

// 表1
    $objPHPExcel->getActiveSheet()->setCellValue("A$row", '项目名称');
    $objPHPExcel->getActiveSheet()->setCellValue("B$row", $Forshort);
    $row++;
    $objPHPExcel->getActiveSheet()->setCellValue("A$row", '楼栋/层');
    $objPHPExcel->getActiveSheet()->setCellValue("B$row", $buildFloor);
    $row++;
    $objPHPExcel->getActiveSheet()->setCellValue("A$row", 'PC编号');
    $objPHPExcel->getActiveSheet()->setCellValue("B$row", $PcName);
    $row++;
    $objPHPExcel->getActiveSheet()->setCellValue("A$row", '混凝土强度');
    $objPHPExcel->getActiveSheet()->setCellValue("B$row", $CStr);
    $row++;
    $objPHPExcel->getActiveSheet()->setCellValue("A$row", '台车号');
    $objPHPExcel->getActiveSheet()->setCellValue("B$row", $liningno);
    $objPHPExcel->getActiveSheet()->setCellValue("C$row", $ProductId);
    $row += 5;

// 字体
    $objPHPExcel->getDefaultStyle()->getFont()->setName("黑体")->setSize(8);
}
// 设置行高
for ($i = 1; $i <= $row; $i++) {
    $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
}


$dates = date('Y-m-d');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=PC构件-二维码-' . $created . '.xlsx');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>