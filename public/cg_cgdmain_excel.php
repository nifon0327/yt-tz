<?php
include "../basic/chksession.php";
include "../basic/parameter.inc";

$SealCompanyStr = $SealCompanyId == 1 ? "上海研砼治筑建筑科技有限公司" : "常州砼筑建筑科技有限公司";

$SealCompanyStr = $SealCompanyStr . "采购合同";

$StockResult = mysql_query("SELECT M.PurchaseID,M.DeliveryDate,M.Date,M.Remark,C.PreChar,M.CompanyId,
P.Forshort,P.GysPayMode,I.Company,PM.Name AS GysPayMode,
I.Tel,I.Fax,
L.Name AS Linkman,L.Email,
S.Name,S.Mail,S.ExtNo,
C.Symbol,X.InvoiceTax
FROM $DataIn.cg1_stockmain M 
LEFT JOIN $DataIn.trade_object P ON M.CompanyId =P.CompanyId 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId AND I.Type='8'
LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=I.CompanyId AND L.Type=I.Type AND L.Defaults='0'
LEFT JOIN $DataIn.currencydata C ON C.Id=P.Currency
LEFT JOIN $DataIn.providersheet X ON X.CompanyId=P.CompanyId 
LEFT JOIN $DataIn.providerpaymode PM ON PM.Id = P.GysPayMode
LEFT JOIN $DataIn.staffmain S ON M.BuyerId=S.Number WHERE M.Id='$Mid' ", $link_id);

if ($myrow = mysql_fetch_array($StockResult)) {
    $Remark = $myrow["Remark"];
    $Remark = $Remark == "" ? "" : "备注：" . $Remark;
    $PurchaseID = $myrow["PurchaseID"];
    $Provider = $myrow["Company"];
    $Linkman = $myrow["Linkman"];
    $ExtNo = $myrow["ExtNo"];
    $Tel = $myrow["Tel"];
    $Fax = $myrow["Fax"];
    $Email = $myrow["Email"];
    $InvoiceTax = $myrow["InvoiceTax"];
    $InvoiceTax = $InvoiceTax == "" ? 0 : $InvoiceTax;
    $GysPayMode = $myrow["GysPayMode"];
    $PreChar = $myrow["PreChar"];
    $Symbol = $myrow["Symbol"];
    $PaySTR = $Symbol . $GysPayMode;
    $Buyer = $myrow["Name"];
    $Mail = $myrow["Mail"];
    $Date = $myrow["Date"];

    $CompanyId = $myrow["CompanyId"];
}


/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$style_left = array(
    'font'      => array(
        'size' => 14,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'       => true,
    ),
);

$style_center = array(
    'font'      => array(
        'size' => 14,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'       => true,
    ),
);

$style_right = array(
    'font'      => array(
        'size' => 14,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'       => true,
    ),
);


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);


$objPHPExcel->getActiveSheet()->setCellValue('A1', "$SealCompanyStr");
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');

$objPHPExcel->getActiveSheet()->setCellValue('F1', "$PurchaseID");
$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLACK);
$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setSize(18);
$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('F1:J1');


$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A2', "供应商: $Provider");
$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
$objPHPExcel->getActiveSheet()->setCellValue('F2', "采购日期: $Date");
$objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->mergeCells('F2:J2');


$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A3', "接洽人: $Linkman");
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->mergeCells('A3:E3');
$objPHPExcel->getActiveSheet()->setCellValue('F3', "采 购 人: $Buyer");
$objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->mergeCells('F3:J3');


$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A4', "电 话: $Tel");
$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->mergeCells('A4:E4');
$objPHPExcel->getActiveSheet()->setCellValue('F4', "联系邮箱: $Mail");
$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->mergeCells('F4:J4');


$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A5', "传 真: $Fax");
$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->mergeCells('A5:E5');
$objPHPExcel->getActiveSheet()->setCellValue('F5', "结付方式: $PaySTR");
$objPHPExcel->getActiveSheet()->getStyle('E5')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->mergeCells('F5:J5');


$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
/*$objPHPExcel->getActiveSheet()->setCellValue('A6', 'PO');
$objPHPExcel->getActiveSheet()->setCellValue('B6', '配件ID');
$objPHPExcel->getActiveSheet()->setCellValue('C6', '配件名称');
$objPHPExcel->getActiveSheet()->setCellValue('D6', '需求数');
$objPHPExcel->getActiveSheet()->setCellValue('E6', '增购数');
$objPHPExcel->getActiveSheet()->setCellValue('F6', '实购数');
$objPHPExcel->getActiveSheet()->setCellValue('G6', '含税价');
$objPHPExcel->getActiveSheet()->setCellValue('H6', '单位');
$objPHPExcel->getActiveSheet()->setCellValue('I6', '金额');
$objPHPExcel->getActiveSheet()->setCellValue('J6', '交期');*/
$objPHPExcel->getActiveSheet()->setCellValue('A6', '序号');
$objPHPExcel->getActiveSheet()->setCellValue('B6', '项目');
$objPHPExcel->getActiveSheet()->setCellValue('C6', '配件名称');
$objPHPExcel->getActiveSheet()->setCellValue('D6', '规格');
$objPHPExcel->getActiveSheet()->setCellValue('E6', '需购数');
$objPHPExcel->getActiveSheet()->setCellValue('F6', '含税价');
$objPHPExcel->getActiveSheet()->setCellValue('G6', '单位');
$objPHPExcel->getActiveSheet()->setCellValue('H6', '金额');
$objPHPExcel->getActiveSheet()->setCellValue('I6', '交期');
$objPHPExcel->getActiveSheet()->setCellValue('J6', '备注');
$objPHPExcel->getActiveSheet()->getStyle('A6:J6')->applyFromArray($style_center);
$Rows = 7;

$cgSql = mysql_query("select CONCAT(StockId,'|'),StuffId,OrderPO,sum(Price) as Price,sum(Qty) as Qty,sum(AddQty) as AddQty,sum(FactualQty) as FactualQty,DeliveryDate,ForShort,AddRemark,StuffCname,Spec,Unit 
from (SELECT S.StockId,S.StuffId,S.Price,(S.AddQty+S.FactualQty) AS Qty,S.DeliveryDate,S.AddRemark,
D.StuffCname,D.Spec,U.Name AS Unit ,S.AddQty,S.FactualQty,Y.OrderPO,O.ForShort
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
LEFT JOIN $DataIn.stuffunit U  ON U.Id=D.Unit 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
LEFT JOIN  $DataIn.yw1_ordermain YM ON YM.OrderNumber = Y.OrderNumber 
LEFT JOIN  $DataIn.trade_object O ON O.CompanyId = YM.CompanyId 
WHERE  S.MId='$Mid' ORDER BY D.StuffCname) as ttt GROUP BY StuffId,OrderPO", $link_id);
/*$mySql = "select StockId,StuffId,OrderPO,sum(Price) as Price,sum(Qty) as Qty,sum(AddQty) as AddQty,sum(FactualQty) as FactualQty,DeliveryDate,AddRemark,StuffCname,Spec,Unit from (SELECT S.StockId,S.StuffId,S.Price,(S.AddQty+S.FactualQty) AS Qty,S.DeliveryDate,S.AddRemark,
D.StuffCname,D.Spec,U.Name AS Unit ,S.AddQty,S.FactualQty,Y.OrderPO
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
LEFT JOIN $DataIn.stuffunit U  ON U.Id=D.Unit 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
WHERE  S.MId='$Mid' ORDER BY D.StuffCname) as ttt GROUP BY StuffId,OrderPO";*/
if ($cgRow = mysql_fetch_array($cgSql)) {
    $i = 1;
    $taxThisAmount = 0;

    do {
        $ForShort = $cgRow["ForShort"];
        $StockId = $cgRow["StockId"];
        $OrderPO = $cgRow["OrderPO"];
        $StuffId = $cgRow["StuffId"];
        $DeliveryDate = $cgRow["DeliveryDate"];
        $Spec = $cgRow["Spec"];
        $Unit = $cgRow["Unit"] == "" ? "&nbsp;" : $cgRow["Unit"];
        $CName = $cgRow["StuffCname"];
        $StuffCname = str_replace("$Spec","","$CName");
        $FactualQty = $cgRow["FactualQty"];
        $AddQty = $cgRow["AddQty"];
        $Price = $cgRow["Price"];
        $Qty = $cgRow["Qty"];
        $Amount = sprintf("%.2f", $Qty * $Price);
        //****************************
        $DeliveryDateShow = "未设置";
        if ($DeliveryDate != "" && $DeliveryDate != "0000-00-00") {
            $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$DeliveryDate',1) AS Week", $link_id));
            $CGWeek = $dateResult["Week"];
            if ($CGWeek > 0) {
                $week = substr($CGWeek, 4, 2);
                $weekName = $week . "周";
            }
        }


        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        /*$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$OrderPO");
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$StuffId");
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$StuffCname");
        $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$FactualQty");
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$AddQty");
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Qty");
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$Price");
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Unit");
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Amount");
        $objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$weekName");*/

        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$ForShort");
        $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$StuffCname");
        $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Spec");
        $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Qty");
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Price");
        $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$Unit");
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Amount");
        $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$weekName");
        $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$Remark");

        $Rows++;
        $i++;
    } while ($cgRow = mysql_fetch_array($cgSql));
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$PurchaseID.'.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>