<?php   
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="治工具资料";		//需处理
$upDataSheet="$DataIn.fixturetool";	//需处理
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
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "subprogram/updated_model_3b.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "subprogram/updated_model_3b.php";		break;

    default:
		$FilePath="../download/ztools/";
		if(!file_exists($FilePath)){
		    makedir($FilePath);
		}
		if($Picture!=""){
		   $oldPicture=$Picture;
		   $Newpicture=$ToolsId.".jpg";
		   $upInfo=UploadFiles($oldPicture,$Newpicture,$FilePath);
		   $PictureInfo=$upInfo==""?"":" ,Picture='1'";
		}
		$Remark=FormatSTR($Remark);
		$SetStr="ToolsName='$ToolsName',UseTimes='$UseTimes',Type='$typeId',
		Remark='$Remark',modified='$DateTime',modifier='$Operator' $PictureInfo";
		include "../model/subprogram/updated_model_3a.php";
    break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>