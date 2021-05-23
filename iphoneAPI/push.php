<?php
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head>";

	include "../basic/parameter.inc";
	$pass = "blackberry";	
	$bundleId="ClientAppForWeber";	
	
	$body = array("aps" => array("type" => 'bullletin', "badge" => 1, "sound" => 'received5.caf'));
	$rootPath = $_SERVER['DOCUMENT_ROOT'];
	$perPath = $rootPath ."jt/pushCer/PushCharToClient.pem";
	
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', $perPath); 
	stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);

	 //gateway.sandbox.push.apple.com:2195  //gateway.push.apple.com:2195
	$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
	if($fp)
	{
		echo "success <br>";
	}
	else
	{
		echo "ErrorCode:$err Error:$errstr <br>";
		
	}
	$payload = json_encode($body);

	$tokenSql = "Select token From $DataIn.push_clientapp Where bundleId = '$bundleId' ";
	$tokenResult = mysql_query($tokenSql,$link_id);
	while($tokenRow = mysql_fetch_assoc($tokenResult))
	{
		$deviceToken = $tokenRow["token"];
		
		$msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
		
		if(fwrite($fp, $msg))
		{
		   echo "Success <br>";
		}
		else{
			echo "Fail:$msg <br>";
		}
	}
	fclose($fp);
?>