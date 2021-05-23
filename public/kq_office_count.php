<?php 
//电信-EWEN
include "../model/modelhead.php";
include "kqcode/kq_function.php";
//步骤2：
$nowWebPage ="kq_office_count";
$fromWebPage="kq_office_read";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage";
//步骤3：
$tableMenuS=600;
$CountType=$CountType==""?0:$CountType;
$TypeTemp="CountType".strval($CountType); 
$$TypeTemp="selected";
if($CountType==0){
	$tableWidth=955;
	ChangeWtitle("$SubCompany 日考勤统计");//需处理
	include "kq_office_reportd.php";
	}
else{
	$tableWidth=855;
	ChangeWtitle("$SubCompany 月考勤统计");//需处理
	include "kq_office_reportm.php";
	}
?>