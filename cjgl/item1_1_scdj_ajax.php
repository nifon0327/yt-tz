<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//步骤2：
$Log_Item="生产记录";			//需处理
$Log_Funtion="保存";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理

$inRecode="INSERT INTO $DataIn.sc1_cjtj (Id,GroupId,POrderId,sPOrderId,StockId,Qty,Remark,Date,Estate,Locks,Leader) VALUES 
(NULL,'$Operator','$POrderId','$sPOrderId','$StockId','$Qty','$Remark','$Date','1','0','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	    $Log="$TitleSTR 成功!<br>";
		// add by zx 2013-03-18 加入每个外箱装产品数量
		if ($Relation>0){
			$inRelation="REPLACE INTO $DataIn.sc1_newrelation (Id,POrderId,Relation,Date,Operator) VALUES 
	(NULL,'$POrderId','$Relation','$DateTime','$Operator')";
			$inAction=@mysql_query($inRelation);
			
		}

    $manufacture = $_POST['Item[1]'];

    $CheckSql = mysql_query("SELECT F.ModuleName FROM $DataIn.sc4_funmodule F where F.ModuleId = '$manufacture'");
    if($CheckRow=mysql_fetch_array($CheckSql)){
        $Log_Item = $CheckRow['ModuleName'];
    }

    include_once "../weixin/weixin_api.php";

    $weixin = new weixin_api();

    if($Log_Item == "脱模入库" && $manufacture = 166){

        $touser = 'op_Tyw10O-jX91XfciS3Hg_GAcX0'; //微信王培涛 open_id

        $next_user = '王培涛';//发送给的用户名字，与$touser相对应


    }else {

        $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信徐琴 open_id

        $next_user = '徐琴';//发送给的用户名字，与$touser相对应
    }

        $login_user = $_SESSION['Login_Name'];  //当前登录用户

        //$Log_Item = '钢筋下料';  //当前操作

        $login_time = date('Y-m-d H:i:s');//操作时间

        $time = explode(' ', $login_time);

        $time = $time[1];

        $login_detail = $login_user . '于今日' . $time . '完成' . $Log_Item . '流程。现需要您完成下一步"半成品质检申请"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

        $remark = "\n 流程测试，如有疑问，请及时联系" . $login_user . "或ＩＴ部。";//备注

        $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);

        if ($res) {
            $Log .= "已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
        }


	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR.$inRecode 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
?>
