<?php   
//未使用电信---yang 20120801
for($d=1;$d<=$days;$d++){//计算当天的数据
	$WorksA=0;		//当天不考勤员工总数
	$WorksB=0;		//当天考勤员工总数
	$Hours=0;		//当天总工时数
	$SearchDay=" AND S.Date='$DateNow'";
	//从当天分组中读取该小组不用考勤的员工数量
	$checkKqSql=mysql_fetch_array(mysql_query("SELECT count(*) AS WorksA 
	FROM $DataIn.sc1_memberset S 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
	WHERE M.KqSign>1 $SearchDay AND S.GroupId='$theId' $CheckTimeSTR",$link_id));
	$WorksA=$checkKqSql["WorksA"];
	include "desk_cjrtj_work.php" ;  //调用考勤列表，每天统计，工作时间，及加班时间
	$MonthWorkHours+=$Hours+$WorksA*10;	//当天小组工作总工时,不考勤的按1天10小时算
	$DateNow=date("Y-m-d",strtotime("$DateNow + 1 day"));
	}//end for
?>
