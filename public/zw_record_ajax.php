<?php 
//OK
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
switch($Action){
	     case 1:
	          $UpdateSql="Update $DataPublic.fixed_userdata  SET  Estate=0 WHERE Mid='$Id'";
	          $UpdateResult=mysql_query($UpdateSql);
	          if($UpdateResult){
		          echo"Y";
	          }
	          else{
		          echo "N";
	          }
	          break;
	}
?>