<?php 
//步骤1：$DataIn.productimg 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$delSql="DELETE FROM $DataPublic.msg1_picture WHERE Picture='$ImgName'";
$result1 = mysql_query($delSql);
$FilePath="../download/msgfile/".$ImgName;
if(file_exists($FilePath)){
	unlink($FilePath);
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.msg1_picture");
?>