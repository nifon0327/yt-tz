<?php
echo "<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head>
";

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

include "../../basic/chksession.php";
include "../../basic/downloadFileIP.php";  //取得下载文档的IP
//echo "123---- $donwloadFileIP";
if ($donwloadFileIP != "") {   //不为空表示走远程加载
    //echo "$donwloadFileIP";
    $url = "$donwloadFileIP/remoteDloadFile/R_UpLoadFiles.php?Login_P_Number=$Login_P_Number&UpFileSign=stuffP";

    $str = file_get_contents(iconv("UTF-8", "GB2312", $url));                //注意：要将地址转为GB2312，否则读取失败
    //$content= str_replace("\"","'",$str);
    $content = $str;
    $start = "^";
    $strP = strpos($content, $start);
    $tempStr = substr($content, $strP + 1);
    echo "远程图片加载成功:$tempStr";

}

?>