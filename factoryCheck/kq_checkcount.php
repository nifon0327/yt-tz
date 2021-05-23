<?php
include "../model/modelhead.php";
include_once('../public/kqcode/kq_function.php');
//步骤2：
$nowWebPage ="kq_checkio_count";
$fromWebPage="kq_checkio_read";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage";

$tableMenuS=600;
$CountType=$CountType==""?0:$CountType;
$TypeTemp="CountType".strval($CountType); 
$$TypeTemp="selected";
if($CountType==0){
    $tableWidth=955;
    ChangeWtitle("$SubCompany 日考勤统计");//需处理
    if(strtotime($CheckDate) >= strtotime("2014-03-01") || $CheckMonth >= "2014-03"){
        include "kq_checkioreportd.php";
    }
    else{
        include "../public/kq1_checkio_reportd.php";
    }
    
}
else{
    $tableWidth=1060;
    ChangeWtitle("$SubCompany 月考勤统计");//需处理
    if(strtotime($CheckDate) >= strtotime("2014-03-01") || $CheckMonth >= "2014-03"){
        include "kq_checkioreportm.php";
    }
    else{
        include "../public/kq1_checkio_reportm.php";
    }
}

?>