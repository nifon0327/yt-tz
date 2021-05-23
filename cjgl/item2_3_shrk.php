<?php
$sumQty=$sumQty==""?0:$sumQty;
$myResult=$myPDO->query("CALL proc_ck1_rksheet_save('$Id','$sumQty',$Operator);");
$myRow = $myResult->fetch(PDO::FETCH_ASSOC);

$myResult=null;$myRow=null;
$OperResult = $myRow['OperationResult'];

?>