<?php   
//新品推送功能 

header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
include "d:/website/mc/basic/parameter.inc";

$msgArray=array();
$DateTime=date("Y-m-d H:i:s");
$willPushUsers = array('10005','10009','11965');

 //黄红梅 蔡圳 ＋ all salesmen
$findSql = mysql_query('select Number from staffmain  where ( GroupId=301 ) and Estate>0 ');
while ($findSqlRow = mysql_fetch_array($findSql)) {
		$tempNumber = $findSqlRow['Number'];
		$willPushUsers[]= "$tempNumber";

}
$conditionsP = ' and  Pushed<1 ';
$userIdSTR = implode(',',$willPushUsers);
/*
	
	if ($_REQUEST['cz']==1) {
	$userIdSTR = '11965';
	$conditionsP = ' limit 1';
}
*/
$conditions = "";
if ($insert_id && $insert_id>0) {
	$conditions = " and Id in ($insert_id ) ";	
}
 $CheckResult=mysql_query("select Name,Id from new_arrivaldata where 1  $conditions  $conditionsP ",$link_id);
while($CheckRow = mysql_fetch_array($CheckResult)){
        $newId=$CheckRow["Id"];
        $newName=$CheckRow["Name"];
     
	     $message="新品'$newName'已发布.";
		  mysql_query("update new_arrivaldata set Pushed=Pushed+1 where id=$newId");
        
         $userinfo="new_arrival";   $bundleId="DailyManagement";
          include "d:/website/mc/iphoneAPI/push_apple.php";
 }

?> 