<?php 
//电信-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="忘签记录";		//需处理
$upDataSheet="d7check.fakecheckinout";	//需处理
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
		//更新条件,该月份的月考勤未统计
		$Pagination="";
		$Page="";		
		$theMonth=substr($CheckDate,0,7);		
		$CheckTime=$CheckDate." ".$CheckTime.":00";
		$KrSign=0;
		if($CheckType=="K"){
			$CheckType="O";
			$KrSign=1;
			}
		
		//条件：未生成月考勤统计
		$OtherWhere="AND Number NOT IN (SELECT kqdata.Number FROM $DataIn.kqdata WHERE Month='$theMonth' ORDER BY Number,Month DESC)";
		$SetStr = "KrSign='$KrSign',CheckType='$CheckType',CheckTime='$CheckTime',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		$chooseMonth=$theMonth;
		break;
	}
$ALType="From=$From&chooseMonth=$chooseMonth&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>