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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(27);
$objPHPExcel->getActiveSheet()->setCellValue('A1', '订单编号');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '客户名称');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '产品编号');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '产品名称');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '价格');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '数量');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '总额');

$objPHPExcel->getActiveSheet()->getStyle( 'A1:G1')->applyFromArray($style_center);


$mySql="select S.Id, S.POrderId,C.Forshort, S.ProductId, P.cName, S.Price,S.Qty from $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M on M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.trade_object  C ON M.CompanyId=C.CompanyId 
INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
WHERE S.id in ($Ids) order by S.Id";

$Rows=2;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $i=1;
    do{
        $POrderId = $myRow['POrderId'];
        $Forshort = $myRow['Forshort'];
        $ProductId = $myRow['ProductId'];
        $cName = $myRow['cName'];
        $Price = $myRow['Price'];
        $Qty = $myRow['Qty'];
        
        $Price=$Price==""?0:$Price;
        $Qty=$Qty==""?0:$Qty;
        
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", $POrderId);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $Forshort);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $ProductId);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $cName);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $Price);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $Qty);
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $Price * $Qty);
        
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        
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