<?php
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$CompanyId = $_GET['CompanyId'];
$id = $_GET['id'];

$mySql = "SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,
          S.Id,S.POrderId,S.ProductId, S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,
          P.cName,P.eCode, P.Weight,P.MainWeight,P.TestStandard,P.pRemark,P.bjRemark,
          U.Name AS Unit,PI.PI,PI.Leadtime,S.dcRemark,PI.Remark AS PIRemark,X.name as taxName,C.Forshort 
          FROM $DataIn.yw1_ordermain M 
          INNER JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
          INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
          INNER JOIN $DataIn.packingunit U ON U.Id=P.PackingUnit 
          INNER JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
          LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
          LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId 
          LEFT JOIN $DataIn.clientsub B ON B.Id=M.SubClientId 
          LEFT JOIN $DataIn.yw7_clientOutData O ON O.POrderId=S.POrderId AND O.Sign=1 
          LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId 
          WHERE 1 and S.Estate>0 and M.CompanyId=$CompanyId AND S.Id=$id 
          ORDER BY M.CompanyId,M.OrderDate ASC,M.Id DESC ";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)) {
    $d = anmaIn("download/productfile/", $SinkOrder, $motherSTR);
    $dirforstuff = anmaIn("download/stufffile/", $SinkOrder, $motherSTR);

    $Id = $myRow["Id"];
    $POrderId = $myRow["POrderId"];
    $OrderPO = $myRow["OrderPO"];
    $Client = $myRow["Forshort"];
    $cName = $myRow["cName"];
    $eCode = $myRow["eCode"] == "" ? "&nbsp;" : $myRow["eCode"];
    $Code = $myRow["Code"] == "" ? "&nbsp;" : $myRow["Code"];
    $TestStandard = $myRow["TestStandard"];
    include "../admin/Productimage/getProductImage.php";
//include "../admin/subprogram/product_teststandard.php";
    $TypeName = $myRow["TypeName"];
    $OrderPo = $myRow["OrderPo"];
}
$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'QRCode'.DIRECTORY_SEPARATOR;
$PNG_WEB_DIR = 'QRCode/';
include "../cjgl/phpqrcode/phpqrcode.php";
if (!file_exists($PNG_TEMP_DIR)){
    mkdir($PNG_TEMP_DIR,0777);
}
$errorCorrectionLevel = 'L';
$matrixPointSize = 6;
$filename = $PNG_TEMP_DIR.$POrderId.".png";
// if (!file_exists($filename)) {
//     QRcode::png($cName, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
// }
QRcode::png($cName, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
?>
<div style="background:#fff;">
<!--startprint-->
    <div id="printdiv" style="width: 400px;height:200px;padding: 20px;font-family:SimHei;font-size:14px;font-weight:bold;text-align:left;">
        <div style="margin-top: 16px;margin-bottom: 25px;">客　　户：<?php echo $Client ?></div>
        <div style="position: relative;">
            <div>Ｐ　　Ｏ：<?php echo $OrderPO ?></div>
            <div style="margin: 25px 0;">中 文 名：<?php echo $cName ?></div>
            <div>Product Code：<?php echo $eCode ?></div>

        </div>
        <div style="position: absolute;right:25px;top:30px;text-align:center;">
            <?php echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />' ?>
        </div>
    </div>
<!--endprint-->
    <div style="text-align: center">
    	<input type="button" value="取消" onclick="window.close();" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="打印" onclick="toPrint()"/>
    </div>
</div>
<script>
    function toPrint() {
        var oldstr = document.body.innerHTML;
        var bdhtml = window.document.body.innerHTML;
        var sprnstr = "<!--startprint-->";
        var eprnstr = "<!--endprint-->";
        var headstr = "<html><head><title></title></head><body>";  //打印头部
        var footstr = "</body></html>";  //打印尾部
        var prnhtml = bdhtml.substr(bdhtml.indexOf(sprnstr) + 17);
        prnhtml = prnhtml.substring(0, prnhtml.indexOf(eprnstr));
        window.document.body.innerHTML = headstr + prnhtml + footstr;
        window.print();
        document.body.innerHTML = oldstr;
        return false;
    }
</script>
