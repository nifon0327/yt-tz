<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";


$OperationResult = "Y";
$Log_Funtion = "生产入库确认";
$Operator = $Login_P_Number;

$IdArr = explode("|", $Ids);

for ($i = 0; $i < count($IdArr); $i++) {
    $Id = $IdArr[$i];

    //成品入库
    $UpdateSql = "UPDATE yw1_ordersheet Y LEFT JOIN ch1_shipsplit C ON C.POrderId=Y.POrderId SET Y.SeatId = '$SeatId' WHERE C.Id = $Id";
    $UpdateResult = @mysql_query($UpdateSql,$link_id);
    if ($UpdateResult && mysql_affected_rows() > 0) {
        $OperationResult = "Y";
        $Log .= "<div class=greenB>修改库位成功!</div><br>";
    }
    else {
        $OperationResult = "N";
        $Log .= "<div class=redB>修改库位失败!</div><br>";
    }
}

echo $OperationResult;
?>