<?php 
if($AI!="" && $AO!=""){
	//检查是否有早退或迟到 $AI_Default/$AO_Default;$AI_F,$AO_F
	$today_WorkTime=$today_WorkTime+intval(abs(strtotime($AIvalue)-strtotime($AOvalue))/3600/0.5)*0.5;
	}
else{//检查是否请假，有计请假，没有计旷工
	if($kqDefaultTime[0]!=""){
		$qjS=$toDay." ".$kqDefaultTime[0].":00";
		$qjE=$toDay." ".$kqDefaultTime[1].":00";
		$qj_Result2 = mysql_query("SELECT * FROM $DataPublic.kqqjsheet WHERE Number=$DefaultNumber and  StartDate<='$qjS' and EndDate>='$qjE'",$link_id);
		if($qj_Row1=mysql_fetch_array($qj_Result2)){
			$qj_Type1=$qj_Row1["Type"];
			$qj_Hours1=ceil(abs(strtotime($qjS)-strtotime($qjE))/3600);//向上取整
			switch($qj_Type1){
				case "S":$today_SJTime=$qj_Hours1;break;
				case "B";$today_BJTime=$qj_Hours1;break;
				case "X":$today_BXTime=$qj_Hours1;break;
				case "W":$today_WXJTime=$qj_Hours1;break;
				}
			}
		else{//计算旷工时间
			//是否已离职
			if(($toDay>=$Dimission && $Dimission!="0000-00-00") || ($toDay<$ComeIn)){//已离职
				if($today_GTime!=0){
					if($today_WorkTime!=0){
						$Sum_WorkTime=$Sum_WorkTime-$today_WorkTime;
						}
					$today_WXTime=$today_WXTime+4;
					$Sum_WXTime=$Sum_WXTime+4;
					}
				}
			else{//在职
				$today_KGTime=$today_KGTime+4;
				$today_BKTime=$today_BKTime+$today_KGTime*2;
				$Sum_BKTime=$Sum_BKTime+$today_KGTime*2;
				$Sum_KGTime=$Sum_KGTime+4;
				}
			}
		}
	}
				
if($BI!="" && $BO!=""){//早退跟迟到的处理
		$This_B_WorkTime=intval(abs(strtotime($BIvalue)-strtotime($BOvalue))/3600/0.5)*0.5;
		$today_WorkTime=$today_WorkTime+$This_B_WorkTime;
		}
else{
	if($BI=="" && $BO==""){//旷工处理
		$qjS=$toDay." ".$kqDefaultTime[2].":00";
		$qjE=$toDay." ".$kqDefaultTime[3].":00";
		$qj_Result2 = mysql_query("SELECT * FROM $DataPublic.kqqjsheet WHERE Number=$DefaultNumber and  StartDate<='$qjS' and EndDate>='$qjE'",$link_id);
		if($qj_Row1=mysql_fetch_array($qj_Result2)){
			$qj_Type1=$qj_Row1["Type"];
			$qj_Hours1=ceil(abs(strtotime($qjS)-strtotime($qjE))/3600);//向上取整
			switch($qj_Type1){
				case "S":$today_SJTime=$today_SJTime+$qj_Hours1;break;
				case "B";$today_BJTime=$today_BJTime+$qj_Hours1;break;
				case "X":$today_BXTime=$today_BXTime+$qj_Hours1;break;
				case "W":$today_WXJTime=$today_WXJTime+$qj_Hours1;break;
				}
			}
		else{//计算旷工时间,前提是在职
			//是否已离职
			if(($toDay>=$Dimission && $Dimission!="0000-00-00") || ($toDay<$ComeIn)){//已离职
				if($today_GTime!=0){
					if($today_WorkTime!=0){
						$Sum_WorkTime=$Sum_WorkTime-$today_WorkTime;
						}
					$today_WXTime=$today_WXTime+4;
					$Sum_WXTime=$Sum_WXTime+4;
					}
				}
			else{//在职
				$today_KGTime=$today_KGTime+4;
				$today_BKTime=$today_BKTime+$today_KGTime*2;
				$Sum_BKTime=$Sum_BKTime+$today_KGTime*2;
				$Sum_KGTime=$Sum_KGTime+4;
				}
			}
		}
	}
if($AO=="" && $BI==""){//中午直落的情况
	if($AI!="" && $BO!=""){//直落有效
		if($BOZLSign==0){
			$today_WorkTime=$today_WorkTime+intval(abs(strtotime($BOvalue)-strtotime($AIvalue))/3600/0.5)*0.5;
			}
		else{//直落无效
			$today_WorkTime=$today_WorkTime+intval(abs(strtotime($BOvalue)-strtotime($kqDefaultTime[2])+strtotime($kqDefaultTime[1])-strtotime($AIvalue))/3600/0.5)*0.5;
			}
		}
	}
if($CI!="" && $CO!=""){
	$today_GJTime=$today_GJTime+intval(abs(strtotime($CIvalue)-strtotime($COvalue))/3600/0.5)*0.5;
	}
				
//判断直落多出来的加班时间
if($today_WorkTime>=8){
	$today_GJTime=$today_GJTime+$today_WorkTime-8;
	$today_WorkTime=8;
	//重新检查旷工情况
	$Sum_BKTime=$today_BKTime==0?$Sum_BKTime:$Sum_BKTime-$today_BKTime;
	$today_KGTime=0;
	$today_BKTime=0;
	}
//合计
$Sum_WorkTime=$Sum_WorkTime+$today_WorkTime;//当天实到工时
$today_GJTime=$today_GJTime+$ZLTime-$MidwayRest;//当天加点工时
$Sum_GJTime=$Sum_GJTime+$today_GJTime;//当月总加点工时

?>