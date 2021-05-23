<?php 
//电信-ZX  2012-08-01
//考勤记录
$ToDay=date("Y-m-d");
$PreDay=date("Y-m-d",strtotime("+1 day"));
$CheckIOsql=mysql_query("
SELECT Name,CheckTime,CheckType FROM (
	SELECT M.Name,DATE_FORMAT(I.CheckTime,'%H:%i') AS CheckTime,I.CheckType 
		FROM $DataIn.checkinout I,$DataPublic.staffmain M 
		WHERE M.Number=I.Number AND DATE_FORMAT(I.CheckTime,'%Y-%m-%d')='$ToDay' AND I.KrSign='0'
	UNION
	SELECT M.Name,DATE_FORMAT(I.CheckTime,'%H:%i') AS CheckTime,I.CheckType
		FROM $DataIn.checkiotemp I,$DataIn.stafftempmain M
		WHERE M.Number=I.Number AND DATE_FORMAT(I.CheckTime,'%Y-%m-%d')='$ToDay' AND I.KrSign='0' 
) A ORDER BY CheckTime DESC
",$link_id);
$checkGDSql=mysql_query("SELECT M.Name FROM $DataPublic.staffmain M WHERE M.Estate=1 AND M.KqSign>1 AND Number NOT IN (SELECT Number FROM $DataPublic.kqqjsheet WHERE DATE_FORMAT(StartDate,'%Y-%m-%d')<='$ToDay' AND DATE_FORMAT(EndDate,'%Y-%m-%d')>='$ToDay' )ORDER BY M.JobId DESC,M.Number DESC",$link_id);
$i=mysql_num_rows($CheckIOsql)+mysql_num_rows($checkGDSql);//记录总数
if($CheckIOrow=mysql_fetch_array($CheckIOsql)){
	do{
		$Name=$CheckIOrow["Name"];
		$CheckTime=$CheckIOrow["CheckTime"];
		$CheckType=$CheckIOrow["CheckType"]=="I"?"签到":"签退";
		$ListTable.="<tr><td align='center'>$i</td><td>$Name</td><td>$CheckTime$CheckType</td></tr>";
		$i--;
		}while ($CheckIOrow=mysql_fetch_array($CheckIOsql));
	}
//固定薪人员
if($checkGDRow=mysql_fetch_array($checkGDSql)){
	do{
		$Name=$checkGDRow["Name"];
		$ListTable.="<tr><td align='center'><span style='color:#FF0000'>$i</span></td><td><span style='color:#FF0000'>$Name</span></td><td><span style='color:#FF0000'>08:00签到</span></td></tr>";
		$i--;
		}while($checkGDRow=mysql_fetch_array($checkGDSql));
	}
//加班通知
$CheckJbMsg=mysql_fetch_array(mysql_query("SELECT Content FROM $DataPublic.msg2_overtime WHERE Date='$ToDay' ORDER BY Date DESC LIMIT 1",$link_id));
$JbMsg=$CheckJbMsg["Content"];
//人事通知
$CheckRsMsg=mysql_fetch_array(mysql_query("SELECT Content FROM $DataPublic.msg3_notice WHERE 1 ORDER BY Date DESC,Id DESC LIMIT 1",$link_id));
$RsMsg=$CheckRsMsg["Content"];
//各部门上班人数
/*
$chekCountSql=mysql_query("
	SELECT  count(*) AS Nums,J.Name
	FROM $DataIn.checkinout I
	LEFT JOIN $DataPublic.staffmain M ON M.Number=I.Number
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	WHERE 1 AND DATE_FORMAT(I.CheckTime,'%Y-%m-%d')='$ToDay' AND I.KrSign='0' AND I.CheckType='I' GROUP BY M.JobId ORDER BY M.JobId
	",$link_id);
if($checkCountRow=mysql_fetch_array($chekCountSql)){
	$CountInfo="";
	do{
		$Name=$checkCountRow["Name"];
		$Nums=$checkCountRow["Nums"];
		$CountInfo.=$Name."上班人数:".$Nums.".&nbsp;&nbsp;";
		}while ($checkCountRow=mysql_fetch_array($chekCountSql));
	}
*/
?>