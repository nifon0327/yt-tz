<?php

session_start();

error_reporting(E_ALL & ~E_NOTICE);

include_once('../configure.php');

include_once('../log.php');

$Log_Item = "生产记录";            //需处理

$Log_Funtion = "保存";

$DateTime = date("Y-m-d H:i:s");

$Date = date("Y-m-d");

$Operator = $_SESSION['user_id'];

$OperationResult = "Y";

$POrderId = $_POST['POrderId'];

$sPOrderId = $_POST['sPOrderId'];

$StockId = $_POST['StockId'];

$Estate = $_POST['Estate'];

$DateTemp = date("Ymd");

$Tempyear = date("Y");
if ($Estate == 1) {
    $inRecode = "UPDATE $DataIn.sc1_cjtj SET Estate='2'
            WHERE POrderId=$POrderId AND StockId = $StockId AND (Estate =1 or Estate = 3)";
}elseif($Estate == 2){
    $inRecode = "UPDATE $DataIn.sc1_cjtj SET Estate='3'
            WHERE POrderId=$POrderId AND StockId = $StockId AND (Estate =2 or Estate = 1)";
}
$inAction = @mysql_query($inRecode);

if ($inAction && mysql_affected_rows()>0) {

    $Log = "$TitleSTR 成功!<br>";

    $result = 1;

    $title = urlencode("质检登记成功");
}
else {

    $Log = $Log . "<div class=redB>$TitleSTR.$inRecode 失败!</div><br>";

    $result = 0;

    $OperationResult = "N";

    $title = urlencode("质检登记结果相同，无需重复登记！");

}

header("Location:msg.php?result=$result&title=$title&msg=$msg");

?>
