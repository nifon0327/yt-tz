<?php   
$MaxResult=mysql_fetch_array(mysql_query("SELECT MAX(Num) AS Num FROM $DataIn.yw9_blsheet",$link_id));
$MaxNum=$MaxResult["Num"]==""?1:$MaxResult["Num"]+1;
$InSql="INSERT INTO $DataIn.yw9_blsheet(Id, Num, POrderId, blDate, Estate, Date, Operator)values(NULL,'$MaxNum','$POrderId','$Date','1','$Date','$Operator')";
$InResult=@mysql_query($InSql);
?>