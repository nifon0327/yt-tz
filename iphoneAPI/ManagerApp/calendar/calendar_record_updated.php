<?php 
$baseUrl = "http://www.ashcloud.com/iphoneAPI/ManagerApp/calendar/recordImage/";
$Log_Funtion="保存";
$RecordContent = $info[2];
$EndTime = $info[3];
$ShareWith = $info[4] ;
$ShareWith = $ShareWith== "" ? "NULL" : "'$ShareWith'";
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
	  $allFileNames = array();
	 $FileArr = array();
	 
		 $imgCount = $_POST["AudioCount"];
	 if ($imgCount>0) {
		 for ($i=0; $i<$imgCount;$i++) {
			
			  	$fileUpName = 'Audio'."$i";
			    $fileName="EventId_au_".$eventId."_$i.caf";
				
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
	
	$insertSql = "INSERT INTO `ac`.`event_recordsheet`
(`Id`,
`EventId`,
`EndTime`,
`FileNames`,
`Record`,
`Date`,
`Operator`,
`Estate`,
`Locks`,
`PLocks`,
`creator`,
`created`,
`modifier`,
`modified`,
ShareWith)
VALUES
(null,
$eventId,
$EndTime,
\"$allFileNamesStr\",
\"$RecordContent\",
'$curDate',
'$Operator',
1,
0,
0,
null,
null,
null,
null,
$ShareWith); ";
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
				"RID"=>"$RecordId",
				"FileArray"=>$FileArr,
				"EndTime"=>"$EndTimeText"
			);
			
			 
 $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log $infoSTR','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

?>