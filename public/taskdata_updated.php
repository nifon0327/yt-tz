<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="特殊功能";		//需处理
$upDataSheet="$DataPublic.tasklistdata";	//需处理
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
	case 9:
		$Log_Funtion="权限设置";
		$Numbers="";
		//取Number
		for($i=0;$i<=count($checkid);$i++){
			$Number=$checkid[$i];
			if ($Number!=""){
				$Numbers=$Numbers==""?$Number:($Numbers.",".$Number);
				}
			}
		//清空此功能的所有权限
		$delSql="DELETE FROM $DataIn.taskuserdata WHERE ItemId='$ItemId'";
		$delRresult = mysql_query($delSql);
		//重新加入权限
		$inRecode="INSERT INTO $DataIn.taskuserdata SELECT NULL,'$ItemId',Number,'1','0','0','$Operator','$DateTime',null,null,null,null FROM $DataIn.usertable WHERE Number IN($Numbers)";
		$inAction=@mysql_query($inRecode);
		if ($inAction){ 
			$Log="$TitleSTR 成功!<br>";
			} 
		else{
			$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
			$OperationResult="N";
			} 
		break;
	default:
		$Date=date("Y-m-d");
		$Title=FormatSTR($Title);
		$Description=FormatSTR($Description);
		$Extra=FormatSTR($Extra);
		$SetStr="Title='$Title',Description='$Description',Extra='$Extra',TypeId='$TypeId',
		InCol='$InCol',Oby='$Oby',Locks='0',Date='$Date',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>