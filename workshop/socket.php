<?php

//--------测试代码---------------
/*
$host = "192.168.30.214";  
$port = 30030;
echo socket_send_msg($host,$port);
*/

//echo display_send_msg('3A-4','reload');

function display_send_msg($Identifier,$msg){

    include($_SERVER['DOCUMENT_ROOT'] .  "/basic/parameter.inc");
   
    $status=0;
	$checkResult=mysql_query("SELECT IP,Port FROM $DataPublic.ot2_display WHERE Identifier='$Identifier' LIMIT 1",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)) {
	   $IP=$checkRow["IP"];
	   $Port=$checkRow["Port"];
	   $status=socket_send_msg($IP,$Port,$msg);
	}
	return $status;
}

function socket_send_msg($host,$port,$msg='reload')
{
    set_time_limit(0);
	
	$fp = stream_socket_client("tcp://$host:$port", $errno, $errstr, 30,STREAM_CLIENT_CONNECT);  
	if (!$fp) {  
	        //fclose($fp); 
	        return "$errstr ($errno)";  
	} 
	else { 
	        $msg="reload"; 
	        $status=fwrite($fp, $msg,strlen($msg));
	        fclose($fp); 
	        return  $status;
	 }  
}

?>