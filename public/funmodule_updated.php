<?php 
//电信-joseph
//代码、数据库共享，加标识-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="功能模块";		//需处理
$upDataSheet="$DataPublic.funmodule";	//需处理
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
		$Log_Funtion="锁定";	$SetStr="Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";		include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$ModuleName=FormatSTR($ModuleName);
		$Parameter=FormatSTR($Parameter);
		
		$KeyWebPage="";   //add by zx 2013-01-21为了避免权限传递有误而改的,权限设置转移到read_model_3.php中
		if ($Parameter!="")
		{
			$pos = strrpos($Parameter, ".php");
			if($pos !== FALSE){
				$substr1=substr($Parameter,0,$pos);
				//echo "$substr1";
				$pos1 = strrpos($substr1, "/");  //有些是直接文件名的，没有
				if($pos1 == FALSE){
					$pos1=-1;	
				}
				$KeyWebPage=substr($substr1,$pos1+1);
			}
		}		
		
		
		$SetStr="ModuleName='$ModuleName',Parameter='$Parameter',KeyWebPage='$KeyWebPage',
		TypeId='$TypeId',OrderId='$OrderId',Estate='$Estate',Locks='0',Date='$DateTime',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>