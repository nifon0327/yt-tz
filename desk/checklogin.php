<?php
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-10

session_start();

echo "
<html>
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
</head>
<body>";

include "../basic/parameter.inc";            //数据库连接
include "../model/modelfunction.php";        //读入函数

$url = "javascript:history.back()";

// 输入检错
if ($Name == "" || $Password == "") {
    echo "<SCRIPT LANGUAGE=JavaScript>alert('对不起，没有输入用户名或密码');";
    echo "location.href='$url'";
    echo "</script>";
}

$MDPassword = MD5($Password);

// 检查两边的数据库中是否存在该用户
$mySql = "
SELECT Id,uType,uName,uPwd,Number,uSeal,lDate,Date,WebStyle,FaxNO, uSign,Estate,Locks,Operator,'7' AS cSign FROM $DataIn.UserTable WHERE uName='$Name' AND uPwd='$MDPassword'";
$myResult = mysql_query($mySql . " $PageSTR", $link_id) or die ("数据连接错误!");
if ($myRow = mysql_fetch_array($myResult)) {
    $Estate = $myRow["Estate"];
    if ($Estate == 0) {        //禁用状态
        echo "<SCRIPT LANGUAGE=JavaScript>alert('登录失败，该帐号目前禁用!');";
        echo "location.href='$url'";
        echo "</script>";
    } else {                //非禁用状态
        /////////////////////
        $cSign = $myRow["cSign"];
        $checkAddInfo = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.companys_group WHERE cSign='$cSign' ", $link_id));
        $InIP = $checkAddInfo["InIP"];            //内网连接IP
        $OutIP = $checkAddInfo["OutIP"];    //外网连接IP
        $DomainName = $checkAddInfo["DomainName"];    //域名
        if (preg_match('/^10\.0/', $_SERVER['HTTP_HOST']) || preg_match('/^192\.168/', $_SERVER['HTTP_HOST'])) {//内部IP登录
            $ToAddress = $InIP; //通过内网IP转向相应的系统
        } else {
            if (preg_match('/^113\.105/', $_SERVER['HTTP_HOST'])) {//外部IP登录
                $ToAddress = $OutIP; //通过外网IP转向相应的系统
            } else {//否则为外网登录
                $ToAddress = $DomainName; //通过哉名转向相应的系统
            }
        }
        $Login_IP = GetIP();
        header("Location: http://$ToAddress/desk/index.php?Name=$Name&Password=$Password&Login_IP=$Login_IP"); //通过哉名转向相应的系统
        exit();
        /////////////////////
    }
} else {//找不到帐户资料
    //如果在同一个IP用同一个用户名登录失败三次，且该用户名存在，则该帐号锁定
    $_SESSION["X"]++;
    if ($_SESSION["X"] > 2) {
        //锁定记录
        //$sql = "UPDATE $DataIn.UserTable SET Estate=0 WHERE 1 AND uName='$Name'";
        //$result = mysql_query($sql);
    }
    echo "<SCRIPT LANGUAGE=JavaScript>alert('登录失败,帐号或密码错误!');";
    echo "location.href='$url'";
    echo "</script>";
}
echo "</body></html>";