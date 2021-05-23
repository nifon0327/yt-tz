<?php
include "../basic/chksession.php";
include "../basic/parameter.inc";

$POrderIds = $_REQUEST["selectsPOrderIds"];
$workShop = $_REQUEST["workShop"];
$Forshort = $_REQUEST["Forshort"];
$created = $_REQUEST["created"];
/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
// function
function setMerge($objPHPExcel, $first, $second)
{
    $objPHPExcel->getActiveSheet()->mergeCells("A$first:A$second");
    if ($first != 33 && $second != 36) {
        $objPHPExcel->getActiveSheet()->mergeCells("B$first:B$second");
    }
    $objPHPExcel->getActiveSheet()->mergeCells("F$first:F$second");
    $objPHPExcel->getActiveSheet()->mergeCells("G$first:G$second");
    $objPHPExcel->getActiveSheet()->mergeCells("H$first:H$second");
    $objPHPExcel->getActiveSheet()->mergeCells("I$first:I$second");
}

function setCMerge($objPHPExcel, $first, $second)
{
    $objPHPExcel->getActiveSheet()->mergeCells("C$first:C$second");
}

function setBFont($objPHPExcel, $site, $font, $size)
{
    $objPHPExcel->getActiveSheet()->getStyle("$site")->getFont()
        ->setName("$font")
        ->setSize("$size");
}
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
    'font'      => array(
        'size' => 10,
        'bold' => true,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ),
);
$POrderIds = explode('|',$_REQUEST['selectsPOrderIds']);
foreach ($POrderIds as $k => $v) {
    // 获取构件名称，生产时间
    $cName = $scDate = $liningNo = ' ';
    $mySql = "SELECT DISTINCT P.ProductId,P.cName,SC.scDate,S.liningNo ,IFNULL(P.CStr,TD.CStr) AS CStr  FROM $DataIn.yw1_scsheet SC
          INNER JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
          INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
          LEFT JOIN $DataIn.trade_drawing TD ON TD.Id=P.drawingId 
          WHERE 1 and S.Estate>0 AND SC.POrderId=$v";

    $myResult = mysql_query($mySql." $PageSTR",$link_id);
    if($myRow = mysql_fetch_array($myResult)) {
        $cName = $myRow['cName'];
        $liningNo = $myRow['liningNo'];
        $CStr = $myRow['CStr'];
        $ProductId = $myRow["ProductId"];
        $scDate = date('Y-m-d',strtotime($myRow['scDate']));
    }


    // 分页
    if($k != 0) {
        $objPHPExcel->createSheet();
    }
    $objPHPExcel->setActiveSheetIndex($k)->setTitle($v);
    // 插入图片
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    // 本地图片文件路径
    $objDrawing->setPath('../images/cz_logo.png');
    // 图片高
    $objDrawing->setHeight(40);
    // 图片宽
    $objDrawing->setWidth(120);
    // 单元格
    $objDrawing->setCoordinates('B1');
    $objDrawing->setOffsetY(2);
    $objDrawing->setName('cz_logo');
    $objDrawing->setDescription('常州工厂logo');

    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

    // 插入二维码
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    // 本地图片文件路径
    $objDrawing->setPath("./QRCode/$v.png");
    // 图片高
    $objDrawing->setHeight(120);
    // 图片宽
    $objDrawing->setWidth(120);
    // 单元格
    $objDrawing->setCoordinates('H1');
    // 图片偏移距离
    $objDrawing->setOffsetX(8);
    $objDrawing->setOffsetY(13);
    $objDrawing->setName('二维码');
    $objDrawing->setDescription('二维码');

    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

    //设置列宽
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('12');
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('6.55');
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('7');
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('7.55');
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('12');
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('9');
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('13');
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('9');
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('9.5');

    //设置边框
    $sharedStyle1 = new PHPExcel_Style();
    $sharedStyle1->applyFromArray(
        array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        ));
    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A2:G5");
    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A7:I37");

    // 设置对齐
    $objPHPExcel->getActiveSheet()->getStyle('C1:G1')->applyFromArray($style_center);
    $objPHPExcel->getActiveSheet()->getStyle('A7:I37')->applyFromArray($style_center);
    $objPHPExcel->getActiveSheet()->getStyle("H46:H47")->applyFromArray($style_center);

    $objPHPExcel->getActiveSheet()->getStyle('A2:A5')->applyFromArray($style_left);
    $objPHPExcel->getActiveSheet()->getStyle('B2:B5')->applyFromArray($style_center);
    $objPHPExcel->getActiveSheet()->getStyle('E2:E5')->applyFromArray($style_left);
    $objPHPExcel->getActiveSheet()->getStyle('F2:F5')->applyFromArray($style_center);

    // 设置行高
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(32);
    $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(19);
    $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(19);
    $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(19);
    $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(19);
    $objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('9')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('10')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('11')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('12')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('13')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('14')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('15')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('16')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('17')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('18')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('19')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('20')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('21')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('22')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('23')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('24')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('25')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('26')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('27')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('28')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('29')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('30')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('31')->setRowHeight(30);
    $objPHPExcel->getActiveSheet()->getRowDimension('32')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('33')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('34')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('35')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('36')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('37')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('38')->setRowHeight(12);
    $objPHPExcel->getActiveSheet()->getRowDimension('39')->setRowHeight(12);
    $objPHPExcel->getActiveSheet()->getRowDimension('40')->setRowHeight(12);
    $objPHPExcel->getActiveSheet()->getRowDimension('41')->setRowHeight(12);
    $objPHPExcel->getActiveSheet()->getRowDimension('42')->setRowHeight(12);
    $objPHPExcel->getActiveSheet()->getRowDimension('43')->setRowHeight(12);
    $objPHPExcel->getActiveSheet()->getRowDimension('44')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('45')->setRowHeight(10);
    $objPHPExcel->getActiveSheet()->getRowDimension('46')->setRowHeight(10);
    // 合并单元格
    $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
    $objPHPExcel->getActiveSheet()->mergeCells('C1:G1');
    $objPHPExcel->getActiveSheet()->mergeCells('H1:I4');
    $objPHPExcel->getActiveSheet()->mergeCells('B2:D2');
    $objPHPExcel->getActiveSheet()->mergeCells('F2:G2');
    $objPHPExcel->getActiveSheet()->mergeCells('B3:D3');
    $objPHPExcel->getActiveSheet()->mergeCells('F3:G3');
    $objPHPExcel->getActiveSheet()->mergeCells('B4:D4');
    $objPHPExcel->getActiveSheet()->mergeCells('F4:G4');
    $objPHPExcel->getActiveSheet()->mergeCells('B5:D5');
    $objPHPExcel->getActiveSheet()->mergeCells('F5:G5');

    for ($i = 7; $i <= 36; $i++) {
        $objPHPExcel->getActiveSheet()->mergeCells("D$i:E$i");
    }
    setMerge($objPHPExcel, 8, 18);
    setMerge($objPHPExcel, 19, 23);
    setMerge($objPHPExcel, 24, 27);
    setMerge($objPHPExcel, 28, 30);
    setMerge($objPHPExcel, 31, 32);
    setMerge($objPHPExcel, 33, 36);
    setCMerge($objPHPExcel, 10, 16);
    setCMerge($objPHPExcel, 17, 18);

    $objPHPExcel->getActiveSheet()->mergeCells("B33:C36");
    $objPHPExcel->getActiveSheet()->mergeCells("A40:I40");
    $objPHPExcel->getActiveSheet()->mergeCells("A41:I41");
    $objPHPExcel->getActiveSheet()->mergeCells("A42:I42");
    $objPHPExcel->getActiveSheet()->mergeCells("A43:I43");
    $objPHPExcel->getActiveSheet()->mergeCells("B37:C37");
    $objPHPExcel->getActiveSheet()->mergeCells("G37:I37");
    $objPHPExcel->getActiveSheet()->mergeCells("H46:I46");

    // 标题
    $objPHPExcel->getActiveSheet()->setCellValue("C1", 'PC构件工序流转单');

    $tempArr = explode('-',$cName);
    $buildFloor = $tempArr[0].'# '.$tempArr[1].'F';
    $Count = count($tempArr);
    for ($i = 2; $i < $Count-1; $i++) {
        if ($i ==2){
            $PcName = $tempArr[$i];
        }else{
            $PcName .= '-'.$tempArr[$i];
        }
    }
    // 表1
    $objPHPExcel->getActiveSheet()->setCellValue("A2", '项目名称');
    $objPHPExcel->getActiveSheet()->setCellValue("B2", $Forshort);
    $objPHPExcel->getActiveSheet()->setCellValue("A3", '楼层');
    $objPHPExcel->getActiveSheet()->setCellValue("B3", $buildFloor);
    $objPHPExcel->getActiveSheet()->setCellValue("A4", '生产编号/日期');
    $objPHPExcel->getActiveSheet()->setCellValue("B4", $scDate);
    $objPHPExcel->getActiveSheet()->setCellValue("A5", '混凝土设计强度');
    $objPHPExcel->getActiveSheet()->setCellValue("B5", $CStr);
    $objPHPExcel->getActiveSheet()->setCellValue("E2", '线别');
    $objPHPExcel->getActiveSheet()->setCellValue("F2", $workShop);
    $objPHPExcel->getActiveSheet()->setCellValue("E3", '产品名称/编号');
    $objPHPExcel->getActiveSheet()->setCellValue("F3", $PcName );
    $objPHPExcel->getActiveSheet()->setCellValue("E4", '台车号');
    $objPHPExcel->getActiveSheet()->setCellValue("F4", "$liningNo");
    $objPHPExcel->getActiveSheet()->setCellValue("E5", '构件Id');
    $objPHPExcel->getActiveSheet()->setCellValue("F5", $ProductId);

    // 表2标题
    $objPHPExcel->getActiveSheet()->setCellValue("A7", '序号');
    $objPHPExcel->getActiveSheet()->setCellValue("B7", '项目分类');
    $objPHPExcel->getActiveSheet()->setCellValue("C7", '项目名称');
    $objPHPExcel->getActiveSheet()->setCellValue("D7", '图纸及技术要求');
    $objPHPExcel->getActiveSheet()->setCellValue("F7", '自检签名');
    $objPHPExcel->getActiveSheet()->setCellValue("G7", '自检完成时间');
    $objPHPExcel->getActiveSheet()->setCellValue("H7", '检验员签名');
    $objPHPExcel->getActiveSheet()->setCellValue("I7", '检验完成时间');

    // 表2内容
    // A列
    $objPHPExcel->getActiveSheet()->setCellValue("A8", '1');
    $objPHPExcel->getActiveSheet()->setCellValue("A19", '2');
    $objPHPExcel->getActiveSheet()->setCellValue("A24", '3');
    $objPHPExcel->getActiveSheet()->setCellValue("A28", '4');
    $objPHPExcel->getActiveSheet()->setCellValue("A31", '5');
    $objPHPExcel->getActiveSheet()->setCellValue("A33", '6');
    $objPHPExcel->getActiveSheet()->setCellValue("A37", '7');

    // B列
    $objPHPExcel->getActiveSheet()->setCellValue("B8", '组模');
    $objPHPExcel->getActiveSheet()->setCellValue("B19", '预埋预留');
    $objPHPExcel->getActiveSheet()->setCellValue("B24", '钢筋绑扎');
    $objPHPExcel->getActiveSheet()->setCellValue("B28", '浇捣');
    $objPHPExcel->getActiveSheet()->setCellValue("B31", '后处理');
    $objPHPExcel->getActiveSheet()->setCellValue("B33", '脱模吊装');
    $objPHPExcel->getActiveSheet()->setCellValue("B37", '检验结论');

    // C列
    $objPHPExcel->getActiveSheet()->setCellValue("C8", '模具清理');
    $objPHPExcel->getActiveSheet()->setCellValue("C9", '涂刷脱模油');
    $objPHPExcel->getActiveSheet()->setCellValue("C10", '组模尺寸');
    $objPHPExcel->getActiveSheet()->setCellValue("C17", '门、窗 预留');
    $objPHPExcel->getActiveSheet()->setCellValue("C19", '预埋吊钉');
    $objPHPExcel->getActiveSheet()->setCellValue("C20", '预埋套筒');
    $objPHPExcel->getActiveSheet()->setCellValue("C21", '预埋暗盒');
    $objPHPExcel->getActiveSheet()->setCellValue("C22", '预 埋 管');
    $objPHPExcel->getActiveSheet()->setCellValue("C23", '预 留 孔');
    $objPHPExcel->getActiveSheet()->setCellValue("C24", '布筋');
    $objPHPExcel->getActiveSheet()->setCellValue("C25", '扎筋');
    $objPHPExcel->getActiveSheet()->setCellValue("C26", '筋网摆放');
    $objPHPExcel->getActiveSheet()->setCellValue("C27", '预留筋');
    $objPHPExcel->getActiveSheet()->setCellValue("C28", '浇捣时间');
    $objPHPExcel->getActiveSheet()->setCellValue("C29", '布料、振捣');
    $objPHPExcel->getActiveSheet()->setCellValue("C30", '后预留、预埋');
    $objPHPExcel->getActiveSheet()->setCellValue("C31", '表面处理');
    $objPHPExcel->getActiveSheet()->setCellValue("C32", '养护');

    // D列
    $objPHPExcel->getActiveSheet()->setCellValue("D8", '干净无杂物，不得影响装模及钢筋安装');
    $objPHPExcel->getActiveSheet()->setCellValue("D9", '不允许漏涂、局部积油');
    $objPHPExcel->getActiveSheet()->setCellValue("D10", '边模上口平直，与底模不垂直度小于3');
    $objPHPExcel->getActiveSheet()->setCellValue("D11", '长：梁、楼板±3mm；墙、柱-3，0mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D12", '宽：墙、楼板-3，0mm；梁、柱-3，2mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D13", ' 高/厚：楼板、梁、柱-3，2mm；墙-3，0mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D14", '对角线偏差不大于5mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D15", '拼模不允许明显高差，拼缝不大于2mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D16", '侧模弯曲：楼板、梁、柱L/1000，且＜10mm；墙L/1500mm，且＜15mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D17", '位置5mm洞口尺寸+5~0，弯曲按侧模');
    $objPHPExcel->getActiveSheet()->setCellValue("D18", '对角线差≤4mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D19", '规格、数量正确，牢固，位置偏差5mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D20", '位置准确、安装牢固，规格符合要求');
    $objPHPExcel->getActiveSheet()->setCellValue("D21", '位置3mm,同一高度的暗盒高度差5mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D22", '数量、规格正确，位置偏差5mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D23", '数量、规格正确，位置偏差5mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D24", '牌号、规格、位置符合图纸要求');
    $objPHPExcel->getActiveSheet()->setCellValue("D25", '捆扎牢固，间距偏差±10mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D26", '位置正确、牢固，垫层符合规范要求');
    $objPHPExcel->getActiveSheet()->setCellValue("D27", '外露长度：灌浆套筒钢筋允许偏差-5，0mm；其它非受力钢筋0-8mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D28", '时期/时间');
    $objPHPExcel->getActiveSheet()->setCellValue("D29", '布料均匀、振捣充分到位、密实');
    $objPHPExcel->getActiveSheet()->setCellValue("D30", '符合图纸及标准要求');
    $objPHPExcel->getActiveSheet()->setCellValue("D31", '按工艺操作，楼板、梁上表面粗糙处理，外墙内侧面压实抹平，其余产品表面需抹光，平整度≤3mm');
    $objPHPExcel->getActiveSheet()->setCellValue("D32", '保证构件湿润，符合养护要求,确定入窑位置及统计');
    $objPHPExcel->getActiveSheet()->setCellValue("D33", '表面无蜂窝、露筋、麻面、无裂逢、掉角；');
    $objPHPExcel->getActiveSheet()->setCellValue("D34", '清理边缘飞边、泡沫无残留；');
    $objPHPExcel->getActiveSheet()->setCellValue("D35", '正确核对PC型号、楼层，张贴标示；');
    $objPHPExcel->getActiveSheet()->setCellValue("D36", '检查预埋、定位是否拆卸完成，检查预埋、表面是否平整，异常提出返修处理');
    $objPHPExcel->getActiveSheet()->getStyle('D8:D36')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->setCellValue("F37", '检验员签名');

    // 注释
    $objPHPExcel->getActiveSheet()->setCellValue("A40", '注：1、检验合格可打“√”表示，有检测数据直接填写，所检项目不合格则在对应栏填写缺陷代号和返工情况。');
    $objPHPExcel->getActiveSheet()->setCellValue("A41", '　　2、表中所述要求不尽全面，实际还应包含具体的相关标准、工艺要求。');
    $objPHPExcel->getActiveSheet()->setCellValue("A42", '　　3、生产编号按墙板、楼板、隔墙、其它四类区分，用日期＋两位数按顺序号表示，如：2015080101，表示2015年8月1日。');
    $objPHPExcel->getActiveSheet()->setCellValue("A43", '　　4、自检完成时间和检验完成时间最小填写单位为分钟。');

    // 下角标
    $objPHPExcel->getActiveSheet()->setCellValue("H46", 'TZ-QF-005-009');

    // 字体
    setBFont($objPHPExcel, 'C1', '黑体', 14);
    setBFont($objPHPExcel, 'A2:G5', '黑体', 9);
    setBFont($objPHPExcel, 'A7:I7', '宋体', 8);
    setBFont($objPHPExcel, 'A40:I43', '宋体', 8);
    setBFont($objPHPExcel, 'H46', '宋体', 9);
    setBFont($objPHPExcel, 'A8:I37', '宋体', 6);
}

$dates = date('Y-m-d');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=PC构件-流转单-' . $created . '.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>