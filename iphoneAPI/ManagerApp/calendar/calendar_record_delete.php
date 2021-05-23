<?php 
$baseUrl = "http://www.ashcloud.com/iphoneAPI/ManagerApp/calendar/recordImage/";
$Log_Funtion="修改";

 $EndTimeText = "";
 $Log_Item="会议记录"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
$Log = "";
{
					 
	 $oldInfo = mysql_fetch_assoc(mysql_query("select FileNames,Record from $DataPublic.event_recordsheet where EventId=$eventId "));
	 $oldFiles = $oldInfo["FileNames"];
	$Record =  $oldInfo["Record"];
	$Log.=" 记录内容：  ".$Record."\n";
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
			
			$record = @mysql_query("DELETE FROM $DataPublic.event_recordsheet WHERE EventId ='$eventId' "); 
			if ($record) {
				$Log.="删除会议记录成功。";
				$OperationResult = "Y";
			} else {
				$Log.="删除会议记录失败。";
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