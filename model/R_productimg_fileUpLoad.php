<?php 
//$Log_Funtion="PDF图片上传";
include "../basic/parameter.inc";
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$returnstr="";  //(远程更新)
$SetStr="";

switch ($doAction) {
	case "UpPackZip":
	$returnstr.="NoFind|-1|-1|-1|";
	 $FindResult=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.productdata 
	 WHERE ProductId='$ProductId'",$link_id));
		$CompanyId=$FindResult["CompanyId"];
		$ProductType=$FindResult["TypeId"];
		$FileRemark=$FindResult["cName"];
		$eCode=$FindResult["eCode"];
		$FileRemark=str_replace("'","''",$FileRemark); 
		$TempFilePath="../download/tmp_zipstandarddrawing/";
		$originalFilePath="../download/teststandard/";

	
	 if($originalPicture!=""){
		      
		      $TempFile=$TempFilePath.$originalPicture;
			  $SaveFile="T".$ProductId."_H(pack)".".zip";
			  $PreFile=$originalFilePath.$SaveFile;
	          //$Attached=UploadFiles($Ohycfile,$PreFileName,$originalFilePath);
		  if(file_exists($TempFile)){
			//$CompanyId=1074;
			if($CompanyId==1074 && $eCode!="" ){
				$DateYmd=date("Ymd");
            	$straxFilePath="../client/strax/picture/";
				$straxFileName=$straxFilePath.$eCode."-".$DateYmd."(pack).zip";
				copy($TempFile, $straxFileName);
			}
			
			if(copy($TempFile, $PreFile)) {  //拷贝成功，则删除临时文件
			  unlink($TempFile);
			  $returnstr="CopyOK|";
              $DelSql1="DELETE  FROM $DataIn.productimg WHERE  ProductId=$ProductId AND Type=1"; $DelResult1=@mysql_query($DelSql1);
			  $inRecode1="INSERT INTO $DataIn.productimg (Id,ProductId,Picture,Date,Type,Operator) VALUES (NULL,'$ProductId','$SaveFile','$Date','1','$Operator')";
			  $result=@mysql_query($inRecode1);
			  //$result = mysql_query($sql,$link_id);	
			   if($result){
					 //$Log="StuffId号为 $StuffId 的图档删除成功.</br>";
					  $returnstr.="1|$ProductId|$PreFileName|";    //1表示更新表成功，0表示更新表失败
				 }
				 else{
					//$Log="StuffId号为 $StuffId 图档删除失败! $sql</br>";
					//$OperationResult="N";
					$returnstr.="0|$ProductId|$PreFileName|";    //1表示更新表成功，0表示更新表失败
			   }  //	 if($result){		   
			   
			  
			}  // if(copy($TempFile, $PreFileName))
			  
		  }   //  if(file_exists($TempFile)){
		  

			  
	   }   //  if($originalPicture!=""){
	  else{
		   //$Log.="<div class='redB'>ID为 $ProductId 的产品标准图原件没有上传</div><br>";
	  }	
	break;


	case "UpNoPackZip":
	$returnstr.="NoFind|-1|-1|-1|";
	 $FindResult=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.productdata 
	 WHERE ProductId='$ProductId'",$link_id));
		$CompanyId=$FindResult["CompanyId"];
		$ProductType=$FindResult["TypeId"];
		$FileRemark=$FindResult["cName"];
		$eCode=$FindResult["eCode"];
		$FileRemark=str_replace("'","''",$FileRemark); 
		$TempFilePath="../download/tmp_zipstandarddrawing/";
		$originalFilePath="../download/teststandard/";

	
	 if($originalPicture!=""){
		      
		      $TempFile=$TempFilePath.$originalPicture;
			  $SaveFile="T".$ProductId."_H(no-pack)".".zip";
			  $PreFile=$originalFilePath.$SaveFile;
	          //$Attached=UploadFiles($Ohycfile,$PreFileName,$originalFilePath);
		  if(file_exists($TempFile)){

			//$CompanyId=1074;
			if($CompanyId==1074 && $eCode!="" ){
				$DateYmd=date("Ymd");
            	$straxFilePath="../client/strax/picture/";
				$straxFileName=$straxFilePath.$eCode."-".$DateYmd."(no-pack).zip";
				copy($TempFile, $straxFileName);
			}
			
			if(copy($TempFile, $PreFile)) {  //拷贝成功，则删除临时文件
			  unlink($TempFile);
			  $returnstr="CopyOK|";
              $DelSql1="DELETE  FROM $DataIn.productimg WHERE  ProductId=$ProductId AND Type=2"; $DelResult1=@mysql_query($DelSql1);
			  $inRecode1="INSERT INTO $DataIn.productimg (Id,ProductId,Picture,Date,Type,Operator) VALUES (NULL,'$ProductId','$SaveFile','$Date','2','$Operator')";
			  $result=@mysql_query($inRecode1);
			  //$result = mysql_query($sql,$link_id);	
			   if($result){
					 //$Log="StuffId号为 $StuffId 的图档删除成功.</br>";
					  $returnstr.="1|$ProductId|$PreFileName|";    //1表示更新表成功，0表示更新表失败
				 }
				 else{
					//$Log="StuffId号为 $StuffId 图档删除失败! $sql</br>";
					//$OperationResult="N";
					$returnstr.="0|$ProductId|$PreFileName|";    //1表示更新表成功，0表示更新表失败
			   }  //	 if($result){		   
			   
			  
			}  // if(copy($TempFile, $PreFileName))
			  
		  }   //  if(file_exists($TempFile)){
		  

			  
	   }   //  if($originalPicture!=""){
	  else{
		   //$Log.="<div class='redB'>ID为 $ProductId 的产品标准图原件没有上传</div><br>";
	  }	
	break;

}

//echo "^$returnstr";  远程需要返回


?>