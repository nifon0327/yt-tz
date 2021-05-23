<?php
$PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId'  AND Property IN (1,2,10)",$link_id);
while($PropertyRow=mysql_fetch_array($PropertyResult)){
          $Property=$PropertyRow["Property"];
          if($Property>0)$StuffCname=$StuffCname."<img src='image/property_$Property.png'  width='36' height='36'>";
  }
 ?>