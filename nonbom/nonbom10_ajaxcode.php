<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
  switch($ActionId){
         case "1":
             $DelSql="DELETE  FROM $DataIn.nonbom10_bffixed  WHERE Id=$Id";
              $DelResult=@mysql_query($DelSql);
               if($DelResult && mysql_affected_rows()>0){
                  echo "Y";
                  }
          break;
    }
?>