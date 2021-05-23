<?php 
//步骤1 $DataIn.development 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"] = $nowWebPage;
//步骤2：
$Log_Item="研发发项目";		//需处理
$upDataSheet="$DataIn.projectset_sheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		  $Log_Funtion="锁定";	$SetStr="Locks=0";include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		  $Log_Funtion="解锁";	$SetStr="Locks=1";include "../model/subprogram/updated_model_3d.php";		break;
	case 15:
	      $Log_Funtion="退回";
		  $SetStr="Estate=1"; include "../model/subprogram/updated_model_3d.php";		break;
		  
	case 17:
	      $fromWebPage=$funFrom."_m";
	      $Log_Funtion="新研发项目审批通过";

		  $SetStr="Estate=1,Approval=$Operator,ApprovalDate=$Date";   include "../model/subprogram/updated_model_3d.php";	
	  break;
   default:
		$FilePath="../download/projectset/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
		}
		
		$uploadSTR='';
		if($Attached!=""){
			$OldFile=$Attached;
			$FileType=substr("$Attached_name", -4, 4);
			$PreFileName=$ItemId.$FileType;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			$uploadSTR=$uploadInfo==""?"":" ,Attached= $Attached";
		}
		$ItemName=FormatSTR($ItemName);
	    $Description=FormatSTR($Description);
	
		$Participant="";
		if($_POST['ListId']){//如果指定了操作对象
			$Counts=count($_POST['ListId']);
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];
				$Participant.=$Participant==""?$thisId:",".$thisId;
			}
		}
		
		$Remark=addslashes($Remark);
		$SetStr="ItemName='$ItemName',Description='$Description',Principal='$Principal',Participant='$Participant',
		StartDate='$StartDate',EstimatedDate='$EstimatedDate',Remark='$Remark',Estate='$Estate' $uploadSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  