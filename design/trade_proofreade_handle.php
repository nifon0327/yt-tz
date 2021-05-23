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

$mySql="SELECT  a.TradeId, a.Estate, a.CmptTotal, b.count + c.count + d.count as total from trade_info a 
LEFT JOIN (select TradeId, count(*) AS count from  trade_drawing GROUP BY TradeId) b on a.TradeId = b.TradeId
LEFT JOIN (select TradeId, count(*) as count from  trade_steel GROUP BY TradeId) c on a.TradeId = c.TradeId
LEFT JOIN (select TradeId, count(*) as count from  trade_embedded GROUP BY TradeId) d on a.TradeId = d.TradeId
 WHERE a.TradeId='$proId' ";
$result = mysql_query($mySql);
if($result && $myRow = mysql_fetch_array($result)){
    $Estate = $myRow["Estate"];
    $CmptTotal = $myRow["CmptTotal"];
    $total = $myRow["total"];
    //     if ($Estate1 != $Estate) {
    //         echo json_encode(array(
    //                 'rlt'=> false,
    //                 'msg'=> '数据错误,请重新检索'
    //         ));
    //         return;
    //     }
    
    //初校 复校
    if ($Estate != 1 && $Estate != 2) {
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '项目审核状态不对'
        ));
        return;
    }
    
    //导入的数量不够
    if ($total && $total < $CmptTotal) {
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '项目导入构件数量不满足要求'
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


//成品图纸
$FilePath="./dwgFiles/$proId/Pord/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$tmpFile=$_FILES['PordFile']['tmp_name'];
if ($tmpFile!=""){
    $imgname = $_FILES["PordFile"]["name"]; //获取上传的文件名称
    
    $PreFileName=$FilePath .$imgname;
    $uploadInfo=move_uploaded_file($tmpFile,$PreFileName);
    chmod($PreFileName,0777);
    //echo iconv("UTF-8","gb2312",$PreFileName);
    if($uploadInfo ==""){
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '文件上传失败'
        ));
        return;
    }
}

//模具图纸
$FilePath="./dwgFiles/$proId/Mould/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$tmpFile=$_FILES['MouldFile']['tmp_name'];
if ($tmpFile!=""){
    $imgname = $_FILES["MouldFile"]["name"]; //获取上传的文件名称
    
    $PreFileName=$FilePath .$imgname;
    $uploadInfo=move_uploaded_file($tmpFile,$PreFileName);
    chmod($PreFileName,0777);
    if($uploadInfo ==""){
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '文件上传失败'
        ));
        return;
    }
}

//钢筋图纸
$FilePath="./dwgFiles/$proId/Steel/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$tmpFile=$_FILES['SteelFile']['tmp_name'];
if ($tmpFile!=""){
    $imgname = $_FILES["SteelFile"]["name"]; //获取上传的文件名称
    
    $PreFileName=$FilePath .$imgname;
    $uploadInfo=move_uploaded_file($tmpFile,$PreFileName);
    chmod($PreFileName,0777);
    if($uploadInfo ==""){
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '文件上传失败'
        ));
        return;
    }
}

//预埋件图纸
$FilePath="./dwgFiles/$proId/Embedded/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$tmpFile=$_FILES['EmbeddedFile']['tmp_name'];
if ($tmpFile!=""){
    $imgname = $_FILES["EmbeddedFile"]["name"]; //获取上传的文件名称
    
    $PreFileName=$FilePath .$imgname;
    $uploadInfo=move_uploaded_file($tmpFile,$PreFileName);
    chmod($PreFileName,0777);
    if($uploadInfo ==""){
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '文件上传失败'
        ));
        return;
    }
}

//校对
$mySql="update $DataIn.trade_info set";
if ($Estate == 1) {
    $state = $chooseState == 1?4:5; //不进行复校
    $mySql .=" Estate='$state',Proofreader='$Operator',Proofreaded='$DateTime',PReasons='$txtReasons' ";
} else {
    $state = $chooseState == 1?4:5;
    $mySql .=" Estate='$state',Proofreader1='$Operator',Proofreaded1='$DateTime',PReasons1='$txtReasons' ";
}

$mySql .=" where TradeId='$proId' ";

$result = mysql_query($mySql);
if($result && mysql_affected_rows()>0){
    echo json_encode(array(
            'rlt'=> true
    ));

    include_once "../weixin/weixin_api.php";

    $weixin = new weixin_api();

    $touser = 'op_Tyw-otSllLx4wG3Pl0o4y9MMU'; //微信已校审 open_id

    $next_user = '张战勇';//发送给的用户名字，与$touser相对应

    $login_user = $_SESSION['Login_Name'];  //当前登录用户

    $Log_Item = '设计-校审';  //当前操作

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
            'msg'=> '校对操作出错'
    ));
}


