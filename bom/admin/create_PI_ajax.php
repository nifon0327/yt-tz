<?php
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;  //产品属性|70|
$IdsArr = explode('|',$Ids);
foreach ($IdsArr as $id) {
    $PINO = date('Ym',time());
    $PIcreatetime = date('Y-m-d',time());
    $sql = "select PINO from ch1_shipmain where id = $id limit 1";
    $res = mysql_query($sql,$link_id);
    $ret = mysql_fetch_assoc($res);
//    if($ret['PINO']!=null){
//        continue;
//    }
    $sql = "update ch1_shipmain set PINO = $PINO,PIcreator='$Login_Name',PIcreatetime='$PIcreatetime' where id = $id";
    $res = mysql_query($sql,$link_id);
}

