<?php
	include "../basic/chksession.php" ;
	include "../basic/parameter.inc";
	
	$targetAddress = "192.168.30.55";
	
	//set_time_limit(1);  
	$cardNumbers = array();
	$cardResult = mysql_query("Select * From $DataIn.dsrc_list");
	while($cardRow = mysql_fetch_assoc($cardResult))
	{
		$cardNumbers[] = $cardRow["CardNumber"]."0000";
	}
	
	$cardsCount = count($cardNumbers);
	$groupCount = ($cardsCount%10 == 0)?intval($cardsCount/10):intval($cardsCount/10)+1;
	for($i = 0; $i< $groupCount; $i++)
	{
		$stateByte = chr(0xff);
		$functionByte = "81a1";
		$currentNumbers = ($i+1 == $groupCount)? $cardsCount-$i*10:10;
		$lengthInByte = sprintf("%04x", $currentNumbers*8+7); //长度
		$packNumber = sprintf("%04x", $i+1); //帧号
		$countInByte = sprintf("%04x", $currentNumbers);
		$isEnd = ($i+1 == $groupCount)?"01":"00";
		
		$cards = "";
		for($j=0; $j<$currentNumbers; $j++)
		{
			$cards.=$cardNumbers[$i*10+$j];
		}
		
		$packageNumbersInStr = $functionByte.$lengthInByte.$packNumber.$countInByte.$isEnd.$cards;
		$checkSeed = chr(0x00);
		$packageNumbersInByte = "";
		for($k=0; $k<strlen($packageNumbersInStr)/2; $k++)
		{
			$tempByte = chr(hexdec(substr($packageNumbersInStr, $k*2, 2)));
			$checkSeed ^= $tempByte;
			$packageNumbersInByte .= $tempByte;
		}
		
		$packUp = $stateByte.$packageNumbersInByte.$checkSeed.$stateByte;
		$finalPackage .= $packUp;
	}
	
	$socket = socket_create (AF_INET, SOCK_STREAM, SOL_TCP);
	socket_connect($socket, $targetAddress, 21003);
	socket_write($socket, $finalPackage, strlen($finalPackage));
	
	
	
?>