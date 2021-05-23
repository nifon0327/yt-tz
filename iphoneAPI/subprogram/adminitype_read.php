<?php 
   //读取行政费用分类
   include "../../basic/parameter.inc";
  
    $jsonArray=array();
    $jsonArray[]="READ";
    $typeIdArray=array();
    $typeArray=array();
    $currencyArray=array();
    $currencyIdArray=array();
    //读取行政分类信息
    $mySql = "SELECT TypeId,Name FROM $DataPublic.adminitype WHERE Estate=1 order by Id";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
     {
        do{
            $typeIdArray[]=$myRow["TypeId"];
            $typeArray[] =$myRow["Name"];		
        } while($myRow = mysql_fetch_assoc($myResult));
         $jsonArray[]=$typeIdArray;
         $jsonArray[]= $typeArray;
     }
     //读取货币信息
     $mySql = "SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
     {
        do{
            $currencyIdArray[] =$myRow["Id"];
            $currencyArray[]=$myRow["Name"];		
        }while($myRow = mysql_fetch_assoc($myResult));
           $jsonArray[]=$currencyIdArray;
           $jsonArray[]=$currencyArray; 
     }
   
   echo json_encode($jsonArray); 
	
?>