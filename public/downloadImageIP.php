<?php
//<a href=\"../admin/openorload.php?  //这是在本地的，如果在本地下，请用此变量

if (substr($_SERVER["SERVER_NAME"],0,3)=='192'){
	$donwloadFileIP="http://" . $_SERVER ['HTTP_HOST'];
}
else {
	 $donwloadFileIP="http://" . $OutDomainNameStr;
}

$donwloadFileaddress="$donwloadFileIP/remoteDloadFile/R_openorload.php";
?>