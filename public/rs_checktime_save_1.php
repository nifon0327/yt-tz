<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployk
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
include "kqcode/kq_function.php";
//步骤2：
$Log_Item="考勤工时资料";         //需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");

$rateArray = explode('|', $rate);
$rate = $rateArray[0];
$typeI = $rateArray[1];
$DateType = $typeI == 3?'X':'G';
if($allday != ''){
    include "Rs_checktime_day.php";
}else{
    include "Rs_checktime_hours.php";
}



$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

<?php
function exchangeTime($time){
    $timeArray = explode('.', $time);
    switch (count($timeArray)){
        case 1:
            return $timeArray[0].':00';
        break;
        case 2:
            $rate = '0.'.$timeArray[1];
            $mintues = 60*$rate;
            return $timeArray[0].':'.$mintues;
        break;
    } 
} 
?>
