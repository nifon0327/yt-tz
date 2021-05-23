<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$UpdateSql="UPDATE $DataIn.ch1_deliverymain  SET Estate=1 WHERE Id=$Mid";
$UpdateResult=mysql_query($UpdateSql);
if($UpdateResult)echo "Y";
else echo "N";

?>