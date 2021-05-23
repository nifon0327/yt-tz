<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$Log_Item = "生产入库确认";
$DateTime = date("Y-m-d H:i:s");

$OperationResult = "Y";
$Log_Funtion = "生产入库确认";
$Operator = $Login_P_Number;

$POrderIdArr = explode("|", $POrderIds);
$StockIdArr = explode("|", $StockIds);
$chooseDateArr = explode("|", $chooseDates);

for ($i = 0; $i < count($POrderIdArr) && $i < count($StockIdArr) && $i < count($chooseDateArr); $i++) {
    $POrderId = $POrderIdArr[$i];
    $StockId = $StockIdArr[$i];
    $chooseDate = $chooseDateArr[$i];

    switch ($level) {
        case "0": //成品质检审核
            $UpdateSql = "UPDATE $DataIn.sc1_cjtj SET Estate='2'
            WHERE POrderId=$POrderId AND StockId = $StockId AND (Estate =3 or Estate = 1) AND DATE_FORMAT(Date,'%Y-%m-%d') = '$chooseDate'";
            $UpdateResult = @mysql_query($UpdateSql);
            if ($UpdateResult && mysql_affected_rows() > 0) {

                $OperationResult = "Y";
                $Log .= "<div class=greenB>" . $POrderId . "成品质检审核成功(结果为合格)!</div><br>";

            } else {
                $OperationResult = "N";
                $Log .= "<div class=redB>" . $POrderId . "成品质检审核失败!</div><br>";
            }
            break;

        case "1": //成品入库

            $StackNo = strtoupper($stackNO);

            $UpdateSql="UPDATE $DataIn.sc1_cjtj SET Estate='0'
            WHERE POrderId=$POrderId AND StockId = $StockId AND Estate =2 AND DATE_FORMAT(Date,'%Y-%m-%d') = '$chooseDate'";
            $UpdateResult=@mysql_query($UpdateSql);
            if($UpdateResult && mysql_affected_rows()>0) {
                $PutawayDate = date("Y-m-d H:i:s");
                $UpdateSeatId = mysql_query("update $DataIn.yw1_ordersheet set SeatId='$SeatId', StorageNO = '$storageNO', StackId = '$stackNO', PutawayDate = '$PutawayDate' WHERE POrderId='$POrderId' AND Estate > 0");
                $UpdateCH = mysql_query("update $DataIn.ch1_shipsplit set Estate= 1 WHERE POrderId='$POrderId' AND Estate > 0");
                $OperationResult = "Y";
                $Log .= "<div class=greenB>" . $POrderId . "成品入库确认成功!</div><br>";

            } else {
                $OperationResult = "N";
                $Log .= "<div class=redB>" . $POrderId . "成品入库确认失败!请查看相关状态。</div><br>";
            }
            break;

        case "2": //半成品入库确认，不需插入入库数据
            $UpdateSql = "UPDATE $DataIn.sc1_cjtj SET Estate='2'
            WHERE POrderId=$POrderId AND StockId = $StockId AND Estate =1 AND DATE_FORMAT(Date,'%Y-%m-%d') = '$chooseDate'";
            $UpdateResult = @mysql_query($UpdateSql);
            if ($UpdateResult && mysql_affected_rows() > 0) {
                $OperationResult = "Y";

                $Log .= "<div class=greenB>" . $POrderId . "成品入库确认成功!</div><br>";
            } else {
                $OperationResult = "N";
                $Log .= "<div class=redB>" . $POrderId . "成品入库确认失败!</div><br>";
            }
            break;

        case '3': // 成品质检不合格
            $UpdateSql = "UPDATE $DataIn.sc1_cjtj SET Estate='3'
            WHERE POrderId=$POrderId AND StockId = $StockId AND (Estate =2 or Estate = 1) AND DATE_FORMAT(Date,'%Y-%m-%d') = '$chooseDate'";
            $UpdateResult = @mysql_query($UpdateSql);
            if ($UpdateResult && mysql_affected_rows() > 0) {

                $OperationResult = "Y";
                $Log .= "<div class=greenB>" . $POrderId . "成品质检审核成功!(结果为不合格)</div><br>";

            } else {
                $OperationResult = "N";
                $Log .= "<div class=redB>" . $POrderId . "成品质检审核失败!</div><br>";
            }
            break;
    }
}
//步骤4：
$IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res = @mysql_query($IN_recode);
echo $OperationResult;
?>