<?php 
//电信-ZX  2012-08-01
//步骤1 $upDataSheet="$DataIn.bulletin"; 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="公告";		//需处理
$upDataSheet="$DataIn.bulletin";	//需处理
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
		if($Attached!=""){//有上传文件
			$FileType=substr("$Attached_name", -4, 4);
			$OldFile=$Attached;
			$FilePath="../download/bulletinattached/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			if($oldAttached!=""){
				$PreFileName=$oldAttached;
				}
			else{
				$datelist=newGetDateSTR();
				$PreFileName=$datelist.$FileType;
				}
			$upAttached=UploadPictures($OldFile,$PreFileName,$FilePath);
			if($upAttached!=""){
				$AttachedSTR=",Attached='$PreFileName'";
				}
			}
		$Date=date("Y-m-d");
		$Caption=FormatSTR($Caption);
		$SetStr="Caption='$Caption',cSign='$cSign',Date='$Date',Operator='$Operator',Locks='0' $AttachedSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>