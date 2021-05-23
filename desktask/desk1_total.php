
<?php   
	//电信---yang 20120801
	//获联打印任务中未上传图片任务
	/*
	$mySql="SELECT  count( * ) as fileCount 
		FROM (
		SELECT DISTINCT IFNULL( F.ProductId, 0 ) AS FileSign, S.CodeType, Y.ProductId
		FROM $DataIn.sc3_printtasks S
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
		LEFT JOIN $DataIn.file_codeandlable F ON F.ProductId = Y.ProductId AND F.CodeType = S.CodeType
		WHERE S.CodeType <>3 
		)A
		WHERE FileSign =0";
	*/
	$mySql="SELECT  count( * ) as fileCount FROM $DataIn.sc3_printtasks S  WHERE  S.Estate>0";
		
	$checkPrittask=mysql_fetch_array(mysql_query($mySql,$link_id));
	$fileCount103=$checkPrittask["fileCount"];  //103表示tasklistdata表中的ItemId的值.
	//echo "filecount: $fileCount"; 

?>
