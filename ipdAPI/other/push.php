<?php 

	include "../basic/parameter.inc";

	$pass = "vulcan1130";	
	
	$body = array("aps" => array("alert" => "$message", "badge" => 0, "sound" => 'received5.caf'));
	
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem'); 
	stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
	 
	$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
	/*
	if (!$fp)
	{
    	return;
	}	
	*/
	// send message
	$payload = json_encode($body);
	
	$tokenSql = "Select token From $DataIn.push_app Where bundleId = 'pushTest'";
	$tokenResult = mysql_query($tokenSql);
	while($tokenRow = mysql_fetch_assoc($tokenResult))
	{
		$deviceToken = $tokenRow["token"];
		$msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
		//print "Sending message :" . $payload . "\n";  
		fwrite($fp, $msg);
	}
	fclose($fp);
	
?>