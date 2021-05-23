<?php 
//电信-EWEN
$reomotefile=$file;   //远程图片路径，可使用正则提取。
$tempfile=substr($reomotefile,strrpos($reomotefile,'/')+1); //获取文件名
$newfile=$FilePath.$tempfile;
if (!copy($file, $newfile)) {
    echo "-1";  //失败
}
else {
	echo "1";  //成功
}
?>

