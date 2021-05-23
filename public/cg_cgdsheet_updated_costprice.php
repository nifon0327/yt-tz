<?php
/*更新半成品配件成本价格*/
  $MyPDOEnabled=1;
  include "../basic/parameter.inc";
  $myPDO->query("CALL proc_cg1_semifinished_costprice('$StockId',@OutLog);");
  $myResult=$myPDO->query("SELECT @OutLog;");
  $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
  $Log.=strlen($myRow['@OutLog'])>0?'&nbsp;&nbsp;<br><b>' . $myRow['@OutLog'] . '半成品配件的成本价格已更新;</b>':'';
?>