<?php 
//代码数据共享-EWEN 2012-11-25
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="总务用品资料";		//需处理
$upDataSheet="$DataPublic.zwwp3_data";	//需处理
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
    case 17://审核通过
			$Log_Funtion="审核名称";
			$upDataSheet="$DataIn.zw3_purchaset";	//需处理
			$SetStr="Estate=1";
			include "../model/subprogram/updated_model_3d.php";
			$fromWebPage=$funFrom."_m";
			break;
	default:
		$FilePath="../download/zwwp/";
		$PreFileName1="Z".$Id.".jpg";
		if($Attached!=""){
			$OldFile1=$Attached;
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
			$AttachedSTR=$uploadInfo1==""?",Attached='0'":",Attached='1'";
			}
		if($AttachedSTR=="" && $oldAttached!=""){//没有上传文件并且已选取删除原文件
			$FilePath1=$FilePath."/$PreFileName1";
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				}
			$AttachedSTR=",Attached='0'";
			}
		$GoodsName=FormatSTR($GoodsName) ;
		if ($OldGoodsName!=$GoodsName){
			$GoodsNameStr="GoodsName='$GoodsName',Estate='2',";
		}else{
			$GoodsNameStr="";
		}
		$SetStr="$GoodsNameStr TypeId='$TypeId',Date='$DateTime',Operator='$Operator',Locks='0' $AttachedSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>