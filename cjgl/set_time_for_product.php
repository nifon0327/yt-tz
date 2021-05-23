<?php
// xfy 2018-6-20
include "../basic/parameter.inc";

// 获取数据
$date = $_REQUEST['date'];
$POrderId = $_REQUEST['POrderId'];

$POrderId = explode('|', $POrderId);
foreach ($POrderId as $v) {
    $mysql = "update $DataIn.yw1_scsheet set scDate = '$date' where porderid = $v";
    $res = mysql_query($mysql,$link_id);
    if ($res == false) {
        echo json_encode(array(
            'rlt' => false
                         ));
        return;
    }
}
echo json_encode(
    array(
        'rlt' => true
    )
);
return;