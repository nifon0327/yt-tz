<?php
include "../basic/chksession.php";
include "../basic/parameter.inc";

$Ids = $_REQUEST["Ids"];
$S = $_REQUEST['S'];
/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$style_left = array(
    'font'      => array(
        'size' => 10,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'       => true,
    ),
);

$style_center = array(
    'font'      => array(
        'size' => 10,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'       => true,
    ),
);

$style_right = array(
    'font'      => array(
        'size' => 10,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'       => true,
    ),
);

$style_center_bold = array(
    'font' => array (
        'size' => 10,
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
)
);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
/*$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);*/

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(15);
$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(27);

$objPHPExcel->getActiveSheet()->setCellValue('A1', $S);
$objPHPExcel->getActiveSheet()->setCellValue('A2', '以配件为基准');
$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($style_center_bold);

$objPHPExcel->getActiveSheet()->setCellValue('A3', '');
$objPHPExcel->getActiveSheet()->setCellValue('B3', '项目名称');
$objPHPExcel->getActiveSheet()->setCellValue('C3', '配件ID');
$objPHPExcel->getActiveSheet()->setCellValue('D3', '配件编码');
$objPHPExcel->getActiveSheet()->setCellValue('E3', '含税价');
$objPHPExcel->getActiveSheet()->setCellValue('F3', '税率');
$objPHPExcel->getActiveSheet()->setCellValue('G3', '配件名称');
$objPHPExcel->getActiveSheet()->setCellValue('H3', '供应商');
$objPHPExcel->getActiveSheet()->setCellValue('I3', '品牌');
$objPHPExcel->getActiveSheet()->setCellValue('J3', '采购员');
$objPHPExcel->getActiveSheet()->setCellValue('K3', '单位');
$objPHPExcel->getActiveSheet()->setCellValue('L3', '需购数量');
$objPHPExcel->getActiveSheet()->setCellValue('M3', '使用库存');
$objPHPExcel->getActiveSheet()->setCellValue('N3', '增购数量');
$objPHPExcel->getActiveSheet()->setCellValue('O3', '实够数量');
$objPHPExcel->getActiveSheet()->setCellValue('P3', '金额');
/*$objPHPExcel->getActiveSheet()->setCellValue('L1', '预定交期');*/
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style_left);
$objPHPExcel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($style_center);
/*$objPHPExcel->getActiveSheet()->getStyle( 'A1:L1')->applyFromArray($style_center);*/

$mySql = "select OrderPO,Forshort,StuffId,StuffCname,StuffEname,Price,Forshort2,sum(StockQty) as StockQty,sum(AddQty) as AddQty,sum(FactualQty) as FactualQty,Remark ,`name`,DeliveryDate,UnitName,TaxName from(SELECT Y.OrderPO, O1.Forshort, S.StuffId, A.StuffCname, A.StuffEname, S.Price, O2.Forshort AS Forshort2 , S.StockQty,S.AddQty,S.FactualQty,
 A.Remark, F.name,  S.DeliveryDate, U.name AS UnitName, X.name AS TaxName
from $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object O1 ON O1.CompanyId = M.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.trade_object O2 ON O2.CompanyId = S.CompanyId
LEFT JOIN $DataPublic.staffmain F ON F.Number = S.BuyerId
LEFT JOIN $DataIn.providersheet P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataIn.provider_addtax X ON X.Id = P.AddValueTax 
WHERE S.id in ($Ids) order by S.Id) as ttt GROUP BY StuffId";
$Rows = 4;
$OrderPO = $tempPO ='';
$myResult = mysql_query($mySql, $link_id);
if ($myResult && $myRow = mysql_fetch_assoc($myResult)) {
    $i = 1;
    do {
        $OrderPO = explode('-', $myRow['OrderPO'])[2];
        $Forshort = $myRow['Forshort'];
        $StuffId = $myRow['StuffId'];
        $StuffCname = $myRow['StuffCname'];
        $StuffEname = $myRow['StuffEname'];
        $Price = $myRow['Price'];
        $Forshort2 = $myRow['Forshort2'];
        $name = $myRow['name'];
        $StockQty = $myRow['StockQty'];
        $AddQty = $myRow['AddQty'];
        $FactualQty = $myRow['FactualQty'];
        $UnitName = $myRow['UnitName'];
        $Qty = $AddQty + $FactualQty;
        $Amount = sprintf("%.2f", $Qty * $Price);//本记录金额合计

        $Price = $Price == "" ? 0 : $Price;

        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", '');
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $Forshort);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $StuffId);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $StuffEname);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $Price);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $myRow['TaxName']); //税率
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $StuffCname);
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $Forshort2);
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $myRow['Remark']); //品牌
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $name);
        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $UnitName);
        $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $FactualQty);
        $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", $StockQty);
        $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", $AddQty);
        $objPHPExcel->getActiveSheet()->setCellValue("O$Rows", $Qty);
        $objPHPExcel->getActiveSheet()->setCellValue("P$Rows", $Amount);
//        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $StockId);
        /*  $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $DeliveryDate);*/
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        /*$objPHPExcel->getActiveSheet()->getStyle("A$Rows:L$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        */
        $i++;
        $Rows++;
    } while ($myResult && $myRow = mysql_fetch_array($myResult));
}
$objPHPExcel->getActiveSheet()->getStyle("A4:A$Rows")->applyFromArray($style_center);

$line_PO = $Rows + 1;
$objPHPExcel->getActiveSheet()->setCellValue("A$line_PO", "以PO为基准");
$objPHPExcel->getActiveSheet()->getStyle("A$line_PO")->applyFromArray($style_center_bold);

$Rows = $Rows + 2;

$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "PO");
$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "项目名称");
$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "配件ID");
$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "配件编码");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "含税价");
$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "税率");
$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "配件名称");
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "供应商");
$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "品牌");
$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "采购员");
$objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "单位");
$objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "需购数量");
$objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "使用库存");
$objPHPExcel->getActiveSheet()->setCellValue("N$Rows", "增购数量");
$objPHPExcel->getActiveSheet()->setCellValue("O$Rows", "实够数量");
$objPHPExcel->getActiveSheet()->setCellValue("P$Rows", "金额");

$objPHPExcel->getActiveSheet()->getRowDimension($Rows)->setRowHeight(27);
$objPHPExcel->getActiveSheet()->getStyle("A{$Rows}:P{$Rows}")->applyFromArray($style_center);

$mySql1 = "select OrderPO,Forshort,StuffId,StuffCname,StuffEname,Price,Forshort2,sum(StockQty) as StockQty,sum(AddQty) as AddQty,sum(FactualQty) as FactualQty,Remark ,`name`,DeliveryDate,UnitName,TaxName from(SELECT Y.OrderPO, O1.Forshort, S.StuffId, A.StuffCname, A.StuffEname, S.Price, O2.Forshort AS Forshort2 , S.StockQty,S.AddQty,S.FactualQty,
 A.Remark, F.name,  S.DeliveryDate, U.name AS UnitName, X.name AS TaxName
from $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object O1 ON O1.CompanyId = M.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.trade_object O2 ON O2.CompanyId = S.CompanyId
LEFT JOIN $DataPublic.staffmain F ON F.Number = S.BuyerId
LEFT JOIN $DataIn.providersheet P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataIn.provider_addtax X ON X.Id = P.AddValueTax 
WHERE S.id in ($Ids) order by S.Id) as ttt GROUP BY OrderPO,StuffId";
$Rows++;
$OrderPO = $tempPO ='';
$myResult1 = mysql_query($mySql1, $link_id);
if ($myResult && $myRow = mysql_fetch_assoc($myResult1)) {
    $i = 1;
    do {
        $OrderPO = $myRow['OrderPO'];
        $Forshort = $myRow['Forshort'];
        $StuffId = $myRow['StuffId'];
        $StuffCname = $myRow['StuffCname'];
        $StuffEname = $myRow['StuffEname'];
        $Price = $myRow['Price'];
        $Forshort2 = $myRow['Forshort2'];
        $name = $myRow['name'];
        $StockQty = $myRow['StockQty'];
        $AddQty = $myRow['AddQty'];
        $FactualQty = $myRow['FactualQty'];
        $UnitName = $myRow['UnitName'];
        $Qty = $AddQty + $FactualQty;
        $Amount = sprintf("%.2f", $Qty * $Price);//本记录金额合计

        $Price = $Price == "" ? 0 : $Price;

        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", $OrderPO);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $Forshort);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $StuffId);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $StuffEname);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $Price);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $myRow['TaxName']); //税率
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $StuffCname);
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $Forshort2);
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $myRow['Remark']); //品牌
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $name);
        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $UnitName);
        $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $FactualQty);
        $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", $StockQty);
        $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", $AddQty);
        $objPHPExcel->getActiveSheet()->setCellValue("O$Rows", $Qty);
        $objPHPExcel->getActiveSheet()->setCellValue("P$Rows", $Amount);
//        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $StockId);
        /*  $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $DeliveryDate);*/
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        /*$objPHPExcel->getActiveSheet()->getStyle("A$Rows:L$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        */
        $i++;
        $Rows++;
    } while ($myResult && $myRow = mysql_fetch_array($myResult1));
}

$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=straxdata.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>