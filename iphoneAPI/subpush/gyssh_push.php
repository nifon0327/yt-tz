<?php   
//供应商送货到达提醒功能推送
//header("Content-Type: text/html; charset=utf-8");
//header("cache-control:no-cache,must-revalidate");
include "d:/website/ac/basic/parameter.inc";

$msgArray=array();$StuffIdArray=array();
$DateTime=date("Y-m-d H:i:s");
$shEstate=$shEstate==""?2:$shEstate;


 $CheckResult=mysql_query("SELECT DISTINCT D.StuffId,D.StuffCname,S.Qty ,CM.BuyerId
 
                        FROM $DataIn.gys_shsheet S  
                        LEFT JOIN $DataIn.gys_shmain  M ON M.Id=S.Mid 
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
						
						LEFT join $DataIn.cg1_stocksheet CG on CG.StockId=S.StockId 
						LEFT join $DataIn.cg1_stockmain CM on CM.Id=CG.Mid
						
                        WHERE S.Estate=2 AND S.Id IN ($shIds)  AND M.CompanyId IN (2668,2688,2681,2679,2684,2332)",$link_id);
						 $pushBuyerId = "";
                 if($CheckRow = mysql_fetch_array($CheckResult)){
					 
	                  do{
	                       $StuffId=$CheckRow["StuffId"];
	                       $StuffCname=$CheckRow["StuffCname"];
						   $pushBuyerId = $CheckRow["BuyerId"];
	                       $Qty=$CheckRow["Qty"];
	                      $message=$message==""?"供应商已送货:$StuffId-$StuffCname,数量:$Qty; ":"$StuffId-$StuffCname,数量:$Qty; ";
	                  }while($CheckRow = mysql_fetch_array($CheckResult));
	                     $userinfo="1";   $bundleId="DailyManagement";
		                 $userIdSTR="10009,10306,10658";//黄红枚、别慧敏、黄海波
		                 $userIdSTR.=",11965";
						 if ( $pushBuyerId != "") {
							  $userIdSTR.=",$pushBuyerId";
						 }
		                  include "d:/website/ac/iphoneAPI/push_apple.php";
}

$msgArray=array();$StuffIdArray=array();
$DateTime=date("Y-m-d H:i:s");
 $CheckResult=mysql_query("SELECT DISTINCT D.StuffId,D.StuffCname,IF(D.Pjobid>0,D.Pjobid,T.Picjobid) AS Picjobid 
                        FROM $DataIn.gys_shsheet S  
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
                        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
                        WHERE S.Estate='$shEstate' AND S.Id IN ($shIds)  AND D.Picture=0 AND D.StuffId NOT IN(SELECT DISTINCT StuffId FROM $DataIn.push_stuff) ORDER BY PicJobid  ",$link_id);
                        
                       
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
			                       case 6:$userIdSTR="10009,10262,10898";$message=$msgArray[6];break;//开发A
			                       case 7:$userIdSTR="10130,10554";$message=$msgArray[7];break;//开发B
			                       case 32:$userIdSTR="10888,11886,10399";$message=$msgArray[32];break;//开发C
		                     }
		                     $userIdSTR.=",11965";
		                     include "d:/website/ac/iphoneAPI/push_apple.php";
	                   }
	          }

?> 