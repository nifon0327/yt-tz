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
    // $openId = "op_TywyUUOhnkWAFYVPUb6kTs5tY";//$_SESSION["openid"];
    if(isset($_SESSION["openid"]))
    {
        $openId = $_SESSION["openid"];
    }
    switch ($action) {

        case 'getNameRule':
            $result = $db->get_name_rule();
            StatusCode(0,$result);
            break;

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

        //创建构建记录    todo
        case 'createInspectionRecord':
            $recordNo = GetParam("recordNo");
            $recordName = GetParam("recordName");
            $workShopId = GetParam("workShopId");
            $status = GetParam("status");//1 成品   0 生产中
            $result = $db->create_inspection_record($recordNo, $recordName, $workShopId ,$status,$openId);
            if($result['status'] == 1){
                StatusCode(1, 0, "参数缺失");
            }else{
                StatusCode(0,$result);
            }
            break;

        //上传图片
        case 'insertImageUrl':
            $inspectionRecordId = GetParam("inspectionRecordId");
            $imageUrl = GetParam("imageUrl");
            $result = $db->InsertImageUrl($inspectionRecordId,$imageUrl);
            StatusCode(0, $result);
            break;

        //获取生产线构件
        case 'getProductByWorkshop':
            $workshopId = GetParam("workshopId");
            $tradeId = GetParam("tradeId");
            $result = $db->get_product_by_workshop($workshopId,$tradeId);
            StatusCode(0,$result);
            break;

        //构件插入记录
        case 'insertInspectionRecord':
            $inspectionRecordId = GetParam("inspectionRecordId");
            $product = json_decode($_POST["product"],true);
            $status = GetParam("status");
            $result = $db->InsertInspectionRecord($inspectionRecordId,$product,$openId,$status);
            StatusCode(0,$result);
            break;

        //扫码添加成品
        case 'insertInspectionProductByQrCode':
            $inspectionRecordId = GetParam("inspectionRecordId");
            $productName = GetParam("productName");
            $workShopId = GetParam("workShopId");
            $result = $db->InsertInspectionProductByQrCode($inspectionRecordId ,$productName,$workShopId,$openId);
            StatusCode(0,$result);
            break;
        //扫码添加生产
        case 'insertInspectionProductByQrCodeProducting':
            $inspectionRecordId = GetParam("inspectionRecordId");
            $productName = GetParam("productName");
            $workShopId = GetParam("workShopId");
            $result = $db->InsertInspectionProductByQrCodeProducting($inspectionRecordId ,$productName,$workShopId,$openId);
            StatusCode(0,$result);
            break;

        //删除构件（通用）
        case 'deleteInspectionProduct':
            $inspectionProductId = json_decode($_POST["inspectionProductId"], true);
            $status = GetParam("status");
            $result = $db->DeleteInspectionRecord($inspectionProductId,$status);
            StatusCode(0,$result);
            break;

        //质检构件
        case 'inspectProduct':
//            $productId = json_decode($_POST["productId"], true);
            $cjtjId = json_decode($_POST["cjtjId"], true);
            $status = GetParam("status");
            $result = $db->inspectProduct($cjtjId,$openId,$status);
            StatusCode(0,$result);
            break;

        //构件详情
        case 'getProductByInspectionRecord':
            $inspectionRecordId = GetParam("inspectionRecordId");
            $status = GetParam("status");
            $result = $db->getProductByInspectionRecord($inspectionRecordId,$status);
            StatusCode(0,$result);
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

        //获取上一次搜索条件
        case 'getLastSearch':
            $inspectionRecordId = GetParam("inspectionRecordId");
            $result = $db->get_last_search($inspectionRecordId, $openId);
            StatusCode(0, $result);
            break;

        //查询构件()
        case 'searchProducts':
            $workshopId = GetParam("workshopId");
            $tradeId = GetParam("tradeId");
            $status = GetParam("status");
            $buildingNo = $_POST["buildingNo"];
            $floorNo = $_POST["floorNo"];
            $type = $_POST["type"];
            $productCode = $_POST["productCode"];
            $inspectionRecordId = GetParam("inspectionRecordId");
            $result = $db->search_product($workshopId,$tradeId, $buildingNo, $floorNo, $type, $productCode,$status,$inspectionRecordId,$openId);//获取构件信息
            StatusCode(0, $result);
            break;
        case 'searchInspectionRecord':
            $workshopId = GetParam("workshopId");
            $tradeId = GetParam("tradeId");
            $status = GetParam("status");
            $date = $_POST["date"];
            $buildingNo = $_POST["buildingNo"];
            $floorNo = $_POST["floorNo"];
            $type = $_POST["type"];
            $productCode = $_POST["productCode"];
            $result = $db->search_inspection_record($date,$workshopId,$tradeId, $buildingNo, $floorNo, $type, $productCode,$status);
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