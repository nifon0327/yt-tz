<?php   
/*
$DataIn.online
二合一已更新$DataIn.电信---yang 20120801
*/
//踢出已登录者
$delOnline = "DELETE FROM $DataIn.online WHERE uId='$Id'"; 
$delResult = mysql_query($delOnline);
if($delResult){
	$Log.="&nbsp;&nbsp;在线踢出成功. $Del <br>";
	}
else{
	$Log.="<div class='redB'>&nbsp;&nbsp;在线踢出失败. $Del </div><br>";
	$OperationResult="N";
	}		
?>