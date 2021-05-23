<?php
include "../basic/chksession.php";
include "../basic/parameter.inc";

$kWorkShopId = $_REQUEST["kWorkShopId"];
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
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
//$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
//$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
//$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);




$objPHPExcel->getActiveSheet()->setCellValue('A1', '序号');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '项目名称');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '工单流水号');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'PO');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '名称');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '数量');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '生产时间');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '操作人');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '领料时间');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '生产车间');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '备注');
//$objPHPExcel->getActiveSheet()->setCellValue('L1', '需购数量');
//$objPHPExcel->getActiveSheet()->setCellValue('M1', '使用库存');
//$objPHPExcel->getActiveSheet()->setCellValue('N1', '增购数量');
//$objPHPExcel->getActiveSheet()->setCellValue('O1', '实够数量');
//$objPHPExcel->getActiveSheet()->setCellValue('P1', '金额');
//$objPHPExcel->getActiveSheet()->setCellValue('L1', '预定交期');
//$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style_left);
$objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($style_center);
/*$objPHPExcel->getActiveSheet()->getStyle( 'A1:L1')->applyFromArray($style_center);*/

if($kWorkShopId == '101') {
    $mySql="SELECT DISTINCT O.Forshort,M.CompanyId,M.OrderDate,
    S.POrderId,S.OrderPO,S.Price,S.sgRemark,S.DeliveryDate,S.ShipType,
    SC.Id,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId,SC.Remark,
    P.ProductId,P.cName,P.eCode,P.TestStandard,P.pRemark,
    U.Name AS Unit,PI.Leadtime,PI.Leadweek,W.Name AS WorkShopName,SF.Name,ck.created 
	FROM  $DataIn.yw1_scsheet SC 
	INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=S.POrderId
	LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
	    LEFT JOIN $DataIn.ck5_llsheet ck ON ck.sPOrderId = SC.sPOrderId
    LEFT JOIN  $DataIn.staffmain SF ON SF.Number = ck.Operator
	WHERE 1  $SearchRows ORDER BY M.OrderDate";
}else{
    $mySql = "SELECT DISTINCT SC.Id,O.Forshort,SC.POrderId,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId,SC.Remark,SC.scDate,
    D.StuffId,D.StuffCname,D.Picture,OM.OrderPO,P.eCode, 
    G.DeliveryDate,G.DeliveryWeek,SM.PurchaseID,W.Name AS WorkShopName,SF.Name,ck.created 
    FROM  $DataIn.yw1_scsheet SC 
    LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
    LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = S.OrderNumber
    LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
    INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
    INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
    INNER JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
    INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
    LEFT  JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
    INNER JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
    LEFT JOIN $DataIn.ck5_llsheet ck ON ck.sPOrderId = SC.sPOrderId
    LEFT JOIN  $DataIn.staffmain SF ON SF.Number = ck.Operator
    WHERE 1  $SearchRows AND G.Mid>0 ";
}

$Rows = 2;
$myResult = mysql_query($mySql, $link_id);
if ($myResult && $myRow = mysql_fetch_assoc($myResult)) {
    $i = 1;
    do {
        $Id=$myRow["Id"];
        $Forshort=$myRow['Forshort'];
        $POrderId=$myRow["POrderId"];
        $sPOrderId=$myRow["sPOrderId"];
        $ProductId=$myRow["ProductId"];
        $OrderPO=$myRow["OrderPO"];
        $TestStandard = $myRow["TestStandard"];
        $eCode=$myRow["eCode"];
        if($kWorkShopId == '101') {
            $StuffName = $myRow["cName"];
        }else {
            $StuffName = $myRow["StuffCname"];
        }
        $Estate = $myRow["Estate"];
        $WorkShopName=$myRow["WorkShopName"];
        $PurchaseID=$myRow["PurchaseID"];
        $Qty=$myRow["Qty"];
        $scDate= $myRow["scDate"] != ''?date('Y-m-d',strtotime($myRow["scDate"])):'';
        $Remark=$myRow["Remark"];
        $OrderDate=$myRow["OrderDate"];
        $Leadtime=$myRow["Leadtime"];
        $Leadweek=$myRow["Leadweek"];

        $sumQty=$sumQty+$Qty;

        $Name = $myRow['Name'];
        $created = $myRow['created'];

        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", $i);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $Forshort);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $sPOrderId);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $OrderPO);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $StuffName);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $Qty);
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $scDate);
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $Name);
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $created);
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $WorkShopName);
        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $Remark);
//        $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", '');
//        $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", '');
//        $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", '');
//        $objPHPExcel->getActiveSheet()->setCellValue("O$Rows", $Qty);
//        $objPHPExcel->getActiveSheet()->setCellValue("P$Rows", $Amount);
//        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $StockId);
        /*  $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $DeliveryDate);*/
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
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
header('Content-Disposition: attachment;filename=车间变更'.$dates.'.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>