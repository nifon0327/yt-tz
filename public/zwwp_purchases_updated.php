<?php 
//ewen 1012-12-16
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="总务采购记录";		//需处理
$upDataSheet="$DataIn.zwwp4_purchase";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="审核退回";$SetStr="Estate=1,Locks=1";include "../model/subprogram/updated_model_3d.php";		break;
	case 17:
		$Log_Funtion="审核";   $SetStr="Estate=3,Locks=0";	include "../model/subprogram/updated_model_3d.php";		break;
	case 52:
		$Log_Funtion="申购";$SetStr="Estate=2,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$Remark=FormatSTR($Remark) ;
		$SetStr="Date='$Date',Operator='$Operator',Qty='$Qty',Remark='$Remark',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
        break;
	  break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>