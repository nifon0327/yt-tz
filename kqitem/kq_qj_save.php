<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="请假记录";			//需处理
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
//锁定表?多表操作不能锁定
$StartDate=$StartDate." ".$StartTime.":00";
$EndDate=$EndDate." ".$EndTime.":00";
$Date=date("Y-m-d");
$Reason=FormatSTR($Reason);
$MonthTemp=substr($StartDate,0,7);
	//新加条件,加入的月份未生成
	if($DataIn !== 'ac'){
		$inRecode="INSERT INTO $DataPublic.kqqjsheet 
		SELECT NULL,Number,'$StartDate','$EndDate','$Reason','0','$Type','$bcType','0','$Date','0','$Operator','$DateTime' 
		FROM $DataPublic.staffmain WHERE Number='$Number' 
		AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month='$MonthTemp' ORDER BY Id)";
	}else{
		$inRecode="INSERT INTO $DataPublic.kqqjsheet 
		SELECT NULL,Number,'$StartDate','$EndDate','$Reason','0','$Type','$bcType','0','$Date','0','$Operator','$DateTime', 0, 0, null, NOW(), null, NOW()
		FROM $DataPublic.staffmain WHERE Number='$Number' 
		AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month='$MonthTemp' ORDER BY Id)";
	}
	$inAction=@mysql_query($inRecode);
//解锁表
	if($inAction){ 
		$Log="$TitleSTR 操作成功!(如果记录未保存则输入的记录无效)<br>";
		} 
	else{
		$Log="<div class=redB>$TitleSTR 操作失败! $inRecode </div><br>";
		$OperationResult="N";
		}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
