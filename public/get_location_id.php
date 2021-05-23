<?php
include_once '../basic/parameter.inc';
    $stuff = $_REQUEST['StuffId'];
    $mysql = mysql_query("select SeatId from $DataIn.stuffdata where stuffid = $stuff and estate = 1");

    $myRow = mysql_fetch_assoc($mysql);

    echo json_encode($myRow);


?>