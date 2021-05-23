<?php
//$MyPDOEnabled=1;

if (!isset($ipadTag)) $ipadTag="no";
if($ipadTag != "yes"){
    include "../basic/chksession.php";
}
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";

$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$DateTime=date("Y-m-d H:i:s");

$updateSql = "update $DataIn.cg1_stocksheet
set DeliveryDate='$PIDate'
where id in ($pIds)";
//echo $updateSql;
$result = mysql_query($updateSql);
if($result && mysql_affected_rows()>0){
    
} else {
    echo "设置交期失败";
}


/*
$myOrderResult=$myPDO->query("SELECT S.Id, S.POrderId, PI.Leadtime FROM $DataIn.yw1_ordersheet S
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId = S.Id WHERE S.POrderId IN ($pIds)");

if ($myOrderResult && $myOrderRow = $myOrderResult->fetch()) {
    do {
        $POrderId=$myOrderRow["POrderId"];
        $Leadtime=$myOrderRow["Leadtime"];
        
        $hasLeadtimeSign="";
        if ($Leadtime==""){
            $checkTimeResult = $myPDO->query("SELECT Leadtime FROM $DataIn.yw3_pileadtime WHERE POrderId='$POrderId' LIMIT 1");
            $checkTimeRow=$checkTimeResult->fetch();
            $Leadtime=$checkTimeRow["Leadtime"];
            $Leadtime=$Leadtime==""?"&nbsp;":$Leadtime;
        }
        
        $weekName="";
        if ($Leadtime!="" && $Leadtime!="&nbsp;" ){
            
            $Leadtime=str_replace("*", "", $Leadtime);
            $dateResultPDO = $myPDO->query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek");
            $dateResult=$dateResultPDO->fetch();
            //$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek",$link_id));
            $PIWeek=$dateResult["PIWeek"];
            
            if ($PIWeek>0){
                $week=substr($PIWeek, 4,2);
                $weekName="Week " . $week;
            }
        }
        
        if($weekName!=""){
            $hasLeadtimeSign ="YES";
        }
        //采购交期设置
        $ReduceWeeks="";
        $CheckResult = $myPDO->query("SELECT ReduceWeeks FROM $DataIn.yw2_cgdeliverydate WHERE POrderId='$POrderId' LIMIT 1");
        //$CheckResult=mysql_query("SELECT ReduceWeeks FROM $DataIn.yw2_cgdeliverydate WHERE POrderId='$POrderId' LIMIT 1",$link_id);
        if($CheckRow=$CheckResult->fetch()){
            $ReduceWeeks=$CheckRow["ReduceWeeks"];
        }
        
        if($hasLeadtimeSign=="YES"){         //PI交期变动，变动交期要通知主管审核才有效
            $ChangeSql = "REPLACE INTO  $DataIn.yw3_pileadtimechange(Id,POrderId,UpdateLeadtime,OldLeadtime,ReduceWeeks,OldReduceWeeks,Estate,Remark,Date,Operator) Values (NULL,'$POrderId','$PIDate','','$ReduceWeeks','','1','$updateWeekRemark','$Date','$Operator') ";
            $count = $myPDO->exec($ChangeSql);
            
            if ($msg == "") {
                $msg = "PI交期变动，变动交期要通知主管审核才有效";
                echo $msg;
            }
        }
        else{
            //设置订单采购交期
            if ($ReduceWeeks==='') $ReduceWeeks=-1;
            $myResult=$myPDO->query("CALL proc_yw1_ordersheet_setdeliverydate('$POrderId','$PIDate',$ReduceWeeks,'1',$Operator);");
            $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
            $OperationResult = $myRow['OperationResult'];
            $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
            
            $myResult=null;
            $myRow=null;
            
            $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','产品订单更新','更新PI交期','$Log','$OperationResult','$Operator')";
            $myPDO->exec($IN_recode);
        }
        
    } while ($myOrderRow = $myOrderResult->fetch());
}
*/
?>