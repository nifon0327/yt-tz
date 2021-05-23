<?php 
//电信-ZX  2012-08-01
include "basic/parameter.inc";
//考勤记录
$ToDay=date("Y-m-d");
$PreDay=date("Y-m-d",strtotime("+1 day"));
//$CheckIOsql=mysql_query("SELECT M.Name,DATE_FORMAT(I.CheckTime,'%H:%i') AS CheckTime,I.CheckType,I.KrSign FROM $DataIn.iotest I,d0.staffmain M WHERE M.Number=I.Number AND ((DATE_FORMAT(I.CheckTime,'%Y-%m-%d')='$ToDay' AND I.KrSign='0') OR (DATE_FORMAT(I.CheckTime,'%Y-%m-%d')='$PreDay' AND I.KrSign='1')) ORDER BY I.CheckTime",$link_id);

$CheckIOsql=mysql_query("SELECT M.Name,DATE_FORMAT(I.CheckTime,'%H:%i') AS CheckTime,I.CheckType 
						FROM $DataIn.iotest I,$DataPublic.staffmain M WHERE M.Number=I.Number AND DATE_FORMAT(I.CheckTime,'%Y-%m-%d')='$ToDay' AND I.KrSign='0' ORDER BY I.CheckTime",$link_id);
if($CheckIOrow=mysql_fetch_array($CheckIOsql)){
	$i=1;
	do{
		$Name=$CheckIOrow["Name"];
		$CheckTime=$CheckIOrow["CheckTime"];
		$CheckType=$CheckIOrow["CheckType"]=="I"?"签到":"签退";
		$ListTable.="<tr><td align='center'>$i</td><td>$Name</td><td>$CheckTime$CheckType</td></tr>";
		$i++;
		}while ($CheckIOrow=mysql_fetch_array($CheckIOsql));
	}
//加班通知
$CheckJbMsg=mysql_fetch_array(mysql_query("SELECT Content FROM $DataPublic.msg2_overtime WHERE Date='$ToDay' ORDER BY Date DESC LIMIT 1",$link_id));
$JbMsg=$CheckJbMsg["Content"];
//人事通知
$CheckRsMsg=mysql_fetch_array(mysql_query("SELECT Content FROM $DataPublic.msg3_notice WHERE Date='$ToDay' ORDER BY Date DESC LIMIT 1",$link_id));
$RsMsg=$CheckRsMsg["Content"];
?>