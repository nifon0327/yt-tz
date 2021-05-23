<?php   
//$DataPublic.staffmain
//二合一已更新
function Get_Remote_File($file,$FilePath,$Passvalue){ //$Passvalue 为了扩充，请使用A|B|C|D
	//上传文件，目前主要是：fixed_assets_save.php?file=http://127.0.0.1:8085/download/fixedFile/311_1.jpg&FilePath=../download/fixedFile/
	/*
	$DataPublic.adminitype
	$DataPublic.currencydata
	*/
	//步骤1 二合一已更新
	$reomotefile=$file;   //远程图片路径，可使用正则提取。 
	//echo "$reomotefile <br>";
	$tempfile=substr($reomotefile,strrpos($reomotefile,'/')+1); //获取文件名
	//echo "$tempfile <br>";
	
	//$newfile="../download/".$path."/".$tempfile;
	$newfile=$FilePath.$tempfile;
	
	//$newfile = '123.jpg';  //保存在本地的图片路径，可对图片重命名
	//echo "$newfile";
	
	if (!copy($file, $newfile)) {
		return  "-1";  //失败
	}
	else {
		return  "1";  //成功
	}
	//步骤4：需处理;
}


?>