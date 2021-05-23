<?php 
/*$DataPublic.kqqjsheet 二合一已更新*******电信---yang 20120801
YYYY-MM-DD 8:00			$dDateTimeIn
YYYY-MM-DD 17:00		$dDateTimeOut
休息时间				$dRestTime;
跨日标志				$dKrSign=0;
许可迟到				$dInLate=0;
许可早退				$dOutEarly=0;
*/

$DateTypeColor="class='yellowB'";
$ddInfo="(临)";
if($AI=="" || $AO==""){//任何一个为空，皆为缺勤
	$WorkTime=0;
	$KGTime=8;
	$aiTime="";
	$aoTime="";
	}
else{
	if($AI>date("Y-m-d H:i:00",strtotime("$dDateTimeIn + $dInLate minute"))){//迟到
		$InLates=1;
		$AIcolor="class='yellowB'";
		}
	else{
		$AI=$dDateTimeIn;
		}
	if($AO<date("Y-m-d H:i:00",strtotime("$dDateTimeOut - $dOutEarly minute"))){//早退
		$OutEarlys=1;
		$AOcolor="class='yellowB'";
		}	
	//工作工时
	$WorkTime=abs(strtotime(rounding_out($AO))-strtotime(rounding_in($AI))-($dRestTime*60))/3600;
	if($KrSign==1){//跨日签退
		$AOcolor="class='greenB'";
		$YBs=1;
		}
	//加班工时
	if($WorkTime>=8){		//实际工时大于8小时，则不计迟到、早退，
		$InLates=0;$OutEarlys=0;
		$GJTime=$WorkTime-8;
		$WorkTime=8;
		}
	}
//是否需要检查请假
if($WorkTime>0 && $WorkTime<8){//检查请假记录
	//检查有没有请假
	$qjHours=0;
	$qjSTemp=$dDateTimeIn;
	$qjETemp=$dDateTimeOut;
	//条件：考勤当天$CheckDate在请假起始日期~请假结束日期之间
	$qjResult1 = mysql_query("SELECT StartDate,EndDate,Type FROM $DataPublic.kqqjsheet WHERE Number=$Number and ('$CheckDate' between left(StartDate,10) and left(EndDate,10))",$link_id);
	if($qjRow1=mysql_fetch_array($qjResult1)){//有请假
		$StartDate=$qjRow1["StartDate"];
		$EndDate=$qjRow1["EndDate"];
		$qjType=$qjRow1["Type"];
		//请假情况分析，总共9种情况
		if($StartDate==$qjSTemp){//A：请假起始时间与考勤当天的起始时间一致
			if($EndDate>=$qjETemp){			//情况1和2：请假时间比考勤起始时间多;
				$qjHours=8;
				}
			else{							//情况3：从早上开始，请部分假				
				$qjHours=abs(strtotime($EndDate)-strtotime($qjSTemp))/3600;
				}
			}
		else{
			if($StartDate<$qjSTemp){//B：请假起始时间少于考勤当天的起始时间
				if($EndDate>=$qjETemp){		//情况4和5：请假结束时间大于或等于考勤当天的签退时间
					$qjHours=8;
					}
				else{						//情况6：请假线路束时间少于当天的考勤签退时间，即上半日有请假
					$qjHours=abs(strtotime($EndDate)-strtotime($qjSTemp))/3600;
					}
				}
			else{					//C：请假起始时间大于考勤当天的结束时间
				if($EndDate>=$qjETemp){		//情况7和8：请假结束的时间大于或等于当天考勤签退的时间，即后半日有请假
					$qjHours=abs(strtotime($qjETemp)-strtotime($StartDate))/3600;
					}
				else{						//情况9：请假结束时间少于当天考勤签退时间，即中段有假
					$qjHours=abs(strtotime($EndDate)-strtotime($StartDate))/3600;
					}
				}
			}
		$QQTime=8-$WorkTime-$qjHours;
		if($QQTime==0){//如果没有缺勤，则不计算迟到早退
			$InLates=0;
			$OutEarlys=0;
			}
		$QjTimeTemp="QjTime".strval($qjType); 
		$$QjTimeTemp=$qjHours;
		}
	else{//没有请假计算缺勤；
		$QQTime=8-$WorkTime-$qjHours;
		}
	}
//以上计算出当天请假工时、实到工时、加点工时、迟到次数、早退次数、夜班数、缺勤工时
?>