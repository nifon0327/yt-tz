<?php
/*
 * By.LWH
 * 领料信息导出
 * */
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

$name = "straxdata";
$type = "2003";

$sPOrderIdArr = $_REQUEST["sPOrderId"];
$StockIdArr = $_REQUEST["StockId"];

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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);


$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(27);


$objPHPExcel->getActiveSheet()->setCellValue('A1', '客户');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '业务单号');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '产品中文名');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '交期');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '半成品名称');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '生产数量');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '配件名称');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '需求单流水号');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '配料人');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '单位');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '需领料数');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '已领料');
$objPHPExcel->getActiveSheet()->setCellValue('M1', '本次备料');

$objPHPExcel->getActiveSheet()->getStyle( 'A1:M1')->applyFromArray($style_center);


$mySql="SELECT  L.POrderId,L.sPOrderId,O.Forshort,SC.Qty, M.PurchaseID,SM.mStockId,
D.StuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,CG.DeliveryDate,CG.DeliveryWeek,(CG.addQty+CG.FactualQty) AS xdQty,Y.OrderPO,P.cName
FROM $DataIn.ck5_llsheet L 
INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
INNER JOIN $DataIn.cg1_semifinished  SM ON SM.StockId = SC.StockId
LEFT  JOIN $DataIn.cg1_stocksheet    CG  ON CG.StockId = SC.mStockId
LEFT  JOIN $DataIn.stuffdata         D  ON D.StuffId = SM.mStuffId 
LEFT  JOIN $DataIn.stufftype         T  ON T.TypeId = D.TypeId
LEFT  JOIN $DataIn.cg1_stockmain     M  ON M.Id = CG.Mid 
WHERE 1 AND L.Estate>0 AND L.sPOrderId in ($sPOrderIdArr) GROUP BY SC.sPOrderId  ORDER BY DeliveryWeek,Y.OrderPO";


$Rows=2;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){

    $i=1;
    do{
        $Id=$myRow["Id"];
        $POrderId=$myRow["POrderId"];
        $sPOrderId=$myRow["sPOrderId"];
        $mStockId=$myRow["mStockId"];
        $Forshort=$myRow["Forshort"];
        $Qty=$myRow["Qty"];
        $xdQty=$myRow["xdQty"];
        $Relation=$Qty/$xdQty;
        $PurchaseID=$myRow["PurchaseID"];
        $Remark=$myRow["Remark"];
        $Date =$myRow["Date"];
        $StuffId=$myRow["StuffId"];
        $StuffCname=$myRow["StuffCname"];
        $Picture=$myRow["Picture"];

        $OrderPO=$myRow["OrderPO"];
        $cName=$myRow["cName"];


       /* $DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
        include "../model/modelfunction.php";
        //PI交期转换成周数显示
        if ($DeliveryDate!="" && $DeliveryDate!="&nbsp;" ){
            if ($curWeeks==""){
                $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
                $curWeeks=$dateResult["CurWeek"];
            }
            $DeliveryDate=str_replace("*", "", $DeliveryDate);
            $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$DeliveryDate',1) AS Week",$link_id));
            $toWeek=$dateResult["Week"];

            if ($toWeek>0){
                $week=substr($toWeek, 4,2);
                $dateArray= GetWeekToDate($toWeek,"m/d");
                $weekName="Week " . $week;
                $dateSTR=$dateArray[0] . "-" .  $dateArray[1];

                $Delivery_Color=($toWeek<=$curWeeks && $Delivery_NoColor==0)?"#FF0000" : "#000000";
                $DeliveryDate="$weekName\r\n$dateSTR";
            }
        }*/


        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", $Forshort);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", $OrderPO);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", $cName);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", $DeliveryDate);
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", $StuffCname);
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", $Qty);


        $checkStockSql=mysql_query("SELECT (A.OrderQty*$Relation) AS OrderQty,A.StockId,
		        K.tStockQty,D.StuffId,D.StuffCname,D.Picture,F.Remark,M.Name,
		        P.Forshort,U.Name AS UnitName,U.Decimals
				FROM   $DataIn.cg1_semifinished   A 
                INNER JOIN $DataIn.cg1_stocksheet G  ON G.StockId = A.StockId
				INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId 
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
				LEFT JOIN  $DataIn.staffmain M ON M.Number=G.BuyerId 
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
				LEFT JOIN  $DataIn.base_mposition F ON F.Id=D.SendFloor
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				INNER JOIN $DataIn.stuffmaintype MT ON MT.Id=T.mainType
				INNER JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				WHERE A.POrderId='$POrderId' AND A.mStockId='$mStockId' AND MT.blSign=1 AND A.StockId in ($StockIdArr) 
        ORDER BY D.SendFloor",$link_id);

        if($checkStockRow=mysql_fetch_array($checkStockSql)){
            $j=1;
            do{
                $Name=$checkStockRow["Name"];
                //$Forshort=$checkStockRow["Forshort"];
                $StockId=$checkStockRow["StockId"];
                $StuffId=$checkStockRow["StuffId"];
                $StuffCname=$checkStockRow["StuffCname"];
                $UnitName=$checkStockRow["UnitName"];
                $Picture=$checkStockRow["Picture"];
                $Decimals=$checkStockRow["Decimals"];
                $tStockQty=round($checkStockRow["tStockQty"],$Decimals);
                $OrderQty= round($checkStockRow["OrderQty"],$Decimals);
                $Remark=$checkStockRow["Remark"];

//本次领料数
                $UnionSTR3=mysql_query("SELECT SUM(Qty) AS thisQty FROM $DataIn.ck5_llsheet WHERE   sPOrderId = '$sPOrderId' AND StockId='$StockId' AND Estate='1'",$link_id);
                $thisQty=mysql_result($UnionSTR3,0,"thisQty");
                $thisQty=$thisQty==""?0:$thisQty;
                //已备料总数
                $UnionSTR4=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE sPOrderId = '$sPOrderId' AND StockId='$StockId' AND Estate='0'",$link_id);
                $llQty=mysql_result($UnionSTR4,0,"llQty");
                $llQty=$llQty==""?0:$llQty;
                if($llQty>$OrderQty){//领料总数大于订单数,提示出错
                    $llBgColor="class='redB'";
                }
                else{
                    if($llQty==$OrderQty){//刚好全领，绿色
                        $llBgColor="class='greenB'";
                    }
                    else{				//未领完
                        $llBgColor="";
                    }
                }

                        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", $StuffCname);
                        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", $StockId);
                        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", $blMan);
                        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", $UnitName);
                        $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "$OrderQty");
                        $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "$llQty");
                        $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "$thisQty");

                        $objPHPExcel->getActiveSheet()->getStyle("A$Rows:M$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $objPHPExcel->getActiveSheet()->getStyle("A$Rows:M$Rows")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                //$objPHPExcel->getActiveSheet()->getStyle("K$Rows")->getAlignment()->setWrapText(true);//换行

                $j++;
                $Rows++;
            }while($checkStockRow=mysql_fetch_array($checkStockSql));


        }

        $i++;
        $Rows++;
    }while ($myRow = mysql_fetch_array($myResult));
}


/* by.lwh */
if($type == '2007') { //导出excel2007文档
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
} elseif ($type == '2003') { //导出excel2003文档
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$name.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
} else {
    echo "<script>alert('导出文件格式有误，请联系管理员')</script>";
}

exit;
?>