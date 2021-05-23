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
    $db     = new DbConnect();
    $action = $_POST['action'];
    switch ($action) {
        //新增车牌
        case 'createCarNo':
            $carNo  = $_POST["carNo"];
            $result = $db->create_car_no($carNo);
            StatusCode(0, $result);
            break;

        //权限校验
        case 'apiauth':
            $tag    = $_POST["tag"];
            $openId = $_SESSION["openid"];// "op_TywycltKhK-e4ViFL61DWX9Ow"
            $result = $db->apiAuth($openId, $tag);
            StatusCode(0, $result);
            break;

        case 'getOperatorName':
            $openId = $_SESSION["openid"]; //"op_TywycltKhK-e4ViFL61DWX9Ow";
            $result = $db->get_operator_name($openId);
            StatusCode(0, $result);
            break;

        //出货
        case 'updateInvoiceEstate':
            $carNo     = $_POST["carNo"];
            $dateTime  = $_POST["opDatetime"];
            $invoiceNo = $_POST["invoiceNo"];
            $imageUrl  = $_POST["imageUrl"];
            $GZG       = $_POST["GZG"];
            $MF        = $_POST["MF"];
            $result    = $db->update_invoice_estate($carNo, $dateTime, $invoiceNo, $imageUrl, $GZG, $MF);
            StatusCode(0, $result);
            break;
        //获取车辆信息
        case 'getCarNo':
            $result = $db->get_car_no();
            StatusCode(0, $result);
            break;

        //根据出货流水单号查询相关信息
        case 'getInvoiceInfo':
            $invoiceNo = $_POST["invoiceNo"];
            $result    = $db->get_invoice_info($invoiceNo);
            StatusCode(0, $result);
            break;

        //根据日期查询出货流水单号
        case 'getInvoiceNoByDate':
            $currentDate = $_POST["currentDate"];
            $CompanyId   = $_POST["CompanyId"];
            $result      = $db->get_invoice_no_by_date($currentDate, $CompanyId);
            StatusCode(0, $result);
            break;
        case 'getInvoice':
            $result = $db->get_invoice_no();
            if (!empty($result))
                $returnData = array_column($result, 'InvoiceNO');
            StatusCode(0, $returnData);
            break;
        case 'getForshort':
            $result = $db->get_for_short();
            StatusCode(0, $result);
            break;
        case 'getDate':
            $CompanyId = $_POST["CompanyId"];
            $result    = $db->get_date($CompanyId);
            if (!empty($result))
                $result = array_values(array_unique(array_column($result, 'Date')));
            StatusCode(0, $result);
            break;
        default:
            StatusCode(1, 0, "无效的请求地址！");
    }
} catch (Exception $ex) {
    StatusCode(1, null, $ex->getMessage());
}

function StatusCode($status, $result, $msg = '')
{
    echo json_encode([
        'status' => $status,
        'result' => $result,
        'msg'    => $msg
    ]);
}

function GetParam($string)
{
    $param = $_POST[ $string ];
    if (is_null($param))
        throw new Exception("参数" . $string . "缺失");
    return $param;
}
