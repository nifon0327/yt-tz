<?php
include "../model/modelhead.php";
$DateTime = date("Y-m-d H:i:s");
$Date = date("Y-m-d");
$POrder = date("Ymd") . '9001';
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
    $Id = strip_tags($z[0]);                //顺序号
    $trolleyId = strip_tags($z[1]);         //台车号
    $sql = 'update ' . $DataIn . '.bom_mould set TrolleyId = "' . $trolleyId . '" where id = ' . $Id;
    mysql_query($sql);
}
