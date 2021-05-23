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

$POrderIdArr = explode("|", $POrderIds);
$sPOrderIdArr = explode("|", $sPOrderIds);
$StockIdArr = explode("|", $StockIds);

for ($i= 0;$i< count($POrderIdArr) && $i< count($sPOrderIdArr) && $i< count($StockIdArr); $i++) {
    $POrderId = $POrderIdArr[$i];
    $sPOrderId = $sPOrderIdArr[$i];
    $StockId = $StockIdArr[$i];


    $CheckSql = mysql_query("SELECT P.CompanyId,C.Forshort,S.OrderPO,
            S.ProductId,P.cName,P.eCode,S.POrderId,S.Qty AS OrderQty,SC.Qty
            FROM $DataIn.yw1_scsheet SC
            LEFT JOIN $DataIn.yw1_ordersheet S ON  S.POrderId = SC.POrderId
            LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
            WHERE 1 AND S.POrderId='$POrderId' AND SC.sPOrderId = '$sPOrderId'", $link_id);
    if ($CheckRow = mysql_fetch_array($CheckSql)) {
        $ProductId = $CheckRow["ProductId"];
        $OrderQty = $CheckRow["OrderQty"];        //订单数量
        $Qty = $CheckRow["Qty"];  //工单数量(生产数量)
        //已完成的工序数量
        $CheckCfQty = mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS cfQty
                FROM $DataIn.sc1_cjtj C
                WHERE C.POrderId='$POrderId' AND C.sPOrderId = '$sPOrderId' AND C.StockId = $StockId", $link_id));
        $OverPQty = $CheckCfQty["cfQty"] == "" ? 0 : $CheckCfQty["cfQty"];
        //未完成订单数
        $UnPQty = $Qty - $OverPQty;
    }

    $Relation = 0;
    $RelationResult = mysql_query("SELECT Relation FROM $DataIn.sc1_newrelation
            WHERE POrderId='$POrderId' LIMIT 1", $link_id);
    if ($RelationRows = mysql_fetch_array($RelationResult)) {
        $Relation = $RelationRows["Relation"];
    } else {
        $BoxResult = mysql_query("SELECT P.Relation FROM $DataIn.pands P
                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
                WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040' ", $link_id);
        if ($BoxRows = mysql_fetch_array($BoxResult)) {
            $Relation = $BoxRows["Relation"];
            if ($Relation != "") {
                $RelationArray = explode("/", $Relation);
                $Relation = $RelationArray[1];
            }
        }
    }

    //步骤3：需处理
    $inRecode = "INSERT INTO $DataIn.sc1_cjtj (Id,GroupId,POrderId,sPOrderId,StockId,Qty,Remark,Date,Estate,Locks,Leader) VALUES
    (NULL,'$Operator','$POrderId','$sPOrderId','$StockId','$UnPQty','','$Date','1','0','$Operator')";

    //echo $inRecode;
    $inAction = @mysql_query($inRecode);
    if ($inAction) {
        $Log .= "$TitleSTR 成功!<br>";
        // add by zx 2013-03-18 加入每个外箱装产品数量
        if ($Relation > 0) {
            $inRelation = "REPLACE INTO $DataIn.sc1_newrelation (Id,POrderId,Relation,Date,Operator) VALUES
            (NULL,'$POrderId','$Relation','$DateTime','$Operator')";
            $inAction = @mysql_query($inRelation);

        }

        $manufacture = $_POST['Item[1]'];

        $CheckSql = mysql_query("SELECT F.ModuleName FROM $DataIn.sc4_funmodule F where F.ModuleId = '$manufacture'");
        if ($CheckRow = mysql_fetch_array($CheckSql)) {
            $Log_Item = $CheckRow['ModuleName'];
        }

        include_once "../weixin/weixin_api.php";

        $weixin = new weixin_api();

        if ($Log_Item == "脱模入库" && $manufacture == "166") {

            $touser = 'op_Tyw10O-jX91XfciS3Hg_GAcX0'; //微信王培涛 open_id

            $next_user = '王培涛';//发送给的用户名字，与$touser相对应

            $login_detail = $login_user . '于今日' . $time . '完成' . $Log_Item . '流程。现需要您完成下一步"成品入库"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情


        } else {

            $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信徐琴 open_id

            $next_user = '徐琴';//发送给的用户名字，与$touser相对应

            $login_detail = $login_user . '于今日' . $time . '完成' . $Log_Item . '流程。现需要您完成下一步"半成品质检申请"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

        }

        $login_user = $_SESSION['Login_Name'];  //当前登录用户

        //$Log_Item = '钢筋下料';  //当前操作

        $login_time = date('Y-m-d H:i:s');//操作时间

        $time = explode(' ', $login_time);

        $time = $time[1];

        $remark = "\n 流程测试，如有疑问，请及时联系" . $login_user . "或ＩＴ部。";//备注

        $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);

        if ($res) {
            $Log .= "已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
        }

    }
}

//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

echo $OperationResult;
?>
