<?php 
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="小组资料";		//需处理
$upDataSheet="$DataIn.staffgroup";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$checkSql=mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE Name LIKE '$StaffName'",$link_id);
		if($checkRow=mysql_fetch_array($checkSql)){
			$GroupLeader=$checkRow["Number"];
			$GroupName=FormatSTR($GroupName);
			//检查对应的员工ID
			$SetStr="GroupName='$GroupName',BranchId='$BranchId',GroupLeader='$GroupLeader',
			TypeId='$TypeId',Date='$DateTime',Operator='$Operator',Locks='0'";
			include "../model/subprogram/updated_model_3a.php";
			}
		else{
			$OperationResult="N";
			$Log="<div class='redB'>组长($StaffName)资料提取失败!</div>";
			}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>