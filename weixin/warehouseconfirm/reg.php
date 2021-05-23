<?php

session_start();

error_reporting(E_ALL & ~E_NOTICE);

include_once('../configure.php');

include_once('../log.php');

$Log_Item = "入库记录";            //需处理

$Log_Funtion = "保存";

$DateTime = date("Y-m-d H:i:s");

$Date = date("Y-m-d");

$Operator = $_SESSION['user_id'];

$OperationResult = "Y";

$POrderId = $_POST['POrderId'];

$sPOrderId = $_POST['sPOrderId'];

$StockId = $_POST['StockId'];

$Estate = $_POST['Estate'];

$storageNO = $_POST['storageNO'];

$StackId = $_POST['StackId'];

$SeatId = $_POST['Seat'];

$DateTemp = date("Ymd");

$Tempyear = date("Y");
if ($Estate == 1 && $SeatId && $storageNO) {
    $inRecode = "UPDATE $DataIn.sc1_cjtj SET Estate='0'
            WHERE POrderId=$POrderId AND StockId = $StockId AND Estate =2";
}
$inAction = @mysql_query($inRecode);

if ($inAction && mysql_affected_rows()>0) {
$UpdateSeatIdSql = "update $DataIn.yw1_ordersheet set SeatId='$SeatId', StorageNO = '$storageNO',StackId = '$StackId' ,PutawayDate = '$DateTime' WHERE POrderId='$POrderId' AND Estate > 0";
    $UpdateSeatId = mysql_query($UpdateSeatIdSql);
//    $UpdateCH = mysql_query("update $DataIn.ch1_shipsplit set Estate= 1 WHERE POrderId='$POrderId' AND Estate > 0");
    if ($UpdateSeatId && mysql_affected_rows() > 0 ) {

        $UpdateCH = mysql_query("update $DataIn.ch1_shipsplit set Estate= 1 WHERE POrderId='$POrderId' AND Estate > 0");
        if (mysql_affected_rows() > 0 && $UpdateCH) {
            $Log = "$TitleSTR 成功!<br>";

            $result = 1;

            $title = urlencode("入库登记成功");
        }else{
            mysql_query("UPDATE $DataIn.sc1_cjtj SET Estate='2'
            WHERE POrderId='$POrderId' AND StockId = '$StockId' AND Estate =0");

            $Log = $Log . "<div class=redB>$TitleSTR.$inRecode 失败!</div><br>";

            $result = 0;

            $OperationResult = "N";

            $title = urlencode("入库登记状态更新失败！");
        }

    }else{
        mysql_query("UPDATE $DataIn.sc1_cjtj SET Estate='2'
            WHERE POrderId='$POrderId' AND StockId = '$StockId' AND Estate =0");
        $Log = $Log . "<div class=redB>$TitleSTR.$UpdateSeatIdSql 失败!</div><br>";

        $result = 0;

        $OperationResult = "N";

        $title = urlencode("入库登记库位失败！");
    }
}
else {

    $Log = $Log . "<div class=redB>$TitleSTR.$inRecode 失败!</div><br>";

    $result = 0;

    $OperationResult = "N";

    $title = urlencode("入库登记失败！");

}

header("Location:msg.php?result=$result&title=$title&msg=$msg");

?>
