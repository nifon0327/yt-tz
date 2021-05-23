<?php
/*
 * By.LWH
 * 领料信息导出
 * */
include "../basic/chksession.php";
include "../basic/parameter.inc";

$name = "领料单-" . date('Ymd');
$type = "2003";
$SearchRows = " AND L.Estate>=0 ";
$blWorkShopId = $_REQUEST['blWorkShopId'];
$bigClass = $_REQUEST['bigClass'];
$bigClassName = $_REQUEST['bigClassName'];
$llTypeId = $_REQUEST['llTypeId'];
$llTypeIdName = $_REQUEST['llTypeIdName'];
$khCompanyId = $_REQUEST['khCompanyId'];
$OrderPO = $_REQUEST['OrderPO'];
$creator = $_REQUEST['creator'];
$created = $_REQUEST['created'];
if ($blWorkShopId && $blWorkShopId != 'all' && $blWorkShopId != 'undefined') $SearchRows .= " AND SC.WorkShopId='$blWorkShopId'";
if ($khCompanyId && $blWorkShopId != 'all' && $blWorkShopId != 'undefined') $SearchRows .= " and O.Forshort='$khCompanyId' ";
if ($OrderPO && $OrderPO != 'all' && $OrderPO != 'undefined') $SearchRows .= " and Y.OrderPO='$OrderPO' ";
if ($creator && $creator != 'all' && $creator != 'undefined') $SearchRows .= " and STM.Number='$creator' ";
if ($created && $created != 'all' && $created != 'undefined') $SearchRows .= " and DATE_FORMAT(SC.scDate,'%Y-%m-%d') = '$created' ";
if ($bigClass && $bigClass != 'all' && $bigClass != 'undefined') $SearchRows1 = " and T.TypeId = '$bigClass' ";
if ($bigClass == "9002" ){
    $SearchRows1 .= " OR D.Spec = '桁架' ";
}else if ($bigClass == "9019"){
    $SearchRows1 .= " AND D.Spec <> '桁架' ";
}

$SearchRows1 .= " AND D.Spec NOT IN ('扎丝','脱模剂','垫块','线管')  ";

$mySql = "SELECT DISTINCT L.StockId
FROM $DataIn.ck5_llsheet L 
INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
LEFT  JOIN $DataIn.staffmain STM  ON STM.Number = L.creator
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
INNER JOIN $DataIn.cg1_semifinished  SM ON SM.StockId = SC.StockId
LEFT  JOIN $DataIn.cg1_stocksheet    CG  ON CG.StockId = SC.mStockId
LEFT  JOIN $DataIn.stuffdata         D  ON D.StuffId = SM.mStuffId 
LEFT  JOIN $DataIn.stufftype         T  ON T.TypeId = D.TypeId
LEFT  JOIN $DataIn.cg1_stockmain     M  ON M.Id = CG.Mid 
WHERE  1 $SearchRows ORDER BY L.StockId";
//echo $mySql;die;
$myResult = mysql_query($mySql, $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $i = 1;
    do {
        if ($i == 1) {
            $StockId = $myRow['StockId'];
        }
        else {
            $StockId .= ',' . $myRow['StockId'];
        }
        $i++;
    } while ($myRow = mysql_fetch_array($myResult));

}

/** Include PHPExcel */
include '../plugins/PHPExcel/Classes/PHPExcel.php';
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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);

if ($blWorkShopId) {
    $myResult = mysql_query("select `name` from $DataIn.workshopdata where Id = $blWorkShopId", $link_id);

    $myRow = mysql_fetch_array($myResult);
    $blWorkShopId = $myRow['name'];
}

if ($creator) {
    $myResult = mysql_query("select `name` from $DataIn.staffmain where  `Number` = $creator", $link_id);
    $myRow = mysql_fetch_array($myResult);
    $creator = $myRow['name'];
}
$blWorkShopId = ($blWorkShopId == '' || $blWorkShopId == 'all' || $blWorkShopId == 'undefined') ? '全部' : $blWorkShopId;
$khCompanyId = ($khCompanyId == '' || $khCompanyId == 'all' || $khCompanyId == 'undefined') ? '全部客户' : $khCompanyId;
$OrderPO = ($OrderPO == '' || $OrderPO == 'all' || $OrderPO == 'undefined') ? '全部' : $OrderPO;
$creator = ($creator == '' || $creator == 'all' || $creator == 'undefined') ? '全部' : $creator;
$created = ($created == '' || $created == 'all' || $created == 'undefined') ? '全部' : $created;


$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
$objPHPExcel->getActiveSheet()->setCellValue('A1', "$bigClassName|$blWorkShopId|$llTypeIdName|$created|$khCompanyId|$OrderPO|$creator");

$objPHPExcel->getActiveSheet()->getStyle("A1:G1")->applyFromArray($style_left);
$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(27);


$objPHPExcel->getActiveSheet()->setCellValue('A2', '序号');
$objPHPExcel->getActiveSheet()->setCellValue('B2', '配件ID');
$objPHPExcel->getActiveSheet()->setCellValue('C2', '配件名称');
$objPHPExcel->getActiveSheet()->setCellValue('D2', '单位');
$objPHPExcel->getActiveSheet()->setCellValue('E2', '库存');
$objPHPExcel->getActiveSheet()->setCellValue('F2', '需领料');
$objPHPExcel->getActiveSheet()->setCellValue('G2', '库位编号');


$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($style_center);


$mySql = "SELECT
	SUM( A.OrderQty * 1 ) AS OrderQty,
	K.tStockQty,
	D.StuffId,
	D.StuffCname,
	D.Picture,
	F.Remark,
	M.NAME,
	P.Forshort,
	U.NAME AS UnitName,
	U.Decimals ,
	T.SeatId
FROM
	$DataIn.cg1_semifinished A
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = A.StockId
	INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId = A.StuffId
	INNER JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number = G.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId = G.CompanyId
	LEFT JOIN $DataIn.base_mposition F ON F.Id = D.SendFloor
	INNER JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
	INNER JOIN $DataIn.stuffmaintype MT ON MT.Id = T.mainType
	INNER JOIN $DataIn.stuffunit U ON U.Id = D.Unit 
WHERE A.StockId IN (
$StockId) 
	AND MT.blSign = 1 $SearchRows1
GROUP BY
	D.StuffId 
ORDER BY
	D.StuffId";


$Rows = 3;
$Forshorts = "";
$myResult = mysql_query($mySql, $link_id);
if ($myRow = mysql_fetch_array($myResult)) {

    $i = 1;
    do {
        $StuffId = $myRow['StuffId'];
        $StuffCname = $myRow['StuffCname'];
        $UnitName = $myRow['UnitName'];
        $OrderQty = $myRow['OrderQty'];//需领料
        $tStockQty = $myRow['tStockQty'];//实物库存
        $SeatId = $myRow['SeatId']; //库位编号

        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", $i);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $StuffId);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $StuffCname);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $UnitName);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $tStockQty);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $OrderQty);
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $SeatId);

        $i++;
        $Rows++;
    } while ($myRow = mysql_fetch_array($myResult));
}
$objPHPExcel->getActiveSheet()->getStyle("A3:G$Rows")->applyFromArray($style_center);

/* by.lwh */
if ($type == '2007') { //导出excel2007文档
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
}
else if ($type == '2003') { //导出excel2003文档
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $name . '.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}
else {
    echo "<script>alert('导出文件格式有误，请联系管理员')</script>";
}

exit;
?>