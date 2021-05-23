<?php 
 include "../basic/parameter.inc";
 include "../model/subprogram/read_datain.php";
 
 switch($Action){
	   case "IdCard":
	     $checkSql=mysql_query("SELECT Number FROM  $DataIn.lw_staffsheet  WHERE Idcard='$IdCard' ",$link_id);
		 if (mysql_num_rows($checkSql)>0){
			  echo "Y"; 
		 }
	  break;

 }
?>