<?php

include_once "../../basic/parameter.inc";
include "../../workshop/socket.php";

$id = $_POST['id'];
//$id = '4';
$stuffId = $_POST['stuffId'];
//$stuffId = '157376';
$result = 'N';
$updateLineStateSql = "UPDATE $DataIn.qc_currentcheck SET stuffId = $stuffId Where Id = $id";
if(mysql_query($updateLineStateSql)){
    $result = $stuffId;
    //display_send_msg('3A-'.$id, 'reload');
}

echo json_encode(array('result'=>$result));

?>