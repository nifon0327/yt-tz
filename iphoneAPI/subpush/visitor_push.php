<?php   
//来访到达时的推送功能
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
include "d:/website/mc/basic/parameter.inc";

$msgArray=array();
$DateTime=date("Y-m-d H:i:s");
 $CheckResult=mysql_query("SELECT I.Name,I.InTime,I.Remark,I.CompanyId,I.Operator,C.Name AS TypeName,P.Forshort    
FROM $DataPublic.come_data I 
LEFT JOIN $DataPublic.come_type C ON C.Id=I.TypeId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=I.CompanyId 
WHERE I.Id='$Id'",$link_id);
if($CheckRow = mysql_fetch_array($CheckResult)){
       $Name=$CheckRow["Name"];
        $InTime=$CheckRow["InTime"];
        $TypeName=$CheckRow["TypeName"];
        $CompanyId=$CheckRow["CompanyId"];

        if ($CompanyId>0){
             $Forshort=$CheckRow["Forshort"];
	         $pResult =  mysql_fetch_array(mysql_query("SELECT G.BuyerId  FROM $DataIn.cg1_stockmain G
                  WHERE G.CompanyId='$CompanyId' ORDER BY G.Date DESC LIMIT 1",$link_id));
                  $userIdSTR=$pResult["BuyerId"];
                  if ($userIdSTR=="10556") $userIdSTR="10387";//杨敏->谢心诚
                  if ($userIdSTR=="10399") $userIdSTR="10795";//张莉荣->胡红梅
                   
                  $message=$Forshort . " ($Name) ". $TypeName  . " 已于 " .$InTime. " 到达公司";
                 // $userIdSTR.=",10868";
        }
        else{
	        $userIdSTR=$CheckRow["Operator"];
	        $message=$Name  . $TypeName . " 已于 " .$InTime. " 到达公司";
        }
        
         $userinfo="1";   $bundleId="DailyManagement";
          include "d:/website/mc/iphoneAPI/push_apple.php";
  }

?> 