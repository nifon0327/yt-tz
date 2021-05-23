<?php 

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

switch($Type){
	case 1:
	  $checkSql = mysql_query("SELECT Id,CarNo,User FROM $DataIn.cardata WHERE Estate=1",$link_id);
	  if($checkRow=mysql_fetch_array($checkSql)){
	      echo "<table><tr>
	       <td>选项<td>
	       </tr>";
	     do{
	     
	     }while($checkRow=mysql_fetch_array($checkSql));  
	     echo "</table>";
	  }
	  break;
}
?>