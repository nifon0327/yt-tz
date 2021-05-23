<?php
/**
 * Created by PhpStorm.
 * User: Kyle
 */
function StatusCode($status, $result, $msg = '成功')
{
    echo json_encode(array(
        'status' => $status,
        'result' => $result,
        'msg' => $msg
    ));
}

function GetParam($string){
    $param = $_POST[$string];
    if(is_null($param))
        throw new Exception("参数".$string."缺失");
    return $param;
}

function ThridStatusCode($status,$result,$msg='')
{
    echo json_encode(array(
        'success' => $status,
        'errorCode' => $result,
        'errorMsg' => $msg
    ));
}
function ThridResultCode($status,$result,$msg='')
{
    echo json_encode(array(
        'taskList'=>$result,
        'success' => $status,
        'errorCode' => 0,
        'errorMsg' => $msg
    ));
}

function TralleyResultCode($KilnId='',$Status='',$KType='',$LineNo='',$success=false,$errorCode=1,$errorMsg='')
{
    echo json_encode(array(

       'KilnId' => $KilnId,
        'Status' => $Status,
        'KType' => $KType,
        'LineNo' => $LineNo,
        'success' => $success,
        'errorCode' => $errorCode,
        'errorMsg' => $errorMsg
    ));

}
