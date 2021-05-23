<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");


if( ($database=='' || $TABLE_NAME=='' ||  $MyFieldName=='' )) {  //点击开始，开始倒入数据
    echo "无相关数据！";
	
}
else {
	
	$dbserver   = "$host";
	$dbusername = "$user";
	$dbpassword = "$pass";

	$database      = "$database";
	$mysql_conn=$link_id;
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
	if ($table_result ){ 
		echo "Y";
	}
	else {
		echo $chsql;
	}

mysql_close($mysql_conn);
}


?>