<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="出货文档附图";		//需处理
$upDataSheet="$DataIn.ch7_shippicture";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 6:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "subprogram/updated_model_3b.php";		break;
	case 7:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "subprogram/updated_model_3b.php";		break;
	default:
		//上传文档
		if($Picture!=""){	
			$FileDir="invoice";
			$OldFile=$Picture;
			$PreFileName=$oldPicture;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FileDir);
			$Log.="图档 $oldPicture 更新成功.<br>";
			}
		$Date=date("Y-m-d");
		$SetStr="Remark='$Remark',Date='$Date',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>