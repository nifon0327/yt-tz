<?php 
   //读取车辆信息
   include "../../basic/parameter.inc";
  
    $jsonArray=array();
    $jsonArray[]="READ";
    $carIdArray=array();
    $carArray=array();
    $driverArray=array();
    $NumberArray=array();
    //读取车辆信息
    $mySql = "select Id,CarNo FROM $DataPublic.cardata WHERE Estate=1  AND UserSign IN(1,2) order by Id";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
     {
        do{
            $carIdArray[]=$myRow["Id"];
            $carArray[] =$myRow["CarNo"];		
        } while($myRow = mysql_fetch_assoc($myResult));
         $jsonArray[]=$carIdArray;
         $jsonArray[]= $carArray;
     }
     //读取司机信息
     $mySql = "select Number,Name FROM $DataPublic.staffmain WHERE JobId=11 AND Estate=1 order by Number";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
     {
        do{
            $NumberArray[] =$myRow["Number"];
            $driverArray[]=$myRow["Name"];		
        }while($myRow = mysql_fetch_assoc($myResult));
     }
   $NumberArray[] ="0";
   $driverArray[]="自驾"; 
   
   $jsonArray[]=$NumberArray;
   $jsonArray[]=$driverArray; 
   echo json_encode($jsonArray); 
	
?>