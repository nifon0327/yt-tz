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
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
/*$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);*/

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(27);

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PO');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '项目名称');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '配件ID');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '配件编码');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '含税价');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '税率');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '配件名称');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '供应商');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '品牌');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '采购员');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '采购流水号');
/*$objPHPExcel->getActiveSheet()->setCellValue('L1', '预定交期');*/

$objPHPExcel->getActiveSheet()->getStyle( 'A1:K1')->applyFromArray($style_center);
/*$objPHPExcel->getActiveSheet()->getStyle( 'A1:L1')->applyFromArray($style_center);*/


$mySql="SELECT Y.OrderPO, O1.Forshort, S.StuffId, A.StuffCname, A.StuffEname, S.Price, O2.Forshort AS Forshort2, 
 A.Remark, F.name, S.StockId, S.DeliveryDate, X.name AS TaxName
from $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object O1 ON O1.CompanyId = M.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataIn.trade_object O2 ON O2.CompanyId = S.CompanyId
LEFT JOIN $DataPublic.staffmain F ON F.Number = S.BuyerId
LEFT JOIN $DataIn.providersheet P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataIn.provider_addtax X ON X.Id = P.AddValueTax 
WHERE S.id in ($Ids) order by S.Id";

$Rows=2;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $i=1;
    do{
        $OrderPO = $myRow['OrderPO'];
        $Forshort = $myRow['Forshort'];
        $StuffId = $myRow['StuffId'];
        $StuffCname = $myRow['StuffCname'];
        $StuffEname = $myRow['StuffEname'];
        $Price = $myRow['Price'];
        $Forshort2 = $myRow['Forshort2'];
        $name = $myRow['name'];
        $StockId = $myRow['StockId'];
        $DeliveryDate = $myRow['DeliveryDate'];
        
        $Price=$Price==""?0:$Price;
        
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
        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $StockId);
      /*  $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $DeliveryDate);*/

        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        /*$objPHPExcel->getActiveSheet()->getStyle("A$Rows:L$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        */
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