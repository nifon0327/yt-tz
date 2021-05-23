<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$OperationResult = "N";
$Log_Funtion = "变更生产单位";

$sPOrderIdsArr = explode("|", $sPOrderIds);

for ($i = 0; $i < count($sPOrderIdsArr); $i++) {
    $sPOrderId = $sPOrderIdsArr[$i];

    //$UpdateSql="UPDATE $DataIn.yw1_scsheet SET WorkShopId='$changeWorkShopId'  WHERE sPOrderId=$sPOrderId";
    $UpdateSql = "UPDATE $DataIn.yw1_scsheet SET WorkShopId='$changeWorkShopId'  WHERE POrderId=$sPOrderId";
    $UpdateResult = @mysql_query($UpdateSql);
    if ($UpdateResult && mysql_affected_rows() > 0) {
        $OperationResult = "Y";
    }
}

//步骤4：
$IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res = @mysql_query($IN_recode);
if ($_REQUEST['fromPage'] == '5_3') {
    if ($OperationResult == 'Y') {
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

}
else {
    echo $OperationResult;
}


?>