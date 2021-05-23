<?php
/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2019/2/25
 * Time: 15:54
 */
ini_set('display_errors',1);
error_reporting(E_ALL);
include( 'ch_shippinglist_account_ws_class.php');
include('../model/soap/SoapDiscovery2.class.php');
//$disco = new SoapDiscovery();
//$x =  $disco->getWSDL('test', 'http://'.$_SERVER['HTTP_HOST'].'/admin/ch_shippinglist_account_ws_server.php');
//header("Content-type:text/xml;charset=UTF-8");
//echo $x;


//$reflection = new ReflectionClass('test');
//foreach ($reflection->getMethods() as $method) {
//
//    if ($method->isPublic())
//        var_dump($method);
//}

header("Content-type:text/xml;charset=UTF-8");
try {

    $disc=new SoapDiscovery('AccountWebservice','account','http://'.$_SERVER['HTTP_HOST'].'/admin/ch_shippinglist_account_ws_server.php');

    echo $disc->getWSDL();
} catch (Exception $e) {
    echo $e->getMessage();
}
