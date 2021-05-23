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
    $openId =$_SESSION["openid"];  //"op_TywycltKhK-e4ViFL61DWX9Ow"; //"op_TywycltKhK-e4ViFL61DWX9Ow";
    switch ($action) {

        //获取车间计划
        case 'getWorkPlan':
            $startDate = $_POST["startDate"];
            $result = $db->get_work_plan($startDate);
            StatusCode(0,$result);
            break;

        //获取生产线
        case 'getWorkShop':
            $result = $db->get_work_shop();
            StatusCode(0,$result);
            break;
        //获取工时信息
        case 'getWorkHourInfo':
            $workdate = $_POST["workdate"];
            $workshopId = $_POST["workshopId"];
            $result = $db->get_work_hour($workshopId,$workdate);
            StatusCode(0,$result);
            break;
        //更新工时信息
        case 'updateWorkHourInfo':
            $workdate = $_POST["workdate"];
            $workshopId = $_POST["workshopId"];
            $workHours = $_POST["workHours"];
            $workerNum = $_POST["workerNum"];
            $causeAnalysis = $_POST["causeAnalysis"];
            $result = $db->update_work_hour($workshopId,$workdate,$workHours,$workerNum,$causeAnalysis);
            StatusCode(0,$result);
            break;

        //权限校验
        case 'apiauth':
            $tag = $_POST["tag"];
            $result = $db->apiAuth($openId,$tag);
            StatusCode(0,$result);
            break;

        case 'getUserName':
            //$openId = $_SESSION["openid"]; //"op_TywycltKhK-e4ViFL61DWX9Ow";
            $result = $db->get_user_name($openId);
            StatusCode(0,$result);
            break;

        default:
            StatusCode(1, 0, "无效的请求地址！");
    }
} catch (Exception $ex) {
    StatusCode(1, null, $ex->getMessage());
}

 function StatusCode($status, $result, $msg = '')
{
    echo json_encode(array(
        'status' => $status,
        'result' => $result,
        'msg' => $msg
    ));
}
