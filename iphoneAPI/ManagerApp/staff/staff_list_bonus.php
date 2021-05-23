<?
//獎金 -- 三節獎金 + 其它獎金

$BundleId = 'AshCloudApp';
$Device = 'iphoneAPI';
$appVersion = $AppVersion;
$segment = 'staff';
$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];

$uri = 'staff/staff_list_bonus';
				          
				          
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


mysql_query($sql);

$lastYear = date("Y") - 1; 
$mySql = "(SELECT ItemName, '' AS Content, Amount, Rate, Date, Estate
				FROM $DataIn.cw11_jjsheet_frist
				WHERE Number = '$Number'
				AND Date >= '$lastYear')
			UNION ALL
				(SELECT  '其它奖金' AS ItemName, Content, Amount, '' AS Rate, Date, Estate
				FROM $DataIn.cw20_bonussheet
				WHERE Number = '$Number'
				AND Date >= '$lastYear')";

$myResult = mysql_query($mySql);
while($myRow = mysql_fetch_assoc($myResult))
{
	$title = $myRow["ItemName"];
/* 	echo substr($title, -8)."<br>"; */
	$endString = "年终奖金";
	$endLength = strlen($endString);
	if (substr($title, $endLength * -1) == $endString) {
		$title = "年终奖";
	}
	$amount = $myRow["Amount"];
	$content = $myRow["Content"];
	$rate = $myRow["Rate"];
	$date = $myRow["Date"];
	$estate = $myRow["Estate"];
	
	$jsonArray[]=array(	
		"Title"		=> $title,
        "Col1"		=> "¥".number_format($amount),
        "Col2"		=> $date,
        "Col3"		=> ($rate > 0) ? number_format($rate)."%" : "",
        "Remark"	=> $content,
        "Estate"	=> $estate
	);
}
?>