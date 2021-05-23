<?php
//行事曆的事件提醒
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");

	include "d:/website/ac/basic/parameter.inc";
	
	/*
		傳入參數
		$bundleId
		$userIdSTR
		$message
	*/
	$bundleId = "AshCloudApp";
	$userIdSTR = "";//"11998,10341,10868,10001,10691";
	$message = "";
	
	$sendIdList = array();
	$staffSql = "SELECT Number FROM $DataPublic.staffmain WHERE Estate = 1";
	$staffResult = mysql_query($staffSql, $link_id);
	if ($staffRow = mysql_fetch_array($staffResult)) {
		do {
			$sendIdList[] = $staffRow["Number"];
		}
		while ($staffRow = mysql_fetch_array($staffResult));
	}
/* 	$userIdSTR = implode(", ", $sendIdList); */
	
	//紀錄成功與失敗數量
	$sumSuccess = 0;
	$sumFail = 0;
	
	//需要發送的事件Id
	$selectIds = array();
	
	//modify by cabbage 20150305 加上Target和NotifyTime的時間判別
	//2小時前提醒
	$sqlString = "SELECT Id, DateTime, DATE_FORMAT(DateTime,'%H:%i') AS EventTime, Remark, Target, Place
					FROM $DataPublic.event_sheet
					WHERE TIMESTAMPDIFF(MINUTE, CURRENT_TIMESTAMP, DateTime) BETWEEN -10 AND NotifyTime AND Notified = 0 ";

	$result = mysql_query($sqlString, $link_id);
	if($checkRow = mysql_fetch_array($result)){
	
		do{
			$selectIds[] = $checkRow["Id"];
			$eventTime = $checkRow["DateTime"];
			$place = $checkRow["Place"];
			$remark = $checkRow["Remark"];
			$message .= $checkRow["EventTime"]." ".$place." ".$remark."\n";
			
			$target = $checkRow["Target"];
			if ($target == "all") {
				$userIdSTR = implode(", ", $sendIdList);
			}
			else {				
				$userIdSTR = $target;
			}
			
/* 			echo "send to ".$userIdSTR."<br>".$message."<br>"; */
			
			include "d:/website/ac/iPhoneAPI/push_apple.php";
			
			if ($sumSuccess > 0)
			{
				$updateIdString = implode(", ", $selectIds);
				if (strlen($updateIdString) > 0)
				{			
					$updateSqlString = "UPDATE $DataPublic.event_sheet SET Notified = 1 WHERE Id IN ($updateIdString);";	
					$updateResult = mysql_query($updateSqlString, $link_id);
					
					echo $sumSuccess." notifications have been pushed";	
				}
			}
			else
			{
				
			}
		}
		while ($checkRow = mysql_fetch_array($result));
	}
	else
	{
		echo "no events.";
	}
?>