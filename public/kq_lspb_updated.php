<?php 
//电信-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="临时排班记录";		//需处理
$upDataSheet="$DataIn.kqlspbb";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		//条件：当月未生成月考勤统计
		$InTime=$StartDate." ".$STime;
		$MonthTemp=substr($StartDate,0,7);
		if($TimeType==0){
			$DayTemp=date("Y-m-d",strtotime("$StartDate+1 days"));
			$OutTime=$DayTemp." ".$ETime;
			}
		else{
			$OutTime=$StartDate." ".$ETime;
			}
		//条件//该月的考勤统计未生成
		$OtherWhere="AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month='$MonthTemp' ORDER BY Number)";
		$SetStr="InTime='$InTime',OutTime='$OutTime',InLate='$InLate',OutEarly='$OutEarly',TimeType='$TimeType',RestTime='$RestTime',Locks='0',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
