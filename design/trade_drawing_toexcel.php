<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

$statusType = $_POST["statusType"];
$proId = $_POST["proId"];
//构件类型
$type = $_POST["type"];
$period = $_POST["period"];
$status = $_POST["status"];
$floor = $_POST["floor"];
$cmptNo = $_POST["cmptNo"];

$titles = array();
//项目数据检索
$mySql="SELECT a.Titles FROM $DataIn.trade_drawing_hole a where a.TradeId = $proId";
$myResult = mysql_query($mySql, $link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
     $titles = json_decode( $myRow["Titles"]);   
}

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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(9);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(5);

for ($i= 0;$i< count($titles); $i++){
    //$str= $titles[$i];
    $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(15 + $i)->setWidth(8);
}
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(15 + count($titles))->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(16 + count($titles))->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(17 + count($titles))->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(18 + count($titles))->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(19 + count($titles))->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(20 + count($titles))->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(21 + count($titles))->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(22 + count($titles))->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(23 + count($titles))->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(24 + count($titles))->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(25 + count($titles))->setWidth(10);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(27);
$objPHPExcel->getActiveSheet()->setCellValue('A1', '选项');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '顺序号');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '项目编号');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '项目名称');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '构件类型');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '楼栋编号');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '楼层编号');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '构件编号');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '产品条码');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '审核状态');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '模具编号');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '混泥土强度');
$objPHPExcel->getActiveSheet()->setCellValue('M1', '长');
$objPHPExcel->getActiveSheet()->setCellValue('N1', '宽');
$objPHPExcel->getActiveSheet()->setCellValue('O1', '厚');

for ($i= 0;$i< count($titles); $i++){
    $str= $titles[$i];
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15 + $i, 1, $str);
}
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15 + count($titles), 1, '洞口体积');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16 + count($titles), 1, '面积');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17 + count($titles), 1, '图纸体积');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18 + count($titles), 1, '混凝土体积');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19 + count($titles), 1, '重量');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20 + count($titles), 1, '构件成品图纸');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21 + count($titles), 1, '构件预埋图纸');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22 + count($titles), 1, '构件钢筋图纸');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23 + count($titles), 1, '模具图纸');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24 + count($titles), 1, '导入日期');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25 + count($titles), 1, '修改日期');

$lastCol = PHPExcel_Cell::stringFromColumnIndex(25 + count($titles));

$objPHPExcel->getActiveSheet()->getStyle( 'A1:' . $lastCol . '1')->applyFromArray($style_center);

$SearchRows = "";
if ($statusType == 0) {
    if ($status == "") {
        $SearchRows .= " AND A.Estate in (4, 5, 6)";
    } else {
        $SearchRows .= " AND A.Estate= $status";
    }
} else if ($statusType == 1) {
    if ($status == "") {
        $SearchRows .= " AND A.Estate in (0, 1, 2, 3)";
    } else {
        $SearchRows .= " AND A.Estate= $status";
    }
} else if ($statusType == 2) {
    $SearchRows .= " AND A.Estate= 7";
} else if ($statusType == 3) {
    $SearchRows .= " AND A.Estate= 8";
}

$SearchRows .= " AND A.TradeId= $proId";
if ($type) {
    $SearchRows .= " AND A.CmptType= '$type'";
}

if ($period == 1) {
    //最近7天
    $SearchRows .= " TO_DAYS(NOW()) - TO_DAYS(A.Date) <= 7";
    
} else if ($period == 2) {
    //最近15天
    $SearchRows .= " TO_DAYS(NOW()) - TO_DAYS(A.Date) <= 15";
    
} else if ($period == 3) {
    //最近30天
    $SearchRows .= " TO_DAYS(NOW()) - TO_DAYS(A.Date) <= 30";
    
} else if ($period == 4) {
    //30天前
    $SearchRows .= " TO_DAYS(NOW()) - TO_DAYS(A.Date) > 30";
}

if ($floor) {
    $SearchRows .= " AND A.FloorNo= $floor";
}
if ($cmptNo) {
    $SearchRows .= " AND A.CmptNo like '%$cmptNo%'";
}
$Orderby = "order by a.Id ";

$mySql="SELECT A.Id, A.TradeId, A.CmptType, A.BuildingNo, A.FloorNo, A.CmptNo,
A.ProdCode, A.Estate, A.MouldNo, A.CStr, A.Length, A.Width, A.Thick, A.Sizes,
A.HoleVol, A.Area, A.DwgVol, A.CVol, A.Weight, A.EndDwg, A.EmbeddedDwg,
A.SteelDwg, A.DieDwg, A.UpdateReasons, A.ReturnReasons,
A.Locks, A.Operator, A.PLocks, A.creator, A.created,
A.proofreader, A.proofreaded, A.modifier, A.modified,
B.Forshort, C.TradeNo
FROM $DataIn.trade_drawing A
LEFT JOIN $DataIn.trade_object B ON A.TradeId = b.Id
LEFT JOIN $DataIn.trade_info C ON A.TradeId = C.TradeId
WHERE 1 $SearchRows $Orderby";

$Rows=2;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $i=1;
    do{
        $Estate=$myRow["Estate"];
        switch($Estate){
            case 0:$Estate="未提交";
            break;
            case 1:$Estate="申请校对";
            break;
            case 2:$Estate="已经校对";
            break;
            case 3:$Estate="申请审核";
            break;
            case 4:$Estate="通过";
            break;
            case 5:$Estate="未通过";
            break;
            case 6:$Estate="退回";
            break;
            case 7:$Estate="生产中";
            break;
            case 8:$Estate="已生产";
            break;
            default:
                $Estate="未通过";
                break;
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
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $myRow['ProdCode']);
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$Estate");
        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $myRow['MouldNo']);
        $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $myRow['CStr']);
        $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", $myRow['Length']);
        $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", $myRow['Width']);
        $objPHPExcel->getActiveSheet()->setCellValue("O$Rows", $myRow['Thick']);
        
        $Sizes = json_decode($myRow['Sizes']);
        
        for ($j= 0;$j< count($titles) && $j< count($Sizes); $j++){
            $str= $Sizes[$j];
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15 + $j, $Rows, "$str");
        }
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15 + count($titles), $Rows, $myRow['HoleVol']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16 + count($titles), $Rows, $myRow['Area']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17 + count($titles), $Rows, $myRow['DwgVol']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18 + count($titles), $Rows, $myRow['CVol']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19 + count($titles), $Rows, $myRow['Weight']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20 + count($titles), $Rows, $myRow['EndDwg']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21 + count($titles), $Rows, $myRow['EmbeddedDwg']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22 + count($titles), $Rows, $myRow['SteelDwg']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23 + count($titles), $Rows, $myRow['DieDwg']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24 + count($titles), $Rows, date("Y-m-d",time($myRow["created"])));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25 + count($titles), $Rows, date("Y-m-d",time($myRow["modified"])));

        $objPHPExcel->getActiveSheet()->getStyle("B$Rows:" . $lastCol . $Rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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