<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/class.php";
$timer = new timer;  
$timer->start();  
if (!$_SESSION["Login_Id"]){
	echo "<SCRIPT LANGUAGE=JavaScript>"; 
	echo "parent.location.href='/cjgl'"; 
	echo "</script>";
	exit(); 
	}
?>