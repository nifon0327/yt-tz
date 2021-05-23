<?php

$cSignResult = mysql_query("SELECT cSign FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id);
if ($cSignRow = mysql_fetch_array($cSignResult)) {
	$DataIn = ($cSignRow["cSign"] == 3) ? $DataOut : $DataIn;
}
			      
$Log_Item = "行事历";
switch($ActionId) {
	case "SAVE"://新增记录
	
		$Log_Funtion="保存";
		
		$insertSql = "";
		if (count($info) > 3) {
			//modify by cabbage 20150305 加上邀請人(Target)、地點(Place)、提醒(NotifyTime)
			//2015/03/06 15:00|7|11983|120|台北办公室|TestData
			$dateTime = $info[0];
			$eventType = $info[1];
			$target = $info[2];
			if (strlen($target) == 0) {
				$target = "all";
			}
			$notifyTime = $info[3];
			$place = $info[4];
			$remark = $info[5];
			
			$insertSql = "INSERT INTO $DataPublic.event_sheet (Id, Remark, EventType, DateTime, Target, Place, NotifyTime, Estate, Locks, Date, Operator)
			 				VALUES (NULL, '$remark', '$eventType', '$dateTime', '$target', '$place', '$notifyTime', '1', '1', '$Date', '$LoginNumber');";
							
							//echo "$insertSql";
		}
		else {
			$dateTime = $info[0];
			$eventType = $info[1];
			$remark = $info[2];
			//預設的推送收信人
			$target = "11998,10341,10868,10001,10691";
			
			$insertSql = "INSERT INTO $DataPublic.event_sheet (Id, Remark, EventType, DateTime, Target, Estate, Locks, Date, Operator)
				 			VALUES (NULL, '$remark', '$eventType', '$dateTime', '$target', '1', '1', '$Date', '$LoginNumber');";
							//echo "$insertSql"."    cz";
		}
		 
		$result = @mysql_query($insertSql);
		if ($result && mysql_affected_rows() > 0) {
			$OperationResult="Y";
			$Log = $Log_Item.$Log_Funtion ."成功!<br>";
			$infoSTR = $Log_Funtion ."成功";
		}
		else {
			$Log="<div class='redB'>$Log_Item $Log_Funtion 失败！</div><br>";
			$infoSTR = $Log_Funtion ."失败";
			echo $insertSql."<br>";
		}
	
	break;
	case "UPDATE":
	
		$Log_Funtion="更新";
		
		//add by cabbage 20150306 如果有變動的話要重新設定notify的flag		
		$updateSql = "";
		if (count($info) > 4) {
			//modify by cabbage 20150305 加上邀請人(Target)、地點(Place)、提醒(NotifyTime)
			//2015/03/06 15:00|7|11983|120|台北办公室|TestData
			$id = $info[0];
			$dateTime = $info[1];
			$eventType = $info[2];
			$target = $info[3];
			if (strlen($target) == 0) {
				$target = "all";
			}
			$notifyTime = $info[4];
			$place = $info[5];
			$remark = $info[6];
			
			$updateSql = "UPDATE $DataPublic.event_sheet SET Remark = '$remark', EventType = '$eventType',
							Target = '$target', Place = '$place', NotifyTime = '$notifyTime', Notified = 0,
							DateTime = '$dateTime', Operator = '$LoginNumber' WHERE Id = '$id';";
		}
		else {
			$id = $info[0];
			$dateTime = $info[1];
			$eventType = $info[2];
			$remark = $info[3];
			//預設的推送收信人
			$target = "11998,10341,10868,10001,10691";
			
			$updateSql = "UPDATE $DataPublic.event_sheet SET Remark = '$remark', EventType = '$eventType', Target = '$target', Notified = 0,
							DateTime = '$dateTime', Operator = '$LoginNumber' WHERE Id = '$id';";
		}
		
/* 		echo $updateSql; */
		$result = mysql_query($updateSql);
		if ($result) {
			$OperationResult = "Y";
			$Log = $Log_Item . $Log_Funtion . "成功!<br>";
			$infoSTR .= $Log_Funtion . "成功";
		}
		else {
			$Log = "<div class='redB'>$Log_Item $Log_Funtion 失败！</div><br>";
			$infoSTR .= $Log_Funtion . "失败";
		}  
	break;
	case "DEL":
	
		$Log_Funtion = "删除";
		
		$id = $info[0];
		
		$deleteSql = "DELETE FROM $DataPublic.event_sheet WHERE Id ='$id' and Estate IN (1,2);"; 
		
		
		$result = mysql_query($deleteSql);
		
		if ($result && mysql_affected_rows() > 0) {
			$OperationResult = "Y";
			$Log = $Log_Item . $Log_Funtion . "成功!<br>";
			$infoSTR = $Log_Funtion . "数据成功";
			
			{
					 
	 $oldInfo = mysql_fetch_assoc(mysql_query("select FileNames from $DataPublic.event_recordsheet where EventId=$id "));
	 $oldFiles = $oldInfo["FileNames"];
	
	 $oldFilesArr = explode(",",$oldFiles);
	 $oldCount = count($oldFilesArr);
	 if ($oldCount > 0) {
		 foreach ($oldFilesArr as $oldFileOne) {
		 $oldPath = "../../iphoneAPI/ManagerApp/calendar/recordImage/".$oldFileOne;
		 if (@unlink($oldPath)) {
			 $Log.="删除旧图".$oldFileOne."成功。";
		 } else {
			  $Log.="删除旧图".$oldFileOne."失败。";
		 }
		 }
	 }
	 	
			}
			
			$record = @mysql_query("DELETE FROM $DataPublic.event_recordsheet WHERE EventId ='$id' "); 
			if ($record) {
				$Log.="删除会议记录成功。";
			} else {
				$Log.="删除会议记录失败。";
			}
		}
		else {
			$Log = "<div class='redB'>$Log_Item $Log_Funtion 失败</div><br>";
			$infoSTR = $Log_Funtion . "数据失败";
		}
		/*
		$Log_Funtion="删除";
		$Id= $info[0];
		//删除数据库记录
		$delSql = "DELETE FROM $DataIn.hzqksheet  WHERE Id ='$Id'  and Estate IN (1,2) and Mid=0"; 
		$delRresult = mysql_query($delSql);
		if($delRresult && mysql_affected_rows()>0){
		$OperationResult="Y";
		$Log=$Log_Item .$Log_Funtion ."成功!<br>";
		$infoSTR=$Log_Funtion ."数据成功";
		
		$fileName="H".$Id.".jpg";
		$path = "../../download/cwadminicost/".$fileName;
		if(file_exists($path)){
		unlink($path);
		}
		}
		else{
		$Log="<div class='redB'>$Log_Item $Log_Funtion 失败</div><br>";
		$infoSTR=$Log_Funtion ."数据失败";
		}
		*/
	break;
}

?>