<?php
include_once '../basic/chksession.php';
include_once '../basic/parameter.inc';
// 获取相关请求头
$proId = addslashes($_POST['proId']);
$operation = $Login_P_Number;

// 获取bom初始化结果
$isBomInit = '';
$result = mysql_query("select isBomInit from $DataIn.bom_object where TradeId = $proId", $link_id);
if ($result) {
    $myRow = mysql_fetch_array($result);
    $isBomInit = $myRow['isBomInit'];
}

// 1为成功，0为失败
if ($isBomInit == 1) {
    echo json_encode(
        array(
            'rlt' => true,
        )
    );

    return;
}
else {
    echo json_encode(
        array(
            'rlt' => false,
        )
    );

    return;

}
?>