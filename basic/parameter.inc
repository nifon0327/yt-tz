<?php
defined('IN_COMMON') || include 'common.php';

//配置文件
define("mLOCK", 16);
define("mDELETE", 8);
define("mUPDATE", 4);
define("mADD", 2);
define("mREAD", 1);
define('MEM_CACHE_HOST', '192.168.1.250');//by Aitch.Zung
$onClickCSS = "style='CURSOR: pointer;color:#333333'";//单击事件的颜色
$theDefaultColor = "#FFFFFF";                        //白色：行非选定色
$thePointerColor = "#FFE9D2";                        //浅黄：行鼠标移过色
$theMarkColor = "#FFCC99";                        //泥黄：行选定色

$OutDomainNameStr = "www.ashcloud.com";
$DomainCerKey = 'ZDCE';

$CompanyShortName = "泰州研砼";
$CompanyNameStr = "泰州砼筑建筑科技有限供公司";

$execImageMagick = "D:\webserver\ImageMagick-6.7.0-Q16\convert.exe";//图片格式转换调用外部程序

$factoryCheck = date('Y-m-d') == '2016-08-25' ? 'on' : 'off';//验厂模式参数

//连接服务器参数
$DataPublic = $DataIn = "ac_tz_test";          // 数据库名称
$host = "106.15.180.165";                      // 数据库IP
$user = "root";         // 登录名
$pass = "jianbao2017@wolfhe";       // 登录密码
$db = "mysql";

if (!isset($MyPDOEnabled)) $MyPDOEnabled = 0;

if ($MyPDOEnabled == 1) {
    try {
        $myPDO = new PDO('mysql:host=' . $host . ';dbname=' . $DataIn, $user, $pass);

        $myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $myPDO->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

        $myPDO->exec("SET NAMES 'utf8'");

    } catch (PDOException$e) {

        print"Error!:" . $e->getMessage() . "<br/>";
        die("无法选择数据库!");
    }
}
else {
    $link_id = mysql_connect($host, $user, $pass);
    mysql_query("SET NAMES 'utf8'");
    mysql_select_db($DataIn, $link_id) or die("无法选择数据库!");//默认数据库为$DataIn
}

// 模拟 register_globals = On
if (!ini_get('register_globals')) {

    $superglobals = array($_SERVER, $_ENV, $_FILES, $_COOKIE, $_POST, $_GET);

    if (isset($_SESSION)) {
        array_unshift($superglobals, $_SESSION);
    }

    foreach ($superglobals as $superglobal) {
        extract($superglobal, EXTR_SKIP);
    }
}
