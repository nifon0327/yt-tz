<?php
/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2019/2/25
 * Time: 15:31
 */
//ini_set('display_errors',1);
//error_reporting(E_ALL);
include( 'ch_shippinglist_account_ws_class.php');
//实例化的参数手册上面有，这个是没有使用wsdl的，所以第一个参数为null，如果有使用wsdl，那么第一个参数就是这个wsdl文件的地址。
//$server = new SoapServer(null, array('uri' =>'http://soap/','location'=>'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']));
ini_set('soap.wsdl_cache_enabled',0);
$server=new SoapServer('http://test.matechstone.com/admin/ch_shippinglist_account_ws_wsdl.php',array('uri' =>'http://soap/','soap_version'=> SOAP_1_2));
//无wsdl
//$server=new SoapServer(null,array('uri' =>'http://soap/'));
//echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$server->setClass('AccountWebservice');
//$server->addFunction('getUserInfo');
$server->handle();
