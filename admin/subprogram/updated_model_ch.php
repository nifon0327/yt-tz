<?php   
//电信---yang 20120801
include "../../basic/parameter.inc";
$DelResult="DELETE FROM $DataIn.ch4_logistics_date WHERE Mid='$Id'";
$DelRow=mysql_query($DelResult);
$InResult="insert into $DataIn.ch4_logistics_date(Id, Mid, Date)VALUES(NULL,'$Id','$LogDate')";
$InRecode=@mysql_query($InResult);
if($InRecode){
      echo "Y";
     }
else{
     echo "N";
    }
?>