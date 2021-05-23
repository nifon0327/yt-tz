<?php
include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";
include "../basic/chksession.php";

$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;

@$proId = addslashes($_POST['proId']);
@$Estate = addslashes($_POST['Estate']);
@$txtReasons = addslashes($_POST['txtReasons']);
@$chooseState = addslashes($_POST['chooseState']);

$mySql="select Estate from $DataIn.trade_info WHERE TradeId='$proId' ";
$result = mysql_query($mySql);
if($result && $myRow = mysql_fetch_array($result)){
    $Estate = $myRow["Estate"];
//     if ($Estate1 != $Estate) {
//         echo json_encode(array(
//                 'rlt'=> false,
//                 'msg'=> '数据错误,请重新检索'
//         ));
//         return;
//     }

    //审核
    if ($Estate != 6) {
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '项目审核状态不对'
        ));
        return;
    }
}
else{
    echo json_encode(array(
            'rlt'=> false,
            'msg'=> '数据错误,请重新检索'
    ));
    return;
}

//校对
$mySql="update $DataIn.trade_info set Estate='$chooseState',
Checker='$Operator',
Checked='$DateTime',
CReasons='$txtReasons'
where TradeId='$proId' ";

$result = mysql_query($mySql);
if($result && mysql_affected_rows()>0){
    echo json_encode(array(
            'rlt'=> true
    ));

    include_once "../weixin/weixin_api.php";

    $weixin = new weixin_api();

    $touser = 'op_Tyw-otSllLx4wG3Pl0o4y9MMU'; //微信已审核 open_id

    $next_user = '张战勇';//发送给的用户名字，与$touser相对应

    $login_user = $_SESSION['Login_Name'];  //当前登录用户

    $Log_Item = '设计-审核';  //当前操作

    $login_time = date('Y-m-d H:i:s');//操作时间

    $time = explode(' ', $login_time);

    $time = $time[1];

    $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

    $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注

    $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);




}
else{
    echo json_encode(array(
            'rlt'=> false,
            'msg'=> '审核操作出错'
    ));
}
