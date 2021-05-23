<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2019/3/17
 * Time: 15:57
 */

include '../DBConnect/WCSThirdSql.php';
include '../DBConnect/Config/DbConnect.php';
include '../DBConnect/CommonSql.php';
include '../utils/CommonUtils.php';
session_start();

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});
header('Content-Type:application/json;charset=utf-8');

try {
    $db = new WCSThirdSql();
    $action = $_POST["notifyType"];
    switch ($action) {
        case 'Test':
            $result = session_create_id();
            ThridStatusCode(true,0);
            break;
        case 'MSG01'://获取待入窑确认的数据
            $WorkshopId = $_POST["WorkshopId"];
            $TralleyNo = $_POST["TralleyNo"];
            $result = $db->getKilnsInfo($WorkshopId,$TralleyNo);
            if(is_null($result))
			{
				TralleyResultCode();
			}else{
				TralleyResultCode($result[0]["KilnId"],$result[0]["Status"],$result[0]["KType"],$result[0]["LineNo"],true,0,"成功");	
			}
            break;
        case 'MSG02':
            $taskList = json_decode($_POST["taskList"], true);
            foreach ($taskList as $task){
                if($task["IsSuccess"])
                $db->operateConfirm($task["KilnId"],$task["TaskType"],$task["FinishTime"]);
            }
            ThridStatusCode(true,0);
            break;
        case 'MSG03':
            $dataList = json_decode($_POST["dataList"], true);
            foreach ($dataList as $data){
                $db->updateKilnBitParams($data["TemperatureValue"],$data["HumidityValue"],$data["KType"],$data["WorkshopdataId"]);
            }
            ThridStatusCode(true,0);
            break;
        case 'MSG04'://
            $WorkshopId = $_POST["WorkshopId"];
            $result = $db->getKilnsOutInfo($WorkshopId);
            ThridResultCode(true,$result,0);

            break;
        default:
            ThridStatusCode(false, 1, "无效的请求地址！");
    }
} catch (Exception $ex) {
    ThridStatusCode(false, 2, $ex->getMessage());
}