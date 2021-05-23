<?php 
   //读取餐厅菜式名称
   include "../../basic/parameter.inc";
  
    $jsonArray=array();
    $MenuArray=array();
    $mySql="SELECT A.Id,A.Name,A.CtId,A.Price,B.Name AS CTName 
     FROM $DataPublic.ct_menu A 
     LEFT JOIN $DataPublic.ct_data B ON B.Id=A.CtId
    WHERE A.Estate=1 ORDER BY A.CtId DESC,A.Id DESC";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
     { 
         $oldCtId=$myRow["CtId"];
         $CTName=$myRow["CTName"];
        do{
            $CtId=$myRow["CtId"];	
            if ($CtId!=$oldCtId){
	             $jsonArray[]=array("$oldCtId","$CTName",$MenuArray);
	             $oldCtId=$CtId;
	             $CTName=$myRow["CTName"];
	             $MenuArray=array();
            }
            $MenuId=$myRow["Id"];
            $MenuName=$myRow["Name"];	
            $Price=$myRow["Price"];
            $MenuArray[]=array("$MenuId","$MenuName","$Price");
        } while($myRow = mysql_fetch_assoc($myResult));
        $jsonArray[]=array("$oldCtId","$CTName",$MenuArray);
     }
   echo json_encode($jsonArray); 	
?>