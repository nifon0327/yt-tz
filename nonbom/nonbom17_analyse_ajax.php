<?php 
include "../model/modelhead.php";
$Date=date("y-m-d");
$Sql = "UPDATE $DataIn.nonbom7_code SET Estate='$LastEstate' WHERE BarCode='$BarCode'";
$Result = mysql_query($Sql);

?>