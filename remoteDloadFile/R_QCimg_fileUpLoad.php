<?php 
//$Log_Funtion="PDF图片上传";
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$returnstr="";  //(远程更新)
$SetStr="";
$FileType="";
switch ($doAction) {
	case "UporiginalQCFile":
		$FileType=2;
		break;
	case "UporiginalErrorFile":
		$FileType=5;		
		break;
	default :
		break;
	
}
if ($FileType!="") {
		$returnstr.="NoFind|-1|-1|-1|";
		$CompanyId="1047";
		$ProductType=$ProductId;
		$FileRemark=str_replace("'","''",$FileRemark); 
		$TempFilePath="../download/tmp_standarddrawing/";
		$originalFilePath="../download/standarddrawing/";
		/*
	if(!file_exists($originalFilePath)){
		   makedir($originalFilePath);
	 }
	 */
	$returnstr.="$FileRemark 0|$ProductId|$PreFileName|"; 
	if($originalPicture!=""  ){
		      
		      $TempFile=$TempFilePath.$originalPicture;
		      //$FType=substr("$originalPicture_name", -4, 4);
			  $Field=explode(".",$originalPicture);
			  $FType=$Field[1];
	          //$Ohycfile=$originalPicture;
	          $datelist=newGetDateSTR();
	          $PreFileName=$datelist.".".$FType;
			  $PreFile=$originalFilePath.$datelist.".".$FType;
	          //$Attached=UploadFiles($Ohycfile,$PreFileName,$originalFilePath);
		  if(file_exists($TempFile)){
			 
			if(copy($TempFile, $PreFile)) {  //拷贝成功，则删除临时文件
			  unlink($TempFile);
			  $returnstr="CopyOK|";
			   $sql="INSERT INTO $DataIn.doc_standarddrawing(Id,FileType,FileRemark,
			   FileName,CompanyId,ProductType,Estate,Locks,Date,Operator) VALUES 
			   (NULL,'$FileType','$FileRemark','$PreFileName','$CompanyId',
			   '$ProductType','1','0','$Date','$Operator')";	
			  //$returnstr.= " $sql ";
			  $result = mysql_query($sql,$link_id);	
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


}

echo "^$returnstr"; 

?>