<?php 
//讀取行事曆事件明細
function getNameUseNumber($Number,$DataPublic,$DataIn) {
if ($Number>0){
		$pResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Number ORDER BY Number LIMIT 1");
		if($pRow = mysql_fetch_array($pResult)){
			   $Number=$pRow["Name"];
		}
		else
		{
		   //外部人员资料
		   $otResult = mysql_query("SELECT Name FROM $DataIn.ot_staff WHERE Number=$Number ORDER BY Number LIMIT 1");
		   if($otRow = mysql_fetch_array($otResult)){
			     $Number=$otRow["Name"];
		     } 
	    }
}

return $Number;
}

/**/
if ($LoginNumber == 11965 ) 
 $LoginNumber = 10001;
$eventList = array();
//
$nowDate = date("Y-m-d");
$nowTime = date("Y/m/d H:i");
$noHappen = 0;
$timeNow = strtotime($nowDate);
$timeSel = strtotime($selectDate);
if ($timeNow  < $timeSel ) {
	$noHappen = 1;
}
$powerArray=array(10868,10001);
/*
	
	 else if ($timeNow == $timeSel) {
	$noHappen = 2;
} 
*/
$hasRecord = versionToNumber($AppVersion)>=311 ? true : false;
$baseUrl = "http://www.ashcloud.com/iphoneAPI/ManagerApp/calendar/recordImage/";
//modify by cabbage 20150305 加上「地點」和權限
$mySql = "
SELECT E.Id, DATE_FORMAT(E.DateTime,'%H:%i') AS Time, DATE_FORMAT(E.DateTime,'%Y/%m/%d %H:%i') AS EventDateTime, 
			E.Operator LIKE '$LoginNumber' AS AllowEdit,
			E.Place, E.EventType, E.Remark, E.Operator, E.NotifyTime,
			 IF(E.Target LIKE 'all', '', E.Target) AS Target ,
			 R.FileNames,if(R.EndTime is null,'',DATE_FORMAT(R.EndTime,'%H:%i')) AS EndTime,R.Record,R.Operator as Recorder,R.Id as RecordId,R.ShareWith
			FROM $DataPublic.event_sheet E
			
			left join $DataPublic.event_recordsheet R on R.EventId=E.Id  and R.Id!=65
			
			WHERE DATE_FORMAT(DateTime,'%Y-%m-%d') = '$selectDate' AND E.Estate = 1 ORDER BY Time;
			
			";
			/*WHERE DATE_FORMAT(DateTime,'%Y-%m-%d') = '$selectDate' AND Estate = 1 ORDER BY Time;
SELECT E.Id, DATE_FORMAT(E.DateTime,'%H:%i') AS Time, DATE_FORMAT(E.DateTime,'%Y/%m/%d %H:%i') AS EventDateTime, 
			E.Operator LIKE '$LoginNumber' AS AllowEdit,
			E.Place, E.EventType, E.Remark, E.Operator, E.NotifyTime, IF(E.Target LIKE 'all', '', E.Target) AS Target 
			FROM $DataPublic.event_sheet E
			
			left join $DataPublic.event_recordsheet R on R.EventId=E.Id
			
			WHERE DATE_FORMAT(DateTime,'%Y-%m-%d') = '$selectDate' AND Estate = 1 ORDER BY Time;*/

$myResult = mysql_query($mySql, $link_id);

// cansee canoper 
if($myRow = mysql_fetch_array($myResult)) {
	do {
        
        $targetNames = "";
		$canSee = "0";
        if (strlen($myRow["Target"]) > 0) {
	        $targetIds = explode(",", $myRow["Target"]);
	        foreach ($targetIds as &$Operator) {
				if ($Operator == $LoginNumber) {
			$canSee = "1";
		}
		        include "../../model/subprogram/staffname.php";
		        $targetNames .= ($Operator."、");
	        }
	        if (in_array($LoginNumber,$targetIds)) {
				$canSee = "1";
			}
	        $targetNames = rtrim($targetNames, "、");
        }
        else {
			$canSee = "1";
	        $targetNames = "全部人";
        }       
		 $EndTime = $myRow["EndTime"];
		 $ShareWith = $myRow["ShareWith"];
		 
        $Operator = $myRow["Operator"];
		if ($Operator == $LoginNumber ||  in_array($LoginNumber,$powerArray)) {
			$canSee = "1";
		}
		$ShareWithIds =  $ShareWithNms = "";
		$isShareId = 0;
			$canEditRecord = ($EndTime=="" && $canSee=="1" )? "1":"0";
				 $canShare = "0";
			 if ($canSee == 1) {
				 $canShare = "1";
			 }
				
		if ($ShareWith!="") {
			$ShareWithIds = explode(",",$ShareWith);
			$allShareNames = array();
			foreach ($ShareWithIds as $shareId) {
				if ($shareId == $LoginNumber) {
					$canSee = "1";
					$isShareId = 1;
				}
		       $shareName = getNameUseNumber($shareId,$DataPublic,$DataIn);
			   $allShareNames[]=$shareName;
			   
	        }
			$ShareWithNms= implode("、",$allShareNames);
			
		}
        include "../../model/subprogram/staffname.php"; 
		
		if ($canSee!="1") {
			$ShareWith= $ShareWithNms="";
		}
	
		$eventList[] = array(
			"Id" => $myRow["Id"],
			"Col1" => $myRow["Time"],
			"Col2" => $Operator,
			"Col3" => $myRow["Place"],
			"Col4" => $targetNames,
			"NotifyTime" => $myRow["NotifyTime"],
			"Target" => $myRow["Target"],
			"AllowEdit" => $EndTime==""?$myRow["AllowEdit"]:"0",
			"Remark" => $myRow["Remark"],
			"EventType" => $myRow["EventType"],
			"EventDateTime" => $myRow["EventDateTime"],"EndTime" => "$EndTime", 
			"Share"=>"$ShareWithNms","ShareIds"=>"$ShareWith"
		);
		if ($noHappen==1) {
			$canEditRecord = "0";
		} else if ($noHappen == 2) {
			if ($canEditRecord == "1") {
				$getTime = $myRow["EventDateTime"];
				if (strtotime($nowTime) < strtotime($getTime)) {
					$canEditRecord = "0";
				}
			}
		}
		if ($hasRecord == true) {
			// R.FileNames,R.EndTime,R.Record,R.Operator as Recorder,R.Id as RecordId
			 $FileNames = $myRow["FileNames"];
			 
			
			 
			 
			   $Record = $myRow["Record"];
			    $Recorder = $myRow["Recorder"];
				if ($Operator!="") {
					$Operator = $Recorder;
					include "../../model/subprogram/staffname.php";
					$Recorder = $Operator;
				}
				
			
				 $RecordId = $myRow["RecordId"];
				 $eventId = $myRow["Id"];
				 if (10341 == $LoginNumber) {
					 $canSee = "1";
				 }
				 if ($canSee == "0") {
					$RecordId = "";
					$canEditRecord = "0";
					$Record = "";
					$Recorder = "";
					$EndTime = "";
				
					$FileNames = "";
				}
				 $FileArr = array();
			 if ($FileNames != "") {
				$FileArrNames = explode(",",$FileNames);
				foreach ($FileArrNames  as $singleFile) {
					$singleFileType = explode(".",$singleFile);
					$sType = $singleFileType[1];
			
					$FileArr[]=array("Type"=>$sType=="caf"?"caf":"img","url"=>"$baseUrl"."$singleFile","sType"=>"$sType");
				}
			 }
			 
			 $Record = trim($Record);
			 if ($Record!="") {
			 $Record = "\n".$Record."";
			 }
			 /*
				 "pad_attr"=>array(array("退回原因：\n","#FF0000","11"),array("有订单可以继续使用","#AAAAAA","11"),array("有订单可以继续使用","#AAAAAA","11"),array("有订单可以继续使用","#AAAAAA","11"),array("有订单可以继续使用","#AAAAAA","11"),array("有订单可以继续使用","#AAAAAA","11"),array("有订单可以继续使用","#AAAAAA","11"),array("有订单可以继续使用","#AAAAAA","11"))
			 */
			
			$eventList[] = array(
			"Xib"=>"1","ID"=>"$eventId",
			"RID" => "$RecordId",
			"Record" => "$Record",
			"Recorder" => "$Recorder",
			"EndTime" => "$EndTime", 
			"Files" =>"$FileNames",
			"EventType" => $myRow["EventType"],
			"EventDateTime" => $myRow["EventDateTime"],
			"FileArray"=>$FileArr,"CanEdit"=>"$canEditRecord",
			"Remark" => "","AllowEdit" =>"","NotifyTime" =>"",
			"Share"=>"$ShareWithNms","ShareIds"=>"$ShareWith","CanShare"=>"$canShare",
			
		);
		}
		
	} while ($myRow = mysql_fetch_array($myResult));
}

//return json
$jsonArray[] = $eventList;
?>