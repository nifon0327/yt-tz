<?php
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

/*$mySql="SELECT D.StuffCname, D.StuffEname, S.Qty, Y.cgRemark, C.Forshort
FROM $DataIn.ck1_rksheet S
LEFT JOIN $DataIn.ck1_rkmain M ON S.Mid=M.Id
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
LEFT JOIN $DataIn.productdata CP ON CP.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=CP.CompanyId
WHERE S.id = $Id";*/

$mySql="SELECT O.Forshort,Y.POrderId,Y.OrderPO,Y.Qty AS OrderQty,SUM(S.Qty) AS Qty,S.StockId,
    P.ProductId,P.cName,P.eCode,P.TestStandard,P.pRemark,
    U.Name AS Unit,
    PI.Leadtime,PI.Leadweek
	FROM $DataIn.sc1_cjtj  S 
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId 
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=Y.POrderId
	WHERE S.Estate=1  AND G.Level = 1 AND Y.POrderId = $POrderId";


$result = mysql_query($mySql);

if($myRow = mysql_fetch_array($result)){
    $Id=$myRow["Id"];
    $Forshort=$myRow['Forshort'];
    $POrderId=$myRow["POrderId"];
    $ProductId=$myRow["ProductId"];
    $OrderPO=toSpace($myRow["OrderPO"]);
    $cName=$myRow["cName"];
    $eCode=toSpace($myRow["eCode"]);
    $StockId =$myRow["StockId"];
    $Qty=$myRow["Qty"];

}

$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'QRCode'.DIRECTORY_SEPARATOR;
$PNG_WEB_DIR = 'QRCode/';
include "./phpqrcode/phpqrcode.php";
if (!file_exists($PNG_TEMP_DIR))
    mkdir($PNG_TEMP_DIR);

$errorCorrectionLevel = 'L';
$matrixPointSize = 6;
$filename = $PNG_TEMP_DIR.$POrderId.-date(YmdH).".png";
if (!file_exists($filename)) {
    QRcode::png($cName, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
}

?>
<div style="background:#fff;">
<!--startprint-->
    <div id="printdiv" style="width: 400px;height:200px;padding: 20px;font-family:SimHei;font-size:14px;font-weight:bold;text-align:left;">
        <div style="margin-top: 16px;margin-bottom: 25px;">客　　户：<?php echo $Forshort ?></div>
        <div style="position: relative;">
            <div>Ｐ　　Ｏ：<?php echo $OrderPO ?></div>
            <div style="margin: 25px 0;">数　　量：<?php echo $Qty ?></div>
            <div>中 文 名：<?php echo $cName ?></div>

        </div>
        <div style="position: absolute;right:25px;top:30px;text-align:center;">
            <?php echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />' ?>
        </div>
    </div>
<!--endprint-->
    <div style="padding: 15px;">
    	<input type="button" value="取消" onclick="closeMaskDiv()" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="打印" onclick="toPrint()"/>
    </div>
</div>
