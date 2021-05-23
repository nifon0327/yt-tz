<?php
include "../../basic/parameter.inc";

    $StuffId = $_POST['stuffId'];
    $basketType = $_POST['basketType'];

    $result = 'N';
    $recordStuffFrameCapacitySql = "UPDATE $DataIn.stuffdata Set basketType=$basketType Where StuffId='$StuffId'";
    if(mysql_query($recordStuffFrameCapacitySql)){
        $result = 'Y';
    }

    echo json_encode(array('result'=>$result));

?>