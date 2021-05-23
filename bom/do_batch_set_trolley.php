<?php
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
include "../basic/chksession.php";

$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$DateTime=date("Y-m-d H:i:s");
// 获取相关请求头
$tempTrolley = $_REQUEST['tempTrolley'];

// 处理相关数据
function trimAll($str)
{
    $reg = array(" ", "　", "\t", "\n", "\r");

    return str_replace($reg, '|', $str);
}

$tempTrolley = array_values(array_filter(explode('|', trimAll($tempTrolley))));
$num = 0;
$parts = $part = [];
foreach ($tempTrolley as $k => $v) {
    if($k % 2 == 0){
        $part[0] = strip_tags($v);
    }
    else if ($k % 2 == 1) {
        $part[1] = preg_replace("/(，)/" ,',' ,strip_tags($v));
        $parts[$num] = $part;
        $num++;
    }
}

// 根据构件名称设置时间
$log = '';
foreach ($parts as $value) {
    $sql = 'update bom_mould set TrolleyId = "'.$value[1].'",Modifier = "'.$Operator.'",Modified = "'.$DateTime.'" where mouldno = "'. $value[0].'"';
    mysql_query($sql);
}

