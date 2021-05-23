<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="车辆信息记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//删除图片
		$FilePath="../download/cardata/";
		$FileResult = mysql_query("SELECT C.DriveLic,C.Enrollment,C.Insurance,C.YueTong,C.OilCard FROM $DataPublic.cardata C WHERE C.Id=$Id",$link_id);
		if($FileRow = mysql_fetch_array($FileResult)){
			do{
				$DriveFile=$FileRow["DriveLic"];
				$EnrollmentFile=$FileRow["Enrollment"];
				$InsuranceFile=$FileRow["Insurance"];
				$YueTongFile=$FileRow["YueTong"];
				$OilCardFile=$FileRow["OilCard"];
			}while($FileRow = mysql_fetch_array($FileResult));
		}

		$DriveFilePath=$FilePath.$DriveFile;
		$EnrollmentPath=$FilePath.$EnrollmentFile;
		$InsurancePath=$FilePath.$InsuranceFile;
		$YueTongPath=$FilePath.$YueTongFile;
		$OilCardPath=$FilePath.$OilCardFile;
		
		if(file_exists($DriveFilePath)){
			unlink($DriveFilePath);
			$Log="&nbsp;&nbsp;ID在( $Id )的 行驶证 删除成功.<br>";
		}
		if(file_exists($EnrollmentPath)){
			unlink($EnrollmentPath);
			$Log="&nbsp;&nbsp;ID在( $Id )的 登记证书 删除成功.<br>";
		}
		if(file_exists($InsurancePath)){
			unlink($InsurancePath);
			$Log="&nbsp;&nbsp;ID在( $Id )的 保险单 删除成功.<br>";
		}
		if(file_exists($YueTongPath)){
			unlink($YueTongPath);
			$Log="&nbsp;&nbsp;ID在( $Id )的 粤通卡 删除成功.<br>";
		}
		if(file_exists($OilCardPath)){
			unlink($OilCardPath);
			$Log="&nbsp;&nbsp;ID在( $Id )的 加油卡 删除成功.<br>";
		}
		//
		$delSql = "DELETE FROM $DataPublic.cardata WHERE Id=$Id"; 
		$delRresult = mysql_query($delSql);

		if ($delRresult && mysql_affected_rows()>0){
		$Log.="&nbsp;&nbsp;ID在( $Id )的 $TitleSTR 成功.<br>";
		}
	else{
		$OperationResult="N";
		$Log.="<div class='redB'>ID在( $Id )的 $TitleSTR 失败.</div><br>";
		}//end if ($Del_result)	
	}
}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>