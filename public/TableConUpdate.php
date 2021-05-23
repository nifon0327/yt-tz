<?php
//输出
echo "<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<title>'.$title.'</title>

</head>"; 

echo "
<body  oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' action='' method='post' >";
//echo "checktranstable:$checktranstable <br>";
//echo "Table_Size:$Table_Size <br>";
if( ($database=='' || $TABLE_NAME=='' ||  $MyFieldName=='' )) {  //点击开始，开始倒入数据
    echo "无相关数据！";
	
}
else {


?>

<?php

/**
 * 生成mysql数据字典
 */

//配置数据库
/*
$dbserver   = "127.0.0.1";
$dbusername = "Admin";
$dbpassword = "Admin@12345";
*/

	
	include "../basic/parameter.inc";
	
	$dbserver   = "$host";
	$dbusername = "$user";
	$dbpassword = "$pass";
	
	
	
	
	$database      = "$database";
	//其他配置
	$title = '数据字典';
	
	//$mysql_conn = @mysql_connect("$dbserver", "$dbusername", "$dbpassword") or die("Mysql connect is error.");
	$mysql_conn=$link_id;
	//mysql_select_db($database, $mysql_conn);
	mysql_query('SET NAMES utf8', $mysql_conn);
	$chsql  = 'SELECT * FROM ';
	$chsql .= "$database.To_oracle ";
	$chsql .= 'WHERE ';
	$chsql .= " MyTableName='$TABLE_NAME' AND MyFieldName = '$MyFieldName' ";
	$table_result = mysql_query($chsql, $mysql_conn);
	if ($t = mysql_fetch_array($table_result)  ) {
		//如果存在，则不在插入
		$chsql  = "update  $database.To_oracle ";
			$chsql .= " set oracle_fieldName='$oracle_fieldName',oracle_fieldType='$oracle_fieldType',Name_describe='$Name_describe',
			Out_key_Table='$Out_key_Table',Remark='$Remark' ";
			$chsql .= 'WHERE ';
			$chsql .= " MyTableName='$TABLE_NAME' AND MyFieldName = '$MyFieldName' ";
	}
	
	else {
		
		$chsql  = "Insert into $database.To_oracle values (NULL,'$TABLE_NAME','$MyFieldName','$oracle_fieldName','$oracle_fieldType','$Name_describe','$Out_key_Table','$Remark','') ";
		
	} //if ($t = mysql_fetch_array($table_result) ) {
		


	$table_result = mysql_query($chsql, $mysql_conn);
	echo "$chsql <br>";
	if ($table_result ){ 
		echo "数据更新成功！";
	}
	else {
		"数据更新失败：$chsql";
	}

mysql_close($mysql_conn);
}
echo '</form> </body></html>';
?>
