<?php
    include_once "../../basic/parameter.inc";

    $shipId = $_POST['shipId'];
    $values = $_POST['values'];

    // $shipId = '11300';
    // $values = '201412240705,0|201412240708,0|201501171217,0|201501171207,0|201501171216,0|201412221201,0|201412221203,0|201501171205,0';

    $insertContent = array();
    $PorderIdArray = array();
    $values = explode('|', $values);
    for($i=0; $i< count($values); $i++){
        $tmpValues = explode(',', $values[$i]);
        $PorderId = $tmpValues[0];
        $qty = $tmpValues[1];

        $PorderIdArray[] = $PorderId;
        $insertContent[] = "(NULL, $shipId, $PorderId, $qty)";
    }

    $insertInfomation = implode(',', $insertContent);
    $PorderIds = implode(',', $PorderIdArray);
    $insertContentQty = "Insert Into $DataIn.printsync (Id, ShipId, POrderId, printQty) Values $insertInfomation";

    mysql_query("BEGIN");
    //先删除之前的数据
    $deleteQtySql = "DELETE From $DataIn.printsync Where ShipId = $shipId and POrderId in ($PorderIds)";
    if(mysql_query($deleteQtySql) && mysql_query($insertContentQty)){
        mysql_query("COMMIT");//执行事务
        $result = 'Y';
    }else{
        mysql_query("ROOLBACK");//判断执行失败回滚
        $result = 'N';
    }

    echo json_encode(array($result));

?>