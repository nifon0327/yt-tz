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

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

//文件信息-BOM信息
if ($_POST["infoChk"]) {
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle('BOM信息');

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(9);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
    
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(27);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '选项');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '顺序号');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '项目编号');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '项目名称');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '构件类型');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '配件分类');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '物料编号');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '审核状态');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '配件主分类');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '是否新增');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '名称');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '规格');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '单位');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '数量');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', '单价');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', '总额');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', '损耗');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', '备注');
        
    $objPHPExcel->getActiveSheet()->getStyle( 'A1:R1')->applyFromArray($style_center);
    
    $mySql="select a.Id, a.TradeId, a.CmptTypeId, a.CmptType, a.MaterNo, 
a.StuffTypeId, e.TypeName as StuffType, a.MStuffTypeId, f.TypeName as MStuffType, a.IsNew, a.MaterName,
a.Spec, a.Unit, a.Quantity, a.Price, a.Total, a.Loss, a.Remark,
b.TradeNo, c.Forshort, d.Estate
from $DataIn.bom_info a
LEFT JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
LEFT JOIN $DataIn.trade_object c on c.id = a.TradeId
INNER JOIN $DataIn.bom_object d on d.TradeId = a.TradeId
LEFT JOIN $DataIn.stufftype e on a.StuffTypeId = e.TypeId 
LEFT JOIN $DataIn.stuffmaintype f on a.MStuffTypeId = f.id
where a.TradeId = $proId order by a.Id ";
    
    $Rows=2;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_array($myResult)){
        $i=1;
        do{
            $Estate=$myRow["Estate"];
            switch($Estate){
                case 0:$Estate="未提交";
                break;
                case 1:$Estate="未审核";
                break;
                case 2:$Estate="审核通过";
                break;
                case 3:$Estate="审核不通过";
                break;
                case 4:$Estate="审核退回";
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
            $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $myRow['StuffType']);
            $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $myRow['MaterNo']);
            $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Estate");
            $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $myRow['MStuffType']);
            $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $myRow['IsNew']);
            $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", $myRow['MaterName']);
            $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", $myRow['Spec']);
            $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", $myRow['Unit']);
            $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", round($myRow['Quantity'], 3));
            $objPHPExcel->getActiveSheet()->setCellValue("O$Rows", $myRow['Price']);
            $objPHPExcel->getActiveSheet()->setCellValue("P$Rows", $myRow['Total']);
            $objPHPExcel->getActiveSheet()->setCellValue("Q$Rows", $myRow['Loss']);
            $objPHPExcel->getActiveSheet()->setCellValue("R$Rows", $myRow['Remark']);

            $objPHPExcel->getActiveSheet()->getStyle("B$Rows:R$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $i++;
            $Rows++;
        }while ($myRow = mysql_fetch_array($myResult));
    }
}

////////////////////////////////////////////////////////////////////////////////////
//文件信息-损耗
if ($_POST["lossChk"]) {
    if ($_POST["infoChk"]) {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1);
    } else {
        $objPHPExcel->setActiveSheetIndex(0);
    }
    $objPHPExcel->getActiveSheet()->setTitle('损耗');
        
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
       
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(27);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '选项');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '顺序号');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '构件类别');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '配件分类');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '单位');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '本次标准');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'PC定额标准');
    
    $objPHPExcel->getActiveSheet()->getStyle( 'A1:G1')->applyFromArray($style_center);
    
    $mySql="select a.Id, a.TradeId, a.CmptType, a.StuffType,
a.Unit, a.ThisStd, a.PcStd,
b.TradeNo, c.Forshort, d.Estate
from $DataIn.bom_loss a
LEFT JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
LEFT JOIN $DataIn.trade_object c on c.id = a.TradeId
INNER JOIN $DataIn.bom_object d on d.TradeId = a.TradeId
where a.TradeId = $proId order by a.Id ";
    
    $Rows=2;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_array($myResult)){
        $i=1;
        do{
            
            //$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(65);
            $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "");
            $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$i");
            $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $myRow['CmptType']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $myRow['StuffType']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $myRow['Unit']);
            $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $myRow['ThisStd']);
            $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $myRow['PcStd']);

            $objPHPExcel->getActiveSheet()->getStyle("B$Rows:G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $i++;
            $Rows++;
        }while ($myRow = mysql_fetch_array($myResult));
    }
}

////////////////////////////////////////////////////////////////////////////////////
//文件信息-模具信息
if ($_POST["mouldChk"]) {
    if ($_POST["infoChk"] || $_POST["lossChk"]) {
        $objPHPExcel->createSheet();
        if ($_POST["infoChk"] &&  $_POST["lossChk"]) {
            $objPHPExcel->setActiveSheetIndex(2);
        } else {
            $objPHPExcel->setActiveSheetIndex(1);
        }
    } else {
        $objPHPExcel->setActiveSheetIndex(0);
    }
    
    $objPHPExcel->getActiveSheet()->setTitle('模具');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(27);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '选项');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '顺序号');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '项目编号');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '项目名称');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '模具类别');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '模具编号');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '制作数量');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '共模比');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '长(mm)');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '宽(mm)');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '楼栋号');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '楼层号');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '构件编号');


    $objPHPExcel->getActiveSheet()->getStyle( 'A1:Q1')->applyFromArray($style_center);
    
    $mySql="select a.Id, a.TradeId, a.MouldCat, a.MouldNo, a.ProQty, 
    a.Ratio, a.Length, a.Width,
    b.TradeNo, c.Forshort, d.Estate
    from $DataIn.bom_mould a
    LEFT JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
    LEFT JOIN $DataIn.trade_object c on c.id = a.TradeId
    INNER JOIN $DataIn.bom_object d on d.TradeId = a.TradeId
    where a.TradeId = $proId order by a.Id ";
    
    $Rows=2;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_array($myResult)){
        $i=1;
        do{
            $Id=$myRow["Id"];
            $MouldNo=$myRow["MouldNo"];
            
            //$mouldResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS mouldNum FROM $DataIn.trade_drawing a
            //        WHERE a.TradeId= $proId and a.CmptNo = '$MouldNo' ",$link_id));
            //$mouldNum=$mouldResult["mouldNum"];

            //$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(65);
            $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "");
            $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$i");
            $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $myRow['TradeNo']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $myRow['Forshort']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $myRow['MouldCat']);
            $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $myRow['MouldNo']);
            $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $myRow['ProQty']);
            $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $myRow['Ratio']);
            $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $myRow['Length']);
            $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $myRow['Width']);
            
            $mouldResult=mysql_query("SELECT a.BuildingNo,a.FloorNo,a.CmptNo FROM $DataIn.bom_mould_data a
                   WHERE a.MouldId = $Id order by a.Id ",$link_id);
            
            $mgRow = $Rows;
            if ($mouldResult && $mouldRow = mysql_fetch_array($mouldResult)) {
                do {
                    
                    $objPHPExcel->getActiveSheet()->setCellValue("K$mgRow", $mouldRow['BuildingNo']);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$mgRow", $mouldRow['FloorNo']);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$mgRow", $mouldRow['CmptNo']);

                    $objPHPExcel->getActiveSheet()->getStyle("K$mgRow:M$mgRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    $mgRow++;
                } while ($mouldRow = mysql_fetch_array($mouldResult));
            }
            
            if ($mgRow > $Rows) {
                $mgRow--;
            }
            
            //合并
            $objPHPExcel->getActiveSheet()->mergeCells("A$Rows:A$mgRow");
            $objPHPExcel->getActiveSheet()->mergeCells("B$Rows:B$mgRow");
            $objPHPExcel->getActiveSheet()->mergeCells("C$Rows:C$mgRow");
            $objPHPExcel->getActiveSheet()->mergeCells("D$Rows:D$mgRow");
            $objPHPExcel->getActiveSheet()->mergeCells("E$Rows:E$mgRow");
            $objPHPExcel->getActiveSheet()->mergeCells("F$Rows:F$mgRow");
            $objPHPExcel->getActiveSheet()->mergeCells("G$Rows:G$mgRow");
            $objPHPExcel->getActiveSheet()->mergeCells("H$Rows:H$mgRow");
            $objPHPExcel->getActiveSheet()->mergeCells("I$Rows:I$mgRow");
            $objPHPExcel->getActiveSheet()->mergeCells("J$Rows:J$mgRow");
            
            $objPHPExcel->getActiveSheet()->getStyle("B$Rows:J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle("B$Rows:J$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $Rows = $mgRow;

            $i++;
            $Rows++;
        }while ($myRow = mysql_fetch_array($myResult));
    }
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=straxdata.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;

?>