<?php 
//步骤1： $DataPublic.staffmain/$DataIn.smsdata 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="短消息";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");
$Note=FormatSTR($Note);
$User0Array=explode(",",$User0);
$User0Count=count($User0Array);
for($i=0;$i<$User0Count;$i++){
	$Staff=$User0Array[$i];
	$inSql0 = "INSERT INTO $DataPublic.smsdata SELECT NULL,Number,'$DateTime','$Note','1','0','$Operator' FROM $DataPublic.staffmain WHERE Name='$Staff'";
	$inResult0 = @mysql_query($inSql0);
	if($inResult0){
		$Log.="&nbsp;&nbsp;给联系人 $Staff 的 $Log_Item 发送成功。$inSql0</br>";
		}
	else{
		$Log.="<div class='redB'>给联系人 $Staff 的 $Log_Item 发送失败!</div></br>";
		$OperationResult="N";
		}
	}
	
$User1Array=explode(",",$User1);
$User1Count=count($User1Array);
for($j=0;$j<$User1Count;$j++){
	$Staff=$User1Array[$j];
	$inSql1 = "INSERT INTO $DataPublic.smsdata  SELECT NULL,Number,'$DateTime','$Note','1','1','$Operator' FROM $DataPublic.staffmain WHERE Name='$Staff'";
	$inResult1 = @mysql_query($inSql1);
	if($inResult1){
		$Log.="&nbsp;&nbsp;抄送给联系人 $StaffSTR 的 $Log_Item 发送成功。$inSql1</br>";
		}
	else{
		$Log.="<div class='redB'>抄送给联系人 $StaffSTR 的 $Log_Item 发送失败!</div></br>";
		$OperationResult="N";
		}
	}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
