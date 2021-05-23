<?php
//$Log_Funtion="PDF图片上传";
$Date=date("Y-m-d");
$returnstr="";  //(远程更新)
$SetStr="";
$stuffFilePath="../download/stufffile/";
switch ($doAction) {
	case "UpCurFile":
		$returnstr.="NoFind|-1|-1|";
	    $pdffile="$StuffId".'.pdf';

		//$stuffFilePath="../download/zxtest/";
		$TempFilePath="../download/tmp_stuffpdf/";
		$stuffile=$stuffFilePath.$pdffile;
		$TempFile=$TempFilePath.$pdffile;

		//echo "copy file! copy($TempFile, $stuffile)";
		if(file_exists($TempFile)){
			if(copy($TempFile, $stuffile)) {  //拷贝成功，则删除临时文件
			  unlink($TempFile);

			 // $jpgFile=$stuffFilePath . $StuffId . "_s.jpg";
			 // exec("$execImageMagick -colorspace sRGB -transparent white -trim $stuffile $jpgFile");

			  $returnstr="CopyOK|";
			  $sql = "UPDATE $DataIn.stuffdata SET Picture=2 WHERE StuffId=$StuffId";  //这些更新可以返回到调用的执行，不在文件服务器执行！
			  $result = mysql_query($sql,$link_id);
			   if($result){
					 //$Log="StuffId号为 $StuffId 的图档删除成功.</br>";
					  $returnstr.="1|$StuffId|";    //1表示更新表成功，0表示更新表失败
				 }
				 else{
					//$Log="StuffId号为 $StuffId 图档删除失败! $sql</br>";
					//$OperationResult="N";
					$returnstr.="0|$StuffId|";    //1表示更新表成功，0表示更新表失败
			   }

			}  //if(copy
		}

	break;

	case "DelCurFile":
	    $returnstr.="NoFind|-1|-1|";
		$pdfFile=$stuffFilePath.$StuffId.".pdf";
		$jpgFile=$stuffFilePath.$StuffId . "_s.jpg";
		if(file_exists($pdfFile)){
		  unlink($pdfFile);
		  if(file_exists($jpgFile)){
			  unlink($jpgFile);
		  }
		  $returnstr="DeleteOK|";
		  $sql = "UPDATE $DataIn.stuffdata SET Picture=0 WHERE  StuffId=$StuffId";  //这些更新可以返回到调用的执行，不在文件服务器执行！
			//echo $sql;
		  $result = mysql_query($sql,$link_id);
		   if($result){
				 //$Log="StuffId号为 $StuffId 的图档删除成功.</br>";
				  $returnstr.="1|$StuffId|";  //1表示更新表成功，0表示更新表失败
			 }
			 else{
				//$Log="StuffId号为 $StuffId 图档删除失败! $sql</br>";
				//$OperationResult="N";
				$returnstr.="0|$StuffId|";  //1表示更新表成功，0表示更新表失败
		   }
	  }
	break;

}

echo "^$returnstr";

?>