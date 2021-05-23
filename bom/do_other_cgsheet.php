<?php
$MyPDOEnabled = 1;
include "../model/modelhead.php";
$DateTime = date("Y-m-d H:i:s");
$Date = date("Y-m-d");
$POrder = date("Ymd").'9001';
$Operator = $Login_P_Number;
$OperationResult = "Y";

//步骤3：需处理
$DateTemp = date("Ymd");
$value = explode('^^', $value);

//锁定表
include "../model/subprogram/FireFox_Safari_PassVar.php";

$POrderId = strlen($POrderId) >= 12 ? $POrderId : '';

$oldLevel = $oldLevel == '' ? 1 : $oldLevel;

foreach ($value as $v) {
    $z = explode('|', $v);
    $newStuffId = strip_tags($z[0]);            //配件ID
    $newFactualQty = strip_tags($z[1]);        //采购数量
    $newPrice = strip_tags($z[2]);        //采购价格
    $TradeId = strip_tags($z[3]);        //项目编号
    $sql = 'select CompanyId from ' . $DataIn . '.trade_object where Id = ' . $TradeId;
    $res = $myPDO->query($sql);
    foreach ($res as $row) {
        $TradeNo = $row['CompanyId'];
    }
    $newAddRemark = '原因：特采';
    if ($newFactualQty > 0) {
        $tempStr = "$newPrice|$newAddRemark";
        $myResult = $myPDO->query("CALL proc_cg1_stocksheet_add('$POrderId',$newStuffId,$newFactualQty,'$tempStr','$oldLevel',$Operator);");
        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
        $OperationResult = $myRow['OperationResult'] != "Y" ? $myRow['OperationResult'] : $OperationResult;
        if (trim($OperationResult) == 'Y') {
            $sql = "INSERT INTO yw1_ordersheet ( `OrderNumber`, `POrderId`, `OrderPO`, `ProductId`, `Qty`, `Price`, `PackRemark`, `cgRemark`, `sgRemark`, `dcRemark`,`ShipType`, `creator`, `created`,`Date` ) VALUES ( ( SELECT max(`OrderNumber`) + 1 FROM yw1_ordermain ), (select POrderId from (select IFNULL(max(POrderId)+1,$POrder) as POrderId from yw1_ordersheet where Date = '$Date') as POrderId), '-', '0', '$newFactualQty', '$newPrice', '-', '-', '-','-','-', $Operator, '$DateTime','$Date' )";
            $link_id = mysql_connect($host, $user, $pass);
            mysql_query("SET NAMES 'utf8'");
            mysql_select_db($DataIn, $link_id) or die("无法选择数据库!");
            mysql_query($sql);
            $sql = "insert into yw1_ordermain (`CompanyId`,`OrderNumber`,`OrderDate`,`Operator`,`Estate`,`OrderPO`) VALUES ($TradeNo,(select max(`OrderNumber`) from yw1_ordersheet) ,'$Date',$Operator,1,'')";
            $link_id = mysql_connect($host, $user, $pass);
            mysql_query("SET NAMES 'utf8'");
            mysql_select_db($DataIn, $link_id) or die("无法选择数据库!");
            mysql_query($sql);
        }
        $Log .= $OperationResult == "Y" ? $myRow['OperationLog'] : "<div class=redB>" . $myRow['OperationLog'] . "</div>";
        $Log .= "</br>";

        $myResult = null;
        $myRow = null;
    }

}
include "../model/logpage.php";
?>
