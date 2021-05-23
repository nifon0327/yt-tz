<?php
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
include "../basic/chksession.php";

$Date = date("Y-m-d");
$Operator = $Login_P_Number;
$DateTime = date("Y-m-d H:i:s");
// 获取相关请求头
$tempTime = $_REQUEST['tempTime'];

// 获取当前车间id,name数组
$workshopResult = $myPDO->query("SELECT Id,`Name` FROM workshopdata");
$workshop = $workshopResult->fetchAll(PDO::FETCH_ASSOC);
$work_shop = array_column($workshop, 'Id', 'Name');

// 处理相关数据
function trimAll($str)
{
    $reg = array(" ", "　", "\t", "\n", "\r");

    return str_replace($reg, ',', $str);
}

$tempTime = array_values(explode(',', trimAll($tempTime)));
$num = 0;
$parts = $part = [];
foreach ($tempTime as $k => $v) {
    switch ($k % 8) {
        case 0:
            // 台车号
            $part[0] = intval($v);
            break;
        case 1:
            // 构件名称
            $part[1] = $v;
            break;
        case 2:
            $part[1] .= '-' . $v;
            break;
        case 3:
            if ($v == '楼板' || strtoupper($v) == 'PCB') {
                $v = 'PCB';
            }
            else if ($v == '墙板' || strtoupper($v) == 'PCQ') {
                $v = 'PCQ';
            }
            else if ($v == '楼梯' || strtoupper($v) == 'PCLT') {
                $v = 'PCLT';
            }
            else if ($v == '阳台' || strtoupper($v) == 'PCYT') {
                $v = 'PCYT';
            }
            else {
                $v = 'unknow type!';
            }
            $part[2] = $v;
            break;
        case 4:
            $part[1] .= '-' . $v . '-';
            $part[9] = $v;
            break;
        case 5:
            $part[3] = date('Y-m-d 00:00:00', strtotime($v));
            break;
        case 6:
            // 原台车
            $part[4] = $v;
            break;
        case 7:
            // 车间号
            $part[5] = $work_shop[strtoupper($v)];
            $parts[$num] = $part;
            $num++;
            break;
    }
}

// 根据构件名称设置时间
$log = '';
foreach ($parts as $value) {
    if ($value[4] == 0) {
        $myOrderResult = $myPDO->query("SELECT S.Id, S.POrderId, PI.Leadtime FROM productdata PD 
            LEFT JOIN yw1_ordersheet S ON S.ProductId = PD.ProductId 
            LEFT JOIN yw1_scsheet YC ON YC.POrderId = S.POrderId
            LEFT JOIN yw3_pisheet PI ON PI.oId = S.Id 
            WHERE PD.cName like '$value[1]%' AND PD.CmptNo = '$value[9]' AND S.liningNo IS NULL AND YC.Estate = 1 AND YC.ActionId = 104 LIMIT 1");
    }
    else {
        $myOrderResult = $myPDO->query("SELECT S.Id, S.POrderId, PI.Leadtime FROM productdata PD 
            LEFT JOIN yw1_ordersheet S ON S.ProductId = PD.ProductId 
            LEFT JOIN yw1_scsheet YC ON YC.POrderId = S.POrderId
            LEFT JOIN yw3_pisheet PI ON PI.oId = S.Id 
            WHERE PD.cName like '$value[1]%' AND PD.CmptNo = '$value[9]' AND S.liningNo = '$value[4]' AND YC.Estate = 1 AND YC.ActionId = 104 LIMIT 1");

    }
    if ($myOrderResult && $myOrderRow = $myOrderResult->fetch()) {
        do {
            $POrderId = $myOrderRow["POrderId"];
            $Leadtime = $myOrderRow["Leadtime"];
            if ($POrderId == null || $Leadtime != null) {
                continue;
            }
            // 插入台车号
//            $myPDO->exec("UPDATE yw1_ordersheet SET liningNo = '$value[0]' WHERE POrderId = '$POrderId'");
//            $myPDO->exec("UPDATE yw1_scsheet SET WorkShopId = '$value[5]',scDate = '$value[3]' WHERE POrderId = '$POrderId'");
            $myPDO->exec("UPDATE yw1_ordersheet O,yw1_scsheet S SET O.liningNo = '$value[0]' ,O.RealLining = '$value[0]' ,S.WorkShopId = '$value[5]' , S.scDate = '$value[3]' WHERE O.POrderId = S.POrderId AND O.POrderId = '$POrderId'");


            $hasLeadtimeSign = "";
            if ($Leadtime == "") {
                $checkTimeResult = $myPDO->query("SELECT Leadtime FROM $DataIn.yw3_pileadtime WHERE POrderId='$POrderId' LIMIT 1");
                $checkTimeRow = $checkTimeResult->fetch();
                $Leadtime = $checkTimeRow["Leadtime"];
                $Leadtime = $Leadtime == "" ? "&nbsp;" : $Leadtime;
            }

            $weekName = "";
            if ($Leadtime != "" && $Leadtime != "&nbsp;") {

                $Leadtime = str_replace("*", "", $Leadtime);
                $dateResultPDO = $myPDO->query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek");
                $dateResult = $dateResultPDO->fetch();
                $PIWeek = $dateResult["PIWeek"];

                if ($PIWeek > 0) {
                    $week = substr($PIWeek, 4, 2);
                    $weekName = "Week " . $week;
                }
            }

            if ($weekName != "") {
                $hasLeadtimeSign = "YES";
            }

            //采购交期设置
            $ReduceWeeks = "";
            $CheckResult = $myPDO->query("SELECT ReduceWeeks FROM $DataIn.yw2_cgdeliverydate WHERE POrderId='$POrderId' LIMIT 1");
            if ($CheckRow = $CheckResult->fetch()) {
                $ReduceWeeks = $CheckRow["ReduceWeeks"];
            }

            //PI交期变动，变动交期要通知主管审核才有效
            if ($hasLeadtimeSign == "YES") {
                $ChangeSql = "REPLACE INTO  $DataIn.yw3_pileadtimechange(Id,POrderId,UpdateLeadtime,OldLeadtime,ReduceWeeks,OldReduceWeeks,Estate,Remark,Date,Operator) Values (NULL,'$POrderId','$PIDate','','$ReduceWeeks','','1','$updateWeekRemark','$Date','$Operator') ";
                $count = $myPDO->exec($ChangeSql);

                if ($msg == "") {
                    $msg = "PI交期变动，变动交期要通知主管审核才有效";
                    echo $msg;
                }
            }
            else {
                //设置订单采购交期
                if ($ReduceWeeks === '') $ReduceWeeks = -1;
                $myResult = $myPDO->query("CALL proc_yw1_ordersheet_setdeliverydate('$POrderId','$value[3]',$ReduceWeeks,'1',$Operator);");

                $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
                $OperationResult = $myRow['OperationResult'];
                $Log .= $OperationResult == "Y" ? $myRow['OperationLog'] : "<div class=redB>" . $myRow['OperationLog'] . "</div>";

                $myResult = null;
                $myRow = null;

                $IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','产品订单更新','更新PI交期','$Log','$OperationResult','$Operator')";
                $myPDO->exec($IN_recode);
            }
        } while ($myOrderRow = $myOrderResult->fetch());
    }
}
echo json_encode(array(
    'rlt'  => true,
));


?>