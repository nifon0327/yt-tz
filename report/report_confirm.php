<?php
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_confirm";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="导入报表数据";
$Log_Funtion="确定";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;
if($Ids){
    $mySql = "update $DataIn.rep_cube set confirm=1 where Id in($Ids) ";
    $mySqlResult = @mysql_query($mySql, $link_id);
    if($mySqlResult){
        $affectRows = mysql_affected_rows($link_id);
        $Log .= '成功确定'.$affectRows.'条记录！';
    }else{
        $Log .= "<div class=redB>确定失败！</br>$mySql</div>";
        $OperationResult="N";
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