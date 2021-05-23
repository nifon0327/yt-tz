<?php 
//电信-EWEN
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="请假记录";		//需处理
	$upDataSheet="$DataIn.kqovertime";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	
	$updateDb = $updateType==='2'?$DataOut:$DataIn;

	$updateSql = "update $updateDb.kqovertime set workday='$workdayTime', weekday='$weekdayTime', holiday='$holidayTime' Where Id = '$Id'";
	if(mysql_query($updateSql))
	{
		if($updateType === '0'){
			$outUpdateSql = "UPDATE $DataOut.kqovertime as OutOver 
							INNER JOIN $DataIn.kqovertime as InOver On OutOver.otDate = InOver.otDate
							SET OutOver.workday = InOver.workday, OutOver.weekday = InOver.weekday, OutOver.holiday=InOver.holiday
							WHERE InOver.Id = $Id";
			mysql_query($outUpdateSql);
		}
		$Log = "更新成功.";
	}
	else
	{
		$Log = "更新失败.";
	}

	include "../model/logpage.php";


?>