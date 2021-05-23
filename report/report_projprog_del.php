<?php
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="项目进度报表";
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作‘
$x=1;
if($Ids){
    $Ids = explode(',', $Ids);
    $deleteId = '';
    $confirmedId = '';
    foreach ($Ids as $val){
        $query = "select confirm from $DataIn.rp_projectprogress where Id = $val";
        $cursor = mysql_query($query, $link_id);
        if($row = mysql_fetch_row($cursor)){
            if(!$row[0]){
                //不确定的记录，可删
                $mySql = "delete from $DataIn.rp_projectprogress where Id = $val";
                $mySqlResult = @mysql_query($mySql, $link_id);
                $deleteId .= $val.',';
            }else{
                //确定的记录
                $confirmedId .= $val.',';
            }
        }
    }
    $deleteId = rtrim($deleteId, ',');
    $confirmedId = rtrim($confirmedId, ',');
    if($deleteId){
        $affectRows = mysql_affected_rows($link_id);
        $Log .= '成功删除Id为'.$deleteId.'的记录！';
    }
    if($confirmedId){
        $Log .= '<div class=redB>Id为'.$confirmedId.'已确定，不能删除！</div>';
    }
}else{
    $Log .= "<div class=redB>没有选择记录！</div>";
    $OperationResult="N";
}

$ALType="From=$From&CompanyId=$CompanyId";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>