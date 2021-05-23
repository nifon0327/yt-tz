<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<?php   
	
	//include "../model/modelhead.php";
	
	$cName = $_POST["cNameTag"];
	$eCode = $_POST["eCodeTag"];
	$POStr = $_POST["POStrTag"];
	$barCode = $_POST["barCodeTag"];
	
	$startStr = chr(0x02);
	$endStr = chr(0x03);
	
	//$jobSelectStr = $startStr."~JS0|workshop|0|productName|$cName|eCode|$eCode|POcode|$POStr|barCode|$barCode|".$endStr;
	$jobSelectStr = $startStr."~JS0|workshop|0|productName|$cName|eCode|$eCode|POcode|$POStr|barCode|$barCode|".$endStr;
	
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
	$ip = "192.168.30.9";
	$port = "21000";
	
	if(socket_connect($socket, $ip, $port))
	{
		socket_write($socket, $jobSelectStr, strlen($jobSelectStr));
	}

?>