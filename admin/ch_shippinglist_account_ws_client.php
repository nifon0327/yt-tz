<?php
/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2019/2/25
 * Time: 17:39
 */
ini_set('display_errors',1);
error_reporting(E_ALL);
//$soap = new SoapClient('http://test.matechstone.com/admin/ch_shippinglist_account_ws_server.php?wsdl', array('location'=>'http://test.matechstone.com/admin/ch_shippinglist_account_ws_server.php','uri' =>'http://soap/'));
//$soap = new SoapClient('http://test.matechstone.com/admin/ch_shippinglist_account_ws_server.php?wsdl');
//$soap = new SoapClient(null, array(
//    'location'=>'http://test.matechstone.com/admin/ch_shippinglist_account_ws_server.php',
//    'uri'=>'http://soap/'
//));

//$soap = new SoapClient(null, array(
//    'location'=>'http://test.matechstone.com/admin/ch_shippinglist_account_ws_server.php',
//    'uri'=>'http://soap/'
//));

//wsdl
ini_set('soap.wsdl_cache_enabled',0);
$soap = new SoapClient('http://test.matechstone.com/admin/ch_shippinglist_account_ws_server.php?wsdl', array(
    'location'=>'http://test.matechstone.com/admin/ch_shippinglist_account_ws_server.php',
    'uri'=>'http://soap/'
));

//$soap = new SoapClient('http://test.matechstone.com/admin/ch_shippinglist_account_ws_server.php?wsdl');
//echo $soap->show();

//echo $soap->setAuditStatus('1','1');



include( 'ch_shippinglist_account_ws_class.php');
//echo "delete:";
$ws = new AccountWebservice();
//echo $ws->callDeleteAccount("2");
echo $ws->callSendAccount(18);

//echo $ws->setAuditStatus('1','1');
