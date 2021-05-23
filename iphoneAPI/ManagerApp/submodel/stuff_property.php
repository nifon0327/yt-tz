<?php
//配件属性
$StuffProperty="";
  if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
        $StuffProperty="c1";
   }
   else{
       $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' ORDER BY Property",$link_id);
       while($PropertyRow=mysql_fetch_array($PropertyResult)){
                $Property=$PropertyRow["Property"];
                  if($Property>0)$StuffProperty.=$StuffProperty==""?$Property:"|$Property";
         }
   }
?>