<?php
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$value = FormatSTR($_POST['value']);//处理再比较
$field = $_POST['field'];
$table = $_POST['table'];
$where = $_POST['where'];
$sql = "select count(*) from $table where $field = '$value' $where";
$res = mysql_query($sql);
$row = mysql_fetch_row($res);
echo $row[0];
?>