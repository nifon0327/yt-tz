
<?php   
//电信---yang 20120801
	$Today=date("Y-m-d");
	$WarningDate=date("Y-m-d",strtotime($Today."+ 31 day")); //一个月警告
	//获联打印任务中未上传图片任务
	$mySql="SELECT  count( * ) as hzdoccount FROM $DataIn.zw2_hzdoc
		WHERE 1 AND EndDate<='$WarningDate' ";
	
	$checkhzdoccount=mysql_fetch_array(mysql_query($mySql,$link_id));
	$hzdoccount=$checkhzdoccount["hzdoccount"];  //103表示tasklistdata表中的ItemId的值.
	//echo "filecount: $fileCount"; 

?>
