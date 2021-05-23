<?php

	//mc 内部模式:OK ewen2013-08-03
	include "../model/modelhead.php";
	include "kqcode/kq_function.php";
	//步骤2：
	$nowWebPage ="kq_checkio_count";
	$fromWebPage="kq_checkio_read";
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
		include "kq_checkio_reportd.php";
	}
	else{
		$tableWidth=1060;
		ChangeWtitle("$SubCompany 月考勤统计");//需处理
		include "kq_checkio_reportm.php";
	}
