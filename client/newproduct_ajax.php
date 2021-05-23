<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_Id;
$OperationResult="N";

switch($Action){
	 case 1://收藏
	    $checkSql = " SELECT Id,Liked from new_liked where NewId='$Id' AND Liker='$Operator' LIMIT 1";
	    $checkResult = mysql_query($checkSql,$link_id);
	    
		if ($checkRow = mysql_fetch_array($checkResult)){
		     $sId= $checkRow['Id'];
		     
		     $updateSQL = "UPDATE new_liked SET Liked='$Liked' WHERE Id='$sId' ";
		     $updateResult = mysql_query($updateSQL);
		     if ($updateResult){
		         $OperationResult="Y";
		     }
		}
		else{
			$inRecode = "INSERT INTO new_liked (NewId,Liked,Estate,Liker,creator,created)
			                              VALUES('$Id','$Liked','1','$Operator','$Operator','$DateTime')";
			$inAction=@mysql_query($inRecode);
			if ($inAction){ 
				$OperationResult="Y";
			}               
		}
		break;
	case 2:
		break;
}
echo $OperationResult;
?>