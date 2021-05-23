<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$StuffId=$_REQUEST['StuffId'];
$CmptTypeId = $_REQUEST['CmptTypeId'];
$CmptType = $_REQUEST['CmptType'];
$Loss = $_REQUEST['Loss'];
$flag = $_REQUEST['flag'];
$change = $_REQUEST['change'];
$ProId = $_REQUEST['ProId'];


if ($flag == 1) {
    $mysql = "update bom_info set Loss = $change,Modifier=$Login_P_Number,Modified=now() where TradeId =$ProId and CmptTypeId=$CmptTypeId and MaterNo=$StuffId";
    mysql_query($mysql,$link_id);
    $ret = mysql_affected_rows();
    if($ret > 0){
        echo json_encode(
            true
        );
        return;
    }
}else{
    $mysql = 'select id from stuff_loss where StuffId='. $StuffId.' AND CmptTypeId = '.$CmptTypeId;

    $ret = mysql_query($mysql,$link_id);
    if($res = mysql_fetch_row($ret)){
        $mysql =  "update stuff_loss set ThisStd = $Loss,Modifier=$Login_P_Number,Modified=now() where StuffId = $StuffId and CmptTypeId = $CmptTypeId";
        mysql_query($mysql,$link_id);
        $ret = mysql_affected_rows();
        if($ret > 0){
            echo json_encode(
                true
            );
            return;
        }
    }else{
        $mysql = "insert into stuff_loss (id,CmptTypeId,CmptType,StuffId,ThisStd,Creator,Created) values(null,$CmptTypeId,'$CmptType',$StuffId,$Loss,$Login_P_Number,now())";
        mysql_query($mysql,$link_id);
        $ret = mysql_affected_rows();
        if($ret > 0){
            echo json_encode(
                true
            );
            return;
        }
    }
}

