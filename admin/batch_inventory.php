<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
include "../model/modelhead.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
ini_set('date.timezone','PRC');
$OperationResult = "Y";
$Operator = $Login_Name;
$date = date('Y-m-d H:i:s',time());
$IdArr = explode("|", $Ids);

for ($i = 0; $i < count($IdArr); $i++) {
    $Id = $IdArr[$i];

    //成品入库
    $UpdateSql = "UPDATE ch1_shipsplit SET inventory_estate = 1,inventory_checker='$Operator',inventory_time='$date' WHERE id = $Id ";
    $UpdateResult = @mysql_query($UpdateSql);
}

