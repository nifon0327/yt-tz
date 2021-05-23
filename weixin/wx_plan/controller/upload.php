<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2019/1/6
 * Time: 23:22
 */
header('Content-Type:application/json;charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("access-control-allow-methods: GET, POST");
$tempname = "..";
$foldername = "/upload/replenish/";
if (!file_exists($foldername)) {
    mkdir($foldername, 0777, true);
}

$filename =$tempname.$foldername.time().$_FILES["file"]["name"];
//转码，把utf-8转成gb2312,返回转换后的字符串， 或者在失败时返回 FALSE。
$filename = iconv("UTF-8","gb2312",$filename);
if(file_exists($filename))
{
    throw new Exception($filename."已存在");
    StatusCode(1,false,"上传图片重名！");
}
else
{
    //保存文件,   move_uploaded_file 将上传的文件移动到新位置
    $result = move_uploaded_file($_FILES["file"]["tmp_name"],$filename);//将临时地址移动到指定地址if()
    if($result) {
        StatusCode(0,$foldername.time().$_FILES["file"]["name"]);
    }else{
        StatusCode(1,"",$result);
    }
}

function StatusCode($status, $result, $msg = '成功')
{
    echo json_encode(array(
        'status' => $status,
        'result' => $result,
        'msg' => $msg
    ));
    exit();
}
