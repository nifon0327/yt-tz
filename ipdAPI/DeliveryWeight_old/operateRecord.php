<?php
    $operateRecordSql = "INSERT INTO $DataIn.qcDataRecord (Id, stuffId, qty, date, operator, relation) VALUES (NULL, $StuffId, $recordNumber, NOW(),$operator, $relation)";
    @mysql_query($operateRecordSql);
?>