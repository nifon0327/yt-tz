<?php 
//电信-joseph
//代码共享、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="部门资料";		//需处理
$upDataSheet="$DataPublic.branchdata";	//需处理
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
	case 108:
		$FilePath="../download/branch/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$setPicture=0;
		if ($upPicture!=""){	
			$OldFile=$upPicture;
		 	$PreFileName="card_" . $Id .".jpg";
		 	$uploadInfo=$PreFileName;     
			$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
		 	if($uploadInfo!=""){
				$Log="部门 $Id 的图片上传成功.<br>";
				$setPicture=1;
                }
			}

			if ($setPicture==1) $setPicture=",Picture='1'"; 
			else  $setPicture="";
			$upData="UPDATE  $upDataSheet  SET  Color = '$SelColor' $setPicture WHERE Id =$Id";
	       	$upDataResult=mysql_query($upData);
			if ($upDataResult){ 
				$Log.="部门: $Id 颜色设置更新成功!<br>";
	      		} 
			else{
				$Log.=$Log."<div class=redB>部门: $Id 颜色更新失败!</div> $upData<br>";
				$OperationResult="N";
				} 
		break;
	default:
		$Name=FormatSTR($Name);
        $SetStr="Name='$Name',TypeId='$TypeId',SortId='$SortId',Date='$DateTime',Operator='$Operator',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
        if($Manager!=""){
				$checkSql=mysql_query("SELECT Id FROM $DataIn.branchmanager WHERE BranchId='$Id'",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					$inRecode = "UPDATE $DataIn.branchmanager SET Manager='$Manager' WHERE BranchId='$Id'";
					}
				else{
					 $inRecode="INSERT INTO $DataIn.branchmanager (Id,BranchId,Manager)values(NULL,$Id,$Manager)";
					}
				$inRes=mysql_query($inRecode);	
             }
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>