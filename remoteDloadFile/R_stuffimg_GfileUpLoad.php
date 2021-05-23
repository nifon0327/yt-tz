<?php 

//扫描配件图档并更新电信---yang 20120801
//$Date=date("Y-m-d");
$Date=date("Y-m-d H:i:s");
$handle=opendir("../download/upload/");
$returnstr="";  //(远程更新)
//$d=anmaIn("download/upload/",$SinkOrder,$motherSTR);
while($Gfile=readdir($handle)) {
	$FileType=substr($Gfile,-4,4);
	if (($Gfile!=".") and ($Gfile!="..")) {
		//$Id=trim(preg_replace("[^0-9]","",$Gfile));  
		$Id=trim(preg_replace("/([^0-9]+)/i","",$Gfile));  
		if ($Id!=""){

			
			$delsql = "UPDATE $DataIn.stuffprovider SET Estate=0  WHERE StuffId=$Id";  //add by zx 2011-04-15 全部更新为0,准备用新图档
			$delresult = mysql_query($delsql,$link_id);
			
			$Operator=$Login_P_Number;
			$inRecode="INSERT INTO $DataIn.stuffprovider (Id,StuffId,CompanyId,Date,Estate,Operator) VALUES 
			(NULL,'$Id','0','$Date','1','$Operator')";  //记录内部上传
			$inAction=mysql_query($inRecode,$link_id);
			//echo "$inRecode";
			
			$sql = "UPDATE $DataIn.stuffdata SET Gfile='$Gfile',Gstate='6',GfileDate='$Date' WHERE StuffId=$Id";  //把文件名写进去
			//$sql = "UPDATE $DataIn.stuffdata SET Gstate='9' WHERE StuffId=$Id";  //把文件名写进去
			//echo "<br> $sql <br>";	
			$result = mysql_query($sql,$link_id);
			$rerows=mysql_affected_rows();   
			//if($result && ($rerows>0)){ //如果返回值>0则拷文件
			
			if($result ){ //如果返回值>0则拷文件
			    $returnstr.="$Id|";
				$stuffFilePath="../download/stufffile_tmp/";
				//$stuffFilePath="../download/zxtest/";
				$TempFilePath="../download/upload/";
				$stuffile=$stuffFilePath.$Gfile;
				$TempFile=$TempFilePath.$Gfile;		
				//echo "copy file! copy($TempFile, $stuffile)";
				if(copy($TempFile, $stuffile)) {  //拷贝成功，则删除临时文件
				  unlink($TempFile);
				  $sql = "DELETE FROM $DataIn.stuffverify WHERE StuffId='$Id'";
				  $result = mysql_query($sql);
				}  //if(copy
				else{
					echo "copy file! copy($TempFile, $stuffile) 失败！<br>";
				}
						
			}  //if($result
			
		}//if(id!="")
	} //
}
closedir($handle);	
echo "^$returnstr";   //把执行成功的返回过去，这样容易排错
?>