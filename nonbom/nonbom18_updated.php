<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM资产保养记录";		//需处理
$upDataSheet="$DataIn.nonbom7_care";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	default:
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/nonbom18/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="C".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
			 $AttachedStr=",Picture=1";
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			$OperationResult="N";			
			}
		}


		$ByReason=FormatSTR($ByReason);
		$SetStr="BarCode='$BarCode',ByNumber='$ByNumber',ByCompanyId='$ByCompanyId',ByReason='$ByReason',ByDate='$ByDate',Date='$DateTime',Operator='$Operator' $AttachedStr";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>