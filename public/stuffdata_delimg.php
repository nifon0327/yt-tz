<?php 
//步骤1：$DataIn.stuffimg 二合一已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$checkSql=mysql_query("SELECT StuffId FROM $DataIn.stuffimg WHERE Picture='$ImgName'",$link_id);
if($checkRow=mysql_fetch_array($checkSql)){
	$StuffId=$checkRow["StuffId"];
	$delSql="DELETE FROM $DataIn.stuffimg WHERE Picture='$ImgName'";
	$result1 = mysql_query($delSql);
	if($result1){
		//更新
		$sql = "UPDATE $DataIn.stuffdata SET Picture=0 WHERE StuffId=$StuffId AND StuffId NOT IN(SELECT StuffId FROM $DataIn.stuffimg WHERE StuffId=$StuffId)";
		$result = mysql_query($sql);
		$FilePath="../download/stufffile/".$ImgName;
		if(file_exists($FilePath)){
			unlink($FilePath);
			}
		}
	}
?>