<?php
include "../../basic/parameter.inc";

    $qrCode = substr($_POST['qrCode'], 10, 8);
    $getStaffSql = "SELECT Number,Name From $DataIn.staffmain Where IdNum = $qrCode";
    $staffResult = mysql_query($getStaffSql);
    $staffRow = mysql_fetch_assoc($staffResult);
    $staffNumber = $staffRow['Number'];
    $staffName = $staffRow['Name'];

    echo json_encode(array('name'=>$staffName, 'number'=>$staffNumber));

?>