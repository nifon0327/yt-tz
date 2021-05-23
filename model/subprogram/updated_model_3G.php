<?php 
//多记录操作 二合一已更新电信---yang 20120801
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$sql = "UPDATE $upDataSheet SET $SetStr WHERE Id= $Id";
		//echo $sql;
		$result = mysql_query($sql);
		if($result){
			$Log.="ID号在 $Id 的记录成功 $Log_Funtion.</br>";
				$GetGfile=mysql_fetch_array(mysql_query("SELECT Gfile From $upDataSheet  WHERE Id= $Id",$link_id));
				$Gfile=$GetGfile["Gfile"];
				//echo "$Gfile";
			
				$stuffFilePath="../download/stufffile/";
				//$stuffFilePath="../download/zxtest/";
				$TempFilePath="../download/stufffile_tmp/";
				$stuffile=$stuffFilePath.$Gfile;
				$TempFile=$TempFilePath.$Gfile;		
				//echo "copy file! copy($TempFile, $stuffile)";
				if(file_exists($TempFile)){
					if(copy($TempFile, $stuffile)) {  //拷贝成功，则删除临时文件
					  unlink($TempFile);
					}  //if(copy
					else{
						$Log.="ID号在 $Id 的文图档拷贝失败 $Log_Funtion.</br>";
					}
				}
			
			}
		else{
			$Log.="ID号为 $Id 的记录$Log_Funtion 失败! $sql</br>";
			$OperationResult="N";
			}
	}
}
?>