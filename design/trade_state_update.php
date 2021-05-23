<?php
include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";

if($ipadTag != "yes"){
    include "../basic/chksession.php";
}

@$id = addslashes($_POST['id']);
@$state = addslashes($_POST['state']);

//提交校核 判断 /*BY.HXL 20180323*/
if ($state == 1) {
    $mySql="SELECT a.TradeId, a.Estate, a.CmptTotal, b.count as dwgtotal, c.count as steeltotal, d.count as embtotal from trade_info a , 
        (SELECT count(DISTINCT d.CmptNo,d.BuildingNo,d.FloorNo,d.SN) as count FROM trade_drawing d where d.TradeId = $id) b, 
        (SELECT count(DISTINCT s.CmptNo,s.BuildingNo,s.FloorNo,s.SN) as count FROM trade_steel s where s.TradeId = $id) c, 
        (SELECT count(DISTINCT e.CmptNo,e.BuildingNo,e.FloorNo,e.SN) as count FROM trade_embedded e where e.TradeId = $id) d
    WHERE a.TradeId='$id' ";

    $result = mysql_query($mySql);
    if($result && $myRow = mysql_fetch_array($result)){
        $Estate = $myRow["Estate"];
        $CmptTotal = $myRow["CmptTotal"];
        $dwgtotal = $myRow["dwgtotal"];
        $steeltotal = $myRow["steeltotal"];
        $embtotal = $myRow["embtotal"];
        //echo "ttte=" , $total, "<br>";
        //导入的数量不够
        if (!$dwgtotal || !$steeltotal || !$embtotal || $dwgtotal != $CmptTotal || $steeltotal != $CmptTotal || $embtotal != $CmptTotal) {
            echo json_encode(array(
                    'rlt'=> false,
                    'msg'=> '构件的数量有误，请修改完毕后再提交'
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
    
    
//     $mySql="select count(t.CmptNo) from
//     (select a.CmptNo from trade_drawing a  WHERE a.TradeId = $id
//     union ALL
//     select b.CmptNo from trade_steel b  WHERE b.TradeId = $id
//     union ALL
//     select c.CmptNo from trade_embedded C  WHERE C.TradeId = $id) t
//     GROUP BY t.CmptNo
//     HAVING count(t.CmptNo) > 1 LIMIT 1";
    $mySql="select count(a.CmptNo) ,a.CmptNo from trade_drawing a  WHERE a.TradeId = $id
            group by a.CmptNO,A.SN,a.BuildingNO,a.FloorNO 
    HAVING count(a.CmptNo) > 1
union ALL
select count(a.CmptNo) ,a.CmptNo from trade_steel a  WHERE a.TradeId = $id
            group by a.CmptNO,a.SN,a.BuildingNO,a.FloorNO 
    HAVING count(a.CmptNo) > 1
union ALL
select count(a.CmptNo) ,a.CmptNo from trade_embedded a  WHERE a.TradeId = $id
            group by a.CmptNO,a.SN,a.BuildingNO,a.FloorNO 
    HAVING count(a.CmptNo) > 1
LIMIT 1";
    $result = mysql_query($mySql);

    if($result && $myRow = mysql_fetch_array($result)){
        $CmptNo = $myRow["CmptNo"];

        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '构件的编号[' . $CmptNo . ']有误，请修改完毕后再提交'
        ));
        return;
    }
}

//检查两边的数据库中是否存在该用户
$mySql="update $DataIn.trade_info set Estate = '$state' WHERE TradeId='$id' ";
$result = mysql_query($mySql);
if($result && mysql_affected_rows()>0){
    echo json_encode(array(
            'rlt'=> true
    ));
    include_once "../weixin/weixin_api.php";

    $weixin = new weixin_api();

    $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信已出货 open_id

    $next_user = '申请校对';//发送给的用户名字，与$touser相对应

    $login_user = $_SESSION;  //当前登录用户

    $Log_Item = '申请校对';  //当前操作

    $login_time = date('Y-m-d H:i:s');//操作时间

    $time = explode(' ', $login_time);

    $time = $time[1];

    $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

    $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或信息技术部。";//备注

    $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);


}
else{
    echo json_encode(array(
            'rlt'=> false
    ));
}


