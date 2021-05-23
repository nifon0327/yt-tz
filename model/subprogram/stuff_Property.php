<?php
$ClientProSign=0;//是否为客供配件，是为1，不是为0
$FinishProSign=0;//是否为成品配件，是为1，不是为0；
$ComboxMainSign =0;
$ComboxSheetSign =0;
$StuffPropertys=array();

$PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' ORDER BY Property",$link_id);
while($PropertyRow=mysql_fetch_array($PropertyResult)){
      $Property=$PropertyRow["Property"];
      $StuffPropertys[]=$Property;
      
      if($Property>0)$StuffCname=$StuffCname."<img src='../images/gys$Property.png'  width='18' height='18'>";
      if($Property==2)$ClientProSign=1;
      if($Property==3)$FinishProSign=1;
      if($Property==9)$ComboxMainSign=1;
      if($Property==10)$ComboxSheetSign=1;
  }
?>