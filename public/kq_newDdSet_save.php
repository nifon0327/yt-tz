<?php
	
	include "../model/modelhead.php";
	$Log_Item="上班日期对调记录";			//需处理
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_save";
	$_SESSION["nowWebPage"]=$nowWebPage;
	
	$Log_Funtion="保存";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	$Operator=$Login_P_Number;

	// if($XDate == "")
	// {
	// 	$XDate = "NULL";
	// }

	$inRecode="INSERT INTO $DataIn.kq_rqddnew (Id, GDate, GTime, XDate, XTime, Estate, Operator) Values (NULL, '$GDate', '$GTime', '$XDate', '$XTime', '1', '$Operator')";
	$inResult=mysql_query($inRecode);
	//echo "$inRecode";
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
	
?>