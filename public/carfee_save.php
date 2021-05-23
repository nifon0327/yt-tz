<?php 
//电信-EWEN
include "../model/modelhead.php";
include "../model/stafffunction.php";
//步骤2：
$Log_Item="车辆费用记录";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Content=FormatSTR($Content);
$cSign = $cSign ==""? getStaffcSign($Operator,$DataIn,$link_id):$cSign;

$inRecode="INSERT INTO $DataIn.carfee (Id,cSign,Mid,CarId,Content,Amount,Currency,sCourses,eCourses,Bill,TypeId,FirstId,
ReturnReasons,Date,Estate,Locks,Operator) VALUES (NULL,'$cSign','0','$CarId','$Content', '$Amount','$Currency','$sCourses','$eCourses','0','$TypeId','$FirstId','','$theDate','1','1','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){
	$Log.="&nbsp;&nbsp; $TitleSTR 成功! $inRecode <br>";
	$Id=mysql_insert_id();
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/carfee/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="C".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
			$Attached=1;
			//更新刚才的记录
			$sql = "UPDATE $DataIn.carfee SET Bill='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			$OperationResult="N";			
			}
		}
	}
else{
	$Log.="<div class=redB>&nbsp;&nbsp; $TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
