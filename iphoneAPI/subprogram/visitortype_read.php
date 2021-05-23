<?php 
   //读取来访登记分类
   include "../../basic/parameter.inc";
  
    $jsonArray=array();
    $jsonArray[]="READ";
    $typeIdArray=array();
    $typeArray=array();
   
    //读取分类信息
    $mySql = "SELECT Id,Name FROM $DataPublic.come_type WHERE Estate=1 order by Id";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
     {
        do{
            $typeIdArray[]=$myRow["Id"];
            $typeArray[] =$myRow["Name"];		
        } while($myRow = mysql_fetch_assoc($myResult));
         $jsonArray[]=$typeIdArray;
         $jsonArray[]= $typeArray;
     }
   echo json_encode($jsonArray); 
	
?>