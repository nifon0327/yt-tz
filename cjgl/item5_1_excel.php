<?php
include "../basic/chksession.php";
include "../basic/parameter.inc";

$SearchRowes = $_REQUEST["SearchRowes"];
$SearchRows = $_REQUEST['SearchRows'];
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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
//$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
//$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);




$objPHPExcel->getActiveSheet()->setCellValue('A1', '序号');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '项目名称');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '业务单号');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '配件名称');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '采购总数');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '单位');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '备注');
//$objPHPExcel->getActiveSheet()->setCellValue('H1', '供应商');
//$objPHPExcel->getActiveSheet()->setCellValue('I1', '品牌');
//$objPHPExcel->getActiveSheet()->setCellValue('J1', '采购员');
//$objPHPExcel->getActiveSheet()->setCellValue('K1', '单位');
//$objPHPExcel->getActiveSheet()->setCellValue('L1', '需购数量');
//$objPHPExcel->getActiveSheet()->setCellValue('M1', '使用库存');
//$objPHPExcel->getActiveSheet()->setCellValue('N1', '增购数量');
//$objPHPExcel->getActiveSheet()->setCellValue('O1', '实够数量');
//$objPHPExcel->getActiveSheet()->setCellValue('P1', '金额');
//$objPHPExcel->getActiveSheet()->setCellValue('L1', '预定交期');
//$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style_left);
$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($style_center);
/*$objPHPExcel->getActiveSheet()->getStyle( 'A1:L1')->applyFromArray($style_center);*/

$mySql="select CompanyId,
	Id,	Mid,StockId,sum(Qty) as Qty,StuffId,SendSign,StuffCname,Picture,SendFloor,sum(cgQty) as cgQty,Date,UnitName,POrderId,OrderPO,ProductId,
	PQty,PackRemark,sgRemark,ShipType,Leadtime,Client,cName,TestStandard,BillNumber 
	from(
	SELECT M.CompanyId,S.Id,S.Mid,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.Picture,D.SendFloor,(G.AddQty+G.FactualQty) AS cgQty,M.Date,U.Name AS UnitName,G.POrderId,Y.OrderPO,
Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,C.Forshort AS Client,P.cName,P.TestStandard, M.BillNumber  
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
		LEFT JOIN (
				   select S.StuffId,Count(*) as CS FROM $DataIn.gys_shsheet S
				   LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
				   WHERE 1 $SearchRows 
				   Group by S.StuffId
				   ) H  ON H.StuffId=S.StuffId
		WHERE 1 $SearchRowes AND D.ComboxSign!=1 
UNION ALL
       SELECT M.CompanyId,S.Id,S.Mid,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.Picture,D.SendFloor,(G.AddQty+G.FactualQty) AS cgQty,M.Date,U.Name AS UnitName,
                G.POrderId,Y.OrderPO,Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,C.Forshort AS Client,P.cName,P.TestStandard, M.BillNumber  
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stuffcombox B ON B.StockId=S.StockId 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=B.mStockId 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
		LEFT JOIN (
				   select S.StuffId,Count(*) as CS FROM $DataIn.gys_shsheet S
				   LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
				   WHERE 1 $SearchRows 
				   Group by S.StuffId
				   ) H  ON H.StuffId=S.StuffId
		WHERE 1 $SearchRowes AND D.ComboxSign=1 
		ORDER BY StuffId,Id) as res GROUP BY StuffId,StuffCname,OrderPO ORDER BY OrderPO";

$Rows = 2;
$myResult = mysql_query($mySql, $link_id);
if ($myResult && $myRow = mysql_fetch_assoc($myResult)) {
    $i = 1;
    do {
        $CompanyId = $myRow['CompanyId'];
        $Id = $myRow['Id'];
        $Mid = $myRow['Mid'];
        $StockId = $myRow['StockId'];
        $Qty = $myRow['Qty'];
        $StuffId = $myRow['StuffId'];
        $SendSign = $myRow['SendSign'];
        $StuffCname = $myRow['StuffCname'];
        $Picture = $myRow['Picture'];
        $SendFloor = $myRow['SendFloor'];
        $cgQty = $myRow['cgQty'];
        $Date = $myRow['Date'];
        $UnitName = $myRow['UnitName'];
        $POrderId = $myRow['POrderId'];
        $OrderPO = $myRow['OrderPO'];
        $ProductId = $myRow['ProductId'];
        $PQty = $myRow['PQty'];
        $PackRemark = $myRow['PackRemark'];
        $sgRemark = $myRow['sgRemark'];
        $ShipType = $myRow['ShipType'];
        $Leadtime = $myRow['Leadtime'];
        $Client = $myRow['Client'];
        $cName = $myRow['cName'];
        $TestStandard = $myRow['TestStandard'];
        $BillNumber = $myRow['BillNumber'];

        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", $i);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $Client);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $OrderPO);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $StuffCname);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $cgQty);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $UnitName);
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "");
//        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", '');
//        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", '');
//        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $name);
//        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $UnitName);
//        $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", '');
//        $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", '');
//        $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", '');
//        $objPHPExcel->getActiveSheet()->setCellValue("O$Rows", $Qty);
//        $objPHPExcel->getActiveSheet()->setCellValue("P$Rows", $Amount);
//        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $StockId);
        /*  $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $DeliveryDate);*/
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        /*$objPHPExcel->getActiveSheet()->getStyle("A$Rows:L$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        */
        $i++;
        $Rows++;
    } while ($myResult && $myRow = mysql_fetch_array($myResult));
}
$objPHPExcel->getActiveSheet()->getStyle("A1:A$Rows")->applyFromArray($style_center);

$dates = date('Y-m-d');
//$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=送货单'.$dates.'.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>