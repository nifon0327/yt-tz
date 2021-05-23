<?php
//数据连接
$host = "106.15.180.165";//数据库连接地址，需要改
$user = "root";//数据库用户名，需要改
$pass = "StCB1@y6wU";//数据库密码，需要改
$db = "ac_tz_test";//数据库名，需要改
$link_id = @mysql_connect($host, $user, $pass);
mysql_query("SET NAMES 'utf8'");
mysql_select_db($db, $link_id) or die("无法选择数据库!");
//固定参数
define('ROOTPATH', "D:/ytzz/master/yt-tz/");//根目录，需要改
define("TOKEN", "weisibuluke");
define('APIURL', "https://api.weixin.qq.com/");
define('EncodingAESKey', 'jm5soFcHZljj0M07zMTnY3QNT8yxabIDlzvydEeMG09');
$query  = 'select CodeValue from wx_code where CodeName = "AppId" or CodeName = "AppSecret" order by Id';
$cursor = mysql_query($query, $link_id);
$row = mysql_fetch_row($cursor);
define('APPID', $row[0]);
$row = mysql_fetch_row($cursor);
define('APPSECRET', $row[0]);
?>