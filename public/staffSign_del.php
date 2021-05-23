<?php
	
	include "../model/modelhead.php";
	//步骤2：
	ChangeWtitle("$SubCompany 更新薪资逾期扣款记录");//需处理
	$fromWebPage=$funFrom."_read";			
	$nowWebPage  =$funFrom."_del";	
	$_SESSION["nowWebPage"]=$nowWebPage; 
	$Log_Item="薪资签收逾期扣款记录";
	$Log_Funtion = "更新";
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult = "Y";
	
	$ids = implode(",", $checkid);
	$deletePayMentSql = "Delete From $DataPublic.wage_sign_overtime Where Id in ($ids)";
	if(mysql_query($deletePayMentSql))
	{
		$Log = "($ids)记录删除成功<br>";
	}
	else
	{
		$Log = "$deletePayMentSql";
	}

	include "../model/logpage.php";
	
?>