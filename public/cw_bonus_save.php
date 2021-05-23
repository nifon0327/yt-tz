<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="其它奖金记录";			//需处理
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
$Counts=count($_POST["ListId"]);
for($i=0;$i<$Counts;$i++){
		$thisNumber=$_POST["ListId"][$i];
		
	 $CheckNumberRow = mysql_fetch_array(mysql_query("SELECT BranchId,JobId FROM $DataIn.staffmain WHERE Number = '$thisNumber'",$link_id));
		 $BranchId = $CheckNumberRow["BranchId"];
		 $JobId = $CheckNumberRow["JobId"];
		 
		$inRecode="INSERT INTO $DataIn.cw20_bonussheet (Id,Mid,Number,BranchId,JobId,Content,Amount,Currency,Bill,ReturnReasons,Date,Estate,Locks,Operator) 
                                   VALUES (NULL,'0','$thisNumber','$BranchId','$JobId','$Content', '$Amount','$Currency','0','','$theDate','1','1','$Operator')";
		$inAction=@mysql_query($inRecode);
		if ($inAction){
			$Log.="&nbsp;&nbsp; $TitleSTR 成功! $inRecode <br>";
			$Id=mysql_insert_id();
			if($Attached!=""){//有上传文件
				$FileType=".jpg";
				$OldFile=$Attached;
				$FilePath="../download/cw_bonus/";
				if(!file_exists($FilePath)){
					makedir($FilePath);
					}
				$PreFileName="C".$Id.$FileType;
				$newAttached=UploadFiles($OldFile,$PreFileName,$FilePath);
				if($newAttached){
					$Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
					$sql = "UPDATE $DataIn.cw20_bonussheet SET Bill='1' WHERE Id=$Id";
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
