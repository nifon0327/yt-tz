<?php

	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="工作日对调";		//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Operator=$Login_P_Number;
	$OperationResult="Y";

	$ddSetUpdateSql = "Update $DataIn.kq_rqddnew Set GDate='$GDate', GTime='$GTime', XDate='$XDate', XTime='$XTime'
					  Where Id='$Id'";
	if(!mysql_query($ddSetUpdateSql))
	{
		$OperationResult="N";
		$Log .= "更新失败";
	}
	else
	{
		$Log .= "更新成功s";
	}

	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";
?>