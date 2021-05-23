<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
/*
 * 代码、数据库合并后共享-ZXQ 2012-08-08
 * 读取部门、小组、职位等信息
 */
 include "../basic/parameter.inc";
 include "../model/subprogram/read_datain.php";
 
 switch($Action){
	 case "BranchId":
		   $checkSql=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
								   WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 )  order by Id",$link_id);
			while($checkRow=mysql_fetch_array($checkSql)){
			    $sId=$checkRow["Id"];
			    $sName=$checkRow["Name"];
			    $subName[]=array($sId,$sName);
			}
			echo json_encode($subName);
	   break;
	  case "GroupId":
	      $checkSql=mysql_query("SELECT GroupId,GroupName FROM $DataIn.staffgroup WHERE Estate=1 order by GroupId",$link_id);
			while($checkRow=mysql_fetch_array($checkSql)){
			    $sId=$checkRow["GroupId"];
			    $sName=$checkRow["GroupName"];
			    $subName[]=array($sId,$sName);
			}
			echo json_encode($subName);
	   break;
	 case "JobId":
	     $checkSql=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
							     WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
			while($checkRow=mysql_fetch_array($checkSql)){
			    $sId=$checkRow["Id"];
			    $sName=$checkRow["Name"];
			    $subName[]=array($sId,$sName);
			}
			echo json_encode($subName);
	  break;
	  
	   case "IdCard":
	     $checkSql=mysql_query("SELECT Number FROM  $DataPublic.staffsheet  WHERE Idcard='$IdCard' ",$link_id);
		 if (mysql_num_rows($checkSql)>0){
			  echo "Y"; 
		 }
	  break;

 }
?>