<?php   
//打样推送功能 
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
include "d:/website/mc/basic/parameter.inc";

$msgArray=array();
$DateTime=date("Y-m-d H:i:s");
 $CheckResult=mysql_query("SELECT A.StuffId,S.StuffCname,T.DevelopNumber,M.Name   
	                  FROM stuffdevelop A
	                  INNER JOIN stuffdata S ON S.StuffId=A.StuffId 
	                  INNER JOIN  stufftype T  ON T.TypeId=S.TypeId  
	                  INNER JOIN  staffmain M ON M.Number=A.Operator    
					  WHERE   A.StuffId='$StuffId' ",$link_id);
if($CheckRow = mysql_fetch_array($CheckResult)){
        $StuffId=$CheckRow["StuffId"];
        $StuffCname=$CheckRow["StuffCname"];
        $Name=$CheckRow["Name"];
        
         $userIdSTR=$CheckRow["DevelopNumber"];
         $userIdSTR.=",10009";
	     $message="$Name 新增开发项目:$StuffId-$StuffCname;";
        
         $userinfo="1";   $bundleId="DailyManagement";
          include "d:/website/mc/iphoneAPI/push_apple.php";
 }

?> 