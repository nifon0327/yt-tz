<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$CheckNameResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.childclass WHERE Id=$tempClassId",$link_id));
$ClassName=$CheckNameResult["Name"];
$UpdateSql="UPDATE $DataIn.cw19_studyfeesheet SET NowSchool=$tempClassId WHERE Id=$Id";
$UpdateResult=@mysql_query($UpdateSql);
if($UpdateResult && mysql_affected_rows()>0){
        echo $ClassName;
    }


?>