<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/25
 * Time: 16:15
 */

include '../DBConnect/CommonSql.php';
include '../DBConnect/Config/DbConnect.php';
include '../utils/CommonUtils.php';
session_start();

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

header('Content-Type:application/json;charset=utf-8');

try {
    $db = new CommonSql();
    $action = $_POST['action'];
//    $openId = "op_TywyUUOhnkWAFYVPUb6kTs5tY";//$_SESSION["openid"];
    $openId = $_SESSION["openid"];
    switch ($action) {

        //获取项目信息
        case 'getCompanyForShort':
            $result = $db->get_company_forshort();
            StatusCode(0, $result);
            break;

        //获取生产线
        case 'getWorkShop':
            $result = $db->get_work_shop();
            StatusCode(0,$result);
            break;

        //获取操作员信息
        case 'getOperatorName':
            $openId = $_SESSION["openid"]; //"op_TywycltKhK-e4ViFL61DWX9Ow";
//            $openId = "op_TywyUUOhnkWAFYVPUb6kTs5tY"; //"op_TywycltKhK-e4ViFL61DWX9Ow";
            $result = $db->get_operator_name($openId);
            StatusCode(0, $result);
            break;
        //初始化窑位信息
        case 'initKiln':
            $result = $db->init_kiln();
            break;

        //获取台车号
        case 'getTrolleyInfo':
            $result = $db->get_trolley_info();
            StatusCode(0, $result);
            break;
        //获取公司楼栋
        case 'getCompanyBuilding':
            $tradeId = $_POST['tradeId'];
            $result = $db->get_company_building($tradeId);
            StatusCode(0, $result);
            break;

        //获取层数
        case 'getBuildingFloor':
            $tradeId = $_POST["tradeId"];
            $buildingNo = $_POST["buildingNo"];
            $result = $db->get_building_floor($tradeId, $buildingNo);
            StatusCode(0, $result);
            break;

        //获取类型
        case 'getCmptType':
            $tradeId = $_POST["tradeId"];
            $buildingNo = $_POST["buildingNo"];
            $floorNo = $_POST["floorNo"];
            $result = $db->get_cmpttype($tradeId, $buildingNo, $floorNo);
            StatusCode(0, $result);
            break;
        default:
            StatusCode(1, 0, "无效的请求地址！");
    }
} catch (Exception $ex) {
    StatusCode(1, null, $ex->getMessage());
}
