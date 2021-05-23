<?php
include "../../basic/parameter.inc";

    $StuffId = $_POST['stuffId'];
    $recordNumber = $_POST['recordNumber'];

    $result = 'N';
    $recordStuffFrameCapacitySql = "UPDATE $DataIn.stuffdata Set FrameCapacity=$recordNumber Where StuffId='$StuffId'";
    if(mysql_query($recordStuffFrameCapacitySql)){
        $result = 'Y';
    }

    echo json_encode(array('result'=>$result));

?>