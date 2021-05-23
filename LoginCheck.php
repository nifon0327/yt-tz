<?php
/**
 *  Modified by Aitch.Zung (aitch.zung@icloud.com) 2014-06-25
 */
//电信
//代码共享-EWEN 2012-08-24
session_start();

include_once "basic/parameter.inc";
include_once "model/modelfunction.php";

//header("Content-Type: text/html; charset=utf-8");
//header("expires:mon,26jul199705:00:00gmt");
//header("cache-control:no-cache,must-revalidate");
//header("pragma:no-cache");

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	return $string;
}

@$uName = safe_replace(addslashes($_POST['U']));
@$MDPassword = MD5(safe_replace(addslashes($_POST['P'])));

//检查两边的数据库中是否存在该用户
$mySql = "SELECT Id,uType,uName,uPwd,Number,uSeal,lDate,Date,WebStyle,FaxNO,uSign,Estate,Locks,Operator,'7' AS cSign FROM $DataIn.UserTable WHERE uName='$uName' AND uPwd='$MDPassword'";

$myResult = mysql_query($mySql, $link_id) or die ("数据连接错误!");
if ($myRow = mysql_fetch_array($myResult)) {
    $Estate = $myRow["Estate"];
    if ($Estate == 0) {        //禁用状态
        $BackLink = 0;
    } else {                //非禁用状态
        $cSign = $myRow["cSign"];
        $checkAddInfo = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.companys_group WHERE cSign='$cSign' ", $link_id));
        $InIP = $checkAddInfo["InIP"];            //内网连接IP
        $OutIP = $checkAddInfo["OutIP"];    //外网连接IP
        $DomainName = $checkAddInfo["DomainName"];    //域名
        if (preg_match('/^10\.0/', $_SERVER['HTTP_HOST']) || preg_match('/^192\.168/', $_SERVER['HTTP_HOST'])) {//内部IP登录
            $ToAddress = $InIP; //通过内网IP转向相应的系统
            $Login_IP = GetIP();
        } else {
            if (preg_match('/^113\.105/', $_SERVER['HTTP_HOST'])) {//外部IP登录
                $ToAddress = $OutIP; //通过外网IP转向相应的系统
                $Login_IP = $IP;
            } else {//否则为外网登录
                //$ToAddress = $DomainName; //通过哉名转向相应的系统
                $ToAddress = $cSign == 3 ? $DomainName : $_SERVER['HTTP_HOST'];
                $Login_IP = $IP;
            }
        }
        //$Login_IP=GetIP();
        $BackLink = $ToAddress;
        /////////////////////
    }//eo if
} else {//找不到帐户资料
    //如果在同一个IP用同一个用户名登录失败三次，且该用户名存在，则该帐号锁定
    $_SESSION["X"]++;
    if ($_SESSION["X"] > 2) {
        //禁用帐号
    }//eo if
    $BackLink = 1;
}//eo if
echo json_encode(array(
    'rlt' => is_int($BackLink) ? false : true,
    'link' => 'http://' . $BackLink . '/desk/index.php'
));
