<?php 
	//电信-joseph
	include "../model/modelhead.php";
	include "../model/kq_YearHolday.php";
	//步骤2：
	$Log_Item="补休记录";			//需处理
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_save";
	$_SESSION["nowWebPage"]=$nowWebPage;
	$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
	//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
	$Log_Funtion="保存";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$Number = $Login_P_Number;
	$OperationResult="Y";
	//步骤3：需处理

	$StartDate=$StartDate." ".$StartTime.":00";
	$EndDate=$EndDate." ".$EndTime.":00";
	$Date=date("Y-m-d");
	$note=FormatSTR($note);
	
	$bxAllDays=($CalculateType == "0")?calculateHours($StartDate, $EndDate):GetBetweenDateDays($Number,$StartDate,$EndDate,"1",$DataIn,$DataPublic,$link_id);
	if($bxAllDays <= 0)
	{
		$Log="<div class=redB>$TitleSTR 操作失败! $overhours $inRecode </div><br>";
		$OperationResult="N";
	}
	else
	{
		$inRecode="Insert Into $DataPublic.bxsheet (Id, Number, StartDate, EndDate, Note, Date, type, Reason, Estate, Operator, Checker) Values (NULL, '$Login_P_Number', '$StartDate', '$EndDate', '$note', '$Date', '$CalculateType', NULL, '1', '$Operator', NULL)";
		$inAction=@mysql_query($inRecode);
	
		if($inAction)
		{ 
			$Log="$TitleSTR 操作成功!(如果记录未保存则输入的记录无效)<br>";
		} 
	else{
			$Log="<div class=redB>$TitleSTR 操作失败! $overhours $inRecode </div><br>";
			$OperationResult="N";
		}
	}
		//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES 	('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";
?>
