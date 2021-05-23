<?php 
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$weekTemp=date("w",strtotime($toDay));	 
$DateType=($weekTemp==6 || $weekTemp==0)?"X":"G";//确定当天是属于工作日还是休息日
	
$holidayResult = mysql_query("SELECT Sign FROM $DataPublic.kqholiday WHERE Date='$toDay'",$link_id);//检查是法定假日还是有薪假日或无薪假日
if($holidayRow = mysql_fetch_array($holidayResult)){
	$DateType=$holidayRow["Sign"];}
$today_WeekDay="星期".$Darray[$weekTemp];
	
//是否存在对调的工作日
$dd_Result = mysql_query("SELECT * FROM $DataIn.kqrqdd WHERE kqrqdd.Number=$DefaultNumber and (GDate='$toDay' or XDate='$toDay')",$link_id);
$i=1;
if($dd_Row = mysql_fetch_array($dd_Result)) {
	$DateType=$dd_Row["GDate"]=="$toDay"?"X":$DateType="G";}
$Sing_W="&nbsp;";//当天无薪假日标记
$Sing_Y="&nbsp;";//当天有薪假日标记
$today_GTime=0;//当天应到工时，工作日有薪假日，法定假日为8h
$today_WorkTime=0;//当天实到工时，不超过8h
$today_GJTime=0;//当天加点时间(工作日)
$today_XJTime=0;//当天加班时间（休息日）
$today_FJTime=0;//当天加班时间（法定假日）
$today_InLates=0;//当天迟到次数
$today_OutEarlys=0;//当天早退次数
$today_BKTime=0;//当天被扣工时
$today_SJTime=0;//当天事假工时
$today_BJTime=0;//当天病假工时
$today_BXTime=0;//当天补休
$today_WXJTime=0;//当天无薪假
$today_LJTime=0;//年假
$today_WXTime=0;
$today_YBs=0;//夜班
$MidwayRest=0;//当天夜班中途休息
$today_QQTime=0;//当天因迟到早退缺勤工时
$today_KGTime=0;
$ZLTime=0;
$AIvalue="";
$AOvalue="";
?>