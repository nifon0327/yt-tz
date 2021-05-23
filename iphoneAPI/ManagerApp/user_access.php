<?php 
  //取得权限
$itemArray=array();
$modelArray=array();

switch($ReadAccessSign){
   case 1:
	     $ReadTaskuserSign=0;
	     $ReadFunModuleSign=1;
	    break;
	case 2:
	     $ReadTaskuserSign=1;
	     $ReadFunModuleSign=0;
	    break;
   case 3:
         $ReadTaskuserSign=1;
	     $ReadFunModuleSign=2;
	    break;
   case 4:
         $ReadTaskuserSign=0;
	     $ReadFunModuleSign=3;
        break;
   case 5:
        $ReadTaskuserSign=0;
	     $ReadFunModuleSign=4;
	     break;
	default:
	    $ReadTaskuserSign=1;
	     $ReadFunModuleSign=1;  
	 break;     
}
$ReadModuleTypeSign=$ReadModuleTypeSign==""?5:$ReadModuleTypeSign;

if ($ReadTaskuserSign==1){
	$TResult02 = mysql_query("SELECT A.ItemId
	FROM $DataPublic.tasklistdata A
	LEFT JOIN $DataIn.taskuserdata B ON B.ItemId=A.ItemId
	WHERE  A.Estate=1  AND B.UserId='$LoginNumber' ORDER BY A.Oby",$link_id);
	while ($TRow02 = mysql_fetch_array($TResult02)){
	          $itemArray[]=$TRow02["ItemId"];
	 }
}

switch($ReadFunModuleSign){
     case 1:
			$userIdResult=mysql_query("SELECT Id FROM  $DataIn.usertable WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id);
			if  ($userIdRow = mysql_fetch_array($userIdResult)){
			         $userId=$userIdRow["Id"];
			        $rMenuResult = mysql_query("SELECT A.ModuleId
			                FROM $DataIn.upopedom A 
			                LEFT JOIN $DataIn.funmodule B ON B.ModuleId=A.ModuleId 
			                WHERE A.Action>0 AND B.TypeId='$ReadModuleTypeSign' AND A.UserId='$userId' AND B.Estate=1 ORDER BY B.OrderId",$link_id);
			   while ($rMenuRow = mysql_fetch_array($rMenuResult)){
			                $modelArray[]=$rMenuRow["ModuleId"];
			        }
			}
	        break;
	  case 2:
	        $userIdResult=mysql_query("SELECT Id FROM  $DataIn.usertable WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id);
			if  ($userIdRow = mysql_fetch_array($userIdResult)){
			         $userId=$userIdRow["Id"];
			        $rMenuResult = mysql_query("SELECT A.ModuleId
			                    FROM $DataIn.upopedom A 
			                    LEFT JOIN $DataPublic.funmodule B ON B.ModuleId=A.ModuleId 
			                    WHERE A.Action>0 AND ((B.TypeId=2 AND B.Estate=1) OR A.ModuleId IN('1347','1077','1078')) AND A.UserId='$userId' ORDER BY B.OrderId",$link_id);
			   while ($rMenuRow = mysql_fetch_array($rMenuResult)){
			                $modelArray[]=$rMenuRow["ModuleId"];
			    }
			}
	        break;
	  case 3:
	       $ActionArray=array();
	       $userIdResult=mysql_query("SELECT Id FROM  $DataIn.usertable WHERE Number='$LoginUserId' AND Estate=1 LIMIT 1",$link_id);
		    if  ($userIdRow = mysql_fetch_array($userIdResult)){
		            $userId=$userIdRow["Id"];
		            
		            $dModuleIdResult=mysql_query("SELECT dModuleId FROM  $DataPublic.modulenexus WHERE  ModuleId='1044'",$link_id);
		            while ($dModuleIdRow = mysql_fetch_array($dModuleIdResult)){
		               $ModuleId=$dModuleIdRow["dModuleId"];
		               $dModelId.=$dModelId==""?$ModuleId:",$ModuleId";
		            }
		            $rMenuResult = mysql_query("SELECT A.ModuleId,A.Action   
		                    FROM $DataIn.upopedom A 
		                    LEFT JOIN $DataPublic.funmodule B ON B.ModuleId=A.ModuleId 
		                    LEFT JOIN  $DataPublic.modulenexus C ON C.dModuleId=A.ModuleId  
		                    WHERE A.Action>0 AND B.TypeId=5 AND A.UserId='$userId' AND B.Estate=1 AND (C.ModuleId IN ($dModelId) OR  A.ModuleId=1347 OR  A.ModuleId=1245 OR  A.ModuleId=1533) ORDER BY B.OrderId",$link_id);
		        while ($rMenuRow = mysql_fetch_array($rMenuResult)){
		                    $d_MId=$rMenuRow["ModuleId"];
		                    $modelArray[]=$rMenuRow["ModuleId"];
		                    $ActionArray[$d_MId]=$rMenuRow["Action"];
		            }
		  }
	       break;
	   case 4:
	          $userResult=mysql_fetch_array(mysql_query("SELECT uType,uPwd,Estate FROM  $DataIn.usertable WHERE Number='$LoginNumber' AND Estate=1 ",$link_id));
	          $Login_uType=$userResult["uType"];
	          $Login_uPwd=$userResult["uPwd"];
	          $Login_Estate=$userResult["Estate"];
	     break;
}
?>