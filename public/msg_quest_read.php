<?php
	
	include "../model/modelhead.php";
	$From=$From==""?"read":$From;
	
	$tableMenuS=600;
	ChangeWtitle("$SubCompany 提问记录");
	$funFrom="msg_quest";
	$nowWebPage=$funFrom."_read";
	$Th_Col="选项|40|序号|60|内容|500|日期|70";
	
	
?>