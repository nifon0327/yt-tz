<?php 
//电信-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"] = $nowWebPage;
//步骤2：
$Log_Item="标准图文档";		//需处理
$upDataSheet="$DataIn.doc_standarddrawing";	//需处理
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
	//检查之前的图档
		if($Attached!=""){//有上传文件
			$FileType=substr("$Attached_name", -4, 4);
			$QueryFile=$Attached;
			$FilePath="../download/standarddrawing/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			if($Ohycfile!=""){
				$PreFileName=$Ohycfile;
				}
			else{
				$datelist=newGetDateSTR();
				$PreFileName=$datelist.$FileType;
				}
			$upAttached=UploadPictures($QueryFile,$PreFileName,$FilePath);
			if($upAttached!=""){
				$AttachedSTR=",FileName='$PreFileName'";
				}
			}
		$Date=date("Y-m-d");
		$FileRemark=FormatSTR($FileRemark);
		$SetStr="FileRemark='$FileRemark',FileType='$FileType',CompanyId='$CompanyId',ProductType='$ProductType',Date='$Date',Operator='$Operator',Locks='0' $AttachedSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
