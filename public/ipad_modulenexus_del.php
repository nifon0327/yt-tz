<?php 
//电信-ZX
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
$funFrom="ipad_modulenexus";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="模块关系";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//删除模块关系时同时清除权限
if($Action==0){//不处理权限
	$Del = "DELETE FROM $DataPublic.sc4_modulenexus WHERE ModuleId=$Mid and dModuleId=$Did"; 
	$result = mysql_query($Del);
	if($result){
		$Log="上级 $Mid 和下级 $Did 的关系删除成功!<br>";
		}
	else{
		$Log="<div class='redB'>上级 $Mid 和下级 $Did 的关系删除失败!</div><br>";
		$OperationResult="N";
		}
	}
else{			//处理权限
	$Del = "DELETE FROM $DataPublic.sc4_modulenexus WHERE ModuleId=$Mid and dModuleId=$Did"; 
	$result = mysql_query($Del);
	if($result){
		$Log="上级 $Mid 和下级 $Did 的关系删除成功！<br>";
		//清空权限
		$upSql= "UPDATE $DataIn.sc4_upopedom SET Action=0 WHERE ModuleId=$Did";
		$upResult = mysql_query($upSql);
		if($upResult){
			$Log=$Log."有关 $Did 的权限全部清除成功！<br>";
			}
		else{
			$Log=$Log."<div class='redB'>有关 $Did 的权限全部清除失败!</div><br>";
			$OperationResult="N";
			}
		}
	else{
		$Log="<div class='redB'>上级 $Mid 和下级 $Did 的关系删除失败!</div><br>";
		$OperationResult="N";
		}
	}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>