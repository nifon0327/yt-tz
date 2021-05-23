<?
//读取工具栏badgeValue值
switch($mModuleId){
      case "115"://门禁
             $mySql="SELECT I.TypeId,COUNT(*) AS Counts     
FROM $DataPublic.come_data I 
WHERE I.Estate>0   and TIMESTAMPDIFF(HOUR,I.InTime,NOW())>4  GROUP BY I.TypeId Order by TypeId";
             break; 
 }
 if ($mySql!=""){
		$myResult = mysql_query($mySql);
		if($myRow = mysql_fetch_array($myResult))
		  {
		     do {	
		                $Id=$myRow["TypeId"];
		                $Counts=$myRow["Counts"];
		                 $jsonArray[]=array("$Id","$Counts"); 	    
		                 
		      }while($myRow = mysql_fetch_array($myResult));  
		}
}
?>