<?php 
	
	include "../basic/parameter.inc";
	
	$pass = "vulcan1130";	
	
	//$message = $notifiType;
		
	$body = array("aps" => array("type" => 'bullletin', "badge" => 0, "sound" => 'received5.caf'));
	$rootPath = $_SERVER['DOCUMENT_ROOT'];
	$perPath = "$rootPath/ipdAPI/pushCer/Attendance_ck.pem";
	//$tokenStr = "298a3e1b af3e5f19 4b1d165e 6eaa60f5 89f0c9f3 9f2f5e89 1f7b3dc4 9c69b807";
	//					   a15b5415 ee2f2f44 1af30ce6 3a7c8621 9a6ff056 79be5556 df1e0427 6530ac0c
	//$perPath
	$ctx = stream_context_create();
	//stream_context_set_option($ctx, 'ssl', 'local_cert', "pushCer/attendance_production_ck.pem"); 
	stream_context_set_option($ctx, 'ssl', 'local_cert', $perPath); 
	stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
	 
	$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);		
	
	if($fp)
	{
		//echo "okok";
	}
	else
	{
		//echo "error";
	}
	
	$payload = json_encode($body);
	$sign = ($DataIn == "d7")?7:3;
	$tokenSql = "Select token From $DataIn.push_app Where bundleId = 'attendance' And Number = '$sign' ";
	//echo $tokenSql;
	$tokenResult = mysql_query($tokenSql);
	while($tokenRow = mysql_fetch_assoc($tokenResult))
	{
		$deviceToken = $tokenRow["token"];
		
		$msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
		//print "Sending message :" . $payload . "\n";  
		if(fwrite($fp, $msg))
		{
			//echo $deviceToken;
		}
	}
	fclose($fp);

?>