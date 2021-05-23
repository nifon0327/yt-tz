<?php
	
	//步骤1： 
	include "../model/modelhead.php";
	//步骤2：
	$Log_Item="DSRC管理";			//需处理
	$Log_Funtion="保存";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_save";
	$_SESSION["nowWebPage"]=$nowWebPage;
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	$OperationResult="Y";
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	
	$dsrcInsertSql = "Insert into $DataIn.dsrc_list (Id, CardNumber, CarNum,CardHolder, Type, Date, Operator) Values (NULL, '$cardNumber', '$carNum','$cardHolder', '$carType', '$Date', '$Operator')";
	$inAction=@mysql_query($dsrcInsertSql);
if($inAction && mysql_affected_rows()>0){
	$Log="$TitleSTR 成功.<br>";
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败. $inRecode </div><br>";
	$OperationResult="N";
	}
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";	
?>