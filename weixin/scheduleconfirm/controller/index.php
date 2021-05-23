<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/25
 * Time: 16:15
 */

include '../config/dbconnect.php';
session_start();

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

header('Content-Type:application/json;charset=utf-8');

try {
    $db = new DbConnect();
    $action = $_POST['action'];
    switch ($action) {

        // 获取楼栋构件详情
        case 'getProductStateSpecify':
            $CompanyId=GetParam("CompanyId");
            $BuildingNo= GetParam("BuildingNo");
            $FloorNo=GetParam("FloorNo");
            $TypeId = GetParam("TypeId");
            $Status = GetParam("Status");
            $result = $db->getProductStateSpecify($CompanyId, $BuildingNo,$FloorNo,$TypeId,$Status);
            StatusCode(0, $result);
            break;

        // 获取楼栋构件信息进度信息
        case 'getProductSchedule':
            $CompanyId=GetParam("CompanyId");
            $BuildingNo= GetParam("BuildingNo");
            $result = $db->getProductSchedule($CompanyId,$BuildingNo);
            StatusCode(0, $result);
            break;

        //获取项目信息
        case 'getCompanyForShort':
            $result = $db->get_company_forshort();
            StatusCode(0, $result);
            break;

        //获取公司楼栋
        case 'getCompanyBuilding':
            $CompanyId = GetParam("CompanyId");
            $result = $db->get_company_building($CompanyId);
            StatusCode(0, $result);
            break;

        default:
            StatusCode(1, 0, "无效的请求地址！");
    }
} catch (Exception $ex) {
    StatusCode(1, null, $ex->getMessage());
}

function StatusCode($status, $result, $msg = '成功')
{
    echo json_encode(array(
        'status' => $status,
        'result' => $result,
        'msg' => $msg
    ));
}

function GetParam($string){
    $param = $_POST[$string];
    if(is_null($param))
        throw new Exception("参数".$string."缺失");
    return $param;
}