<?php 
//电信-zxq 2012-08-01
//步骤1 $DataPublic.info1_business 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="海关编号记录";		//需处理
$upDataSheet="$DataIn.customscode";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		
		include "../model/subprogram/updated_model_3d.php";		
		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		
		include "../model/subprogram/updated_model_3d.php";		
		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";		
		include "../model/subprogram/updated_model_3d.php";		
		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";		
		include "../model/subprogram/updated_model_3d.php";		
		break;
	default:
			$SetStr="HSCode='$HSCode',Remark='$Remark',Date='$Date',GoodsName='$GoodsName',
			Operator='$Operator',modifier='$Operator',modified='$DateTime'";
			include "../model/subprogram/updated_model_3a.php";
		break;
	}
	


$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>