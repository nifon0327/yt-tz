<?php
/*
 * By.nifon
 * 对账单
 * */
include "../basic/chksession.php";
include "../basic/parameter.inc";

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

// 标题
$titleStart = date('Y年m月份', strtotime($startValue));
$titleEnd = date('m月d日', strtotime($endValue));
$objPHPExcel->getActiveSheet()->setCellValue("A1", $clientName . $titleStart . "预制构件供货对账单截止" . $titleEnd);
$objPHPExcel->getActiveSheet()->setCellValue("A2", '供货单位：常州砼筑建筑科技有限公司');
$objPHPExcel->getActiveSheet()->setCellValue("A3", '序号');
$objPHPExcel->getActiveSheet()->setCellValue("B3", '构件名称');
$buildno = ord('B');
$mySql = "SELECT
	SUBSTRING_INDEX(SUBSTRING_INDEX ( O.OrderPO, '-', -2 ) ,'-',1) AS Building,
	SPLIT_STR(P.eCode,'-',3) as Kind,
	SUM(P.Weight) AS Weight
FROM
	$DataIn.ch1_shipsheet S
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id = S.Mid
LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId = S.POrderId
LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber = O.OrderNumber
LEFT JOIN $DataIn.yw3_pisheet E ON E.oId = O.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId = O.ProductId
WHERE
	S.Mid IN (
		SELECT
			M.Id
		FROM
			$DataIn.ch1_shipmain M
		WHERE
			1
		AND M.Estate = '0'
		AND M.Date >= '$startValue'
		AND M.Date <= '$endValue'
		AND M.CompanyId = '$clientValue'
	)
AND S.Type = '1'
GROUP BY
	Building,Kind";
//echo $mySql;die;
$myResult = mysql_query($mySql, $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $KindArr = [];
    $kindNo = 3;
    do {
        if ($Building == $myRow['Building']) {
            $flag = 1;
        }
        else {
            $flag = 0;
        }
        $Building = $myRow['Building'];
        $Kind = $myRow['Kind'];
        $Weight = $myRow['Weight'];
        if ($flag == 0) {
            $buildno++;
            $site = chr($buildno) . "3";
            $objPHPExcel->getActiveSheet()->setCellValue($site, $Building . "#栋（m³）");
            $BuildArr[$Building] = $buildno;
        }

        if (!in_array($Kind, $KindArr)) {
            $KindArr[] = $Kind;
            $kindNo++;
            $objPHPExcel->getActiveSheet()->setCellValue("A$kindNo", count($KindArr));
            $objPHPExcel->getActiveSheet()->setCellValue("B$kindNo", $Kind);
            $KindTemp[$Kind] = $kindNo;
        }
        $local = chr($BuildArr[$Building]) . $KindTemp[$Kind];
        $objPHPExcel->getActiveSheet()->setCellValue($local, $Weight);

    } while ($myRow = mysql_fetch_array($myResult));

}else{
    echo "数据不存在";
    return false;
}

$row = current(array_slice($KindTemp, -1, 1)) + 1;
$objPHPExcel->getActiveSheet()->setCellValue("B$row", '合计：');
foreach ($BuildArr as $v) {
    $letter = chr($v);
    $str = $letter.'3:'.$letter.($row-1);
    $objPHPExcel->getActiveSheet()->setCellValue($letter.$row, "=sum($str)");

}
$row += 2;
$objPHPExcel->getActiveSheet()->setCellValue("A$row", '供货单位确认人：');
$column = current(array_slice($BuildArr, -1, 1)) + 1;
$zone = chr($column) . $row;
$objPHPExcel->getActiveSheet()->setCellValue("$zone", '收货单位确认人：');
$row += 2;
$objPHPExcel->getActiveSheet()->setCellValue("A$row", '日期：');
$zone = chr($column) . $row;
$objPHPExcel->getActiveSheet()->setCellValue("$zone", '日期：');
$objPHPExcel->getActiveSheet()->setCellValue(chr($column) . "2", '收货单位：');
$objPHPExcel->getActiveSheet()->setCellValue(chr($column) . "3", '合计方量（m³）');
$nowCol = chr($column - 1);
for ($i = 4; $i < count($KindTemp) + 5; $i++) {
    $tempColumn = $nowCol . $i;
    $objPHPExcel->getActiveSheet()->setCellValue(chr($column) . $i, "=sum(C$i:$tempColumn)");
}


$column++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($column) . "3", '含税单价（元）');
$column++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($column) . "2", $clientName);
$objPHPExcel->getActiveSheet()->setCellValue(chr($column) . "3", '总金额（元）');
$column++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($column) . "3", '备注');
$dates = date('Y-m-d');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=对账单-' . $dates . '.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>