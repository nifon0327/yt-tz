<?php
//$MyPDOEnabled = 1;
session_start();
include "../basic/parameter.inc";
header("Content-Type: application/json; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");


$DateTime = date("Y-m-d H:i:s");
$Date = date("Y-m-d");
$Operator = $Login_P_Number;

switch ($ActionId){
    case "upVol":
        $upVolSql="UPDATE $DataIn.ch1_shipsplit SET volume='$volume' WHERE Id='$Id'";
        //echo $upVolSql;
        $upVolRes=mysql_query($upVolSql);
        if($upVolRes && mysql_affected_rows()>0){
            $json['status'] = 'Y';
        }else{
            $json['status'] = 'N';
        }
        break;
}
echo json_encode($json,JSON_UNESCAPED_UNICODE);