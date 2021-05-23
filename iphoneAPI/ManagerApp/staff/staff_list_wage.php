<?php
//员工薪资记录
 
 $BundleId = 'AshCloudApp';
$Device = 'iphoneAPI';
$appVersion = $AppVersion;
$segment = 'staff';
$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];

$uri = 'staff/staff_list_wage';
				          
				          
$sql = "INSERT INTO `ac`.`app_userlog`
(
`BundleId`,
`Device`,
`Version`,
`IP`,
`Segment`,
`Uri`,
`creator`,
`Parameter`)
VALUES
(
'$BundleId',
'$Device',
'$appVersion',
'$user_IP',
'$segment',
'$uri',
'$LoginNumber',
'readNumber=>$Number');
";


mysql_query($sql,$link_id);


 //modify by cabbage 20150105 加上幣別的符號顯示
 $mySql="SELECT S.Month, S.Amount, D.PreChar FROM $DataIn.cwxzsheet S
 LEFT JOIN $DataPublic.currencydata D ON D.Id = S.Currency 
 WHERE S.Number='$Number' AND S.Estate=0
 	
 ORDER BY Month DESC LIMIT 13";
 
/* echo $mySql; */
  $myResult = mysql_query($mySql);
  while($myRow = mysql_fetch_assoc($myResult))
 {
	        $Month=$myRow["Month"];
	        
			//add by cabbage 20150105 加上幣別的符號顯示
	        $preChar = $myRow["PreChar"];	        
	        $Amount= $preChar.number_format($myRow["Amount"]);
	        
	        $jsonArray[]=array("Title"=>"$Month","Value"=>"$Amount");
    }
?>