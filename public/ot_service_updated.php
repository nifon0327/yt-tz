<?php 
//步骤1 $upDataSheet="$DataPublic.ot1_service"; 二合一已更新
//电信-joseph
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="了修项目";		//需处理
$upDataSheet="$DataPublic.ot1_service";	//需处理
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
		$Remark1=FormatSTR($Remark1);
		if($Sign==1){
			$Remark2=FormatSTR($Remark2);$Remark3=FormatSTR($Remark3);
			$Locks=$Estate==1?1:0;
			$SignStr=",Remark2='$Remark2',Remark3='$Remark3',Estate='$Estate',Locks='$Locks'";
			}
		
		$SetStr="Remark1='$Remark1',Servicer='$Servicer' $SignStr";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>