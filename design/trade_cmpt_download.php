<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
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

$proId = $_POST["tradeChoose"];
$build = $_POST["buildChoose"];
if ($build){
    $buildSql = " AND A.BuildingNo=$build ";
}

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

//文件信息-图纸
if ($_POST["drawingChk"]) {
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle('图纸');
    
    $titles = array();
    //项目数据检索
    $mySql="SELECT a.Titles FROM $DataIn.trade_drawing_hole a where a.TradeId = $proId";
    $myResult = mysql_query($mySql, $link_id);
    if($myResult  && $myRow = mysql_fetch_array($myResult)){
        $titles = json_decode( $myRow["Titles"]);
    }

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
       
    $mySql="SELECT A.Id, A.TradeId, A.CmptType, A.BuildingNo, A.FloorNo, A.CmptNo,
    A.ProdCode, A.MouldNo, A.CStr, A.Length, A.Width, A.Thick, A.Sizes,
    A.HoleVol, A.Area, A.DwgVol, A.CVol, A.Weight, A.EndDwg, A.EmbeddedDwg,
    A.SteelDwg, A.DieDwg, A.UpdateReasons, A.ReturnReasons,
    A.Locks, A.Operator, A.PLocks, A.creator, A.created,
    C.proofreader, C.proofreaded, A.modifier, A.modified,
    B.Forshort, C.TradeNo, C.Estate
    FROM $DataIn.trade_drawing A
    LEFT JOIN $DataIn.trade_object B ON A.TradeId = b.Id and b.ObjectSign = 2
    LEFT JOIN $DataIn.trade_info C ON A.TradeId = C.TradeId
    where a.TradeId = $proId  $buildSql
    order by a.Id ";
    
    $Rows=2;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_array($myResult)){
        $i=1;
        do{
            $Estate=$myRow["Estate"];
            switch($Estate){
                case 0:$Estate="未提交";
                break;
                case 1:$Estate="未初校";
                break;
                case 2:$Estate="未复校";
                break;
                case 3:$Estate="初校未通过";
                break;
                case 4:$Estate="校核通过";
                break;
                case 5:$Estate="复校未通过";
                break;
                case 6:$Estate="未审核";
                break;
                case 7:$Estate="审核通过";
                break;
                case 8:$Estate="审核不通过";
                break;
                case 9:$Estate="审核退回";
                break;
                case 10:$Estate="生产中";
                break;
                case 11:$Estate="生产完成";
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
}

////////////////////////////////////////////////////////////////////////////////////
//文件信息-钢筋
if ($_POST["steelChk"]) {
    if ($_POST["drawingChk"]) {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1);
    } else {
        $objPHPExcel->setActiveSheetIndex(0);
    }
    $objPHPExcel->getActiveSheet()->setTitle('钢筋');
    
    $titles = array();
    $specs = array();
    $sizes = array();
    //项目数据检索
    $mySql="SELECT a.Titles, a.Specs, a.Sizes FROM $DataIn.trade_steel_data a where a.TradeId = $proId";
    $myResult = mysql_query($mySql, $link_id);
    if($myResult  && $myRow = mysql_fetch_array($myResult)){
        $titles = json_decode( $myRow["Titles"]);
        $specs = json_decode( $myRow["Specs"]);
        $sizes = json_decode( $myRow["Sizes"]);
    }
    
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
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
    
    for ($i= 0;$i< count($titles); $i++){
        //$str= $titles[$i];
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(14 + $i)->setWidth(8);
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
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '产品条码');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '审核状态');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '长');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '宽');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '厚');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '钢筋');
    $objPHPExcel->getActiveSheet()->setCellValue('N2', '规格');
    $objPHPExcel->getActiveSheet()->setCellValue('N3', '下料尺寸');

    for ($i= 0;$i< count($titles); $i++){
        $str= $titles[$i];
        $str1= $specs[$i];
        $str2= $sizes[$i];

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $i, 1, $str);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $i, 2, $str1);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $i, 3, $str2);
    }
    
    $lastCol = PHPExcel_Cell::stringFromColumnIndex(25 + count($titles));
    
    $objPHPExcel->getActiveSheet()->getStyle( 'A1:' . $lastCol . '1')->applyFromArray($style_center);
      
    $mySql="SELECT A.Id, A.TradeId, A.CmptType, A.BuildingNo, A.FloorNo, A.CmptNo,
    A.ProdCode, A.Length, A.Width, A.Thick, A.Quantities,
    B.Forshort, C.TradeNo, C.Estate
    FROM $DataIn.trade_steel A
    LEFT JOIN $DataIn.trade_object B ON A.TradeId = b.Id and b.ObjectSign = 2
    LEFT JOIN $DataIn.trade_info C ON A.TradeId = C.TradeId
    where a.TradeId = $proId $buildSql 
    order by a.Id ";
    
    $Rows=4;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_array($myResult)){
        $i=1;
        do{
            $Estate=$myRow["Estate"];
            switch($Estate){
                case 0:$Estate="未提交";
                break;
                case 1:$Estate="未初校";
                break;
                case 2:$Estate="未复校";
                break;
                case 3:$Estate="初校未通过";
                break;
                case 4:$Estate="校核通过";
                break;
                case 5:$Estate="复校未通过";
                break;
                case 6:$Estate="未审核";
                break;
                case 7:$Estate="审核通过";
                break;
                case 8:$Estate="审核不通过";
                break;
                case 9:$Estate="审核退回";
                break;
                case 10:$Estate="生产中";
                break;
                case 11:$Estate="生产完成";
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
            $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $myRow['Length']);
            $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $myRow['Width']);
            $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", $myRow['Thick']);
            $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", "数量");
            
            $Quantities = json_decode($myRow['Quantities']);
            
            for ($j= 0;$j< count($titles) && $j< count($Quantities); $j++){
                $str= $Quantities[$j];
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14 + $j, $Rows, "$str");
            }
            
            $objPHPExcel->getActiveSheet()->getStyle("B$Rows:" . $lastCol . $Rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $i++;
            $Rows++;
        }while ($myRow = mysql_fetch_array($myResult));
    }
}


////////////////////////////////////////////////////////////////////////////////////
//文件信息-预埋件
if ($_POST["embeddedChk"]) {
    if ($_POST["drawingChk"] || $_POST["steelChk"]) {
        $objPHPExcel->createSheet();
        if ($_POST["drawingChk"] &&  $_POST["steelChk"]) {
            $objPHPExcel->setActiveSheetIndex(2);
        } else {
            $objPHPExcel->setActiveSheetIndex(1);
        }
    } else {
        $objPHPExcel->setActiveSheetIndex(0);
    }
    
    $objPHPExcel->getActiveSheet()->setTitle('预埋件');
    
    $titles = array();
    $specs = array();
    //项目数据检索
    $mySql="SELECT a.Titles, a.Specs FROM $DataIn.trade_embedded_data a where a.TradeId = $proId";
    $myResult = mysql_query($mySql, $link_id);
    if($myResult  && $myRow = mysql_fetch_array($myResult)){
        $titles = json_decode( $myRow["Titles"]);
        $specs = json_decode( $myRow["Specs"]);
    }
    
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
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
    
    for ($i= 0;$i< count($titles); $i++){
        //$str= $titles[$i];
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(13 + $i)->setWidth(10);
    }
    
    //合并单元格
    $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
    $objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
    $objPHPExcel->getActiveSheet()->mergeCells('C1:C2');
    $objPHPExcel->getActiveSheet()->mergeCells('D1:D2');
    $objPHPExcel->getActiveSheet()->mergeCells('E1:E2');
    $objPHPExcel->getActiveSheet()->mergeCells('F1:F2');
    $objPHPExcel->getActiveSheet()->mergeCells('G1:G2');
    $objPHPExcel->getActiveSheet()->mergeCells('H1:H2');
    $objPHPExcel->getActiveSheet()->mergeCells('I1:I2');
    $objPHPExcel->getActiveSheet()->mergeCells('J1:J2');
    $objPHPExcel->getActiveSheet()->mergeCells('K1:K2');
    $objPHPExcel->getActiveSheet()->mergeCells('L1:L2');
    $objPHPExcel->getActiveSheet()->mergeCells('M1:M2');
    
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(24);
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
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '长');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '宽');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '厚');
    
    for ($i= 0;$i< count($titles); $i++){
        $str= $titles[$i];
        $str1= $specs[$i];
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13 + $i, 1, $str);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13 + $i, 2, $str1);
    }
    
    $lastCol = PHPExcel_Cell::stringFromColumnIndex(25 + count($titles));
    
    $objPHPExcel->getActiveSheet()->getStyle( 'A1:' . $lastCol . '1')->applyFromArray($style_center);
    
    $mySql="SELECT A.Id, A.TradeId, A.CmptType, A.BuildingNo, A.FloorNo, A.CmptNo,
    A.ProdCode, A.Length, A.Width, A.Thick, A.Quantities,
    B.Forshort, C.TradeNo, C.Estate
    FROM $DataIn.trade_embedded A
    LEFT JOIN $DataIn.trade_object B ON A.TradeId = b.Id and b.ObjectSign = 2
    LEFT JOIN $DataIn.trade_info C ON A.TradeId = C.TradeId
    where a.TradeId = $proId $buildSql 
    order by a.Id ";
    
    $Rows=3;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_array($myResult)){
        $i=1;
        do{
            $Estate=$myRow["Estate"];
            switch($Estate){
                case 0:$Estate="未提交";
                break;
                case 1:$Estate="未初校";
                break;
                case 2:$Estate="未复校";
                break;
                case 3:$Estate="初校未通过";
                break;
                case 4:$Estate="校核通过";
                break;
                case 5:$Estate="复校未通过";
                break;
                case 6:$Estate="未审核";
                break;
                case 7:$Estate="审核通过";
                break;
                case 8:$Estate="审核不通过";
                break;
                case 9:$Estate="审核退回";
                break;
                case 10:$Estate="生产中";
                break;
                case 11:$Estate="生产完成";
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
            $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $myRow['Length']);
            $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $myRow['Width']);
            $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", $myRow['Thick']);
            
            $Quantities = json_decode($myRow['Quantities']);
            
            for ($j= 0;$j< count($titles) && $j< count($Quantities); $j++){
                $str= $Quantities[$j];
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13 + $j, $Rows, "$str");
            }
            
            $objPHPExcel->getActiveSheet()->getStyle("B$Rows:" . $lastCol . $Rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $i++;
            $Rows++;
        }while ($myRow = mysql_fetch_array($myResult));
    }
}

if ($_POST["PordDrawingChk"]) {
    //成品图纸
    $FilePath="./dwgFiles/$proId/Pord/";
    
    $files = read_all($FilePath);
}
if ($_POST["SteelDrawingChk"]) {
    //钢筋图纸
    $FilePath="./dwgFiles/$proId/Steel/";
    
    $files = array_merge($files, read_all($FilePath));
}
if ($_POST["MouldDrawingChk"]) {
    //模具图纸
    $FilePath="./dwgFiles/$proId/Mould/";
    
    $files = array_merge($files, read_all($FilePath));
}
if ($_POST["EmbeddedDrawingChk"]) {
    // 预埋件图纸
    $FilePath="./dwgFiles/$proId/Embedded/";
    
    $files = array_merge($files, read_all($FilePath));
}

if (count($files) == 0) {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=straxdata.xlsx');
    header('Cache-Control: max-age=0');
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

    exit;
} else {
    //多个文件zip下载
    $zip = new ZipArchive();
    $filename = "./dwgFiles/$proId/download.zip";
    if (file_exists($filename)) {
        @unlink ($filename);
    }
    if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
        exit('无法打开文件，或者文件创建失败');
    }
    foreach( $files as $val){
        if(file_exists($val)){
            $zip->addFile( $val, basename($val));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
        }
    }
        
    //excel压缩
    if ($_POST["drawingChk"] || $_POST["steelChk"] || $_POST["embeddedChk"]) {

        $exceltempfile = "./dwgFiles/$proId/straxdata.xlsx";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($exceltempfile);
        
        $zip->addFile($exceltempfile, basename($exceltempfile));
    }
    $zip->close();//关闭     
    
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header('Content-disposition: attachment; filename='.basename($filename)); //文件名
    header("Content-Type: application/zip"); //zip格式的
    header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
    header('Content-Length: '. filesize($filename)); //告诉浏览器，文件大小
    @readfile($filename); 
    @unlink($filename);
    @unlink($exceltempfile);
    
    exit;
}

//读目录下文件 不包含子目录
function read_all ($dir){
    $rt = array ();

    if(!is_dir($dir)) return $rt;
    
    $tmp = scandir ( $dir );
    
    foreach ( $tmp as $f ) {
        // 过滤. ..
        if ($f == '.' || $f == '..')
            continue;
        
        $path = $dir . $f;
        if (is_file ( $path )) { // 如果是文件，放入容器中
                $rt [] = $path;
        }
    }
    return $rt;
}

?>