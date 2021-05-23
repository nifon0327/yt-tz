<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/25
 * Time: 16:15
 */
include_once '../config/dbconnect.php';
include_once '../config/dbtakestock.php';

session_start();
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

header('Content-Type:application/json;charset=utf-8');

try {
    $db     = new DbConnect();
    $dbtake = new Dbtakestock();
    $action = $_POST['action'];
    switch ($action) {

        //更新库存ID
        case 'updateStackSeatId':
            $stackId = $_POST["stackId"];
            $seatId = $_POST["seatId"];
            $result = $db->update_stack_seatid($stackId, $seatId);
            StatusCode(0, $result);
            break;

        //权限校验
        case 'apiauth':
            $tag = $_POST["tag"];
            $openId = $_SESSION["openid"];// "op_TywycltKhK-e4ViFL61DWX9Ow"
            $result = $db->apiAuth($openId,$tag);
            StatusCode(0,$result);
            break;

        case 'getOperatorName':
            $openId = $_SESSION["openid"]; //"op_TywycltKhK-e4ViFL61DWX9Ow";
            $result = $db->get_operator_name($openId);
            StatusCode(0,$result);
            break;

        //根据库位号查询垛号
        case 'getStackBySeat':
            $seatId = $_POST["seatId"];
            $result = $db->get_stack_by_seat($seatId);
            StatusCode(0,$result);
            break;

        //移垛
        case 'moveProduct':
            $originStackId = $_POST["originStackId"];
            $stackId = $_POST["stackId"];
            $productIds = json_decode($_POST["productIds"], true);
            $db->move_stack($originStackId,$stackId,$productIds);
            StatusCode(0,"");
            break;

        //获取上一次查询信息
        case 'getLastSearch':
            $stackId = $_POST["stackId"];
            $openid = $_SESSION["openid"];
            $result = $db->get_last_search($stackId, $openid);
            StatusCode(0, $result);
            break;

        //获取库位号
        case 'getStackSeatList':
            $result = $db->get_seat();
            StatusCode(0, $result);
            break;

        //获取库位号
        case 'getStackSeat':
            $stackId = $_POST["stackId"];
            $result = $db->get_stack_seat($stackId);
            StatusCode(0, $result);
            break;

        //获取垛备注
        case 'getStackDesc':
            // $stackId = $_POST["stackId"];
            // $result = $db->get_stack_desc($stackId);
            // StatusCode(0, $result);
            // break;
            $stackId = $_POST["stackId"];
            $result = $db->get_stack_seat($stackId);
            StatusCode(0, $result);
            break;

        //更新垛备注
        case 'updateStackDesc':
            $stackId = $_POST["stackId"];
            $desc = $_POST["desc"];
            $result = $db->update_stack_desc($stackId, $desc);
            StatusCode(0, $result);
            break;

        //盘点
        case 'checkProductResult':
            $stackId = $_POST["stackId"];
            $productIds = json_decode($_POST["productIds"], true);
            $result  = array();
            $openid  = $_SESSION["openid"];

            foreach ($productIds as $item) {
                $productId=$item["productId"];
                $dbtake->setProductInStockByProductId($productId,$stackId,$openid);
                $numResult = $db->update_result($stackId, $productId, 1, 0, $openid, false);
                array_push($result,$numResult);
            }
            Statuscode(0, $result);
            break;

        //复盘
        case "doubleCheck":
            $stackId = $_POST["stackId"];
            $productIds = json_decode($_POST["productIds"], true);
            $result  = array();
            foreach ($productIds as $item) {
                $numResult = $db->update_result($stackId, $item["productId"], 2, 1, $_SESSION["openid"], true);
                array_push($result,$numResult);
            }
            Statuscode(0, $result);
            break;

        //移除构件接口
        case "removeProduct":
            $stackId = $_POST["stackId"];
            $productIds = json_decode($_POST["productIds"], true);
            foreach ($productIds as $item) {
                $productId = $item["productId"];
                $dbtake->cancelProductsStockByProductids($productId,$stackId);
                $db->remove_product($stackId, $productId);
            }
            StatusCode(0, "");
            break;

        //将构件存入垛 多条
        case 'addProductsToStack':
            $stackId = $_POST["stackId"];
            $productIds = json_decode($_POST["productIds"], true);
            $result = $db->add_product_to_stack($stackId, $productIds);
            StatusCode(0, "");
            break;

        //扫码添加
        case 'scanProductToStack':
            $stackId = $_POST["stackId"];
            $productName = $_POST["productName"];
            $result = $db->scan_product_stack($stackId,$productName);
            if($result==1)
            StatusCode(0,"");
            else
            StatusCode(1,"");
            break;
        //判断是否存在垛号
        case 'addStackCode':
            $stackNo = $_POST['stackNo'];
            $creator = $_POST['openId'];
            $result = $db->search_stack($stackNo);
            $stackId = 0;
            if (is_null($result)) {
                $stackId = $db->create_stack($stackNo, $creator);
            } else {
                $stackId = $result['ID'];
            }
            StatusCode(0, $stackId);
            break;

        //获取垛信息
        case 'getStackInfo':
            $stackId   = $_POST['stackId'];
            $openId = $_SESSION["openid"];
            $pStock = $dbtake->getListProductStockByStackIdAndSeatId($stackId);
            $dbtake->setInventoryData($stackId,$pStock,$openid);
            $result = $db->get_stack_product($stackId);
            $userArray = $db->get_user_name($stackId,$openId);
            $_SESSION["creator"] = $userArray["creator"];
            $_SESSION["doubleCheckUser"] = $userArray["doubleCheckUser"];
            StatusCode(0, $result);
            break;

        //获取项目信息
        case 'getCompanyForShort':
            $result = $db->get_company_forshort();
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

        //查询构件()
        case 'searchProducts':
            $tradeId = $_POST["tradeId"];
            $buildingNo = $_POST["buildingNo"];
            $floorNo = $_POST["floorNo"];
            $type = $_POST["type"];
            $productCode = $_POST["productCode"];
            $stackId = $_POST["stackId"];
            $openId = $_SESSION["openid"];
            $result = $db->search_product($tradeId, $buildingNo, $floorNo, $type, $productCode,$stackId,$openId);//获取构件信息
            StatusCode(0, $result);
            break;

        //获取异常垛号
        case 'getStackNo':
            $result = $db->get_stackno();
            StatusCode(0, $result);
            break;

        //查看异常构件
        case 'getErrorProduct':
            $status = $_POST["status"];
            $stackNo = $_POST["stackNo"];
            $result = $db->get_error_product($status, $stackNo);
            StatusCode(0, $result);
            break;
        case 'getListProductStockByStackIdAndSeatId':
            $stackId= isset($_POST["stackid"])?$_POST["stackid"]:0;
            $seatId = isset($_POST["seatid"])?$_POST["seatid"]:0;
            $result = $db->getListProductStockByStackIdAndSeatId($stackId,$seatId);
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