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
</style>
</head>"; 

echo "
<body  oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' action='' method='post' >";
//echo "checktranstable:$checktranstable <br>";
//echo "Table_Size:$Table_Size <br>";
if( ($Thedatabase=='')) {  //点击开始，开始倒入数据
    echo" <input type='button' id='BeginTranS' name='BeginTranS' value='Oracle相关数据转换' onClick='BeginTran()'>";
	echo" <input type='button' id='BeginTranALL' name='BeginTranALL' value='数据字典表格显示' onClick='BeginALL()'>";
	
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
	echo" <input type='button' id='BeginTranS' name='BeginTranS' value='Oracle相关数据转换' onClick='BeginTran()'>";
	echo" <input type='button' id='BeginTranALL' name='BeginTranALL' value='数据字典表格显示' onClick='BeginALL()'>";
	
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
$dbpassword = "Admin@12345";
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

//循环取得所有表的备注及表中列消
/*
if ($tables=='') {
	mysql_close($mysql_conn);
	return false;
}
*/
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
$row=1;
foreach ($tables AS $k=>$v) {
    //$html .= '<p><h2>'. $v['TABLE_COMMENT'] . '&nbsp;</h2>';
    $html .= '<table  border="1" cellspacing="0" cellpadding="0" align="center">';
    $html .= '<caption>' . $v['TABLE_NAME'] .'  '. $v['TABLE_COMMENT']. '</caption>';
    $html .= '<tbody><tr><th>字段名</th><th>数据类型</th><th>默认值</th>
    <th>允许非空</th>
    <th>自动递增</th>';
	$html .= '<th>Oracle字段名</th><th>Oracle字段类型</th><th>字段内容描述</th><th>关联表</th><th>备注</th><th>更新</th></tr>';
    $html .= '';
    $TABLE_NAME=$v['TABLE_NAME'];
	

	
	
    foreach ($v['COLUMN'] AS $f) {
		$row=$row+1;
        $html .= '<tr><td class="c1">' . $f['COLUMN_NAME'] . '</td>';
        $html .= '<td class="c2">' . $f['COLUMN_TYPE'] . '</td>';
        $html .= '<td class="c3">&nbsp;' . $f['COLUMN_DEFAULT'] . '</td>';
        $html .= '<td class="c4">&nbsp;' . $f['IS_NULLABLE'] . '</td>';
        $html .= '<td class="c5">' . ($f['EXTRA']=='auto_increment'?'是':'&nbsp;') . '</td>';
        //$html .= '<td class="c6">&nbsp;' . $f['COLUMN_COMMENT'] . '</td>';
		
		$MyFieldName=$f['COLUMN_NAME'];
		$FieldName= strtoupper($f['COLUMN_NAME']);
		$FieldType= strtoupper($f['COLUMN_TYPE']);
		$oracle_fieldName="";
		switch($FieldName){
			case 'ID':
				$oracle_fieldName="$TABLE_NAME".'_ID';
				break;
			case 'NUMBER':
				 $oracle_fieldName='NO';
				break;
			case 'DATE':
				 $oracle_fieldName='Insert_Date';			
				break;
			default:
			break;
		}	
		$oracle_fieldName=$oracle_fieldName==""?$f['COLUMN_NAME']:$oracle_fieldName;
		$chsql  = 'SELECT * FROM ';
		$chsql .= 'To_oracle ';
		$chsql .= 'WHERE ';
		$chsql .= " MyTableName='$TABLE_NAME' AND  MyFieldName = '$MyFieldName' ";
		$table_result = mysql_query($chsql, $mysql_conn);
		//echo "$chsql <br>";
		$oracle_fieldName="";
		$oracle_fieldType="";
		$Name_describe="";
		$Out_key_Table="";
		$Remark="";
		$OracleSQL="";	
		
		if ($t = mysql_fetch_array($table_result)  ) {
			$oracle_fieldName=$t['oracle_fieldName'];
			//echo "$oracle_fieldName <br>";
			$oracle_fieldType=$t['oracle_fieldType'];
			$Name_describe=$t['Name_describe'];
			$Out_key_Table=$t['Out_key_Table'];
			$Remark=$t['Remark'];
			$OracleSQL=$t['OracleSQL'];			
		}
		
		$oracle_fieldName=$oracle_fieldName==""?"&nbsp;":$oracle_fieldName;
		$oracle_fieldType=$oracle_fieldType==""?"&nbsp;":$oracle_fieldType;
		$Name_describe=$Name_describe==""?"&nbsp;":$Name_describe;
		$Out_key_Table=$Out_key_Table==""?"&nbsp;":$Out_key_Table;
		$Remark=$Remark==""?"&nbsp;":$Remark;
		$OracleSQL=$OracleSQL==""?"&nbsp;":$OracleSQL;	
		$html .= '<td class="c1">&nbsp;' . "<input name='oracle_fieldName$row' id='oracle_fieldName$row'  type='text' class='c1' value='$oracle_fieldName'>" . '</td>';
		$html .= '<td class="c2">&nbsp;' . "<input name='oracle_fieldType$row' id='oracle_fieldType$row'  type='text' class='c2' value='$oracle_fieldType'>" . '</td>';
		$html .= '<td class="c6">&nbsp;' . "<input name='Name_describe$row' id='Name_describe$row'  type='text' class='c6' value='$Name_describe'>" . '</td>';
		$html .= '<td class="c6">&nbsp;' . "<input name='Out_key_Table$row' id='Out_key_Table$row'  type='text' class='c6' value='$Out_key_Table'>" . '</td>';
		$html .= '<td class="c6">&nbsp;' . "<input name='Remark$row' id='Remark$row'  type='text' class='c6'  value='$Remark'>" . '</td>';
		$html .= '<td class="c1">&nbsp;' ."<input type='button' id='updateS' name='updateS' value='更新数据'
		onClick='updateData(\"$database\",\"$TABLE_NAME\",\"$MyFieldName\",\"$row\")'>" . '</td>';
		
		
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
		document.form1.action="TableConstructer1.php";
		document.form1.submit();
}
function BeginALL(){
	
	    window.open ('TableConALL.php ');
}

function updateData(database,TABLE_NAME,MyFieldName,row){
	    
		oracle_fieldName=document.getElementById('oracle_fieldName'+row).value;
		oracle_fieldType=document.getElementById('oracle_fieldType'+row).value;
		Name_describe=document.getElementById('Name_describe'+row).value;
		Out_key_Table=document.getElementById('Out_key_Table'+row).value;
		Remark=document.getElementById('Remark'+row).value;
		
		window.open ('TableConUpdate.php?database='+database+'&TABLE_NAME='+TABLE_NAME+'&MyFieldName='+MyFieldName
					 +'&oracle_fieldName='+oracle_fieldName+'&oracle_fieldType='+oracle_fieldType
					 +'&Name_describe='+Name_describe+'&Out_key_Table='+Out_key_Table+'&Remark='+Remark);
		//document.form1.submit();
}

function BeginTran(){
	
		//document.getElementById('BeginTranS').disabled=true;
		//document.getElementById('Thedatabase').readOnly='readOnly';
		//document.form1.action="TableConstructer1.php";
        window.open ('TableConToOracle.php');
		//document.form1.submit();
}

function InitAjax(){ 
	var ajax=false;
	try{   
　　	ajax=new ActiveXObject("Msxml2.XMLHTTP");
		}
	catch(e){   
　　	try{   
　　　		ajax=new ActiveXObject("Microsoft.XMLHTTP");
			}
		catch(E){   
　　　		ajax=false;
			}   
　		} 
　	if(!ajax && typeof XMLHttpRequest!='undefined'){
		ajax=new XMLHttpRequest();
		}   
　	return ajax;
	}

function updateDataNew(database,TABLE_NAME,MyFieldName,row){
	    
		oracle_fieldName=document.getElementById('oracle_fieldName'+row).value;
		oracle_fieldType=document.getElementById('oracle_fieldType'+row).value;
		Name_describe=document.getElementById('Name_describe'+row).value;
		Out_key_Table=document.getElementById('Out_key_Table'+row).value;
		Remark=document.getElementById('Remark'+row).value;

	var url="TableConstructer_ajax.php?database="+database+"&TABLE_NAME="+TABLE_NAME+"&MyFieldName="+MyFieldName
					 +"&oracle_fieldName="+oracle_fieldName+"&oracle_fieldType="+oracle_fieldType
					 +"&Name_describe="+Name_describe+"&Out_key_Table="+Out_key_Table+"&Remark="+Remark;
	var ajax=InitAjax(); 
	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
		  alert(ajax.responseText);
			if(ajax.responseText=="Y"){//更新成功
		           alert("OK");
				}
             else{
		           alert("更新失败");
                   }
			}
		}
	ajax.send(null); 

}
</script>