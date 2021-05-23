<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="FSC资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&CompanyId=$CompanyId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理

$IN_recode="INSERT INTO $DataPublic.cg3_fscdata (Id,Remark,Attached,Locks,Date,Operator) VALUES (NULL,'$Remark','','0','$DateTime','$Operator')";
$res=@mysql_query($IN_recode);
if ($res){
	$Id=mysql_insert_id();
	$Log="FSC资料添加成功!<br>";
	//上传文件
	if($Attached!=""){
		$FilePath="../download/fscdata";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$FileType=substr("$Attached_name", -4, 4);
		$OldFile=$Attached;
		$PreFileName="fsc".$Id.$FileType;
		$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
		if($uploadInfo){
			$Log.="&nbsp;&nbsp;附件上传成功!<br>";
			$sql = "UPDATE $DataPublic.cg3_fscdata SET Attached='$PreFileName' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;附件上传失败！</div><br>";
			$OperationResult="N";			
			}
		}
 	} 
else{ 
	$Log="<div class='redB'>FSC资料添加失败! </div><br>"; 
	$OperationResult="N";
	}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>