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

        case 'dropScrapProduct':
            $scrapProductIds = $_POST["scrapProductIds"];
            $result=$db->dropScrapProduct($scrapProductIds);
            StatusCode(0,$result);
            break;

        case 'getScrapProduct':
            $companyId =$_POST["companyId"];
            $buildNo=$_POST["buildNo"];
            $orderPO=$_POST["orderPO"];
            $typeId=$_POST["typeId"];
            $scrapDate=$_POST["scrapDate"];
            $result =$db->getScrapProduct($companyId, $buildNo, $orderPO, $typeId, $scrapDate);
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

        case 'createScrapProduct':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $orderPO = $_POST["orderPO"];
            $typeId = $_POST["typeId"];
            $productId= $_POST["productId"];
            $scrapDate= $_POST["scrapDate"];
            $workshopId= $_POST["workshopId"];
            $invoicePath= $_POST["invoicePath"];
            $scrapAnalysis= $_POST["scrapAnalysis"];
            $pOrderId= $_POST["pOrderId"];
            $headerId = $_POST["headerId"];
            $result = $db->createScrapProduct($companyId, $buildNo, $orderPO, $typeId, $productId, $scrapDate, $workshopId, $invoicePath, $scrapAnalysis,$pOrderId,$headerId);
            StatusCode(0,$result);
            break;

        case 'updateScrapProduct':
            $scrapProductIds = $_POST["scrapProductIds"];
            $result=$db->updateScrapProduct($scrapProductIds);
            StatusCode(0,$result);
            break;

        case 'getScrapCompany':
            $result=$db->getScrapCompany();
            StatusCode(0,$result);
            break;
        case 'getScrapBuildNo':
            $companyId = $_POST["companyId"];
            $result=$db->getScrapBuildNo($companyId);
            StatusCode(0,$result);
            break;
        case 'getScrapFloorNo':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $result=$db->getScrapFloorNo($companyId,$buildNo);
            StatusCode(0,$result);
            break;
        case 'getScrapType':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $orderPo = $_POST["orderPo"];
            $result=$db->getScrapType($companyId, $buildNo, $orderPO);
            StatusCode(0,$result);
            break;
        case 'getScrapPlan':
            $companyId = $_POST["companyId"];
            $buildNo = $_POST["buildNo"];
            $orderPO = $_POST["orderPO"];
            $typeId = $_POST["typeId"];
            $scrapDate = $_POST["scrapDate"];
            $result=$db->getScrapPlan($companyId, $buildNo, $orderPO, $typeId, $scrapDate);
            StatusCode(0,$result);
            break;
        case 'InsertWorkPlan':
            $workPlan = $_POST["workPlan"];
            $scrapProductId=$_POST["scrapProductId"];
            $result=$db->InsertWorkPlan($workPlan, $scrapProductId);
            StatusCode(0,$result);
            break;
        case 'getScrapCheckProduct':
            $typeId = $_POST["typeId"];
            $scrapDate = $_POST["scrapDate"];
            $companyId = $_POST["companyId"];
            $result=$db->getScrapCheckProduct($scrapDate, $companyId, $typeId);
            StatusCode(0,$result);
            break;
        case 'insertScrapCheck':
            $scrapProductId=$_POST["scrapProductId"];
            $status = $_POST["status"];
            $result=$db->insertScrapCheck( $scrapProductId, $status);
            StatusCode(0,$result);
            break;

        case 'updateScrapCheck':
            $scrapProductId=$_POST["scrapProductId"];
            $eState = $_POST["eState"];
            $rejectAnalysis = $_POST["$rejectAnalysis"];
            $result=$db->updateScrapCheck($scrapProductId, $eState, $rejectAnalysis);
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
