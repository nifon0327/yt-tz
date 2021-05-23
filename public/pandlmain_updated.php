<?php 
//电信---yang 20120801
//代码、数据库共享-EWEN 2012-08-15
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="损益表主项目";		//需处理
$upDataSheet="$DataPublic.sys8_pandlmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
//==========================颜色转换成RGB
function hex2rgb($hexColor) {
$color = str_replace('#', '', $hexColor);
if (strlen($color) > 3) {
$rgb = array(
'r' => hexdec(substr($color, 0, 2)),
'g' => hexdec(substr($color, 2, 2)),
'b' => hexdec(substr($color, 4, 2))
);
} else {
$color = str_replace('#', '', $hexColor);
$r = substr($color, 0, 1) . substr($color, 0, 1);
$g = substr($color, 1, 1) . substr($color, 1, 1);
$b = substr($color, 2, 1) . substr($color, 2, 1);
$rgb = array(
'r' => hexdec($r),
'g' => hexdec($g),
'b' => hexdec($b)
);
}
return $rgb;
}
$RGB=hex2rgb($SelColor);
//==========================
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
		$Name=FormatSTR($Name);
		$SetStr="ItemName='$Name',ColorCode='$SelColor',SortId='$SortId',Date='$DateTime',Operator='$Operator',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>