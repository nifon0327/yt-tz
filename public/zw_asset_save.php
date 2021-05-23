<?php 
/*
$DataIn.zw1_assetrecord
$DataIn.zw1_assetuse
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="物品资料登记";			//需处理
$fromWebPage="zw_asset";
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
$Date=date("Y-m-d");
$inRecode="INSERT INTO $DataIn.zw1_assetrecord (Id,TypeId,Model,Photo,Number,BrandId,Date,Estate,Locks,delSign,Operator) VALUES (NULL,'$TypeId','$Model','0','$Number','$BrandId','$Date','1','0','0','$Operator')";
$inAction=@mysql_query($inRecode);
$Id=mysql_insert_id();
if ($inAction){
	$Log="物品记录添加成功。<br>";
	//上传图片
	if($Photo!=""){
		$FilePath="../download/mobile/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$OldFile=$Photo;
		$PreFileName="Mobile".$Id.".jpg";
		$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($uploadInfo!=""){
			//更新
			$upsql="UPDATE $DataIn.zw1_assetrecord SET Photo='1' WHERE Id='$Id'";
			$result = mysql_query($upsql);
			}
		}
	//初始领用记录
	$dRecode="INSERT INTO $DataIn.zw1_assetuse (Id,AssetId,Remark,Date,User,Estate,Locks,Operator) VALUES (NULL,'$Id','$useRemark','$useDate','$User','0','0','$Operator')";
	$dRes=@mysql_query($dRecode);
	if($dRes){
		$Log.="&nbsp;&nbsp;该物品初始领用记录登记成功.<br>";
		}
	else{
		$Log.="<div class=redB>&nbsp;&nbsp;该物品初始领用记录登记失败.</div><br>";
		$OperationResult="N";
		}
	} 
else{
	$Log="<div class=redB>物品记录添加失败.</div><br>";
	}
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
