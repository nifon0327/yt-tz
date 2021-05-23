<?php 
//读取通知内容
 switch ($Sign){
	      case  3://人事通知
	       $mySql = "select M.Content FROM $DataPublic.msg3_notice M  WHERE M.Id='$Id' ";
	         $dataArray = array(); 
	         $myResult = mysql_query($mySql);
	         if($myRow = mysql_fetch_assoc($myResult))
	         {
	             $Content=$myRow["Content"];
	             $Picture=$myRow["Picture"]==""?"0":"download/msgfile/" . $myRow["Picture"];
	             $jsonArray= array( "Text"=>"$Content","Image"=>"0");
	         }

             break;
         default:
	         $mySql = "select M.Content,P.Picture FROM $DataPublic.msg1_bulletin M 
	                        LEFT JOIN  $DataPublic.msg1_picture P ON P.Mid=M.Id 
	                        WHERE M.Id='$Id' ";
	         $dataArray = array(); 
	         $myResult = mysql_query($mySql);
	         if($myRow = mysql_fetch_assoc($myResult))
	         {
	             $Content=$myRow["Content"];
	             $Picture=$myRow["Picture"]==""?"0":"download/msgfile/" . $myRow["Picture"];
	             $jsonArray= array( "Text"=>"$Content","Image"=>"$Picture");
	         }
	      break;   
  }
  
  //更新读取信息的最后时间
$DateTime=date("Y-m-d H:i:s");
$recordSql=mysql_query("SELECT Id FROM  $DataPublic.app_readrecord WHERE Number='$LoginNumber' AND Item='Msg' LIMIT 1",$link_id);
if ($recordRow = mysql_fetch_array($recordSql))
{
      $upId=$recordRow["Id"];
      $updateSql="UPDATE $DataPublic.app_readrecord SET ReadTime='$DateTime' WHERE Id=$upId";
      $UpdateResult = mysql_query($updateSql,$link_id);
}
else{
    $insertSql="INSERT INTO  $DataPublic.app_readrecord (Id, Number, Item, ReadTime) VALUES (NULL, '$LoginNumber', 'Msg', '$DateTime')";
    $insertResult = mysql_query($insertSql,$link_id);
}
?>