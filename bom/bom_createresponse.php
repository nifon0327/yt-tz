<?php
include_once '../basic/chksession.php';
include_once '../basic/parameter.inc';
// 获取相关请求头
$proId = addslashes($_POST['proId']);
$BuildNo = explode('-',$_POST['BuildNo'])[0];
$BuildNo1 = $_POST['BuildNo'];
$OrderPO = $_POST['OrderPO'];
$operation = $Login_P_Number;

// 获取总层数
$top = 0;
$result = mysql_query("select max(cast(FloorNo as signed)) as top 
                              from $DataIn.trade_drawing 
                              where BuildingNo = '$BuildNo' and TradeId = $proId"
);
if ($result) {
    $myRow = mysql_fetch_array($result);
    $top = $myRow['top'];
}


// 获取当前层数
$current = 0;
$OrderPO = $OrderPO.'-'.$BuildNo1;
$result = mysql_query(
    "select substring_index(OrderPO,'-',-1) as current 
            from $DataIn.yw1_ordermain 
            where BuildNo = '$BuildNo' and OrderPO = '$OrderPO'
            order by id desc 
            limit 1;"
);
if ($result) {
    $myRow = mysql_fetch_array($result);
    $current = $myRow['current'];
}

// 获取数据库插入结果
$msg = '';
$result = mysql_query(
    "select OperationResult,log from $DataIn.oprationlog 
            where Operator = $operation 
            and Item = '新增订单' 
            order by id desc 
            limit 1"
);
$myRow = mysql_fetch_array($result);
$rlt = $myRow['OperationResult'];
$tempMsg = explode('<br>', $myRow['log']);
$num = count($tempMsg) - 2;
$msg = $tempMsg[$num];

// 根据结果返回相关信息
if ($top == 0 || $current == 0) {
    echo json_encode(
        array(
            'rlt' => false,
            'msg' => '没有' . $BuildNo . '栋的相关层数数据！',
        )
    );

    return;
}

if ($rlt === 'Y') {
    echo json_encode(
        array(
            'rlt' => true,
            'msg' => '楼栋编号: ' . $BuildNo . '栋。已完成' . $current . '层，共' . $top . '层。',
            'top' => $top,
            'cur' => $current,
        )
    );

    return;
}
else {
    echo json_encode(
        array(
            'rlt' => false,
            'msg' => $msg,
        )
    );

    return;
}
?>