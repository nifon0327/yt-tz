<?php
define('IN_COMMON', true);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
define('SITE_PATH', substr(dirname(__FILE__), 0, -6)); // 末尾没有“/”




// 连接服务器参数
$_G['hosts'] = array(
	// 正式环境
	'139.196.94.36' => 'production',

	// 测试环境
	'139.196.94.36:81' => 'testing',

	// 开发环境
	'127.0.0.1:8043' => 'development',
);

if (isset($_G['hosts'][$_SERVER['HTTP_HOST']])) {
	$curEnv = $_G['hosts'][$_SERVER['HTTP_HOST']];
}

if (!isset($curEnv)) {
	die('env config error');
}

define('CUR_ENV', $curEnv);

switch (CUR_ENV) {
	case 'production':
		$DataPublic = "ac_original";
		$DataIn = "ac_original";
		$host = "127.0.0.1";
		$user = "user_yantong";
		$pass = "user*#!yantongzhizhu";
		$db = "mysql";
		break;
	case 'testing':
		$DataPublic = "ac_original";
		$DataIn = "ac_original";
		$host = "127.0.0.1";
		$user = "user_yantong";
		$pass = "user*#!yantongzhizhu";
		$db = "mysql";
		break;
	case 'development':
		$DataPublic = "ac_original";
		$DataIn = "ac_original";
		$host = "127.0.0.1";
		$user = "root";
		$pass = "123456";
		$db = "mysql";
		break;
}

