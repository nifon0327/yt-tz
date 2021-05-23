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
        //获取负责人
        case "getDirector":
            $result = $db->getDirector();
            StatusCode(0,$result);
            break;

        case 'dropReworkProduct':
            $reworkProductIds = $_POST["reworkProductIds"];
            $result=$db->dropReworkProduct($reworkProductIds);
            StatusCode(0,$result);
            break;

        case 'getReworkProduct':
            $companyId =$_POST["companyId"];
            $buildNo=$_POST["buildNo"];
            $orderPO=$_POST["orderPO"];
            $typeId=$_POST["typeId"];
            $reworkDate=$_POST["reworkDate"];
            $result =$db->getReworkProduct($companyId, $buildNo, $orderPO, $typeId, $reworkDate);
            StatusCode(0,$result);
            break;

        case 'getWorkshop':
            $result = $db->getWorkshop();
            StatusCode(0,$result);
            break;

        case 'getForshort':
            $result = $db->getForshort();
            StatusCode(0,$result);
            break;

        case 'getBuildNo':
            $companyId = $_POST["companyId"];
            $result = $db->getBuildNo($companyId);
            StatusCode(0,$result);
            break;

        case 'getFloorNo':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $result = $db->getFloorNo($companyId,$buildNo);
            StatusCode(0,$result);
            break;

        case 'getType':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $orderPO = $_POST["orderPO"];
            $result = $db->getType($companyId,$buildNo,$orderPO);
            StatusCode(0,$result);
            break;

        case 'searchCName':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $orderPO = $_POST["orderPO"];
            $typeId = $_POST["typeId"];
            $pName = $_POST["pName"];
            $result = $db->searchCName($companyId,$buildNo,$orderPO,$typeId,$pName);
            StatusCode(0,$result);
            break;

        case 'createReworkProduct':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $orderPO = $_POST["orderPO"];
            $typeId = $_POST["typeId"];
            $productId= $_POST["productId"];
            $reworkDate= $_POST["reworkDate"];
            $workshopId= $_POST["workshopId"];
            $invoicePath= $_POST["invoicePath"];
            $reworkAnalysis= $_POST["reworkAnalysis"];
            $pOrderId= $_POST["pOrderId"];
            $headerId = $_POST["headerId"];
            $result = $db->createReworkProduct($companyId, $buildNo, $orderPO, $typeId, $productId, $reworkDate, $workshopId, $invoicePath, $reworkAnalysis,$pOrderId,$headerId);
            StatusCode(0,$result);
            break;

        case 'updateReworkProduct':
            $reworkProductIds = $_POST["reworkProductIds"];
            $result=$db->updateReworkProduct($reworkProductIds);
            StatusCode(0,$result);
            break;

        case 'getReworkCompany':
            $result=$db->getReworkCompany();
            StatusCode(0,$result);
            break;
        case 'getReworkBuildNo':
            $companyId = $_POST["companyId"];
            $result=$db->getReworkBuildNo($companyId);
            StatusCode(0,$result);
            break;
        case 'getReworkFloorNo':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $result=$db->getReworkFloorNo($companyId,$buildNo);
            StatusCode(0,$result);
            break;
        case 'getReworkType':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $orderPo = $_POST["orderPo"];
            $result=$db->getReworkType($companyId, $buildNo, $orderPO);
            StatusCode(0,$result);
            break;
        case 'getReworkPlan':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $orderPO = $_POST["orderPO"];
            $typeId = $_POST["typeId"];
            $reworkDate = $_POST["reworkDate"];
            $result=$db->getReworkPlan($companyId, $buildNo, $orderPO, $typeId, $reworkDate);
            StatusCode(0,$result);
            break;
        case 'InsertWorkPlan':
            $workPlan = $_POST["workPlan"];
            $reworkProductId=$_POST["reworkProductId"];
            $result=$db->InsertWorkPlan($workPlan, $reworkProductId);
            StatusCode(0,$result);
            break;
        case 'getReworkCheckProduct':
            $typeId = $_POST["typeId"];
            $reworkDate = $_POST["reworkDate"];
            $companyId = $_POST["companyId"];
            $result=$db->getReworkCheckProduct($reworkDate, $companyId, $typeId);
            StatusCode(0,$result);
            break;
        case 'insertReworkCheck':
            $reworkProductId=$_POST["reworkProductId"];
            $status = $_POST["status"];
            $result=$db->insertReworkCheck( $reworkProductId, $status);
            StatusCode(0,$result);
            break;

        case 'updateReworkCheck':
            $reworkProductId=$_POST["reworkProductId"];
            $eState = $_POST["eState"];
            $rejectAnalysis = $_POST["$rejectAnalysis"];
            $result=$db->updateReworkCheck($reworkProductId, $eState, $rejectAnalysis);
            StatusCode(0,$result);
            break;

        //权限校验
        case 'apiauth':
            $tag = $_POST["tag"];
            $openId = $_SESSION["openid"];// "op_TywycltKhK-e4ViFL61DWX9Ow"
            $result = $db->apiAuth($openId,$tag);
            StatusCode(0,$result);
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
