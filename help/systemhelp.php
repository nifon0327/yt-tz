<?php 
include "../basic/chksession.php" ;
if(!(session_is_registered("Login_help"))){ 
	echo "当前主窗口页面没有帮助资料！"; 
	}
else{
		Header("Location:".$Login_help.".html");
	}
?>
