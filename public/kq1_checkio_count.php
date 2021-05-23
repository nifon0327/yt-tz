<?php 
//mc 验厂文件 ewen 2013-08-03 OK
include "../model/modelhead.php";
include "kqcode/kq_function.php";
//步骤2：
$nowWebPage ="kq_checkio_count";
$fromWebPage="kq_checkio_read";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage";
//步骤3：
$tableMenuS=700;
$CountType=$CountType==""?0:$CountType;
$TypeTemp="CountType".strval($CountType); 
$$TypeTemp="selected";
if($CountType==0){
	$tableWidth=975;
	ChangeWtitle("$SubCompany 日考勤统计");//需处理
		
		if(strtotime($CheckDate) >= strtotime("2014-03-01") || $CheckMonth >= "2014-03")
		{
			include "kqy_reportd.php";
		}
		else
		{
			include "kq1_checkio_reportd.php";
		}

	}
	else
	{
	$tableWidth=855;
	
	ChangeWtitle("$SubCompany 月考勤统计");//需处理
	
	if(strtotime($CheckDate) >= strtotime("2014-03-01") || $CheckMonth >= "2014-03" && $CheckMonth != "2014-06")
	{
		include "kqy_reportm.php";
	}
	else{
		include "kq1_checkio_reportm.php";
	}
	
	}
?>