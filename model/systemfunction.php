<?php
  function getSystemParam($PNumber,$DataIn,$link_id){
   
	   $SystemRow = mysql_fetch_array(mysql_query("SELECT pValue FROM $DataIn.sys6_parameters WHERE PNumber='$PNumber'",$link_id));
	   $SystemValue = $SystemRow["pValue"] ==""?0:$SystemRow["pValue"];
	   return $SystemValue;
  }
  
  function getSystemParamByActionId($ActionId,$DataIn,$link_id){
   
	   $SystemRow = mysql_fetch_array(mysql_query("SELECT pValue FROM $DataIn.sys6_parameters WHERE ActionId='$ActionId'",$link_id));
	   $SystemValue = $SystemRow["pValue"] ==""?0:$SystemRow["pValue"];
	   return $SystemValue;
  }
?>