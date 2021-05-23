<?php   
//产品标准图上传的推送功能
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
include "d:/website/mc/basic/parameter.inc";
				
$msgArray=array();
$DateTime=date("Y-m-d H:i:s");
 $CheckResult=mysql_query("SELECT N.Number,N.Name,P.cName    
				FROM $DataIn.productdata P 
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId  
				LEFT JOIN  $DataPublic.staffmain N ON N.Number=C.Staff_Number
				WHERE  P.ProductId='$ProductId' AND P.TestStandard=2 AND P.Estate>0",$link_id);
if($CheckRow = mysql_fetch_array($CheckResult)){
        $cName=$CheckRow["cName"];
         $userIdSTR=$CheckRow["Number"];
         //$userIdSTR.=",10868";
         $message=$cName  . " 已于 " .$DateTime. " 上传，请按时审核。";
        
         $userinfo="1";   $bundleId="DailyManagement";
          include "d:/website/mc/iphoneAPI/push_apple.php";
  }

?> 