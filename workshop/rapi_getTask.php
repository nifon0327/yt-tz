<?php

include "../basic/parameter.inc";
$targetIp = $_POST['ip'];


$checkRows= mysql_fetch_array(mysql_query("SELECT Url FROM $DataPublic.ot2_display WHERE IP='$targetIp' LIMIT 1",$link_id));
$url='http://192.168.20.1/' . $checkRows["Url"];

/*
$url = '';     
if($targetIp == '192.168.30.214'){
    $url = 'http://10.0.10.1/workshop/qc_sampling.php';
}
*/

echo json_encode(array('url'=>$url));

?>