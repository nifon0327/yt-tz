<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
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

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('库位构件明细');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(8);

$objPHPExcel->getActiveSheet()->setCellValue('A1', '选项');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '序号');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '项目编号');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '项目名称');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '楼号');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '构件类别');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '楼层');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '构件编号');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '数量');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '构件方量');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '库位');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '入库时间');
$objPHPExcel->getActiveSheet()->setCellValue('M1', '备注');

//检索条件
$SearchRows = "where 1=1 ";

if ($CompanyId) {
    $SearchRows .= " and c.CompanyId = '$CompanyId' ";
}
if ($BuildingNo) {
    $SearchRows .= " and a.BuildingNo = '$BuildingNo' ";
}
if ($FloorNo) {
    $SearchRows .= " and a.FloorNo = '$FloorNo' ";
}
if ($SeatId) {
    $SearchRows .= " and e.SeatId = '$SeatId' ";
}
if ($cmptNo) {
    $SearchRows .= " and a.CmptNo like '%$cmptNo%' ";
}

$mySql="select  e.POrderId, e.SeatId,e.PutawayDate, b.TradeNo, c.Forshort, a.BuildingNo, a.FloorNo, a.CmptType, a.CmptNo, a.CVol 
from $DataIn.yw1_ordersheet e 
inner join $DataIn.yw1_ordermain d on e.OrderNumber = d.OrderNumber  
INNER JOIN $DataIn.trade_object c on d.CompanyId = c.CompanyId
inner join $DataIn.trade_info b on c.id = b.TradeId
inner join $DataIn.productdata dr on dr.ProductId = e.ProductId
inner join $DataIn.trade_drawing a on dr.eCode = concat_ws(\"-\",a.BuildingNo,a.FloorNo,a.CmptNo,a.SN)
$SearchRows
order by b.TradeNo, a.BuildingNo, a.FloorNo, a.CmptType, a.CmptNo";

$Rows=2;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $i=1;
    do{

        $POrderId = $myRow["POrderId"];

        $checkShipRow = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.ch1_shipsheet 
            WHERE POrderId='$POrderId'", $link_id));
        $shipQty = $checkShipRow["Qty"];


        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "");
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$i");
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $myRow['TradeNo']);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $myRow['Forshort']);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $myRow['BuildingNo']);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $myRow['CmptType']);
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $myRow['FloorNo']);
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $myRow['CmptNo']);
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $shipQty);
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $myRow['CVol']);
        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $myRow['SeatId']);
        $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $myRow['PutawayDate']);
        $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "");

        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:M$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $i++;
        $Rows++;
    }while ($myRow = mysql_fetch_array($myResult));
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=straxdata.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;

?>