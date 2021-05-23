<?php

//发送登录模板消息
include_once ('weixin_api.php');

$weixin = new weixin_api();
 
$touser = 'op_TywzYDwG4walmycIBLQWKdEn8'; //接收消息的微信用户的openid，此openid可通过微信后台的用户管理处查询
 
$touser = 'op_TywycltKhK-e4ViFL61DWX9Ow'; //何兴亮 

$touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //刘文豪

$login_user = '刘文豪';//登录系统的用户名字

$login_time = date('Y-m-d H:i:s');//登录时间

$time = explode(' ', $login_time);

$time = $time[1];

$login_detail = $login_user.'于今日'.$time.'成功登录系统！';//登录详情

$remark = '您如有疑问，请联系系统管理员！';//备注
 
$res = $weixin->send_login_temp_msg($touser, $login_user, $login_time, $login_detail, $remark);

var_dump($res);

//一次只能发送给一个用户
 
?>