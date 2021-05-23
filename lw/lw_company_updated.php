<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="劳务公司资料";		//需处理
$upDataSheet="$DataPublic.lw_company";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$Forshort=FormatSTR($Forshort);
		$Company=FormatSTR($Company);
		$Tel=FormatSTR($Tel);
		$LinkMan=FormatSTR($LinkMan);
		$Address=FormatSTR($Address);
		$Bank=FormatSTR($Bank);
		$Remark=FormatSTR($Remark);
		$mainSql = "UPDATE $upDataSheet SET Forshort='$Forshort',Company='$Company',Tel='$Tel',LinkMan='$LinkMan',Address='$Address',
		Bank='$Bank',Remark='$Remark',modifier='$Operator',modified='$DateTime' WHERE Id='$Id'";
		$Result = mysql_query($mainSql);
		if($Result){
			$Log="劳务公司 $Forshort 的资料更新成功.<br>";		
		}
		else{
			$Log="<div class=redB>劳务公司 $Forshort 的资料更新失败! $mainSql </div></br>";
			$OperationResult="N";
		}
			
	break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>