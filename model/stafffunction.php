<?php
  function getStaffcSign($Number,$DataIn,$link_id){
   
	   $StaffRow = mysql_fetch_array(mysql_query("SELECT cSign FROM $DataIn.staffmain WHERE Number='$Number'",$link_id));
	   $cSign = $StaffRow["cSign"] ==""?0:$StaffRow["cSign"];
	   return $cSign;
  }
?>