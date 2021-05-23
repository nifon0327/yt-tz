<?php  
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="假日资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="新增保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$HoursTemp=ceil(abs(strtotime($StartDate)-strtotime($EndDate))/3600);//向上取整
$Days=intval($HoursTemp/24);//取整求相隔天数
for($k=0;$k<=$Days;$k++){
	$DayTemp=date("Y-m-d",strtotime("$StartDate+$k days"));
	$inRecode="INSERT INTO $DataPublic.kqholiday (Id,Name,Date,jbTimes,Type,Sign,Locks,Operator) 	VALUES (NULL,'$Name','$DayTemp','$jbTimes','$Type','$Sign','0','$Operator')";
	$inAction=@mysql_query($inRecode);
	if ($inAction){ 
		$Log.="名称为$Name 的 $TitleSTR 成功!<br>";
		} 
	else{
		$Log.="<div class=redB>名称为$Name 的 $TitleSTR 失败!</div><br>";
		$OperationResult="N";
		}
	}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
