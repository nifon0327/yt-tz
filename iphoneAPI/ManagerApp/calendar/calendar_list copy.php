<?php 
//讀取行事曆事件明細

$eventList = array();
//

//modify by cabbage 20150305 加上「地點」和權限
$mySql = "SELECT E.Id, DATE_FORMAT(E.DateTime,'%H:%i') AS Time, DATE_FORMAT(E.DateTime,'%Y/%m/%d %H:%i') AS EventDateTime, 
			E.Operator LIKE '$LoginNumber' AS AllowEdit,
			E.Place, E.EventType, E.Remark, E.Operator, E.NotifyTime, IF(E.Target LIKE 'all', '', E.Target) AS Target 
			FROM $DataPublic.event_sheet E
			
			
			WHERE DATE_FORMAT(DateTime,'%Y-%m-%d') = '$selectDate' AND Estate = 1 ORDER BY Time;";

$myResult = mysql_query($mySql, $link_id);

// cansee canoper 
if($myRow = mysql_fetch_array($myResult)) {
	do {
        
        $targetNames = "";
        if (strlen($myRow["Target"]) > 0) {
	        $targetIds = explode(",", $myRow["Target"]);
	        foreach ($targetIds as &$Operator) {
		        include "../../model/subprogram/staffname.php";
		        $targetNames .= ($Operator."、");
	        }
	        
	        $targetNames = rtrim($targetNames, "、");
        }
        else {
	        $targetNames = "全部人";
        }       
		
        $Operator = $myRow["Operator"];
        include "../../model/subprogram/staffname.php"; 
	
		$eventList[] = array(
			"Id" => $myRow["Id"],
			"Col1" => $myRow["Time"],
			"Col2" => $Operator,
			"Col3" => $myRow["Place"],
			"Col4" => $targetNames,
			"NotifyTime" => $myRow["NotifyTime"],
			"Target" => $myRow["Target"],
			"AllowEdit" => $myRow["AllowEdit"],
			"Remark" => $myRow["Remark"],
			"EventType" => $myRow["EventType"],
			"EventDateTime" => $myRow["EventDateTime"],
		);
		
	} while ($myRow = mysql_fetch_array($myResult));
}

//return json
$jsonArray[] = $eventList;
?>