<?php
header('Content-Type:application/json;charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
session_start();
include '../config/dbconnect.php';
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

try{
    $db = new DbConnect();
    $action = $_REQUEST['action'];
    $db->$action($_REQUEST);
}catch(Exception $e){
    ret( 0 , $e->getMessage() );
}
//返回数据
function ret( $status , $msg = '成功' , $result = null ){
    $ret = array(
        'status' => $status,
		'msg' => $msg,
        'result' => $result,
    );
	$json = json_encode($ret);
	//debug($json);
	echo $json;
}
//调试日志
function debug($str){
	$str = date("Y-m-d H:i:s") . " : " . $str . "\r\n";
	$file = fopen('../log/log.txt','a');
	fwrite($file,$str);
	fclose($file);
}