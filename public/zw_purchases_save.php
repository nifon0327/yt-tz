<?php 
//步骤1： $DataIn.zw3_purchaset 分开已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="采购物品登记";			//需处理
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
if ($newCheck){ //新增物品名称
  $addRecode="INSERT INTO $DataIn.zw3_purchaset (Id,TypeName,TypeId,Attached,Date,Estate,Locks,Operator) VALUES (NULL,'$newTypeName','$sTypeId','0','$Date','2','0','$Operator')";
  $addAction=@mysql_query($addRecode);  
  $TypeId=mysql_insert_id();
  $Id=$TypeId;
  if ($TypeId>0){
      $Log.="新增物品名称成功!<br>";
      }
  else{
      $Log.="<div class=redB>新增物品名称失败! $addRecode </div><br>";
  }
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
			//更新刚才的记录
			$sql = "UPDATE $DataIn.zw3_purchaset SET Attached='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;总务采购物品图片失败！$inRecode </div><br>";
			$OperationResult="N";			
			}
		}	////////////////////////////////////
}
if ($TypeId>0){
//$LockSql=" LOCK TABLES $DataIn.zw3_purchases WRITE";$LockRes=@mysql_query($LockSql);
$inRecode="INSERT INTO $DataIn.zw3_purchases (Id,Mid,Unit,Price,Qty,TypeId,cgSign,WorkAdd,Remark,Estate,qkDate,Cid,BuyerId,Bill,Locks,Date,Operator) VALUES 
(NULL,'0','$Unit','0','$Qty','$TypeId','1','$WorkAdd','$Remark','1','0000-00-00','0','0','0','1','$Date','$BuyerId')";
$inAction=@mysql_query($inRecode);
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
	$Log.="$TitleSTR 成功!<br>";
	} 
else{
	$Log.="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
