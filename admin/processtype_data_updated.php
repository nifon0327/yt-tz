<?php   
//步骤1 $DataPublic.info1_business 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="工序分类资料";		//需处理
$upDataSheet="$DataIn.process_type";	//需处理
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
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "subprogram/updated_model_3b.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "subprogram/updated_model_3b.php";		break;

        default:
 
     $Remark=FormatSTR($Remark);
     $SetStr="gxTypeName='$gxTypeName',SortId='$SortId',Color='$Color',Remark='$Remark',Date='$Date',Operator='$Operator'";
     include "../model/subprogram/updated_model_3a.php";
    break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>