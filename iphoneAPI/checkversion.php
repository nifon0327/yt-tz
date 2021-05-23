<?php 
//检查App版本号
	include "../basic/parameter.inc";
        
                $postInfo = $_POST["info"]==""?$info:$_POST["info"];
                //$postInfo="DailyManagement";
                $mySql="SELECT A.version,A.link,A.updateItem  FROM $Datain.app_sheet A WHERE A.appname='$postInfo' limit 1";
	$jsonArray = array();
	$myResult = mysql_query($mySql);
	if($myRow = mysql_fetch_assoc($myResult))
	{
                       $version = $myRow["version"];
                       $link=$myRow["link"];
                       $updateItem=$myRow["updateItem"];
                       $jsonArray = array( "$postInfo","$version","$link","$updateItem");
                        echo json_encode($jsonArray);
	}
//echo $mySql;	
$fp = fopen("checkversion.log", "a");
fwrite($fp, date("Y-m-d H:i:s") . "    AppName=" . $postInfo ."    ReturnInfo=" . json_encode($jsonArray) ."\r\n");
fclose($fp); 
	
?>