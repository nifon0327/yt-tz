<?php 
$baseUrl = "http://www.ashcloud.com/iphoneAPI/ManagerApp/calendar/recordImage/";
$Log_Funtion="修改";
$RecordContent = $info[2];
$EndTime = $info[3];
 $EndTimeText = "";
if (strlen($EndTime) < 5) {
	$EndTime = "NULL";
} else {
	$EndTimeText = date("H:i",strtotime($EndTime));
	$EndTime = "'$EndTime'";
}







 $Log_Item="会议记录"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
	 $Log = "";
	 //	$eventId = $info[0]; $recordId = $info[1]; 
	 
	 
	 
	 
	 
	 
	 $oldInfo = mysql_fetch_assoc(mysql_query("select EventId,FileNames,ModifyTimes from $DataPublic.event_recordsheet where Id=$recordId "));
	 $oldFiles = $oldInfo["FileNames"];
	 $ModifyTimes = $oldInfo["ModifyTimes"];
	 $aEventId = $oldInfo['EventId'];
	 $ModifyTimes ++;
	 
	 
	 
	 $startTimes = mysql_fetch_assoc(mysql_query("select DateTime from $DataPublic.event_sheet where Id=$aEventId "));
	 $startTime = $startTimes['DateTime'];
	 if (strtotime($EndTime) < strtotime($startTime))  {
		 $Log.="结束时间设置失败，不能在会议时间之前！";
		 $EndTime = "NULL";
	 }
	 
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
	 
	  $allFileNames = array();
	 $FileArr = array();
	 
		 $imgCount = $_POST["AudioCount"];
	 if ($imgCount>0) {
		 for ($i=0; $i<$imgCount;$i++) {
			
			  	$fileUpName = 'Audio'."$i";
			    $fileName="EventId_au_".$eventId."_".$i."_".$ModifyTimes.".caf";
				
				$path = "../../iphoneAPI/ManagerApp/calendar/recordImage/".$fileName;
				if(move_uploaded_file($_FILES[$fileUpName]['tmp_name'],$path))
				{
					$allFileNames[]=$fileName;
					$infoSTR.= "上传文件成功！";	
					$FileArr[]=array("Type"=>"caf","url"=>"$baseUrl"."$fileName","sType"=>"$sType");			
				} else {
					$infoSTR.= "上传文件失败！";		
				}




		 }
	 }
	 $imgCount = $_POST["FileCount"];
	
	 if ($imgCount>0) {
		 for ($i=0; $i<$imgCount;$i++) {
			  $upFile=$_POST["Data$i"];
			  $fileUpName = 'upFile'."$i";
			    $fileName="EventId_".$eventId."_$i.jpg";
				
				$path = "../../iphoneAPI/ManagerApp/calendar/recordImage/".$fileName;
				if(move_uploaded_file($_FILES[$fileUpName]['tmp_name'],$path))
				{
					$allFileNames[]=$fileName;
					$infoSTR.= "上传文件成功！";	
					$FileArr[]=array("Type"=>"img","url"=>"$baseUrl"."$fileName","sType"=>"$sType");		
				} else {
					$infoSTR.= "上传文件失败！";		
				}
		 }
	 }
	
	
	
	$allFileNamesStr = implode(",",$allFileNames);
	
	$insertSql = "UPDATE `ac`.`event_recordsheet`
SET
`EndTime` = $EndTime,
`FileNames` = '$allFileNamesStr',
`Record` = \"$RecordContent\",
`Date` = '$curDate',
`Operator` = '$Operator',
`modifier` = '$Operator',
`modified` = '$DateTime',
`ModifyTimes` = '$ModifyTimes'
WHERE `Id` = $recordId;
";
	 if (@mysql_query($insertSql)) {
		 $OperationResult = "Y";
		 $RecordId = mysql_insert_id();
	 }
 /*
	echo  var_dump($_FILES);
	echo "<br>";
	echo  var_dump($infoSTR);
	*/
	$jsonArray = array(
				"ActionId" => "$ActionId",
				"Result" => "$OperationResult",
				"Info"=>"$infoSTR",
				"RID"=>"$recordId",
				"FileArray"=>$FileArr,
				"EndTime"=>"$EndTimeText"
			);
			
			 
 $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log $infoSTR','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

?>