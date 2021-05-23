<?php 
//电信-ZX  2012-08-01
//$DataPublic.net_cpdata 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="电脑资料";		//需处理
$upDataSheet="$DataPublic.net_cpdata";	//需处理
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
	default:		
		$CpName=FormatSTR($CpName);
		$Model=FormatSTR($Model);
		$SSNumber=FormatSTR($SSNumber);
		$IpAddress=FormatSTR($IpAddress);
		$MacAddress=FormatSTR($MacAddress);
		$Remark=FormatSTR($Remark);
		if($Attached!=""){//有上传文件
			$FileType=substr("$Attached_name", -4, 4);
			$OldFile=$Attached;
			$FilePath="../download/cpreport/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			$PreFileName=$CpName.$FileType;
			$AttachedName=UploadFiles($OldFile,$PreFileName,$FilePath);
			if ($Attached!=""){		
				$Log="附件上传成功.<br>";
				$AttachedSTR=",Attached='$AttachedName'";
				}
			else{
				$Log="<div class='redB'>附件上传失败！</div><br>";
				$AttachedSTR="";
				$OperationResult="N";
				}
			}
		$SetStr="IpAddress='$IpAddress',MacAddress='$MacAddress',CpName='$CpName',TypeId='$TypeId',CompanyId='$CompanyId',Model ='$Model',SSNumber='$SSNumber',BuyDate='$BuyDate',Warranty='$Warranty',User='$User',Date='$DateTime',Operator='$Operator',Remark='$Remark',Locks='0' $AttachedSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>