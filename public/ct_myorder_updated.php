<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="点餐记录";		//需处理
$upDataSheet="$DataPublic.ct_myorder";	//需处理
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
		$Log_Funtion="可用";	$SetStr="Estate=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 17:
	   $fromWebPage=$funFrom."_m";
		$Log_Funtion="审核";		$SetStr="Estate=0,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 15:
      	$fromWebPage=$funFrom."_m";
		$Log_Funtion="退回";		$SetStr="Estate=1,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;


case 20://?
		$Log_Funtion="主点餐单更新";
	   $fromWebPage=$funFrom."_m";
		$Remark=FormatSTR($Remark);
        if($Attached!=""){//有上传文件
		            $FileType=".jpg";
		            $OldFile=$Attached;
		            $FilePath="../download/ctmyorderbill/";
		            if(!file_exists($FilePath)){
			            makedir($FilePath);
			           }
		             $PreFileName=$Mid.$FileType;
		             $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		            if($Attached){
			             $Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
			            }
		       else{
			          $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			           $OperationResult="N";			
			          }
          }
       $fileStr="";
       if($Attached!="")$fileStr=" , Bill='$Attached'";

		$upSql = "UPDATE $DataPublic.ct_myordermain  SET Remark='$Remark'  $fileStr WHERE Id='$Mid'";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log.="主点餐单资料更新成功.<br>";
			}
		else{
			$Log.="<div class='redB'>主点餐单资料更新失败! $upSql </div><br>";
			$OperationResult="N";
			}
		break;


	default:
	   $fromWebPage=$funFrom."_m";
	    $Amount=$Qty*$Price;
		//$SetStr="Qty='$Qty',Price='$Price',Amount='$Amount',Date='$DateTime',Locks='0',Operator='$Operator'";
		$SetStr="Qty='$Qty',Price='$Price',Amount='$Amount',Remark='$Remark' ";
		include "../model/subprogram/updated_model_3a.php";
		$upSql = "UPDATE $DataPublic.ct_menu  SET Price='$Price'   WHERE Id='$MenuId'";
		$upResult = mysql_query($upSql);
		
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>