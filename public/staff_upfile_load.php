<?php
$Date=date("Y-m-d");
$StaffPhotoPath="../download/staffPhoto/"; 
if(!file_exists($StaffPhotoPath)){
	makedir($StaffPhotoPath);
 }
$uploadArray = array();
 
if($IdcardPhoto!=""){
      $PreFileName1 = "C".$Number.".jpg";
      $uploadInfo1=UploadPictures($IdcardPhoto,$PreFileName1,$StaffPhotoPath);
      $upValue1=$uploadInfo1==""?"":"IdcardPhoto='1'";
      if($upValue1)$uploadArray[] = $upValue1;
}


if($Photo!=""){
      $PreFileName2="P".$Number.".jpg";
      $uploadInfo2=UploadPictures($Photo,$PreFileName2,$StaffPhotoPath);
      $upValue2=$uploadInfo2==""?"":"Photo='1'";
      if($upValue2)$uploadArray[] = $upValue2;
}


if($PngPhoto!=""){
      $PreFileName3="P".$Number.".png";
      $uploadInfo3=UploadPictures($PngPhoto,$PreFileName3,$StaffPhotoPath);
}

//通行证

if($PassTicket!=""){
		$PreFileName4="PT".$Number.".jpg";
		$uploadInfo4=UploadPictures($PassTicket,$PreFileName4,$StaffPhotoPath);
		$upValue4=$uploadInfo4==""?"":"PassTicket='1'";
		if($upValue4)$uploadArray[] = $upValue4;
	}

//驾 照
if($DriverPhoto!=""){
		$PreFileName5="D".$Number.".jpg";
		$uploadInfo5=UploadPictures($DriverPhoto,$PreFileName5,$StaffPhotoPath);
		$upValue5 = $uploadInfo5==""?"":"DriverPhoto='1'";
		if($upValue5)$uploadArray[] = $upValue5;
	}

//护 照
if($PassPort!=""){
		$PreFileName6="PP".$Number.".jpg";
		$uploadInfo6=UploadPictures($PassPort,$PreFileName6,$StaffPhotoPath);
		$upValue6 =$uploadInfo6==""?"":"PassPort='1'";
		if($upValue6)$uploadArray[] = $upValue6;
	}
	
//健康体检
if($HealthPhoto!=""){
		$PreFileName7="H".$Number.".jpg";
		$uploadInfo7=UploadPictures($HealthPhoto,$PreFileName6,$StaffPhotoPath);
		$upValue7 =$uploadInfo7==""?"":"HealthPhoto='1'";
		if($upValue7)$uploadArray[] = $upValue7;
	}
	
//职业体检
if($vocationHPhoto!=""){
		$PreFileName8="V".$Number.".jpg";
		$uploadInfo8=UploadPictures($vocationHPhoto,$PreFileName6,$StaffPhotoPath);
		$upValue8 =$uploadInfo8==""?"":"vocationHPhoto='1'";
		if($upValue8)$uploadArray[] = $upValue8;
	}
$uploadLength = count($uploadArray);


$uploadString = "";
if($uploadLength>0){
	$uploadString = implode(",", $uploadArray);
	$UpdateSql = "UPDATE $DataIn.staffsheet SET $uploadString WHERE Number = $Number";
	$UpdateResult = mysql_query($UpdateSql);
    if($UpdateResult){
		 $Log.="ID号为 $Number 的员工相关证件上传成功.<br>";
         }
	else{
		 $Log.="<div class='redB'>ID号为 $Number 的员工相关证件上传失败.</div><br>";
		 $OperationResult="N";
	 }
}
	

//个人证书上传
$CertFilePath="../download/Certificate/";
if(!file_exists($CertFilePath)){
	makedir($CertFilePath);
	}
$EndNumber=1;
$checkEndFile=mysql_fetch_array(mysql_query("SELECT MAX(Picture) AS EndPicture FROM $DataIn.staff_Certificate WHERE Number='$Number'",$link_id));
$EndFile=$checkEndFile["EndPicture"];
if($EndFile!=""){
	$TempArray1=explode("_",$EndFile);
	$TempArray2=explode(".",$TempArray1[1]);
	$EndNumber=$TempArray2[0]+1;
	}
$uploadNums=count($Picture);
for($i=0;$i<$uploadNums;$i++){
	//上传文档				
	$upPicture=$Picture[$i];
	$TempOldImg=$OldImg[$i];
	if ($upPicture!=""){	
		$OldFile=$upPicture;
		//检查是否有原档，如果有则使用原档名称，如果没有，则分配新档名
		if($TempOldImg!=""){
			$PreFileName=$TempOldImg;
			}
		else{
			$PreFileName=$Number."_".$EndNumber.".jpg";
			}
		$uploadInfo=$PreFileName;
		$uploadInfo=UploadFiles($OldFile,$PreFileName,$CertFilePath);
		if($uploadInfo!=""){
			if($TempOldImg==""){//写入记录
				$inRecode="INSERT INTO $DataIn.staff_Certificate(Id,Number,Picture,Date,Estate, Locks,Operator) VALUES (NULL,'$Number','$uploadInfo','$Date','1','0','$Operator')";
				$inAction=@mysql_query($inRecode);
				if($inAction){
					$Log.="ID号为 $Number 的员工证书 $uploadInfo 上传成功.<br>";
					$EndNumber++;
                     }
				else{
					$Log.="<div class='redB'>ID号为 $Number 的员工证书 $uploadInfo 上传 添加失败. $inRecode</div><br>";
					$OperationResult="N";
					}
				}
			}
		}
	}	
	
	
	
	
?>