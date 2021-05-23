<?php  
include "../model/modelhead.php";
include "../model/StaffFunction.php";
//步骤2：
$Log_Item="开发费用";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

//步骤3：需处理
$ItemArray=explode("~",$ItemStr);
$ItemId=$ItemArray[0];
$Provider=FormatSTR($Provider);
$Description=addslashes(FormatSTR($Description));
$Remark=addslashes(FormatSTR($Remark));
$Date=date("Y-m-d");
$cSign = getStaffcSign($Operator,$DataIn,$link_id);
$inRecode="INSERT INTO $DataIn.cwdyfsheet (Id,cSign,Mid,ItemId,TypeID,ModelDetail,Description,Amount,OutAmount,Currency,Remark,Provider,Bill,Estate,Locks,Date,Operator) VALUES (NULL,'$cSign','0','$ItemId','$TypeID','$ModelDetail','$Description','$Amount','$OutAmount','$Currency','$Remark','$Provider','0','1','1','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
$Id=mysql_insert_id();
if ($inAction){
	$Log="$TitleSTR 成功! <br>";
	//上传图档
	if($Attached!=""){//有上传文件
		$OldFile=$Attached;
		$PreFileName="DYF".$Id.".jpg";
		$FilePath="../download/dyf/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功！<br>";
			$sql = "UPDATE $DataIn.cwdyfsheet SET Bill='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
