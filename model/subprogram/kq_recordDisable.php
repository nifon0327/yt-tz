<?php
include_once "../../basic/parameter.inc";
$checkDate = $_GET['CheckDate'];
$isHideState = $_GET['isHideInList'];

$operatorSql = '';
if($_GET['isHideInList'] == '0'){
    $operatorSql = "INSERT INTO $DataIn.disablecheckid (Id, checkId) Values (NULL, '$CheckDate')";
}else{
    $operatorSql = "DELETE FROM $DataIn.disablecheckid WHERE checkId='$CheckDate'";
}

$title = $_GET['isHideInList'] == '0'?'隐藏考勤日期':'显示考勤日期';
if(mysql_query($operatorSql)){
    echo $title.'成功';
}else{
    echo $title.'失败';
}

?>