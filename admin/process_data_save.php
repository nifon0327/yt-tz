<?php   
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="加工工序资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="新增记录";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

//$LockSql=" LOCK TABLES $DataIn.process_data WRITE"; $LockRes=@mysql_query($LockSql);
$maxSql = mysql_query("SELECT MAX(ProcessId) AS Mid FROM $DataIn.process_data",$link_id);
$ProcessId=mysql_result($maxSql,0,"Mid");
if($ProcessId){
	$ProcessId=$ProcessId+1;
	}
else{
	$ProcessId=6001;
     }

$Remark=FormatSTR($Remark);
if ($Picture!=""){
	  $FilePath="../download/process/";
	  if(!file_exists($FilePath)){
		      makedir($FilePath);
	    }
	     $oldPicture=$Picture;
		$FileType=".jpg";
		$Newpicture=$ProcessId.$FileType;
		$upInfo=UploadFiles($oldPicture,$Newpicture,$FilePath);
}
        $TypeId=$TypeId==""?0:$TypeId;
        $PictureInfo=$upInfo==""?0:1;
        $inRecode="INSERT INTO $DataIn.process_data (Id,ProcessId, ProcessName,gxTypeId,TypeId,BassLoss,Price,Picture,Remark,Estate,Locks,Date, Operator)values(NULL,'$ProcessId','$ProcessName','$gxTypeId','$TypeId','$BassLoss','$Price','$PictureInfo','$Remark','1','0','$Date','$Operator')";
        $inAction=@mysql_query($inRecode);
        if($inAction){ 
	        $Log="$TitleSTR 成功!<br>";
	        } 
        else{
	        $Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	        $OperationResult="N";
	       } 

//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>