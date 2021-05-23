<?php 
//讀取行事曆事件
 //$LoginNumber = 11965==$LoginNumber ? "10001" : $LoginNumber;
$vocationList = array();

//event list

$eventTypeList = array();
$eventIdList = array();

$mySql = "SELECT CONCAT('EventType_', Id) AS Name, Id FROM $DataPublic.event_type WHERE ESTATE = '1'";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)) {
	do {
		
		$eventTypeList[] = $myRow["Name"];
		$eventIdList[] = $myRow["Id"];
		
	} while ($myRow = mysql_fetch_array($myResult));
}
$eventList = array();

$sqlString = "";
/*
	SELECT EventDate, SUM(IsHoliday) AS IsHoliday, EventType_1, EventType_2, EventType_3 
	FROM
		((SELECT DATE_FORMAT(DateTime,'%Y-%m-%d') AS EventDate, 0 AS IsHoliday,
		SUM(CASE WHEN (EventType = '1') THEN 1 ELSE 0 END) AS EventType_1,
		SUM(CASE WHEN (EventType = '2') THEN 1 ELSE 0 END) AS EventType_2,
		SUM(CASE WHEN (EventType = '3') THEN 1 ELSE 0 END) AS EventType_3
		FROM event_sheet
		GROUP BY EventDate)
			UNION ALL
		(SELECT H.Date AS EventDate, 1 AS IsHoliday,
		'' AS EventType_1, '' AS EventType_2, '' AS EventType_3
		FROM d0.kqholiday H
		ORDER BY EventDate)) AS C
	GROUP BY EventDate
*/
//把sqlString 組合成類似以上字串
$selectString = "";
$selectEventString = "";
$selectHolidayString = "";

for ($i = 0; $i < count($eventIdList); $i++) {

	$eventId = $eventIdList[$i];
	$eventTypeStr = $eventTypeList[$i];
	
	$selectString .= (", ".$eventTypeStr);
	$selectEventString .= (", SUM(CASE WHEN (EventType = '".$eventId."') THEN 1 ELSE 0 END) AS ".$eventTypeStr);
	$selectHolidayString .= (", '' AS $eventTypeStr");
}

$sqlString = "SELECT EventDate, SUM(IsHoliday) AS IsHoliday, SUM(EventsCount) AS EventsCount $selectString
				FROM
					((SELECT DATE_FORMAT(DateTime,'%Y-%m-%d') AS EventDate, 0 AS IsHoliday, COUNT(Id) AS EventsCount $selectEventString
						FROM $DataPublic.event_sheet
						GROUP BY EventDate)
					UNION ALL
					(SELECT H.Date AS EventDate, 1 AS IsHoliday, 0 AS EventsCount $selectHolidayString
						FROM $DataPublic.kqholiday H
						ORDER BY EventDate)) AS C
				GROUP BY EventDate";
$myResult = mysql_query($sqlString,$link_id);
if($myRow = mysql_fetch_array($myResult)) {
	do {
		
		$date = $myRow["EventDate"];
		$textColor = ($myRow["IsHoliday"] == 1) ? "holiday" : "";
		$icon = "";
		$badgeNumber = "";
		
		for ($i = 0; $i < count($eventIdList); $i++) {

			$eventId = $eventIdList[$i];
			$eventTypeStr = $eventTypeList[$i];
			
			$eventCount = $myRow[$eventTypeStr];
			$icon .= ($eventCount == 0) ? "|" : "$eventId|";
			$badgeNumber .= ($eventCount < 1) ? "|" : "$eventCount|";
			
		}
		
		$hasRecord = "";
		if ($myRow["EventsCount"] > 0) {
			$checkImg = mysql_query("select R.Id,E.Operator,E.Target from event_recordsheet R left join event_sheet E on E.Id=R.EventId 

where DATE_FORMAT(DateTime,'%Y-%m-%d')='$date';");

while ($checkImgRow = mysql_fetch_assoc($checkImg)) {
	$sOperator = $checkImgRow["Operator"];
	 if (in_array($LoginNumber,$powerArray)) {
					 $hasRecord = "1";
					 break;
				 }
	$sTarget = $checkImgRow["Target"];
	if ($sTarget == "all") {
		$hasRecord = "1";
		break;
	} else {
		$sTargetIds = explode(",",$sTarget);
		if ($sOperator==$LoginNumber || in_array($LoginNumber,$sTargetIds)) {
			$hasRecord = "1";
			break;
		}
	}
}
		}
		
		$eventList[$date] = array(
			"textColor" => $textColor,
			"icon" => trim($icon, "|"),
			"badgeNumber" => trim($badgeNumber, "|"),
			"eventsCount" => $myRow["EventsCount"],
			"has"=>$hasRecord,
		);
		
	} while ($myRow = mysql_fetch_array($myResult));
}

//color list
$colorList = array(
	"holiday" => "#e84135"
);

//return json
$jsonArray[] = array(
	"colorList" => $colorList,
	"data" => $eventList
);
?>