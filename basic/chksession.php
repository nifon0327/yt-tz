<?php
/**
 *  Modified by Aitch.Zung (aitch.zung@icloud.com) 2014-06-25
 */
session_start();

defined('IN_COMMON') || include 'common.php';

include_once "class.php";

$timer = new timer();
$timer->start();

if (!$_SESSION['Login_Id']) {
    exit('<script type="text/javascript">alert("对不起，请先登录！");top.window.location.href="/"; </script>');
} else {
    $Login_ExtNo = $_SESSION["Login_ExtNo"];
    $Login_IP = $_SESSION["Login_IP"];

    // 用户类型
    //$Login_cSign=$_SESSION["Login_cSign"];

    // by qianyunlai.com
    // 暂时强制为 7
    $Login_cSign = 7;  // 所属公司，来自于 表 companys_group 的 字段 cSign

    // 用户类型:1-内部员工，2-客户，3-供应商，4-外部员工，5-参观者
    $Login_uType = $_SESSION["Login_uType"];

    // 窗口标题前置字符
    $SubCompany = $_SESSION["SubCompany"];

    // 用户ID
    $Login_Id = $_SESSION["Login_Id"];

    // 登录名
    $Login_uName = $_SESSION["Login_uName"];

    // 用户编号
    $Login_P_Number = $_SESSION["Login_P_Number"];

    // 网页风格
    $Login_WebStyle = $_SESSION["Login_WebStyle"];

    // 上次离线时间
    $Login_LastTime = $_SESSION["Login_LastTime"];
}
