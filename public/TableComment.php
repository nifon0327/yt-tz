<?php
//输出
echo "<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<title>'.$title.'</title>
<style>
body,td,th {font-family:'思源黑体'; font-size:12px;}
table{border-collapse:collapse;border:1px solid #CCC;background:#efefef;}
table caption{text-align:left; background-color:#fff; line-height:2em; font-size:14px; font-weight:bold; }
table th{text-align:left; font-weight:bold;height:26px; line-height:26px; font-size:12px; border:1px solid #CCC;}
table td{height:20px; font-size:12px; border:1px solid #CCC;background-color:#fff;}
.c1{ width: 120px;}
.c2{ width: 120px;}
.c3{ width: 70px;}
.c4{ width: 80px;}
.c5{ width: 80px;}
.c6{ width: 270px;}
.c7{ width: 400px;}
</style>
</head>"; 

echo "
<body  oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' action='' method='post' >";
//echo "checktranstable:$checktranstable <br>";
//echo "Table_Size:$Table_Size <br>";
if( ($Thedatabase=='')) {  //点击开始，开始倒入数据
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<input name='BeginSign' id='BeginSign'  type='hidden' value='1'>";
	echo "&nbsp;&nbsp;要输出数据字典数据库：<input name='Thedatabase' id='Thedatabase'  type='text' style='width:40px' value=''>";
	
	
	echo"&nbsp;<select name='letter' id='letter' >";
	foreach (range('a', 'z') as $AZ) {
	echo"<option value='$AZ' >$AZ</option>";
	}
	echo"</select>&nbsp;";
	
	
	echo" <input type='button' id='Begin' name='Begin' value='开始输出数据字典' onClick='BeginS()'>";
	echo "<br> <br>";
	
}
else {
	
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<input name='BeginSign' id='BeginSign'  type='hidden' value='1'>";
	echo "&nbsp;&nbsp;要输出数据字典数据库：<input name='Thedatabase' id='Thedatabase'  type='text' style='width:40px' value='$Thedatabase'>";
	
	echo"&nbsp;<select name='letter' id='letter' >";
	foreach (range('a', 'z') as $AZ) {
		if($AZ==$letter){
			echo"<option value='$AZ' selected>$AZ</option>";
		}
		else {
			echo"<option value='$AZ' >$AZ</option>";
		}
	}
	echo"</select>&nbsp;";	
	
	echo" <input type='button' id='Begin' name='Begin' value='开始输出数据字典' onClick='BeginS()'>";
	echo "<br> <br>";	

?>

<?php

/**
 * 生成mysql数据字典
 */

//配置数据库
/*
$dbserver   = "127.0.0.1";
$dbusername = "Admin";
$dbpassword = "Admin";
*/

include "../basic/parameter.inc";

$dbserver   = "$host";
$dbusername = "$user";
$dbpassword = "$pass";




$database      = "$Thedatabase";
//其他配置
$title = '数据字典';

//$mysql_conn = @mysql_connect("$dbserver", "$dbusername", "$dbpassword") or die("Mysql connect is error.");
$mysql_conn=$link_id;

mysql_select_db($database, $mysql_conn);
mysql_query('SET NAMES utf8', $mysql_conn);
$table_result = mysql_query("show tables like '$letter%' ", $mysql_conn);
//取得所有的表名
$i=1;
while ($row = mysql_fetch_array($table_result)) {
    $tables[]['TABLE_NAME'] = $row[0];
	$i=$i+1;
	//if($i>10) break;	
}

//循环取得所有表的备注及表中列消息
foreach ($tables AS $k=>$v) {
    $sql  = 'SELECT * FROM ';
    $sql .= 'INFORMATION_SCHEMA.TABLES ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}'  AND table_schema = '{$database}'";
    $table_result = mysql_query($sql, $mysql_conn);
    while ($t = mysql_fetch_array($table_result) ) {
        $tables[$k]['TABLE_COMMENT'] = $t['TABLE_COMMENT'];
    }

    $sql  = 'SELECT * FROM ';
    $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";

    $fields = array();
    $field_result = mysql_query($sql, $mysql_conn);
    while ($t = mysql_fetch_array($field_result) ) {
        $fields[] = $t;
    }
    $tables[$k]['COLUMN'] = $fields;
}
//mysql_close($mysql_conn);


$html = '';
//循环所有表
//$row=1;

if ($tables=='') {
	mysql_close($mysql_conn);
	return false;
}
foreach ($tables AS $k=>$v) {
    //$html .= '<p><h2>'. $v['TABLE_COMMENT'] . '&nbsp;</h2>';
	//更改表注释语句：
	//ALTER TABLE `accessguard_cardtype` COMMENT = '能更改表备注？'
	/*
	$updateTableCommentSQL='ALTER TABLE `'.$v['TABLE_NAME']."` COMMENT = '".$v['TABLE_COMMENT']."'";
	
    $html .= '<table  border="1" cellspacing="0" cellpadding="0" align="center">';
    $html .= '<caption>' . $v['TABLE_NAME'] .' : '. $v['TABLE_COMMENT']. "(SQL语句:$updateTableCommentSQL)".'</caption>';
    $html .= '<tbody><tr><th>字段名</th><th>数据类型</th><th>默认值</th>
    <th>允许非空</th>
    <th>自动递增</th>';
	$html .= '<th>字段内容描述</th><th>SQL语句</th></tr>';
    $html .= '';
	*/
    $TABLE_NAME=$v['TABLE_NAME'];
	
	$updateTableCommentSQL="";
	
	$row=0;
	
    foreach ($v['COLUMN'] AS $f) {
		$row=$row+1;

		$updateFieldCommentSQL="";
		
		$Field_COMMENT="";  //字段名注释
		$MyFieldName=$f['COLUMN_NAME'];
		$FieldName= strtoupper($f['COLUMN_NAME']);
		//echo "$TABLE_NAME:FieldName:$FieldName <br>";
		switch($FieldName){
			case 'DATE':
				$Field_COMMENT="记录生成日期";
				break;
			case 'ESTATE':
				 $Field_COMMENT="记录执行状态";
				break;
			case 'LOCKS':
				$Field_COMMENT="记录操作状态";			
				break;
			case 'OPERATOR':
				$Field_COMMENT="记录操作人";			
				break;
				
			default:
			break;
		}
		
		$chsql  = 'SELECT * FROM ';
		$chsql .= ' `to_oracle` ';
		$chsql .= ' WHERE ';
		$chsql .= " MyTableName='$TABLE_NAME' AND MyFieldName='$MyFieldName' AND EState=1 ";
		$table_result = mysql_query($chsql, $mysql_conn);
		//echo "$chsql <br>";
		$oracle_fieldName="";
		$oracle_fieldType="";
		$Name_describe="";
		$Out_key_Table="";
		$Remark="";
		$OracleSQL="";	
		$Estate=0; //如果为0表示不用更新，1 表示自动更新
		if ($t = mysql_fetch_array($table_result)  ) {
			$oracle_fieldName=$t['oracle_fieldName'];
			//echo "$oracle_fieldName <br>";
			$oracle_fieldType=$t['oracle_fieldType'];
			$Name_describe=$t['Name_describe']; //字段描述
			//$Out_key_Table=$t['Out_key_Table']; //关联表
			$Out_key_Table=$t['key_table_bak'];
			$Remark=$t['Remark'];   //其它备注
			$OracleSQL=$t['OracleSQL'];		
			$Estate=$t['EState'];  	
		}
		
		//echo "$TABLE_NAME:Estate:$Estate <br>";
		//把外联的表也加入到备注那里去
		$Out_Str="";
		$Out_Database="";
		/*
		if($Out_key_Table!=""){
			echo "Out_key_Table:$Out_key_Table <br>";
		}
		*/
		$Out_key=explode('.', $Out_key_Table);
		$Count=count($Out_key);
		if($Count==2 || $Count==3){ //如d0.staffmian.NO,或staffmian.NO
			if($Count==2){ //staffmian.NO
				$Out_Tabble=trim($Out_key[0]);
				$Out_Field=trim($Out_key[1]);
			}
			else{ //d0.staffmian.NO
				$Out_Database=$Out_key[0].'.';
				$Out_Tabble=trim($Out_key[1]);
				$Out_Field=trim($Out_key[2]);				
			}
			
			
			$Out_Str=$Out_Tabble.'.'.$Out_Field;
			if(strtoupper($Out_Tabble.'_ID') ==strtoupper($Out_Field)){ //说明的更改的ID，那么关联就
				$Out_Str=$Out_Tabble.'.ID';
			}
			else {
				//echo strtoupper($Out_Tabble.'_ID').':'.strtoupper($Out_Field).'<br>';
			}
			
			if(strtoupper($Out_Tabble.'.NO') =='STAFFMAIN.NO'){ 
				$Out_Str=$Out_Tabble.'.Number';
			}
			//echo "Out_Str:$Out_Str <br>";
			//$Out_Str=$Out_Database.$Out_Str;
			$Out_Str=',关联表：'.$Out_Str;
			
		}
		
		
		
		
		if($Field_COMMENT==""){ //如果不指定，则
			$Field_COMMENT=trim(str_replace(' ','',$Name_describe));
			//$Field_COMMENT=trim($Name_describe);
		}
		else{
			//$Estate=1;
		}
		
		if($row==1) {  //第一行，“其它备注”就是表名
			
			$html .= '<table  border="1" cellspacing="0" cellpadding="0" align="center">';
			
			if($Estate==1) {
				$updateTableCommentSQL='ALTER TABLE `'.$v['TABLE_NAME']."` COMMENT = '".$Remark."'";
				$html .= '<caption>' . $v['TABLE_NAME'] .' : '. $v['TABLE_COMMENT']. "(SQL语句:$updateTableCommentSQL)".'</caption>';
			}else{
				$html .= '<caption>' . $v['TABLE_NAME'] .' : '. $v['TABLE_COMMENT']. '</caption>';
			}
			
			$html .= '<tbody><tr><th>字段名</th><th>数据类型</th><th>默认值</th>
			<th>允许非空</th>
			<th>自动递增</th>';
			$html .= '<th>字段内容描述</th><th>SQL语句</th></tr>';
			$html .= '';			
		}
		
        $html .= '<tr><td class="c1">' . $f['COLUMN_NAME'] . '</td>';
        $html .= '<td class="c2">' . $f['COLUMN_TYPE'] . '</td>';
        $html .= '<td class="c3">&nbsp;' . $f['COLUMN_DEFAULT'] . '</td>';
        $html .= '<td class="c4">&nbsp;' . $f['IS_NULLABLE'] . '</td>';
        $html .= '<td class="c5">' . ($f['EXTRA']=='auto_increment'?'是':'&nbsp;') . '</td>';
        $html .= '<td class="c6">&nbsp;' . $f['COLUMN_COMMENT'] . '</td>';
		
		if($Estate==1 && $Field_COMMENT!='' && $f['COLUMN_COMMENT']=="") {
			//更改自段的备注：
			//ALTER TABLE `casetoproduct` CHANGE `Id` `Id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'casetoproductt自增增的字段'
			$updateFieldCommentSQL='ALTER TABLE `'.$v['TABLE_NAME'].'` change `'. $f['COLUMN_NAME'].'` `'. $f['COLUMN_NAME'].'`';
			//数据类型
			$updateFieldCommentSQL .=' '.$f['COLUMN_TYPE'];
			
			//是否为空
			if($f['IS_NULLABLE']=="NO"){
				$updateFieldCommentSQL .=' NOT NULL ' ;
			}
			else {
				$updateFieldCommentSQL .=' NULL ' ;
			}
			//默认值
			if ($f['COLUMN_DEFAULT']!=""){
				$updateFieldCommentSQL .=' DEFAULT '.$f['COLUMN_DEFAULT'];
			}
	
			//是否是自增量
			$updateFieldCommentSQL .=' '.$f['EXTRA'];
			
			//备注
			$updateFieldCommentSQL .=" COMMENT '".$Field_COMMENT.$Out_Str."'";
			$html .= '<td class="c7">' . $updateFieldCommentSQL . '</td>';
		}
		else {
			$html .= '<td class="c7"> &nbsp; </td>';
		}
		
		//更新相应的表，及表的字段
		//$update=1;
		if($Estate==1 ){
		//if($Estate==1 && $update==1){	
			
			if($updateTableCommentSQL!=""){
				$table_result = mysql_query($updateTableCommentSQL, $mysql_conn);
				if ($table_result ){ 
					echo "数据更新成功1！：$updateTableCommentSQL <br>";
				}
				else {
					echo "数据更新失败1：$updateTableCommentSQL <br>";
				}				
			}
			
			if($updateFieldCommentSQL!=""){
				$table_result = mysql_query($updateFieldCommentSQL, $mysql_conn);
				if ($table_result ){ 
					echo "数据更新成功2！$updateFieldCommentSQL <br>";
				}
				else {
					echo "数据更新失败2：$updateFieldCommentSQL <br>";
				}					
			}
			
				$chsql  = ' update ';
				$chsql .= ' to_oracle set EState=0';
				$chsql .= ' WHERE ';
				$chsql .= " MyTableName='$TABLE_NAME' AND  MyFieldName = '$MyFieldName'  AND EState=1";
			
				$table_result = mysql_query($chsql, $mysql_conn);
				if ($table_result ){ 
					echo "数据更新成功3！$chsql <br>";
				}
				else {
					echo "数据更新失败3：$chsql <br>";
				}		
			
			
			
		}
		
		

		
		
        $html .= '</tr>';
    }
    $html .= '</tbody></table></p>';
}


echo '<h1 style="text-align:center;">'.$title.'</h1>';

echo $html;

$tables="";
$html="";
mysql_close($mysql_conn);
}
echo '</form> </body></html>';
?>

<script language = "JavaScript">
function BeginS(){
		//document.getElementById('Begin').disabled=true;
		//document.getElementById('BeginTranS').disabled=true;
		//document.getElementById('Thedatabase').readOnly='readOnly';
		document.form1.action="TableComment.php";
		document.form1.submit();
}



function BeginTran(){
	
		//document.getElementById('BeginTranS').disabled=true;
		//document.getElementById('Thedatabase').readOnly='readOnly';
		//document.form1.action="TableConstructer.php";
        //window.open ('TableConToOracle.php');
		//document.form1.submit();
}


</script>

<?
// 如果要执行，则需要如下操作！！！！！！
//增加字段Estate=1,key_table_bak
//update `to_oracle` set key_table_bak=Out_key_Table
//select key_table_bak from  `to_oracle` where TRIM(replace(key_table_bak,' ',''))!=''
//update  `to_oracle` set key_table_bak=TRIM(replace(key_table_bak,' ',''))

?>