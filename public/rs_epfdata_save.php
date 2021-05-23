<?php 
//电信-ZX  2012-08-01
//步骤1： $DataPublic.sbdata 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="社保资料";			//需处理
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

$Counts=count($_POST['ListId']);
for($i=0;$i<$Counts;$i++){
	$StaffSTR=$_POST[ListId][$i];
	$inSql = "INSERT INTO $DataPublic.epfdata (Id,Number,Type,sMonth,eMonth,Note,Date,Estate,Locks,Operator) VALUES (NULL,'$StaffSTR','$Type','$sMonth','','$Note','$Date','1','0','$Operator')";
	$inResult = @mysql_query($inSql);
	if($inResult){
		$Log.="&nbsp;&nbsp;员工 $StaffSTR 的 $Log_Item 已加入.</br>";
		}
	else{
		$Log.="<div class='redB'>&nbsp;&nbsp;员工 $StaffSTR 的 $Log_Item 加入失败! $inSql </div></br>";
		$OperationResult="N";
		}
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
