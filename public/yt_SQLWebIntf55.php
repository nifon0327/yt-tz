<?php
//http://localhost/yt_SQLWebIntf.php?s=C616E8EE2331DBC7984A0B25343BC025B55BA0D9225E34598DC50688D79DB58F83FDD8D40CDE0D6C2ECEFF744C116A2CA64D76C916A76E40F1CC46C4B90A140A95ACB25BC2A4A82137BDCD5AEBA984F00654CE13A9C7EE9792BF5F4E03E6FBF0FD74359B5449F6CC&u=zhangy
//header("Content-Type:text/html; charset=utf-8");

if ( !function_exists( 'hex2bin' ) ) {
    function hex2bin( $str ) {
        $sbin = "";
        $len = strlen( $str );
        for ( $i = 0; $i < $len; $i += 2 ) {
            $sbin .= pack( "H*", substr( $str, $i, 2 ) );
        }

        return $sbin;
    }
}
function des_decrypt($str, $key) {
    $str = hex2bin( strtolower( $str ) );
    $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
    $len = strlen($str);
    $block = mcrypt_get_block_size('des', 'ecb');
    //$pad = ord($str[$len - 1]);
    //return substr($str, 0, $len - $pad);
    return $str;
}
$URI_s=isset($_POST['s']) ? $_POST['s'] : '';
$URI_u=isset($_POST['u']) ? $_POST['u'] : '';
$user=$URI_u;
$key="";
$errorstr="";
$errorcode=0;
$jarr = array();
try{
    //连接数据库
    $con = mysqli_connect("cz.matechstone.com","root","jianbao2017@wolfhe","ac_cz") ;
    if (!$con){
        throw new Exception("connect fieled!".mysqli_connect_error());
    };
    mysqli_query($con,"SET NAMES UTF8");
    //选择数据库
    $selectResult=mysqli_select_db($con,'ac_cz');
    if (!$selectResult){
        throw new Exception("select db failed!".mysqli_error());
    };
    //获得解密密码
    $qrylogin=mysqli_query($con,'SELECT UPWD FROM usertable WHERE uName="'.$user.'"');
    if (!$qrylogin){
        throw new Exception("query key failed!".mysqli_error());
    };
    $qrynum=mysqli_num_rows($qrylogin);
    if ($qrynum==0){
        throw new Exception("login failed!");
    }else{
        $row=mysqli_fetch_array($qrylogin,MYSQL_ASSOC);
        $key=$row["UPWD"];
    }
    mysqli_free_result($qrylogin);
    //使用密码hash前6位解密s
    $sql=des_decrypt($URI_s,substr($key,0,8));
    //判断sql
    if(substr($sql,0,strlen($user))==$user){
        $sql=substr($sql,strlen($user));
    }else{
        throw new Exception("key failed!");
    };
    //执行sql
    //	echo $sql."<br>";
    $qry=mysqli_query($con,$sql);
    if (!$qry){
        throw new Exception("query sql failed!".mysqli_error()." [SQL]".$sql);
    };
    while ($rows=mysqli_fetch_array($qry,MYSQL_ASSOC)){
        $count=count($rows);//不能在循环语句中，由于每次删除 row数组长度都减小
        for($i=0;$i<$count;$i++){
            unset($rows[$i]);//删除冗余数据
        }
        array_push($jarr,$rows);
    }
    mysqli_free_result($qry);
}catch(Exception $e){
    $errorstr=$e->getMessage();
}
mysqli_close($con);
if($errorcode==0 && $errorstr!=""){$errorcode=1;};
//$errorstr=$errorstr.$key;
$string1 = json_encode(array("errorcode"=>$errorcode,"errorstr"=>$errorstr,"data"=>$jarr));
echo($string1);

//jason方式2：
//$jobj=new stdclass();//实例化stdclass，这是php内置的空类，可以用来传递数据，由于json_encode后的数据是以对象数组的形式存放的，
//所以我们生成的时候也要把数据存储在对象中
//foreach($jarr as $key=>$value){
//$jobj->$key=$value;
//}
//echo '传递属性后的对象：';
//print_r($jobj);//打印传递属性后的对象
//echo '<br>';
//echo '编码后的json字符串：'.json_encode($jobj).'<br>';//打印编码后的json字符串



?>
