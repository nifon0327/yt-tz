<?php   
//电信-zxq 2012-08-01
/*
功能：未上传配件图档提醒
独立已更新：zhongxq 2011-2-18 16:08
*/
$get_JobId=mysql_fetch_array(mysql_query("SELECT JobId FROM $DataPublic.staffmain M WHERE  M.Number=$Login_P_Number",$link_id));
$JobIdTemp=$get_JobId[0];

if ($Login_P_Number=='10868') $JobIdTemp=3;
$job_mySql="SELECT COUNT(*) FROM $DataIn.stuffdata S 
     LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId  
	 WHERE (S.Picture in (0,4,7) OR (S.Picture=0 AND S.JobId>0)) 
	  AND S.Estate>0 AND S.JobId='$JobIdTemp'  AND T.mainType<2";
//	 echo $job_mySql;
$job_result = mysql_query($job_mySql,$link_id);
$checkSql=mysql_fetch_array($job_result);
if ($checkSql[0]>0){
 	$OutputInfo.="<li class=TitleA>$Title</li>";
	$OutputInfo.="<li class=DataA><a href='$Extra?JobId=$JobIdTemp' target='_blank' style='CURSOR: pointer;color:#FF6633'>".$checkSql[0] ."</a></li>";
  }
?> 