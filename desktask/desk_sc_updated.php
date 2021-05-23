<?php   
//电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_printtasks";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="打印任务更新";		//需处理
$upDataSheet="$DataIn.sc3_printtasks";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$Ids="";
for($i=0;$i<count($checkid);$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
$x=1;
switch($ActionId){
	case 20:
		$Log_Funtion="已打印"; $SetStr="Estate='0'";		include "../model/subprogram/updated_model_3d.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>