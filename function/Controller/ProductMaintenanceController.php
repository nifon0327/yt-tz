<?php
/**
 * Created by PhpStorm.
 * User: Kyle
 */

include '../DBConnect/ProductMaintenanceSql.php';
include '../DBConnect/Config/DbConnect.php';
include '../DBConnect/CommonSql.php';
include '../utils/CommonUtils.php';
session_start();

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

header('Content-Type:application/json;charset=utf-8');
$openId = $_SESSION["openid"];//"op_TywyUUOhnkWAFYVPUb6kTs5tY";//
try {
    $db = new ProductMaintenanceSql();
    $action = $_POST['action'];
//   $openId = "op_TywyUUOhnkWAFYVPUb6kTs5tY";//$_SESSION["openid"];
//    $openId = $_SESSION["openid"];
    switch ($action) {
        //获取状态
        case 'getMaintenanceStatus':
            $result = $db->getMaintenanceStatus();
            StatusCode(0, $result);
            break;
        //获取位号  列
        case 'getLineNo':
            $workshopId = GetParam("workshopId");
            $type = GetParam("type");
            $result = $db->getLineNo($workshopId, $type);
            StatusCode(0, $result);
            break;

        //获取位号  row
        case 'getRowNo':
            $workshopId = GetParam("workshopId");
            $type = GetParam("type");
            $lineNo = GetParam("lineNo");
            $result = $db->getRowNo($workshopId, $type, $lineNo);
            StatusCode(0, $result);
            break;
        //获取日期
        case 'getDate':
            $workshopId = GetParam("workshopId");
            $tradeId = GetParam("tradeId");
            $result = $db->getDate($workshopId, $tradeId);
            StatusCode(0, $result);
            break;
        //窑位选择
        case 'selectKilnBit':
            $kilnId = GetParam("kilnId");
            $trolleyNo = GetParam("trolleyNo");
            $result = $db->selectKilnBit($kilnId, $trolleyNo, $openId);
            StatusCode(0, $result);
            break;

        //窑位选择
        case 'getKilnBits':
            $workshopId = GetParam("workshopId");
            $result = $db->getKilnBits($workshopId);
            StatusCode(0, $result);
            break;

        //窑位养护列表
        case 'searchProducts':
            $workshopId = GetParam("workshopId");
            $tradeId = GetParam("tradeId");

            $trolleyNo = $_POST['trolleyNo'];
            $scdate = $_POST['scdate'];
            $trolleyNo = $_POST['trolleyNo'];
            $status = $_POST['status'];
            $result = $db->searchProducts($workshopId,$tradeId,$trolleyNo,$scdate,$status);
            StatusCode(0, $result);
            break;

        //入窑确认（多选）
        case 'intoKilnBit':
            $orders = json_decode($_POST["orders"], true);
            $result = $db->intoKilnBit($orders,$openId);
            StatusCode(0, $result);
            break;
        //手动入窑确认（多选）
        case 'intoKilnBitForce':
            $orders = json_decode($_POST["orders"], true);
            $result = $db->intoKilnBitForce($orders,$openId);
            StatusCode(0, $result);
            break;
        //根据订单id获取窑位详情
        case 'getProductsByMaintanOrderId':
            $maintanOrderId = GetParam("maintanOrderId");
            $result = $db->getProductsByMaintanOrderId($maintanOrderId);
            StatusCode(0, $result);
            break;
        //出窑
        case 'outKilnBit':
            $maintanOrderId = GetParam("maintanOrderId");
            $result = $db->outKilnBit($maintanOrderId);
            StatusCode(0, $result);
            break;
        //手动出窑
        case 'outKilnBitForce':
            $maintanOrderId = GetParam("maintanOrderId");
            $result = $db->outKilnBitForce($maintanOrderId);
            StatusCode(0, $result);
            break;
        //获取项目信息
        case 'getCompanyForShort':
            $result = $db->get_company_forshort();
            StatusCode(0, $result);
            break;
        default:
            StatusCode(1, 0, "无效的请求地址！");
    }
} catch (Exception $ex) {
    StatusCode(1, null, $ex->getMessage());
}
