<?php 
//电信-joseph
//代码共享-EWEN 2012-08-17
include "../model/modelhead.php";
//步骤2：
$Log_Item="采购物品分类";			//需处理
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
$TypeName=FormatSTR($TypeName);
$chinese=new chinese;
$Letter=substr($chinese->c($TypeName),0,1);
$Date=date("Y-m-d");
//$LockSql=" LOCK TABLES $DataIn.zw3_purchaset WRITE";$LockRes=@mysql_query($LockSql);
$inRecode="INSERT INTO $DataIn.zw3_purchaset (Id,TypeName,TypeId,Attached,Date,Estate,Locks,Operator) VALUES (NULL,'$TypeName','$TypeId','0','$Date','2','0','$Operator')";
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
			$Log.="&nbsp;&nbsp;总务采购物品图片上传成功！$inRecode <br>";
			$Attached=1;
			$sql = "UPDATE $DataIn.zw3_purchaset SET Attached='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;总务采购物品图片失败！$inRecode </div><br>";
			$OperationResult="N";			
			}
		}
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
