<?php
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
if ($type == 1){
    $mySql = "SELECT D.StuffCname, D.StuffEname, S.Qty, Y.cgRemark, C.Forshort, S.StockId, S.StuffId, S.TaskId ,M.BillNumber,S.Mid
    FROM $DataIn.ck1_rksheet S
    LEFT JOIN $DataIn.ck1_rkmain M ON S.Mid=M.Id
    LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
    LEFT JOIN $DataIn.productdata CP ON CP.ProductId=Y.ProductId
    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=CP.CompanyId
    WHERE S.StuffId = $Id and S.Mid = $MId GROUP BY D.StuffId";
}elseif ($type == 2) {
    $mySql = "SELECT D.StuffCname, D.StuffEname, S.Qty, Y.cgRemark, C.Forshort, S.StockId, S.StuffId, S.TaskId 
    FROM $DataIn.ck1_rksheet S
    LEFT JOIN $DataIn.ck1_rkmain M ON S.Mid=M.Id
    LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
    LEFT JOIN $DataIn.productdata CP ON CP.ProductId=Y.ProductId
    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=CP.CompanyId
    WHERE S.id = $Id";
}
//echo $mySql;
$result = mysql_query($mySql);

if($row = mysql_fetch_array($result)){
    $StuffCname=$row["StuffCname"];
    $StuffEname=$row["StuffEname"];
    $Qty=$row["Qty"];
    $cgRemark=$row["cgRemark"];
    $Forshort=$row["Forshort"];

    $StockId = $row["StockId"];
    $StuffId = $row["StuffId"];
    $TaskId = $row['TaskId'];

    $MId = $row["Mid"];

    @$BillNumber = $row["BillNumber"];

    if ($TaskId == '' || $TaskId == null || !$TaskId) {
        mysql_query("update wms_ini set Value=CONVERT(CONVERT(Value, SIGNED)+1, CHAR) where Id=1 and Name='TaskIDB'");

        $taskIdResult = mysql_fetch_array(mysql_query("select CONCAT('B',Value) as TaskId from wms_ini where Id=1", $link_id));

        $TaskId = $taskIdResult["TaskId"];

        if ($type == 1) {
            //mysql_query("update $DataIn.ck1_rkmain set TaskId = '$TaskId' where BillNumber = '$BillNumber'");//包装编号
            mysql_query("update $DataIn.ck1_rksheet set TaskId = '$TaskId' where StuffId = $StuffId and Mid = $MId");//包装编号

        } elseif ($type == 2) {
            mysql_query("update $DataIn.ck1_rksheet set TaskId = '$TaskId' where StuffId = '$StuffId' and StockId = '$StockId'");//包装编号
        }
    }

    if ($type == 1 ) $Qty = $zQty;

}
?>
<div style="background:#fff;">
    <!--startprint-->
    <div id="printdiv" style="width: 400px;height:200px;padding: 16px; font-family:SimHei;font-size:14px;font-weight:bold;text-align:left;">
        <div style="margin-top: 16px;margin-bottom: 20px;">名　　称:<?php echo $StuffCname ?></div>
        <div>物料编码:<?php echo $StuffEname ?></div>
        <div style="position: relative;">
            <div style="margin: 20px 0;">客　　户:<?php echo $Forshort ?></div>
            <div>数　　量:<?php echo $Qty ?> </div>
            <div style="margin: 20px 0">采购备注:<?php echo $cgRemark ?></div>
            <div style="position: absolute;right:10px;top:0;text-align:center;">
                <img alt="" src="../plugins/barcodegen39/barcode39.php?text=<?php echo $TaskId ?>" style="width:180px;height:106px;"/>
            </div>
        </div>
    </div>
    <!--endprint-->
    <div style="padding: 15px;">
        <input type="button" value="取消" onclick="closeMaskDiv()" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="打印" onclick="toPrint()"/>
    </div>
</div>
