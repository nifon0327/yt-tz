<?php 
//电信-EWEN
include "../model/modelhead.php";
include "../model/stafffunction.php";
//步骤2：
$Log_Item="行政费用记录";			//需处理
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
$TypeIdArray=explode("|",$TypeId);
$TypeId=$TypeIdArray[0];
$OtherId=$OtherId==""?0:$OtherId;
$Property=$Property==""?0:$Property;
$cSign = $cSign ==""? getStaffcSign($Operator,$DataIn,$link_id):$cSign;
$inRecode="INSERT INTO $DataIn.hzqksheet (Id,cSign,Mid,Content,Amount,Currency,Bill,ReturnReasons,Date,Estate,TypeId,OtherId,Property,Locks,Operator) VALUES (NULL,'$cSign','0','$Content', '$Amount','$Currency','0','','$theDate','1','$TypeId','$OtherId','$Property','1','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){
	$Log.="&nbsp;&nbsp; $TitleSTR 成功! $inRecode <br>";
	$Id=mysql_insert_id();
	//上传文件
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/cwadminicost/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="H".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
			$Attached=1;
			//更新刚才的记录
			$sql = "UPDATE $DataIn.hzqksheet SET Bill='1' WHERE Id=$Id";
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
