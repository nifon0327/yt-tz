<?php 
//电信-zxq 2012-08-01
//步骤1 $DataPublic.info1_business 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="标签打印机信息";		//需处理
$upDataSheet="$DataPublic.ot5_printer";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
if($Floor=="")$Floor=0;

//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		 $Log_Funtion="可用";	$SetStr="Estate=1";		$EstateStr=" AND Estate=0"; include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		 $Log_Funtion="禁用";	$SetStr="Estate=0";		$EstateStr=" AND Estate=1"; include "../model/subprogram/updated_model_3d.php";		break;
	default:
          $SetStr= "Floor='$Floor',WorkAdd='$WorkAdd',Identifier='$Identifier',Name='$Name',IP='$IP',Port='$Port'";
          include "../model/subprogram/updated_model_3a.php";
    break;
 }
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>