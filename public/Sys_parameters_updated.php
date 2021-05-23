<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="系统参数";		//需处理
$upDataSheet="$DataIn.sys6_parameters";	//需处理
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
		$Remark=FormatSTR($Remark);
		$pValue=FormatSTR($pValue);
		if($OldpValue!=$pValue || $OldActionId!=$ActionId){
			$InsertLogSql = "INSERT INTO $DataIn.sys6_parameters_log  
			SELECT NULL,PNumber,'$ActionId','$OldActionId',Name,'$pValue','$OldpValue',Remark,Estate,Locks,
			Date,Operator,PLocks,creator,created,modifier,modified FROM $DataIn.sys6_parameters WHERE Id='$Id'";
		    $InsertLogRow = mysql_query($InsertLogSql,$link_id);
		}
		$SetStr="Name='$Name',ActionId='$ActionId',pValue='$pValue',Remark='$Remark',
		Date='$DateTime',Operator='$Operator',modifier='$Operator',modified='$DateTime'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>