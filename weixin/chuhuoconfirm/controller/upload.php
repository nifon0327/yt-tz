<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2019/1/6
 * Time: 23:22
 */

$foldername = "../upload/order/";
if (!file_exists($foldername)) {
    mkdir($foldername, 0777, true);
}

$pathgroup="";
$result=false;
for($i=0;$i<count($_FILES["images"]["tmp_name"]);$i++){
    $filename = $foldername.time()."_".$_FILES["images"]["name"][$i];

    // move uploaded file from temp dir to dst dir
    $result = move_uploaded_file($_FILES["images"]["tmp_name"][$i], $filename);
    if($result) {
        $pathgroup = $pathgroup . $filename . ";";
    }else{
        StatusCode(1,"",$result);
        break;
    }   
}

if($result){
    StatusCode(0,$pathgroup);
}

function StatusCode($status, $result, $msg = '成功')
{
    echo json_encode(array(
        'status' => $status,
        'result' => $result,
        'msg' => $msg
    ));
}
