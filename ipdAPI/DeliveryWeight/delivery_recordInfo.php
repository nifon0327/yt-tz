<?php
    include_once "../../basic/parameter.inc";
    $line = $_POST['line'];
    $number = $_POST['number'];
    $weight = $_POST['weight'];

    $result = 'N';
    $info = array();
    if($number != ''){
        $info[] = "number = $number";
    }

    if($weight != ''){
        $info[] = "weight = $weight";
    }

    $updateInfo = implode(',', $info);
    $updateLineStateSql = "UPDATE $DataIn.qc_currentcheck SET $updateInfo Where Id = $line";
    if(count($info)>0 && mysql_query($updateLineStateSql)){
        $result = 'Y';
    }

    echo json_encode(array('result'=>$result));


?>