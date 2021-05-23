<?php 
//电信-joseph
include "../model/modelhead.php";
include "../model/kq_YearHolday.php";
include "../model/subprogram/qjCalculate.php";
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
$Number = $Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
//锁定表?多表操作不能锁定
//$LockSql=" LOCK TABLES kqqjsheet WRITE";$LockRes=@mysql_query($LockSql);

$StartDate=$StartDate." ".$StartTime.":00";
$EndDate=$EndDate." ".$EndTime.":00";
$Date=date("Y-m-d");
$Reason=FormatSTR($Reason);
$MonthTemp=substr($StartDate,0,7);
mysql_query('START TRANSACTION');
$inAction = true;
	//新加条件,加入的月份未生成
	if($Type != "5"){
		$qjArray = qjCalculate($StartDate, $EndDate);
		for($i=0; $i<count($qjArray); $i++){
			$tempQj = $qjArray[$i];
			$start = $tempQj[0];
			$end = $tempQj[1];
			$inRecode="INSERT INTO $DataPublic.kqqjsheet 
				   SELECT NULL,'$Operator','$start','$end','$Reason','0','$Type','$bcType','1','$Date','0','$Operator','$DateTime','0', 0,'$Operator',NOW(),'$Operator',NOW()
				   FROM $DataPublic.staffmain WHERE Number='$Operator' 
				   AND Number NOT IN (SELECT Number FROM $DataIn.cwxzsheet WHERE Month='$MonthTemp' ORDER BY Id)";
			if(!mysql_query($inRecode)){
				$inAction = false;
				mysql_query('ROLLBACK ');
				break;
			}
		}
	}
	else
	{
		//补休时间
		$checkBxHours = "Select sum(hours) as Hours From $DataPublic.bxsheet Where Number = '$Number'";
		$checkBxHoursResult = mysql_query($checkBxHours);
		if($checkBxHoursRow = mysql_fetch_assoc($checkBxHoursResult))
		{
			$hours = $checkBxHoursRow["Hours"];
		}
		$hours = ($hours == "")?"0":$hours;
		//已经请的补休工时
		$usedBx = 0;
		$bxQjCheckSql = "Select * From $DataPublic.kqqjsheet Where Number = '$Number' and Type= '5' AND DATE_FORMAT(StartDate,'%Y')>='2013'";
		$bxQjCheckResult = mysql_query($bxQjCheckSql);
		while($bxQjCheckRow = mysql_fetch_assoc($bxQjCheckResult))
		{
			$startTime = $bxQjCheckRow["StartDate"];
			$endTime = $bxQjCheckRow["EndDate"];
			$bcType = $bxQjCheckRow["bcType"];
				
			$time = GetBetweenDateDays($Number,$startTime,$endTime,$bcType,$DataIn,$DataPublic,$link_id);
			$usedBx += $time;
		}
		$restHours = intval($hours)-intval($usedBx);
		$qjHours = GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);
		if($restHours < intval($qjHours)){
			$overhours = "请假时间($qjHours)大于补休时间($restHours)";
		}else{
			$qjArray = qjCalculate($StartDate, $EndDate);
			for($i=0; $i<count($qjArray); $i++){
				$tempQj = $qjArray[$i];
				$start = $tempQj[0];
				$end = $tempQj[1];
				$inRecode="INSERT INTO $DataPublic.kqqjsheet 
				   SELECT NULL,'$Operator','$start','$end','$Reason','0','$Type','$bcType','1','$Date','0','$Operator','$DateTime','0', 0,'$Operator',NOW(),'$Operator',NOW()
				   FROM $DataPublic.staffmain WHERE Number='$Operator' 
				   AND Number NOT IN (SELECT Number FROM $DataIn.cwxzsheet WHERE Month='$MonthTemp' ORDER BY Id)";
				if(!mysql_query($inRecode)){
					$inAction = false;
					mysql_query('ROLLBACK ');
					break;
				}
			}
		}

	}
	
	//$inAction=@mysql_query($inRecode);
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
	if($inAction){ 
		mysql_query('COMMIT');
		$Log="$TitleSTR 操作成功!(如果记录未保存则输入的记录无效)<br>";
		} 
	else{
		$Log="<div class=redB>$TitleSTR 操作失败! $overhours $inRecode </div><br>";
		$OperationResult="N";
		}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
