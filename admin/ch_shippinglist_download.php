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

$Id = $_GET["Id"];

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();


if ($Id) {

    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle('出库单');


    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9.5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(9);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(9.5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9.5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(9);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(1);


    //合并单元格
    $objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
    $objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
    $objPHPExcel->getActiveSheet()->mergeCells('B4:C4');
    $objPHPExcel->getActiveSheet()->mergeCells('E4:G4');
    $objPHPExcel->getActiveSheet()->mergeCells('I4:J4');
    $objPHPExcel->getActiveSheet()->mergeCells('B5:C5');
    $objPHPExcel->getActiveSheet()->mergeCells('E5:G5');
    $objPHPExcel->getActiveSheet()->mergeCells('I5:J5');
    $objPHPExcel->getActiveSheet()->mergeCells('B6:D6');
    $objPHPExcel->getActiveSheet()->mergeCells('I6:J6');
    $objPHPExcel->getActiveSheet()->mergeCells('B7:J7');
    $objPHPExcel->getActiveSheet()->mergeCells('A8:A9');
    $objPHPExcel->getActiveSheet()->mergeCells('B8:B9');
    $objPHPExcel->getActiveSheet()->mergeCells('C8:C9');
    $objPHPExcel->getActiveSheet()->mergeCells('D8:F8');
    $objPHPExcel->getActiveSheet()->mergeCells('G8:G9');
    $objPHPExcel->getActiveSheet()->mergeCells('H8:H9');
    $objPHPExcel->getActiveSheet()->mergeCells('I8:I9');
    $objPHPExcel->getActiveSheet()->mergeCells('J8:J9');

    $objPHPExcel->getActiveSheet()->mergeCells('A47:B47');
    $objPHPExcel->getActiveSheet()->mergeCells('D47:E47');
    $objPHPExcel->getActiveSheet()->mergeCells('A49:B49');
    $objPHPExcel->getActiveSheet()->mergeCells('D49:E49');
    $objPHPExcel->getActiveSheet()->mergeCells('A51:J51');

    //行高
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(1);
    $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(22);
    $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(22);
    $objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(19);
    for ($i = 8; $i < 45; $i++) {
        $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(13);
    }
    $objPHPExcel->getActiveSheet()->getRowDimension('48')->setRowHeight(8);
    $objPHPExcel->getActiveSheet()->getRowDimension('50')->setRowHeight(8);

    //字体样式
    $objPHPExcel->getDefaultStyle()->getFont()->setName('宋体');
    $objPHPExcel->getActiveSheet()->getStyle('A8:J46')->applyFromArray($style_border);
    $objPHPExcel->getActiveSheet()->getStyle('A1:J50')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:J50')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A51')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
    $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(18);
    $objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('H4')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('D5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('H5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('H6')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setSize(14);

    $objPHPExcel->getActiveSheet()->getstyle('A51:J51')->getBorders()->getTop()->setBorderstyle(PHPExcel_style_Border::BORDER_THIN);

    //自动换行
    $objPHPExcel->getActiveSheet()->getStyle('E4')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('H5')->getAlignment()->setWrapText(true);


    $objPHPExcel->getActiveSheet()->setCellValue('A2', '常州砼筑建筑科技有限公司');
    $objPHPExcel->getActiveSheet()->setCellValue('A3', 'PC成品销售出货单');
    $objPHPExcel->getActiveSheet()->setCellValue('A4', '销售单号:');
    $objPHPExcel->getActiveSheet()->setCellValue('D4', '项目名称:');
    $objPHPExcel->getActiveSheet()->setCellValue('H4', '运输车号:');
    $objPHPExcel->getActiveSheet()->setCellValue('A5', '客户名称:');
    $objPHPExcel->getActiveSheet()->setCellValue('D5', '栋号/层:');
    $objPHPExcel->getActiveSheet()->setCellValue('H5', '项目联系人:');
    $objPHPExcel->getActiveSheet()->setCellValue('A6', '生产单位:');
    $objPHPExcel->getActiveSheet()->setCellValue('H6', '联系电话:');
    $objPHPExcel->getActiveSheet()->setCellValue('A7', '摘要:');
    $objPHPExcel->getActiveSheet()->setCellValue('A8', '序号');
    $objPHPExcel->getActiveSheet()->setCellValue('B8', '构件类型');
    $objPHPExcel->getActiveSheet()->setCellValue('C8', 'PC编号');
    $objPHPExcel->getActiveSheet()->setCellValue('D8', '规格');
    $objPHPExcel->getActiveSheet()->setCellValue('D9', '长(mm)');
    $objPHPExcel->getActiveSheet()->setCellValue('E9', '宽(mm)');
    $objPHPExcel->getActiveSheet()->setCellValue('F9', '高(mm)');
    $objPHPExcel->getActiveSheet()->setCellValue('G8', '单位');
    $objPHPExcel->getActiveSheet()->setCellValue('H8', '数量');
    $objPHPExcel->getActiveSheet()->setCellValue('I8', '方量/m³');
    $objPHPExcel->getActiveSheet()->setCellValue('J8', '备注');


    $Sql = "SELECT CSN.InvoiceNO,CSN.Wise,CSN.CarNo,CSL.Title,CSL.Company,CSL.Contact,CSL.TEL,CSL.StartPlace,CSL.EndPlace FROM ch1_shipmain CSN
LEFT JOIN ch8_shipmodel CSL ON CSN.ModelId = CSL.Id
WHERE CSN.Id = $Id";
    $Result = mysql_query($Sql, $link_id);
    if ($Row = mysql_fetch_array($Result)) {
        $BuildFloorRes = explode("-", $Row['Wise']);
        $objPHPExcel->getActiveSheet()->setCellValue("B4", $Row['InvoiceNO']);
        $objPHPExcel->getActiveSheet()->setCellValue("E4", $Row['Company']);
        $objPHPExcel->getActiveSheet()->setCellValue("B5", $Row['EndPlace']);
        $objPHPExcel->getActiveSheet()->setCellValue("E5", $BuildFloorRes[0] . '# ' . $BuildFloorRes[1] . '层');
        $objPHPExcel->getActiveSheet()->setCellValue("I5", $Row['Contact']);
        $objPHPExcel->getActiveSheet()->setCellValue("B6", $Row['StartPlace']);
        $objPHPExcel->getActiveSheet()->setCellValue("I6", $Row['TEL']);

    }

    $mySql = "
SELECT S.Id,S.POrderId,S.ProductId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN,P.Weight AS Weight,M.Date,E.Leadtime,P.TestStandard,P.MainWeight,N.OrderDate AS OrderDate,N.ClientOrder,P.buySign
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
    LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=O.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'
UNION ALL
	SELECT S.Id,S.POrderId,S.ProductId,O.SampPO AS OrderPO,O.SampName AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,O.Weight AS Weight,M.Date,'' AS Leadtime,'' AS TestStandard,'' AS MainWeight,O.Date AS OrderDate,'' AS ClientOrder,-1 as buySign
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$Id' AND S.Type='2'
UNION ALL
	SELECT S.Id,S.POrderId,S.ProductId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,'0' AS Weight ,M.Date,'' AS Leadtime,'' AS TestStandard,'' AS MainWeight,O.Date AS OrderDate,'' AS ClientOrder,-1 as buySign
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$Id' AND S.Type='3'";

    $Rows = 10;
    $sun = 0;
    $myResult = mysql_query($mySql, $link_id);
    if ($myRow = mysql_fetch_array($myResult)) {

        $i = 1;
        do {

            $FinishDateSql = "SELECT PD.eCode,PT.TypeName,PD.CmptNo,PD.Length,PD.Width,PD.Thick,IFNULL( CS.volume, IFNULL(PD.dwgVol,TD.DwgVol) ) AS volume 
            FROM productdata PD
	        LEFT JOIN trade_drawing TD ON TD.Id = PD.drawingId
	        LEFT JOIN ch1_shipsheet CT ON CT.ProductId = PD.ProductId
	        LEFT JOIN ch1_shipsplit CS ON CS.ShipId = CT.Id 
            LEFT JOIN producttype PT ON PT.TypeId = PD.TypeId
	        WHERE PD.ProductId = '$myRow[ProductId]' ";
            $FinishDateResult = mysql_query($FinishDateSql, $link_id);

            if ($FinishDateResult && $FinishDateRow = mysql_fetch_array($FinishDateResult)) {
                $FinishDate = $FinishDateRow["FinishDate"];
                $eCode = $FinishDateRow["eCode"];
                $fields=explode("-",$eCode);
                $counts=count($fields)-1;
                for($q=2;$q<$counts;$q++) {
                    if ($q==2){
                        $gjName = $fields[$q];
                    }else {
                        $gjName = $gjName . "-" . $fields[$q];
                    }
                }

                $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
                $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $FinishDateRow['TypeName']);
                $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $gjName);
                $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $FinishDateRow['Length']);
                $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $FinishDateRow['Width']);
                $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $FinishDateRow['Thick']);
                $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", '块');
                $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", '1');
                $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", floor($FinishDateRow['volume'] * 100) / 100);
                $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", '');

                $sun += floor($FinishDateRow['volume'] * 100) / 100;
            }

            $i++;
            $Rows++;
        } while ($myRow = mysql_fetch_array($myResult));
    }

    $objPHPExcel->getActiveSheet()->setCellValue('A46', '合计');
    $objPHPExcel->getActiveSheet()->setCellValue('I46', $sun);

    //footer
    $objPHPExcel->getActiveSheet()->setCellValue('A47', '发货人：');
    $objPHPExcel->getActiveSheet()->setCellValue('D47', '品质：');
    $objPHPExcel->getActiveSheet()->setCellValue('H47', '司机：');
    $objPHPExcel->getActiveSheet()->setCellValue('A49', '项目收货人：');
    $objPHPExcel->getActiveSheet()->setCellValue('D49', '监理：');
    $objPHPExcel->getActiveSheet()->setCellValue('A51', 'TZ-QF-077-102');

}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $Row['Company'] . ' ' .$BuildFloorRes[0] . '# ' . $BuildFloorRes[1] . 'F - '. $Row['EndPlace'].'.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;

?>