<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/25
 * Time: 16:15
 */

include '../DBConnect/ProductFinishedStorageSql.php';
include '../DBConnect/Config/DbConnect.php';
include '../DBConnect/CommonSql.php';
include '../utils/CommonUtils.php';
session_start();

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

header('Content-Type:application/json;charset=utf-8');
$openId = "op_TywyUUOhnkWAFYVPUb6kTs5tY";//$_SESSION["openid"];
try {
    $db = new ProductFinishedStorageSql();
    $action = $_POST['action'];
//    $openId = "op_TywyUUOhnkWAFYVPUb6kTs5tY";//$_SESSION["openid"];
    $openId = $_SESSION["openid"];
    switch ($action) {

        //获取成品构件列表
        case 'searchFinishedProducts':
            $workshopId = GetParam("workshopId");
            $tradeId = GetParam("tradeId");
            $result = $db->searchFinishedProducts($tradeId,$workshopId);
            StatusCode(0, $result);
            break;

        //添加成品构件至垛
        case 'addFinishedProducts':
            $products = json_decode($_POST["products"], true);
            $stackId = GetParam("stackId");
            $result = $db->addFinishedProducts($products,$stackId,$openId);
            StatusCode(0,$result);
            break;
        //扫码获取垛中数据
        case 'getListByStackId':
            $stackNo = GetParam("stackNo");
            $result = $db->getListByStackNo($stackNo,$openId);
            StatusCode(0,$result);
            break;
        //删除构件
        case 'deleteProductByIds':
            $inventoryDataIds = json_decode($_POST["inventoryDataIds"], true);
            $result = $db->deleteProductByIds($inventoryDataIds);
            StatusCode(0,$result);
            break;
        //入库确认
        case 'storageInConfirm':
            $products = json_decode($_POST["products"], true);
            $result = $db->storageInConfirm($products,$openId);
            StatusCode(0,$result);
            break;
        //获取库位
        case 'getSeats':
            $result = $db->getSeats();
            StatusCode(0,$result);
            break;
        //移库
        case 'moveSeat':
            $stackId = GetParam("stackId");
            $seatId = GetParam("seatId");
            $result = $db->moveSeat($stackId, $seatId);
            StatusCode(0,$result);
            break;
        //移垛
        case 'moveStack':
            $originStackId = GetParam("originStackId");
            $stackNo = GetParam("stackNo");
            $productIds = json_decode($_POST["products"], true);
            $result = $db->moveStack($originStackId,$stackNo,$productIds,$openId);
            StatusCode(0,$result);
            break;
        //扫码添加
        case 'addFinishedProductByProductName':
            $cname = GetParam("cname");
            $stackId = GetParam("stackId");
            $result = $db->addFinishedProductByProductName($cname,$stackId,$openId);
            StatusCode(0,$result);
            break;
        //返回
        case 'cancelFinishedProducts':
            $products = json_decode($_POST["products"], true);
            $result = $db->cancelFinishedProducts($products);
            StatusCode(0,$result);
            break;
        //待出构件根据库位查询垛号
        case 'getStackIdBySeat':
            $seatId = GetParam("seatId");
            $result = $db->getStackIdBySeat($seatId);
            StatusCode(0,$result);
            break;
        //根据垛号获取已入库构件
        case 'getProductByStackId':
            $stackId = GetParam("stackId");
            $result = $db->getProductByStackId($stackId);
            StatusCode(0,$result);
            break;
        //根据构件查库位与垛号
        case 'getStackIdAndSeatByProduct':
            $cName = GetParam("cName");
            $result = $db->getStackIdAndSeatByProduct($cName);
            StatusCode(0,$result);
            break;
        case 'searchCName':
            $tradeId = $_POST["tradeId"];
            $buildNo = $_POST["buildNo"];
            $floorNo = $_POST["floorNo"];
            $typeId = $_POST["typeId"];
            $result = $db->searchCName($tradeId,$buildNo,$floorNo,$typeId);
            StatusCode(0,$result);
            break;
        default:
            StatusCode(1, 0, "无效的请求地址！");
    }
} catch (Exception $ex) {
    StatusCode(1, null, $ex->getMessage());
}
