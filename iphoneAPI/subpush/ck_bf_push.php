<?php   
//配件报废审核后推送信息
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
include "d:/website/mc/basic/parameter.inc";


$CheckResult=mysql_query("SELECT B.Qty,D.StuffId,D.StuffCname 
FROM $DataIn.ck8_bfsheet B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId WHERE B.Id IN ($Ids)",$link_id);
                 if($CheckRow = mysql_fetch_array($CheckResult)){
	                  do{
	                       $StuffId=$CheckRow["StuffId"];
	                       $StuffCname=$CheckRow["StuffCname"];
	                       $Qty=$CheckRow["Qty"];
	                      $message=$message==""?"配件:$StuffId-$StuffCname,报废数量:$Qty;":"$StuffId-$StuffCname,报废数量:$Qty;";
	                  }while($CheckRow = mysql_fetch_array($CheckResult));
	                     $message.="已审核通过"; 
	                     $userinfo="1";   $bundleId="DailyManagement";
		                 $userIdSTR="11822,10006";//陈连枝
		                // $userIdSTR="10868";
		               //  echo $message;
		                  include "d:/website/mc/iphoneAPI/push_apple.php";
}
?> 