<?php

include_once "../../basic/parameter.inc";
include "../../workshop/socket.php";

$id = $_POST['id'];
//$id = '3';
$stuffId = $_POST['stuffId'];

$lastStuffIdSql = "SELECT stuffId From $DataIn.qc_currentcheck WHERE Id=$id";
$lastStuffIdResult = mysql_query($lastStuffIdSql);
$lastStuffIdRow = mysql_fetch_assoc($lastStuffIdResult);
$lastStuffId = $lastStuffIdRow['stuffId'];

//$stuffId = '93109';
$number = $_POST['number'];
//$number = '11008';
if($number != ''){
    $numberUpdate = ",number = $number";
}else{
    $numberUpdate = ",number = NULL";
}

if($lastStuffId != $stuffId){
    $numberUpdate .= ",weight=NULL";
}

$result = 'N';
$updateLineStateSql = "UPDATE $DataIn.qc_currentcheck SET stuffId = $stuffId $numberUpdate Where Id = $id";
if(mysql_query($updateLineStateSql)){
    $result = $stuffId;
    //display_send_msg('3A-'.$id, 'reload');
}

echo json_encode(array('result'=>$result));

?>