<?php 
//代码数据共享-EWEN 2012-11-25
include "../model/modelhead.php";
//步骤2：
$Log_Item="总务用品资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$GoodsName=FormatSTR($GoodsName);
$chinese=new chinese;
$Letter=substr($chinese->c($GoodsName),0,1);
$Date=date("Y-m-d");
//$LockSql=" LOCK TABLES $DataIn.zwwp3_data WRITE";$LockRes=@mysql_query($LockSql);
$inRecode="INSERT INTO $DataPublic.zwwp3_data (Id,GoodsName,TypeId,Attached,Date,Estate,Locks,Operator) VALUES (NULL,'$GoodsName','$TypeId','0','$Date','2','0','$Operator')";
$inAction=@mysql_query($inRecode);
$Id=mysql_insert_id();
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	////////////////////////////////////
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/zwwp/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="Z".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;总务用品图片上传成功！$inRecode <br>";
			$Attached=1;
			$sql = "UPDATE $DataIn.zw3_purchaset SET Attached='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;总务用品图片失败！$inRecode </div><br>";
			$OperationResult="N";			
			}
		}
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
