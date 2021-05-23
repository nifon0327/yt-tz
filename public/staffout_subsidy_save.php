<?php 
//电信-EWEN
include "../model/modelhead.php";
include "../model/StaffFunction.php";
//步骤2：
$Log_Item="员工离职补助记录";			//需处理
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
$cSign = getStaffcSign($Number,$DataIn,$link_id);
if($PaySign!=1){
   $totalTimes = ceil($TotalRate);
    for($k=1;$k<=$totalTimes;$k++){    
            if ($k==$totalTimes && $TotalRate!=$totalTimes){
	               $AveAmount=$Amount-$AveAmount*floor($TotalRate);
            }
			$inRecode="INSERT INTO $DataIn.staff_outsubsidysheet (Id,cSign,Mid,Number,TypeId,Content,AveAmount,Amount,TotalRate,Time,PaySign,Currency,Bill,ReturnReasons,Date,
            Estate,Locks,Operator,Auditor) VALUES (NULL,'$cSign','0','$Number','$TypeId','$Content', '$AveAmount','$AveAmount','$TotalRate','$k','0','$Currency','0','','$theDate','1','1','$Operator','0')";
			$inAction=@mysql_query($inRecode);
			if ($inAction){
				$Log.="&nbsp;&nbsp; $TitleSTR 成功!  <br>";//$inRecode
				$Id=mysql_insert_id();
				if($Attached!=""){//有上传文件
					$FileType=".jpg";
					$OldFile=$Attached;
					$FilePath="../download/staff_subsidy/";
					if(!file_exists($FilePath)){
						makedir($FilePath);
						}
					$PreFileName=$Number.$FileType;
					$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
					if($Attached){
						$Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
						$Attached=1;
						$sql = "UPDATE $DataIn.staff_outsubsidysheet SET Bill='1' WHERE Id=$Id";
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
         }
   }
else{  //一次性支付
			$inRecode="INSERT INTO $DataIn.staff_outsubsidysheet (Id,Mid,Number,TypeId,Content,AveAmount,Amount,TotalRate,Time,PaySign,Currency,Bill,ReturnReasons,Date,
 Estate,Locks,Operator,Auditor) VALUES (NULL,'0','$Number','$TypeId','$Content', '$AveAmount','$Amount','$TotalRate','1','$PaySign','$Currency','0','','$theDate','1','1','$Operator','0')";
			$inAction=@mysql_query($inRecode);
			if ($inAction){
				$Log.="&nbsp;&nbsp; $TitleSTR 成功! $inRecode <br>";
				$Id=mysql_insert_id();
				if($Attached!=""){//有上传文件
					$FileType=".jpg";
					$OldFile=$Attached;
					$FilePath="../download/staff_subsidy/";
					if(!file_exists($FilePath)){
						makedir($FilePath);
						}
					$PreFileName=$Number.$FileType;
					$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
					if($Attached){
						$Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
						$Attached=1;
						$sql = "UPDATE $DataIn.staff_outsubsidysheet SET Bill='1' WHERE Id=$Id";
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
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
