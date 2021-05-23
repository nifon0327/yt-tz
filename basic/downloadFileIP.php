<?php
//<a href=\"../admin/openorload.php?  //这是在本地的，如果在本地下，请用此变量

//$isImage_File="1";   1 表示，内部使用，非下载时，如产品标准图 openImage.php
session_start();

if (substr($_SERVER["SERVER_NAME"], 0, 3) == '192') {
    $donwloadFileIP = "http://" . $_SERVER ['HTTP_HOST'];
} else {
    // $donwloadFileIP = "http://" . $OutDomainNameStr;
    $donwloadFileIP = "http://" . $_SERVER["HTTP_HOST"];
}
$donwloadFileaddress = "$donwloadFileIP/remoteDloadFile/R_openorload.php";

?>