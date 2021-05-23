<?php
$propertyStuffIds= "";
$propertySql = "SELECT  StuffId  FROM stuffproperty  WHERE Property = 14";
$propertyResult = mysql_query($propertySql,$link_id);
if($propertyRow = mysql_fetch_array($propertyResult)){
    do{
         $propertyStuffId  =  $propertyRow["StuffId"];
          if($propertyStuffIds ==""){
                $propertyStuffIds =  $propertyStuffId;
            }else{
                 $propertyStuffIds = $propertyStuffIds.",".$propertyStuffId;
              }
        }while($propertyRow = mysql_fetch_array($propertyResult));
}
?>