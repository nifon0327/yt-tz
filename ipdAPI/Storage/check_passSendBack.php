<?php
    include_once "../../basic/parameter.inc";

    $stockId = $_POST['stockid'];

    $updateQcbadSql = "UPDATE $DataIn.qc_badrecord SET Estate = 0 WHERE StockId = $stockId";
    $result = 'false';
    if(mysql_query($updateQcbadSql)){
        $result = 'success';
    }
    echo $result;
?>