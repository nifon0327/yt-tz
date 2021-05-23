<?php 
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
switch($Type){
	case 2:$LogSTR="客户";break;
	case 3:$LogSTR="供应商";break;
	case 4:$LogSTR="Forward";break;
	case 5:$LogSTR="快递公司";break;
	}
//步骤2：
$Log_Item=$LogSTR."联系人资料";		//需处理
$upDataSheet="$DataIn.linkmandata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "subprogram/updated_model_3b.php";		break;
	case 6://禁用则同时禁用登录帐号
		$KillOnline=1;
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "subprogram/updated_model_3b.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		if($Defaults!=1){//如果设为默认联系人，则需先清除原默认人
			$sql = "UPDATE $DataIn.linkmandata SET Defaults='1' WHERE CompanyId='$CompanyId'";
			$result = mysql_query($sql);
			$Defaults=0;
			}
		//默认联系人信息
		$Linkman=FormatSTR($Linkman);
		$Nickname=FormatSTR($Nickname);
		$Headship=FormatSTR($Headship);
		$Mobile=FormatSTR($Mobile);
		$Tel==FormatSTR($Tel);
		$Email=FormatSTR($Email);
		$Remark=FormatSTR($Remark);
		$Date=date("Y-m-d");
		$SetStr="CompanyId='$CompanyId',Name='$Linkman',Sex='$Sex',Nickname='$Nickname',
		Headship='$Headship',Mobile='$Mobile',Tel='$Tel',MSN='$MSN',SKYPE='$SKYPE',Email='$Email',Remark='$Remark',
		Type='$Type',Date='$Date',Operator='$Operator',Defaults='$Defaults',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&ComeFrom=$ComeFrom&Type=$Type";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
