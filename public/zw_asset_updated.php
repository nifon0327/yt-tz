<?php 
/*
$DataIn.zw1_assetrecord
$DataIn.zw1_assetuse
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
$fromWebPage="zw_asset";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="物品资料";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$upDataSheet="$DataIn.zw1_assetrecord";	//需处理

//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";include "../model/subprogram/updated_model_3d.php";		break;
	case 37://
		$Log_Funtion="交接";
		$dRecode="INSERT INTO $DataIn.zw1_assetuse (Id,AssetId,Remark,Date,User,Estate,Locks,Operator) VALUES (NULL,'$Id','$useRemark','$useDate','$newUser','1','0','$User')";
		$dRes=@mysql_query($dRecode);
		if($dRes){
			$Log.="&nbsp;&nbsp;物品交接记录登记成功。<br>";
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;物品交接记录登记失败。</div><br>";
			$OperationResult="N";
			}
		break;
	case 41:
		$Log_Funtion="寄回";
		//需要最后接手人操作，且NUMBER不为MC
		$sql = "UPDATE $DataIn.zw1_assetrecord SET delSign='1' WHERE Id ='$Id'";
		$result = mysql_query($sql);
		$Date=date("Y-m-d");
		if ($result && mysql_affected_rows()>0){
			$dRecode="INSERT INTO $DataIn.zw1_assetuse SELECT NULL,AssetId,'寄回客户','$Date','0','1','0',User,'0','$Operator','$DateTime',null,null FROM $DataIn.zw1_assetuse WHERE AssetId='$Id' ORDER BY Date DESC,Id DESC LIMIT 1";
			$dRes=@mysql_query($dRecode);
			if($dRes){
				$Log.="&nbsp;&nbsp;物品寄回登记成功。<br>";
				}
			else{
				$Log.="<div class=redB>&nbsp;&nbsp;物品寄回登记失败。2 $dRecode</div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;物品寄回登记失败。1 $sql</div><br>";
			$OperationResult="N";
			}
		break;
	default:
		$Log_Funtion="物品记录更新保存";
		$Date=date("Y-m-d");
		$FilePath="../download/mobile/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		if($Photo!=""){
			$OldFile=$Photo;
			$PreFileName="Mobile".$Id.".jpg";
			$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
			$PhotoValue=$uploadInfo==""?"":",Photo='1'";
			}
		if($PhotoValue=="" && $oldFile!=""){//没有上传文件并且已选取删除原文件
			$oldFilePath=$FilePath.$oldFile;
			if(file_exists($oldFilePath)){
				unlink($oldFilePath);
				$PhotoValue=",Photo='0'";
				}			
			}
		$upSql1="UPDATE $DataIn.zw1_assetrecord SET TypeId='$TypeId',Model='$Model',Number='$Number',BrandId='$BrandId',Date='$Date',Locks='0',Operator='$Operator' $PhotoValue WHERE Id=$Id";
		$upResult1 = mysql_query($upSql1);
		if ($upResult1 && mysql_affected_rows()>0){
			$Log="ID号为 $Id 的物品资料更新成功!<br>";
			}
		else{
			$Log="<div class=redB>ID号为 $Id 的物品资料更新失败!($upSql1)</div><br>";
			$OperationResult="N";
			}
		//更新最后的领用记录
		$upSql2="UPDATE $DataIn.zw1_assetuse SET Remark='$useRemark',Date='$useDate',User='$User' WHERE Id='$UseId'";
		$upResult2 = mysql_query($upSql2);
		if($upResult2 && mysql_affected_rows()>0){
			$Log.="ID号为 $UseId 的物品领用记录更新成功!<br>";
			}
		else{
			$Log.="<div class=redB>ID号为 $UseId 的物品领用记录更新失败!($upSql2)</div><br>";
			$OperationResult="N";
			}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>