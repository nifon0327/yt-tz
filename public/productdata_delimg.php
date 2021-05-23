<?php 
include "../model/modelhead.php";
$delSql="DELETE FROM $DataIn.productimg WHERE Picture='$ImgName'";
$result1 = mysql_query($delSql);
$FilePath="../download/teststandard/".$ImgName;
if(file_exists($FilePath)){
	unlink($FilePath);
	}
?>