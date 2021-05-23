<?php 
switch($DateType){
	case "X"://休息日
		$weekDay=$weekTemp."，属于休息日（无薪），上班计2倍薪酬。";
		$Sing_W="√";
		$bgcolor="#CCCCCC";
		//XJTime加班时间计算
		break;
	case "F"://法定假日
		$today_GTime=8;
		$weekDay=$weekTemp."，属于法定假日(有薪)，上班计3倍薪酬。";
		$Sing_Y="√";
		$bgcolor="#FFCCCC";
		break;
	case "G"://工作日
		$weekDay=$weekTemp."，属于正常工作日。";
		$bgcolor="#FFFFFF";
		$today_GTime=8;
		break;
	case "Y"://有薪假日
		$weekDay=$weekTemp."，属于公司有薪假日,上班按正常工作日计算加班费。";
		$Sing_Y="√";
		break;
	case "W"://无薪假日
		$today_GTime=8;
		$weekDay=$weekTemp."，属于公司无薪假日，上班按正常工作日计算加班费。";
		$Sing_W="√";
		$bgcolor="#CCFFCC";
		break;
	case "L"://临时排班
		$today_GTime=8;
		$weekDay=$weekTemp."，属于临时排班，不足8小时的工时计无薪工时。";
		$bgcolor="#FFFFCC";
		break;
	}
?>