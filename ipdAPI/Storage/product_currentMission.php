<?php
    include_once "../../basic/parameter.inc";

    $POrderId = $_POST['POrderId']!=""?$_POST['POrderId']:$_GET['POrderId'];
    $lineNumber = $_POST['lineNumber']!=""?$_POST['lineNumber']:$_GET['lineNumber'];
    $date = date('Y-m-d H:i:s');

    if($POrderId == "" || $lineNumber == ""){
        echo json_encode(array('result' => 'N'));
        exit();
    }

    $insertMissionSql = "INSERT INTO $DataIn.sc_currentmission (Id, POrderId, LineNumber, DateTime, Estate, Locks)
                         VALUE (NULL, '$POrderId', '$lineNumber', '$date', 1, 0)";
    //echo $insertMissionSql;
    $isSuccess = 'Y';
    if(!mysql_query($insertMissionSql)){
        $isSuccess = 'N';
    }

    echo json_encode(array('result' => $isSuccess));

?>