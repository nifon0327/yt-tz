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

$proId = $_GET["proId"];
$build = $_GET["build"];
$type = $_GET["type"];
if ($proId) {
    $ForshortSql = "SELECT Forshort FROM $DataIn.trade_object  where Id = $proId";
    $ForshortResult = mysql_query($ForshortSql, $link_id);
    if ($ForshortResult && $ForshortRow = mysql_fetch_array($ForshortResult)) {
        $Forshort = $ForshortRow["Forshort"];
    }
}

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();


if ($proId && $build && $type) {

    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle('BOM');

    $drawing_titles = array();
    //项目数据检索
    $mySql = "SELECT Titles FROM $DataIn.trade_drawing_hole  where TradeId = $proId";
    $myResult = mysql_query($mySql, $link_id);
    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        $drawing_titles = json_decode($myRow["Titles"]);
    }

    $steel_titles = array();
    $steel_specs = array();
    $steel_sizes = array();
    //项目数据检索
    $mySql = "SELECT Titles, Specs, Sizes FROM $DataIn.trade_steel_data  where TradeId = $proId AND BuildingNo = '$build' AND TypeName = '$type'";

    $myResult = mysql_query($mySql, $link_id);
    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        $steel_titles = json_decode($myRow["Titles"]);
        $steel_specs = json_decode($myRow["Specs"]);
        $steel_sizes = json_decode($myRow["Sizes"]);
    }

    $embedded_titles = array();
    $embedded_specs = array();
    //项目数据检索
    $mySql = "SELECT Titles, Specs FROM $DataIn.trade_embedded_data  where TradeId = $proId AND BuildingNo = '$build' AND TypeName = '$type'";
    $myResult = mysql_query($mySql, $link_id);
    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        $embedded_titles = json_decode($myRow["Titles"]);
        $embedded_specs = json_decode($myRow["Specs"]);
    }

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);

    for ($i = 0; $i < count($steel_titles) + count($embedded_titles); $i++) {
        //$str= $titles[$i];
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(14 + $i)->setWidth(12);
    }

    //合并单元格
    $objPHPExcel->getActiveSheet()->mergeCells('A1:A3');
    $objPHPExcel->getActiveSheet()->mergeCells('B1:B3');
    $objPHPExcel->getActiveSheet()->mergeCells('C1:C3');
    $objPHPExcel->getActiveSheet()->mergeCells('D1:D3');
    $objPHPExcel->getActiveSheet()->mergeCells('E1:E3');
    $objPHPExcel->getActiveSheet()->mergeCells('F1:F3');
    $objPHPExcel->getActiveSheet()->mergeCells('G1:G3');
    $objPHPExcel->getActiveSheet()->mergeCells('H1:H3');
    $objPHPExcel->getActiveSheet()->mergeCells('I1:I3');
    $objPHPExcel->getActiveSheet()->mergeCells('J1:J3');
    $objPHPExcel->getActiveSheet()->mergeCells('K1:K3');
    $objPHPExcel->getActiveSheet()->mergeCells('L1:L3');
    $objPHPExcel->getActiveSheet()->mergeCells('M1:M3');

    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(36);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '选项');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '顺序号');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '项目编号');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '项目名称');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '构件类型');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '楼栋编号');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '楼层编号');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '构件编号');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '浇捣日期');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '混凝土强度');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '图纸体积');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '混凝土体积');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '重量');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '钢筋');
    $objPHPExcel->getActiveSheet()->setCellValue('N2', '规格');
    $objPHPExcel->getActiveSheet()->setCellValue('N3', '下料尺寸');

    for ($i = 0; $i < count($steel_titles); $i++) {
        $steel = $steel_titles[$i];
        $steel1 = $steel_specs[$i];
        $steel2 = $steel_sizes[$i];

        $StuffSql = "SELECT StuffCName FROM stuffdata WHERE StuffEname = '$steel1' AND TypeId = '9018'";
        $StuffResult = mysql_query($StuffSql, $link_id);
        $StuffCName = $steel1;
        if ($StuffResult && $StuffRow = mysql_fetch_array($StuffResult)) {
            $StuffCName = $StuffRow["StuffCName"];
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $i, 1, $steel);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $i, 2, $StuffCName);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $i, 3, $steel2);
    }

//    $lastCol = PHPExcel_Cell::stringFromColumnIndex(25 + count($steel_titles));

    for ($m = 0; $m < count($embedded_titles); $m++) {
        $embedded = $embedded_titles[$m];
        $embedded1 = $embedded_specs[$m];

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $i + $m, 1, "");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $i + $m, 2, $embedded);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $i + $m, 3, $embedded1);
    }

    $lastCol = PHPExcel_Cell::stringFromColumnIndex(25 + count($steel_titles) + count($embedded_titles));

    $objPHPExcel->getActiveSheet()->getStyle('A1:' . $lastCol . '3')->applyFromArray($style_center);

    $mySql = "SELECT
	TI.TradeNo,TT.Forshort,TD.CmptType,TD.BuildingNo,TD.FloorNo,TD.CmptNo,TD.SN,TD.CStr,TD.DwgVol,TD.CVol,TD.Weight,TS.Quantities AS steel_Quantities,TE.Quantities AS embedded_Quantities 
FROM
	trade_drawing TD
	INNER JOIN trade_steel TS ON TS.TradeId = TD.TradeId AND TS.BuildingNo = TD.BuildingNo AND TS.FloorNo = TD.FloorNo AND TS.CmptType = TD.CmptType AND TS.CmptNo = TD.CmptNo AND TS.SN = TD.SN
	INNER JOIN trade_embedded TE ON TE.TradeId = TD.TradeId AND TE.BuildingNo = TD.BuildingNo AND TE.FloorNo = TD.FloorNo AND TE.CmptType = TD.CmptType AND TE.CmptNo = TD.CmptNo AND TE.SN = TD.SN
	LEFT JOIN trade_object TT ON TT.Id = TD.TradeId
	LEFT JOIN trade_info TI ON TI.TradeId = TD.TradeId
    where TD.TradeId = $proId AND TD.BuildingNo = '$build' AND TD.CmptType = '$type' ";

    $Rows = 4;
    $myResult = mysql_query($mySql, $link_id);
    if ($myRow = mysql_fetch_array($myResult)) {
        $i = 1;
        do {

            $FinishDateSql = "SELECT
	YS.FinishDate 
FROM
	yw1_scsheet YS
	INNER JOIN yw1_ordersheet YO ON YO.POrderId = YS.POrderId
	INNER JOIN productdata PD ON PD.ProductId = YO.ProductId
	INNER JOIN trade_drawing TD ON PD.cName = CONCAT_WS( '-', TD.BuildingNo, TD.FloorNo, TD.CmptNo, TD.SN )
	INNER JOIN trade_object TT ON TT.Id = TD.TradeId 
WHERE
	YS.ActionId = '101' 
	AND TD.TradeId = $proId 
	AND PD.cName = CONCAT_WS( '-', '$myRow[BuildingNo]', '$myRow[FloorNo]', '$myRow[CmptNo]', '$myRow[SN]' )";
            $FinishDateResult = mysql_query($FinishDateSql, $link_id);

            if ($FinishDateResult && $FinishDateRow = mysql_fetch_array($FinishDateResult)) {
                $FinishDate = $FinishDateRow["FinishDate"];
                $Finish = "";
                if ($FinishDate) {
                    $Finish = date('Y-m-d', strtotime($FinishDate));
                }
            }


            //$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(65);
            $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "");
            $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$i");
            $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $myRow['TradeNo']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $myRow['Forshort']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $myRow['CmptType']);
            $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $myRow['BuildingNo']);
            $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $myRow['FloorNo']);
            $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $myRow['CmptNo']);
            $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $Finish);
            $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $myRow['CStr']);
            $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $myRow['DwgVol']);
            $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $myRow['CVol']);
            $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", $myRow['Weight']);
            $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", "数量");

            $steel_Quantities = json_decode($myRow['steel_Quantities']);
            $embedded_Quantities = json_decode($myRow['embedded_Quantities']);

            for ($j = 0; $j < count($steel_titles) && $j < count($steel_Quantities); $j++) {
                $str = $steel_Quantities[$j];
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $j, $Rows, "$str");
            }

            for ($r = 0; $r < count($embedded_titles) && $r < count($embedded_Quantities); $r++) {
                $str = $embedded_Quantities[$r];
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $j + $r, $Rows, "$str");
            }

            $objPHPExcel->getActiveSheet()->getStyle("B$Rows:" . $lastCol . $Rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $i++;
            $Rows++;
        } while ($myRow = mysql_fetch_array($myResult));
    }
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $Forshort . ' ' . $build . '# ' . $type . '.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;

?>