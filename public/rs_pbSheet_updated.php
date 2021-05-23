<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployj
$DataPublic.staffmain
$DataIn.cwxzsheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="排班资料";		//需处理
$upDataSheet="$DataIn.pbSetSheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	default:
		$Date=date("Y-m-d");
		$Remark=FormatSTR($Remark);
		//更新
		$UpSql = "Update $upDataSheet Set pbType = '$ActionIn' Where Id = '$Id'";
		$UpResult=mysql_query($UpSql);
		if($UpResult && mysql_affected_rows()>0){
			$Log="员工 $Number 的 $Log_Item$Log_Funtion更新成功.";
			}
		else{
			$Log="<div class='redB'>员工 $Number 的 $Log_Item$Log_Funtion更新失败. $UpSql </div>";
			$OperationResult="N";
			}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>