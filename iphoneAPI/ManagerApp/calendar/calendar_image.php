<?php 
//讀取行事曆事件類別圖片列表

$imageList = array();

$domain = "113.105.80.226";
$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
$folderPath = "http://www.ashcloud.com/iphoneAPI/ManagerApp/calendar/image/";

$mySql = "SELECT Id FROM $DataPublic.event_type WHERE Estate = 1;";
$myResult = mysql_query($mySql, $link_id);
if($myRow = mysql_fetch_array($myResult)) {
	do {
	
		$eventId = $myRow["Id"];
		$imageList[$eventId] = $folderPath . "ic_event_$eventId.png";
		
	} while ($myRow = mysql_fetch_array($myResult));
}

$jsonArray = $imageList;
?>