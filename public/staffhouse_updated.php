<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工购房补助信息";		//需处理
$upDataSheet="$DataIn.staff_housesubsidy";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	default:	
	
	    if($Attached!=""){//有上传文件
			$FileType=".jpg";
			$OldFile=$Attached;
			$FilePath="../download/staffhouseinfo/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			$PreFileName="H".$Id.$FileType;
			$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
			$AttachedStr = $Attached==""?"":",Attached ='$Attached'";
		}
		$SetStr="Number='$Number',Amount='$Amount',Remark='$Remark',Date='$Date',Operator='$Operator' $AttachedStr";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>