<?php 
//借支统计
$JzRow=mysql_fetch_array(mysql_query("SELECT SUM(A.Jz) AS Jz 
  FROM (
       SELECT IFNULL(SUM(Amount),0) AS Jz FROM $DataIn.cwygjz WHERE Number='$Number'
 UNION ALL 
     SELECT IFNULL(SUM(Amount),0) AS Jz FROM $DataSub.cwygjz WHERE Number='$Number'
   )A",$link_id));
$Sum_Jz=$JzRow["Jz"];
if ($Sum_Jz>0){
	 $payJzRow=mysql_fetch_array(mysql_query("SELECT SUM(A.PayJz) AS PayJz 
	    FROM (
	          SELECT IFNULL(SUM(Jz),0) AS PayJz FROM $DataIn.cwxzsheet WHERE Number='$Number' AND Estate=0
	  UNION ALL 
		     SELECT IFNULL(SUM(Jz),0) AS PayJz FROM $DataSub.cwxzsheet WHERE Number='$Number' AND Estate=0
      )A",$link_id));
	 $Sum_PayJz=$payJzRow["PayJz"];
	  if ($Sum_PayJz<$Sum_Jz){
		   $Jz=$Sum_Jz-$Sum_PayJz;
	  }
}	
?>