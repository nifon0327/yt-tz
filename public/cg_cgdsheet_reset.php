<?php 
//电信-zxq 2014-07-18 传入参数Id iphoneAPI/系统共享
//采购单重置
 // echo $Id;
  
  $MyPDOEnabled=1;
  include "../basic/parameter.inc";
  echo "CALL proc_cg1_stocksheet_resetqty('$Id','',$Operator);";
  $myResult=$myPDO->query("CALL proc_cg1_stocksheet_resetqty('$Id','',$Operator);");
  $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
  $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
  
  $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
  $Log.="</br>";
	
?>