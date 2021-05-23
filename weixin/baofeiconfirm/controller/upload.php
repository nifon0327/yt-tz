<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2019/1/6
 * Time: 23:22
 */

$tempname = "..";
$foldername = "/upload/order/";
if (!file_exists($foldername)) {
    mkdir($foldername, 0777, true);
}

$now = time();
$filename =$tempname.$foldername.$now.$_FILES["invoice"]["name"];

//保存文件,   move_uploaded_file 将上传的文件移动到新位置
$result = move_uploaded_file($_FILES["invoice"]["tmp_name"],$filename);
if($result) {
	StatusCode(0,$foldername.$now.$_FILES["invoice"]["name"]);
}else{
	StatusCode(1,"",$result);
}

function StatusCode($status, $result, $msg = '成功')
{
    echo json_encode(array(
        'status' => $status,
        'result' => $result,
        'msg' => $msg
    ));
}
