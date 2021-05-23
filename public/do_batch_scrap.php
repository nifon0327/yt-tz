<?php
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
include "../basic/chksession.php";

$Date=date("Y-m-d");
$ProposerId = $Operator = $Login_P_Number;
$DateTime=date("Y-m-d H:i:s");
// 获取相关请求头
$tempScrap = $_REQUEST['scrap'];

// 处理相关数据
function trimAll($str)
{
    $reg = array(" ", "　", "\t", "\n", "\r");

    return str_replace($reg, ',', $str);
}

$tempScrap = array_values(array_filter(explode(',', trimAll($tempScrap))));
$num = 0;
$parts = $part = [];
foreach ($tempScrap as $k => $v) {
    if($k % 3 == 0){
        $part[0] = intval($v);
    }
    else if ($k % 3 == 2) {
        $part[1] = intval($v);
        $parts[$num] = $part;
        $num++;
    }
}

// 根据构件id设置出库
$log = '';
foreach ($parts as $value) {
    $Qty = $value[1];
    $StuffId = $value[0];
    $myOrderResult = $myPDO->query("INSERT INTO $DataIn.ck8_bfsheet  SELECT NULL,'$ProposerId',StuffId,'$Qty','','$Remark','4','0','','','2','$Date','1','0','$Operator','$DateTime',0,'$Operator','$DateTime','$Operator','$DateTime'
FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' and (oStockQty-mStockQty)>=$Qty and tStockQty>=$Qty");


}


?>