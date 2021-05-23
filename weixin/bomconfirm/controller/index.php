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
    $openId = $_SESSION["openid"]; //"op_TywycltKhK-e4ViFL61DWX9Ow";
    switch ($action) {

        //更新模状态
        case 'updateMouldStatus':
            $mouldArray =json_decode($_POST["mouldArray"], true);
            $status = $_POST["status"];
            $result = $db->update_mould_status($mouldArray,$status);
            StatusCode(0,$result);
            break;

        //删除模具
        case 'deleteMouldTrolley':
            $mouldArray =json_decode($_POST["mouldArray"], true);
            $result = $db->delete_mould_trolley($mouldArray);
            StatusCode(0,$result);
            break;

        //添加模具
        case 'addMouldTrolley':
            $mouldArray =json_decode($_POST["mouldArray"], true);
            $trolleyId = $_POST["trolleyId"];
            $result = $db->add_mould_trolley($trolleyId,$mouldArray,$openId);
            StatusCode(0,$result);
            break;

        //获取模具
        case 'getBom':
            $tradeId = $_POST["tradeId"];
            $mouldCat = $_POST["mouldCat"];
            $mouldNo = $_POST["mouldNo"];
            $result = $db->get_bom($tradeId,$mouldCat,$mouldNo);
            StatusCode(0,$result);
            break;

        //获取模具类型
        case 'getModuleCat':
            $tradeId = $_POST["tradeId"];
            $result = $db->get_module_cat($tradeId);
            StatusCode(0,$result);
            break;

        //获取项目信息
        case 'getCompanyForShort':
            $result = $db->get_company_forshort();
            StatusCode(0, $result);
            break;

        //获取当前台号下的模具
        case 'getBomByTrolley':
            $trolleyId = $_POST["trolleyId"];
            $result = $db->get_bom_by_trolley($trolleyId,$openId);
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

 function StatusCode($status, $result, $msg = '成功')
{
    echo json_encode(array(
        'status' => $status,
        'result' => $result,
        'msg' => $msg
    ));
}