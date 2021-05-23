<?php
include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";
include "../basic/chksession.php";

$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;

@$proId = addslashes($_POST['proId']);
@$Estate = addslashes($_POST['Estate']);
@$txtReasons = addslashes($_POST['txtReasons']);
@$chooseState = addslashes($_POST['chooseState']);

$mySql="select Estate from $DataIn.bom_object WHERE TradeId='$proId' ";
$result = mysql_query($mySql);
if($result && $myRow = mysql_fetch_array($result)){
    $Estate = $myRow["Estate"];

    //审核
    if ($Estate != 1) {
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

//审核
$mySql="update $DataIn.bom_object set Estate='$chooseState',
Checker='$Operator',
Checked='$DateTime',
CReasons='$txtReasons'
where TradeId='$proId' ";

$result = mysql_query($mySql);
if($result && mysql_affected_rows()>0){
    
    //审核通过
    if ($chooseState == 2) {
        /*
        //修改产品审核状体
        $mySql="UPDATE $DataIn.productdata a, $DataIn.trade_object b 
            set a.Estate = 1
        where a.CompanyId = b.CompanyId and b.Id = '$proId' and a.Estate = 2 ";
        $result = mysql_query($mySql);
        
        
        }
        */
    }
     
    echo json_encode(array(
            'rlt'=> true
    ));

    include_once "../weixin/weixin_api.php";

    $weixin = new weixin_api();

    $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信已校审 open_id

    $next_user = '已校审';//发送给的用户名字，与$touser相对应

    $login_user = $_SESSION['Login_Name'];  //当前登录用户

    $Log_Item = '校审';  //当前操作

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
