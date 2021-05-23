<?php   
//采购配件图片未上传提醒功能推送
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
include "d:/website/mc/basic/parameter.inc";

$msgArray=array();$StuffIdArray=array();
$DateTime=date("Y-m-d H:i:s");
 $CheckResult=mysql_query("SELECT DISTINCT D.StuffId,D.StuffCname,T.Picjobid 
                        FROM $DataIn.gys_shsheet S  
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
                        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
                        WHERE S.Estate=1 AND D.Picture=0 AND S.StuffId NOT IN(SELECT DISTINCT StuffId FROM $DataIn.push_stuff) ORDER BY PicJobid  ",$link_id);
                 if($CheckRow = mysql_fetch_array($CheckResult)){
	                  do{
	                       $StuffId=$CheckRow["StuffId"];
	                       $StuffCname=$CheckRow["StuffCname"];
	                       $Picjobid=$CheckRow["Picjobid"];
	                       $msgArray[$Picjobid].=$msgArray[$Picjobid]==""?"未上传配件图片:$StuffId-$StuffCname;":"$StuffId-$StuffCname;";
	                       $InSql="INSERT INTO `$DataIn`.`push_stuff` (`Id`, `ModuleId`, `StuffId`, `Date`, `Estate`, `Operator`) VALUES (NULL, '179', '$StuffId', '$DateTime', '1', '0')";
	                       $InResult=mysql_query($InSql,$link_id);
	                  }while($CheckRow = mysql_fetch_array($CheckResult));
	                  
	                   $userinfo="1";   $bundleId="DailyManagement";
	                   foreach( array_keys($msgArray) as $keys ){
		                     switch($keys){
		                           case 4:$userIdSTR="10341,10161";$message=$msgArray[4];break;//资材
			                       case 6:$userIdSTR="10009,10898";$message=$msgArray[6];break;//开发A
			                       case 7:$userIdSTR="10130,10262,10554";$message=$msgArray[7];break;//开发B
			                       case 32:$userIdSTR="10888,11886,10399";$message=$msgArray[32];break;//开发C
		                     }
		                     //$userIdSTR.=",10868";
		                     include "d:/website/mc/iphoneAPI/push_apple.php";
	                   }
	          }

?> 