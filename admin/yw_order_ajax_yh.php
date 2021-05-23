<?php
// xfy 2018-6-20
include "../basic/parameter.inc";

// 获取数据
$Id = $_REQUEST['Ids'];
$yhdata = $_REQUEST['date'];

$mysql = "UPDATE $DataIn.yw1_ordersheet SET delivery='$yhdata' WHERE Id IN ($Id)";
$upVolRes = mysql_query($mysql, $link_id);
if ($upVolRes && mysql_affected_rows() > 0) {
    echo json_encode(
        array(
            'rlt' => true
        )
    );
} else {
    echo json_encode(
        array(
            'rlt' => false
        )
    );
}
return;