<?php 
//讀取行事曆事件類別

$searchList = array();
$recordList = array();
$baseUrl = "http://www.ashcloud.com/iphoneAPI/ManagerApp/calendar/recordImage/";
$hasRecord = versionToNumber($AppVersion)>=311 ? true : false;
/*$mySql = "SELECT DATE_FORMAT(S.DateTime,'%Y/%m/%d') AS EventDate, DATE_FORMAT(S.DateTime,'%H:%i') AS EventTime,
			S.Remark, S.Operator, T.Name AS EventType
			FROM $DataPublic.event_sheet AS S
			LEFT JOIN $DataPublic.event_type AS T ON S.EventType = T.Id
			ORDER BY S.DateTime";
			*/
$mySql =	"SELECT DATE_FORMAT(S.DateTime,'%Y/%m/%d') AS EventDate, DATE_FORMAT(S.DateTime,'%H:%i') AS EventTime,
			S.Remark, S.Operator, T.Name AS EventType,S.Target,
			 R.FileNames,R.EndTime,R.Record,R.Operator as Recorder,R.Id as RecordId,
			 R.ShareWith
			FROM $DataPublic.event_sheet AS S
			LEFT JOIN $DataPublic.event_type AS T ON S.EventType = T.Id
			left join $DataPublic.event_recordsheet R on R.EventId=S.Id
			ORDER BY S.DateTime desc";

$myResult = mysql_query($mySql, $link_id);
$count = 0;
if($myRow = mysql_fetch_array($myResult)) {
	do {
        $Operator = $myRow["Operator"];
        include "../../model/subprogram/staffname.php";
         $Record = $myRow["Record"];
		$searchList[] = array(
			"eventDate" => $myRow["EventDate"],
			"eventTime" => $myRow["EventTime"],
			"remark" => $myRow["Remark"],
			"operator" => $Operator,
			"eventType" => $myRow["EventType"],
			"RecordIndex"=>"$count",
			"Record"=>$Record!=""?"$$$"."$Record":""
		);
		$Target = $myRow["Target"];
		$ShareWith = $myRow["ShareWith"];
		
		$canSees = 0;
		 if (10341 == $LoginNumber || 11965 == $LoginNumber) {
					 $canSees = 1;
				//	 break;
			}
			if ($canSees == 0) {
				
				if ($Operator==$LoginNumber) {
			$canSees = 1;
		}
		else {
			if ($Target=='all') {
				$canSees = 1;
			} else {
				$targetIds = explode(",", $Target);
				if (in_array("$LoginNumber", $targetIds)) {
					$canSees = 1;
				} else {
					if ($ShareWith!='') {
						$targetIds = explode(",", $ShareWith);
						if (in_array("$LoginNumber", $targetIds)) {
					$canSees = 1;
				}
					}
				}
			}
		}
			}	
		
			
	
		if ($hasRecord == true) {
			$count ++;
			// R.FileNames,R.EndTime,R.Record,R.Operator as Recorder,R.Id as RecordId
			
				 $RecordId = $myRow["RecordId"];
				if ($canSees==0) {
					$FileNames = "";
					$Operator = "";
					$RecordId = "";
					$EndTime = "";
				}
			
			 $FileNames = $myRow["FileNames"];
			 
			 $FileArr = array();
			 
			 
			 if ($FileNames != "") {
				$FileArrNames = explode(",",$FileNames);
				foreach ($FileArrNames  as $singleFile) {
					$singleFileType = explode(".",$singleFile);
					$sType = $singleFileType[1];
			
					$FileArr[]=array("Type"=>$sType=="caf"?"caf":"img","url"=>"$baseUrl"."$singleFile","sType"=>"$sType");
				}
			 }
			 
			 
			  
			    $Recorder = $myRow["Recorder"];
				if ($Operator!="") {
					$Operator = $Recorder;
					include "../../model/subprogram/staffname.php";
					$Recorder = $Operator;
				}
				$Record = "\n".$Record."\n";
		
				 $eventId = '';
			$recordList[] = array(
			"Xib"=>"1","ID"=>"$eventId",
			"RID" => "$RecordId",
			"Record" => "$Record",
			"Recorder" => "$Recorder",
			"EndTime" => "$EndTime", 
			"Files" =>"$FileNames",
		"eventDate" => $myRow["EventDate"],
			"FileArray"=>$FileArr
		);
		}
		
	} while ($myRow = mysql_fetch_array($myResult));
}

//return json
$jsonArray[] = $searchList;
if ($hasRecord == true) {
	$jsonArray = array("Search"=>$searchList,"Record"=>$recordList);
}
?>