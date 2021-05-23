<?php
	
	include "../model/modelhead.php";
	echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
		 <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
		 <link rel='stylesheet' href='../model/css/read_line.css'>
		 <link rel='stylesheet' href='../model/css/sharing.css'>
		 <link rel='stylesheet' href='../model/keyright.css'>
		 <SCRIPT src='../model/pagefun_Sc.js' type=text/javascript></script>
		 <SCRIPT src='../model/checkform.js' type=text/javascript></script>
		 <script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
    //步骤2：需处理
	$ColsNumber=25;
	$tableMenuS=500;
	ChangeWtitle("$SubCompany 签退审核列表");
	$funFrom="attendanceVertify";
	$From=$From==""?"m":$From;
	$Th_Col="选项|55|序号|40|员工ID|45|姓名|55|签退时间|100|原因|100|状态|50";

	
	
	
?>