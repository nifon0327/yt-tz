<?php 
//电信-ZX  2012-08-01
//MC、DP共用代码
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="IT任务";		//需处理
$upDataSheet="$DataPublic.it_worktask";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

if($Estate==2){$theDate="0000-00-00";}

//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$TaskContent=FormatSTR($TaskContent);
		if($TaskType!=""){
			$MisSTR=",TaskType='$TaskType',Estate='$Estate',Handled='$Handled',Remark='$Remark'";
			}
		if($TaskLevel!=""){
			$BonusSTR=",TaskLevel='$TaskLevel',BonusS='$BonusS',BonusH='$BonusH'";
			}
			$DateSTR=",Date='$theDate'";
	
		$SetStr="TaskContent='$TaskContent',TaskDate='$TaskDate',Sponsor='$Sponsor'".$MisSTR.$BonusSTR.$DateSTR;
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>