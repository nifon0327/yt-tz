<?php 

	include "../../basic/parameter.inc";
	include "../../model/modelfunction.php";
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/public/kqClass/Kq_pbSet.php");
	include_once("$path/public/kqClass/Kq_dailyItem.php");
	include_once("$path/public/kqClass/Kq_totleItem.php");
	include_once("$path/public/kqClass/Kq_otHourSet.php");
	
	//include "../model/modelhead.php";
	include "../../public/kqcode/kq_function.php";
	include("getStaffNumber.php");
	
	//Factory inspection Model
	//$factoryCheck = "yes";
	$Number = $_POST["idNum"];
	if(strlen($Number) != 5)
	{
		$Number = getStaffNumber($Number, $DataPublic);
	}
	//$Number = '10373';
	
	/*
if($Number == "10043")
	{
		$factoryInspection = "no";
	}
*/
	
	$CheckMonth = $_POST["targetDate"];
	//$CheckMonth = '2014-04';
	$ipadTag = "yes";
	$nowMonth=date("Y-m");			//现在的月份，默认
	if($CheckMonth=="")                   //如果没有填月份，则为默认的现在月份
	{			
		//$CheckMonth=substr($CheckDate,0,7);
		$CheckMonth = $nowMonth;
	}
	$FristDay=$CheckMonth."-01";
	$EndDay=date("Y-m-t",strtotime($FristDay));
	if($CheckMonth==$nowMonth)
	{
		$Days=date("d")-1;
	}
	else
	{
		$Days=date("t",strtotime($FristDay));
	}
	
	
	$kqSignSql = mysql_query("Select kqSign From $DataPublic.staffmain Where Number = '$Number'");
	$kqSignResult = mysql_fetch_assoc($kqSignSql);
	$kqSign = $kqSignResult["kqSign"];

	$sumGTime=0;
	$sumWorkTime=0;
	$sumGJTime=0;
	$sumXJTime=0;
	$sumFJTime=0;
	$sumInLates=0;
	$sumOutEarlys=0;
	$sumQjTime1=0;
	$sumQjTime2=0;
	$sumQjTime3=0;
	$sumQjTime4=0;
	$sumQjTime5=0;
	$sumQjTime6=0;
	$sumQjTime7=0;
	$sumQjTime8=0;
	$sumQQTime=0;
	$sumKGTime=0;
	$sumYBs=0;
	$sumWXTime=0;
	$sumDk = 0;
	
	$kqArray = array();
	$totleDayItem = new KqTotleItem();
	$pbSet = new KqPbSet();
	for($i=0;$i<$Days;$i++)
	{
		$j=$i+1;
		$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
		
		if(strtotime($CheckDate) >= strtotime("2014-03-01") && $factoryCheck == "on" && $CheckDate !="2014-06" && $CheckDate !="2014-07")
		{
			$lsbResult = mysql_query("SELECT InTime,OutTime,InLate,OutEarly,TimeType,RestTime FROM $DataIn.kqlspbb WHERE Number=$Number and left(InTime,10)='$CheckDate' order by Id",$link_id);
		if($lsbRow = mysql_fetch_assoc($lsbResult))
		{
			$inTime = $CheckDate." "."07:50:00";
			$ioResultSql = "SELECT CheckTime,CheckType,KrSign 
			FROM $DataIn.checkinout 
			WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1')) and CheckType = 'O'
			order by CheckTime";
			$ioResult = mysql_query($ioResultSql);
			$ioResultRow = mysql_fetch_assoc($ioResult);
			$outTime = $ioResultRow["CheckTime"];
		}
		else
		{
			$ioResultSql = "SELECT CheckTime,CheckType,KrSign 
			FROM $DataIn.checkinout 
			WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1'))
			order by CheckTime";
			$ioResult = mysql_query($ioResultSql,$link_id);
			$inTime = "";
			$outTime = "";
			while($ioResultRow = mysql_fetch_assoc($ioResult))
			{
				$checkTime=$ioResultRow["CheckTime"];
				$checkType=$ioResultRow["CheckType"];
				$krSign=$ioResultRow["KrSign"];
			
				switch($checkType)
				{
					case "I":
					{
						$inTime = $checkTime; 
					}
					break;
					case "O":
					{
						$outTime = $checkTime;
					}
					break;
				}	
			}
		}
		
		
		$dayItem = new KqDailyItem($inTime, $outTime, $CheckDate);
		$dayItem->setupDateType($CheckDate, $Number,$DataIn, $DataPublic, $link_id);
		//离职或入职处理
		$outDate = "";
		$comeIn = "";
		$checkD=mysql_query("SELECT A.Id,A.ComeIn,B.outDate FROM $DataPublic.staffmain A LEFT JOIN $DataPublic.dimissiondata B On B.Number = A.Number WHERE A.Number='$Number' and (A.ComeIn>'$CheckDate' OR A.Number IN(SELECT B.Number FROM $DataPublic.dimissiondata WHERE B.Number='$Number' and B.OutDate<'$CheckDate'))",$link_id);
		if($checkDRow = mysql_fetch_array($checkD))
		{
			$outDate = $checkDRow["outDate"];
			$comeIn = $checkDRow["ComeIn"];
		}
		
		if(($outDate!="" && $CheckDate > $outDate) || $comeIn != "" && $CheckDate < $comeIn)
		{
			$dayItem->dayInfomation = "(离)";
		}
		else
		{	
			//读取当天的设定加班工时
			$otHours = new KqOtHourSet($Number,$CheckDate, $DataIn, $DataPublic, $link_id);
		
			//开始计算加班时间
			if(($otHours->getOtHours($dayItem->dateType) != 0 && ($dayItem->dateType == "X" || $dayItem->dateType == "Y")) || $dayItem->dateType == "G")
			{
				//echo $dayItem->checkDate."|".$otHours->getOtHours($dayItem->dateType)."<br>";
				$dayItem->calculateHours($Number, $otHours->getOtHours($dayItem->dateType), $otHours->zlHours, $pbSet, $DataIn, $DataPublic, $link_id);
			}
			else
			{
				if($dayItem->dateType == "X" || $dayItem->dateType == "Y")
				{
					$dayItem->checkInTime = "";
					$dayItem->checkOutTime = "";
				}
			}
		}
		
		$weekInfo = $dayItem->dayOfWeek;
		$DateType = $dayItem->dateType;
		$ddInfo= $dayItem->dayInfomation;
		$aiTime = $dayItem->returnCheckInTime();
		$aoTime = $dayItem->returnCheckOutTime();
		$GTime = $dayItem->workTime;
		$WorkTime = $dayItem->realWorkTime;
		$GJTimeIpad = $dayItem->jbTime;
		$XJTimeIpad = $dayItem->sxTime;
		$FJTimeIpad = $dayItem->jrTime;
		$InLates = $dayItem->beLate;
		$OutEarlys = $dayItem->leaveEarly;
		$QjTime1 = $dayItem->privateLeave;
		$QjTime2 = $dayItem->sickLeave;
		$QjTime3 = $dayItem->noWageLeave;
		$QjTime4 = $dayItem->annualLeave;
		$QjTime5 = $dayItem->notBusyLeave;
		$QjTime6 = $dayItem->marriageLeave;
		$QjTime7 = $dayItem->funeralLeave;
		$QjTime8 = $dayItem->maternityLeave;
		$QQTime = $dayItem->absenteeismHours;
		$KGTime = $dayItem->queQingHours;
		$YBs = $dayItem->nightShit;
		$WXTime = $dayItem->nopayHours;
		$TT = $dayItem->payHours;
		
		$kqArray[] = array("$j","$weekInfo","$DateType$ddInfo","$aiTime","$aoTime","$GTime","$WorkTime","$GJTimeIpad","$XJTimeIpad","$FJTimeIpad","$InLates","$OutEarlys","$QjTime1","$QjTime2","$QjTime3","$QjTime4","$QjTime5","$QjTime6","$QjTime7","$QjTime8","$QQTime","$KGTime","$YBs","$WXTime $TT");
			
			//$totleDayItem->summary($dayItem);
			
			
		$sumGTime=$sumGTime+$GTime;
		$sumWorkTime=$sumWorkTime+$WorkTime;
		$sumGJTime=$sumGJTime+$GJTimeIpad;
		$sumXJTime=$sumXJTime+$XJTimeIpad;
		$sumFJTime=$sumFJTime+$FJTimeIpad;
		$sumInLates=$sumInLates+$InLates;
		$sumOutEarlys=$sumOutEarlys+$OutEarlys;
		$sumQjTime1=$sumQjTime1+$QjTime1;
		$sumQjTime2=$sumQjTime2+$QjTime2;
		$sumQjTime3=$sumQjTime3+$QjTime3;
		$sumQjTime4=$sumQjTime4+$QjTime4;
		$sumQjTime5=$sumQjTime5+$QjTime5;
		$sumQjTime6=$sumQjTime6+$QjTime6;
		$sumQjTime7=$sumQjTime7+$QjTime7;
		$sumQjTime8=$sumQjTime8+$QjTime8;
		$sumQQTime=$sumQQTime+$QQTime;
		$sumKGTime=$sumKGTime+$KGTime;
		$YBs = 0;
		$sumYBs=$sumYBs+$YBs;
		$sumWXTime=$sumWXTime+$WXTime;
			
		}
		else
		{
		//步骤4：星期处理
		$ToDay=$CheckDate;
		//2		计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
		$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
		$weekDay=date("w",strtotime($CheckDate));	 
		$weekInfo="星期".$Darray[$weekDay];
		$DateTypeTemp=($weekDay==6 || $weekDay==0)?"X":"G";
		$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$CheckDate'",$link_id);
		if($holidayRow = mysql_fetch_array($holidayResult))
		{
			$jbTimes=$holidayRow["jbTimes"];
			switch($holidayRow["Type"])
			{
				case 0:		$DateTypeTemp="W";		break;
				case 1:		$DateTypeTemp="Y";		break;
				case 2:		$DateTypeTemp="F";		break;
			}
		}
		
		////////////////////////////////////////
		$test="";		$AIcolor="";	$AOcolor="";	$jbTime=0;
		$AI="";			$AO="";			$aiTime="";		$aoTime="";		$ddInfo="";
		$aiTime=0;		$aoTime=0;		$GTime=0;		$WorkTime=0;		$GJTime=0;
		$XJTime=0;		$FJTime=0;		$InLates=0;		$OutEarlys=0;		
		$QjTime1=0;		$QjTime2=0;		$QjTime3=0;		$QjTime4=0;		$QjTime5=0;		$QjTime6=0;		$QjTime7=0;		$QjTime8=0;
		$QQTime=0;		$KGTime=0;		$YBs=0;			$WXTime=0;
		$DateType=$DateTypeTemp;
		
		$GJTimeIpad = 0; $XJTimeIpad = 0; $FJTimeIpad = 0;
		$OverTimeG=$OverTimeX=$OverTimeF=0;
		$DateType=$DateTypeTemp;
		
		if($Number == "10744" && $factoryCheck == "on")
		{
			$pbType=0;
			$dDateTimeIn=$ToDay." 08:00:00";
			$dTimeIn=date("H:i",strtotime($dDateTimeIn));
			$dDateTimeOut=$ToDay." 17:00:00";
			$dTimeOut=date("H:i",strtotime($dDateTimeOut));
			$dRestTime1=$ToDay." 12:00:00";
			$dRestTime2=$ToDay." 13:00:00";
			$dRestTime3=$ToDay." 18:00:00";
			$dRestTime=60;
			$dInLate=0;
			$dOutEarly=0;
			$dKrSign=0;
		}
		else
		{
			include "../../public/kqcode/checkio_model_pb.php";
			//读取签卡记录:签卡记录必须是已经审核的
		}
		
		//对调情况落实到个人：因为有时是部分员工对调
		$tempWorkTime = 0;
		$exChangeType = "";
		$rqddResult = mysql_query("SELECT Id,XDate,GDate FROM $DataIn.kqrqdd WHERE Number='$Number' AND (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1",$link_id);
		if($rqddRow = mysql_fetch_array($rqddResult)){
			$weekDayTempX=date("w",strtotime($rqddRow["XDate"]));		//调动的休息日
			$weekDayTempG=date("w",strtotime($rqddRow["GDate"]));	//调动的工作日
			
			if($DateType=="G" ){
				$ddInfo=$weekDayTempX==0?"(调日)":"(调六)";
				}
			else{
				$ddInfo="(调".$Darray[$weekDayTempG].")";
				}
			
			
			$tempDDateType = ($weekDayTempX==6 || $weekDayTempX==0)?"X":"G";
			$tempTdateType = ($weekDayTempG==6 || $weekDayTempG==0)?"X":"G";
			
			if($tempDDateType != $tempTdateType)
			{
				$DateType=$DateType=="X"?"G":"X";
			}
			else
			{
				if($CheckDate == "2014-04-07")
				{
					$DateType = "G";
				}
				
				if($CheckDate == "2014-04-01")
				{
					$DateType = "Y";
					$ddInfo = "(调一)";
				}
				
			}
		}
		else
		{
			$nRqddSql = "Select A.GDate,A.GTime,A.XDate,A.XTime From $DataIn.kq_rqddnew A 
						 Left Join $DataIn.kq_ddsheet B On B.ddItem = A.Id
						 Where B.Number = '$Number' and (A.GDate='$CheckDate' OR A.XDate='$CheckDate')";
			$nRqddResult = mysql_query($nRqddSql);
			if(mysql_num_rows($nRqddResult) > 0)
			{
				while($nRqddRow = mysql_fetch_assoc($nRqddResult))
				{
					$gDate = $nRqddRow["GDate"];
					$gTime = $nRqddRow["GTime"];
					$tDate = $nRqddRow["XDate"];
					$tTime = $nRqddRow["XTime"];
					
					
					if($gDate == $CheckDate)
					{
						$exChangeType = "m";
						$gDateArray = explode("-", $gTime);
						$startTime = $gDateArray[0];
						$endTime = $gDateArray[1];
						
						$temppartTime = (strtotime($endTime)-strtotime($startTime))/3600;
						$temppartTime = $temppartTime > 8?8:$temppartTime;
						
						if($temppartTime == 8)
						{
							if($tDate != "")
							{
								$weekDayTempX=date("w",strtotime($tDate));		//调动的休息日
								$weekDayTempG=date("w",strtotime($gDate));	//调动的工作日
								
								if($DateType=="G" )
								{
									$ddInfo=$weekDayTempX==0?"(调日)":"(调六)";
								}
								else{
									$ddInfo="(调".$Darray[$weekDayTempG].")";
								}
			
			
								$tempDDateType = ($weekDayTempX==6 || $weekDayTempX==0)?"X":"G";
								$tempTdateType = ($weekDayTempG==6 || $weekDayTempG==0)?"X":"G";
							}
							else
							{
								$ddInfo="(待定)";
								$DateType = "X";
							}
							
							$temppartTime = 0;
						}
						else if($startTime > "12:00" && $temppartTime != 8)
						{
							$dDateTimeOut = $ToDay." "."12:00";
							$dRestTime2=$ToDay." 12:00:00";
							$dRestTime = (strtotime($dDateTimeOut)-strtotime($dRestTime2))/60;
						
						}
						else if($endTime < "13:00" && $temppartTime != 8)
						{
							$dDateTimeIn = $ToDay." "."13:00";
							$dRestTime = (strtotime($dRestTime2)-strtotime($dDateTimeIn))/60;
						}
						
						$tempWorkTime += $temppartTime;
					}
					else
					{
						$exChangeType = "b";
						$tDateArray = explode("-", $tTime);
						$startTime = $tDateArray[0];
						$endTime = $tDateArray[1];
						$temppartTime = (strtotime($endTime)-strtotime($startTime))/3600;
						$temppartTime = $temppartTime > 8?8:$temppartTime;
						$tempWorkTime += $temppartTime;
						if($temppartTime == 8 && $Number == "10132"){
							$DateType = "G";
						}
					}
					
				}
			}
						 
		}
		
		if($kqSign<3)
		{
		$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign 
		FROM $DataIn.checkinout 
		WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1'))
		 order by CheckTime",$link_id);

		 }
		 else
		 {
			 $ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign 
		FROM $DataIn.kq_office 
		WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1')) and CheckTime > '2014-03'
	order by CheckTime",$link_id);
		 }
		/*
 echo "SELECT CheckTime,CheckType,KrSign 
		FROM $DataIn.checkinout 
		WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1'))
		 order by CheckTime";
*/
		 
		if($ioRow = mysql_fetch_array($ioResult)) 
		{
			do{
				$CheckTime=$ioRow["CheckTime"];
				$CheckType=$ioRow["CheckType"];
				$KrSign=$ioRow["KrSign"];
				
				if($Number == "10744" && $CheckDate > "2014-03" && $factoryCheck == "on")
				{
					$mintArray = array("2", "2", "4", "8", "4", "7", "3", "8", "5", "6","2", "3", "4", "8", "6", "6", "3", "8", "5", "6","2", "2", "4", "8", "4", "2", "3", "8", "2", "6","4");
					$currentTimeMin = substr($CheckDate, 9, 1);
					$CheckTime = ($CheckType == "I")?$CheckDate." 07:5".$mintArray[$i]:$CheckDate." 17:0".$mintArray[count($mintArray)-1-$i];
				}
				
				$KrSign=$ioRow["KrSign"];
				if($KrSign == "1" && $CheckDate > "2014-03" && $factoryCheck == "on")
				{
					$CheckTime = ($CheckType == "I")?$CheckDate." 07:56":$CheckDate." 17:02";
				}
				
				if($CheckDate > "2014-03" && $factoryCheck == "on")
				{
					$overTimehourSql = mysql_query("Select * From $DataIn.kqovertime Where otDate = '$CheckDate'");
					$overTimehourResult = mysql_fetch_assoc($overTimehourSql);
					$overTimeHours = 0;
					switch($DateType)
					{
						case "G":
						{
							$overTimeHours = $overTimehourResult["workday"];
						}
						break;
						case "X":
						case "Y":
						{
							$overTimeHours = $overTimehourResult["weekday"];
						}
						case "F":
						{
							$overTimeHours = $overTimehourResult["weekday"];
						}
						break;
					}
			
					if($overTimeHours == 0 && ($DateType == "X" || $DateType == "F") && $factoryCheck == "on")
					{
						break;
					}
				}
				
				
				$checkDate = substr($CheckTime, 0, 10);
				//echo $checkDate."<br>";
				switch($CheckType)
					{
						case "I":
							$AI=date("Y-m-d H:i:00",strtotime("$CheckTime"));
							$aiTime=date("H:i",strtotime("$CheckTime"));						
							break;
						case "O":
						
							if($factoryCheck == "off")
							{
								$AO=date("Y-m-d H:i:00",strtotime("$CheckTime"));			
								$aoTime=date("H:i",strtotime("$CheckTime"));
								//echo "here";
							}
							else if($factoryCheck == "on")
							{
							
								/*
if($CheckDate > "2014-03" && $KqSign < 3)
								{
									
									if(strtotime($CheckTime) > strtotime($dDateTimeOut))
									{
										//查询是否有直落
										$zlTimeSql = mysql_query("Select Hours From $DataPublic.kqzltime Where Number = '$Number' and Date = '$CheckDate'");
										$zlHoursResult = mysql_fetch_assoc($zlTimeSql);
										$zlHours = ($zlHoursResult["Hours"] == "")?"0":$zlHoursResult["Hours"];
			
										$realTime = date("H:i", strtotime($CheckTime));
										$minCount = substr($realTime, strlen($realTime)-1, 1);
										//更新时间
										if($DateType == "G")
										{
											$addHours = ($overTimeHours - $zlHours < 0)?0:$overTimeHours - $zlHours;
											$otMins = $addHours * 60 + $minCount;
											$checkHolder = $CheckDate." ".$checkOutTime;
											$CheckTime = ($overTimeHours == 0)?date("Y-m-d H:i:s",strtotime("$dDateTimeOut+$otMins minute")):date("Y-m-d H:i:s",strtotime("$dRestTime3+$otMins minute"));
											$ioTime=date("H:i",strtotime($CheckTime));
										}
										else
										{
											$addHours = $overTimeHours;
											$otMins = $addHours * 60 + $dRestTime + $minCount;
											$CheckTime = date("Y-m-d H:i:s",strtotime("$dDateTimeIn+$otMins minute"));
											$ioTime=date("H:i",strtotime($CheckTime));
										}
									}

								}		
								else
								{
*/
								if($pbType==1)
								{//临时班
									$EndTimeGap=strtotime($CheckTime)-strtotime($AI);//时间差秒数
									$EndTimeTemp1=intval($EndTimeGap/3600/0.5)-20;//30分钟的次数
									if($EndTimeTemp1>0)
									{//超出时间:
										$EndMinute=$EndTimeTemp1*30;//超出部分的总分钟数
										$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));
										$ioTime=date("H:i",strtotime($CheckTime));
									}
								//*************
								}
								else
								{//正班：工作日、周六计算，周日不计算
									
									$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 20:00:00");
									$EndTimeTemp1=intval($EndTimeGap/3600/0.5);//30分钟的次数
									if($EndTimeTemp1>0)
									{//超出时间:
										$EndMinute=$EndTimeTemp1*30;//超出部分的总分钟数
										$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));
										$ioTime=date("H:i",strtotime($CheckTime));
									}
								}
								//}
								$AO=date("Y-m-d H:i:00",strtotime("$CheckTime"));
								$aoTime=date("H:i",strtotime("$CheckTime"));
							}						
							break;
					}
									
				}while ($ioRow = mysql_fetch_array($ioResult));
			//读取当天的班次时间
			}
			
		//当天的数据计算开始
		$GTime=$DateType=="G"?8:0;
		if($tempWorkTime > 0 && $DateType == "G")
		{
			if($exChangeType == "m")
			{
				$GTime -= $tempWorkTime;
			}
			else
			{
				$GTime = $tempWorkTime;
				$tempWorkTime = 8 - $tempWorkTime;
			}
		}
		else if($tempWorkTime > 0 && $DateType == "X")
		{
			if($exChangeType == "m")
			{}
			else
			{
				$GTime = $tempWorkTime;
				$tempWorkTime = $tempWorkTime;
			}
		}

		if($DateType=="G")
		{
			//工作日的数据计算
			if($pbType==0)
			{
				include "../../public/kqcode/checkio_model_countG.php";
			}
			else
			{
				include "../../public/kqcode/checkio_model_countGL.php";
			}	
		}
		else
		{
			//非工作日的数据计算			
			if($pbType==0)
			{
				include "../../public/kqcode/checkio_model_countX.php";
			}
			else
			{
				include "../../public/kqcode/checkio_model_countXL.php";
			}
		}

	if($tempWorkTime > 0 && $DateType == "X")
		{
			$WorkTime = ($XJTime - $GTime<0)?$XJTime:$GTime;
			$XJTime = ($XJTime-$GTime>0)?$XJTime-$GTime:0;

			if($WorkTime<8-$tempWorkTime || $isOfficeSign==1){//检查请假记录 modif by zx 2014-04-28, 如果是办公人员 ，可能有中间请部份假的，则要计扣除请假时间
	//检查有没有请假
	//$qjHours=0;
	$qjHours_sum=0;
	$qjSTemp=$dDateTimeIn;
	$qjETemp=$dDateTimeOut;
	$test="";
	//条件：考勤当天$CheckDate在请假起始日期~请假结束日期之间
	$qjResult1 = mysql_query("SELECT StartDate,EndDate,Type FROM $DataPublic.kqqjsheet WHERE Number=$Number and ('$CheckDate' between left(StartDate,10) and left(EndDate,10))",$link_id);
	
	 $WomensTime=4;
	if($qjRow1=mysql_fetch_array($qjResult1)){//有请假
	do{
		$qjHours=0;
		$StartDate=$qjRow1["StartDate"];
		$EndDate=$qjRow1["EndDate"];
		
		$qjType=$qjRow1["Type"];
		$test=$StartDate."-".$EndDate;
		//请假情况分析，总共12种情况
		if($StartDate<=$dDateTimeIn){				//请假的起始时间<=8:00
			$includePath = "kqcode/checkio_model_qjA.php";
			include $includePath;
			}
		else{
			if($StartDate<$dRestTime1){				//请假的起始时间<=12:00
				$includePath = "kqcode/checkio_model_qjB.php";
				include $includePath;
				}
			else{
				if($StartDate<$dRestTime2){			//请假的起始时间<13:00
					$includePath = "kqcode/checkio_model_qjC.php";
					include $includePath;
					}
				else{								//请假的起始时间在13:00之后
					$includePath = "kqcode/checkio_model_qjD.php";
					include $includePath;
					}
				}
			}
		$QjTimeTemp="QjTime".strval($qjType); 
        //$qjHours -= $tempWorkTime;
		$qjHours_sum=$qjHours_sum+$qjHours;
                 //三八妇女节放假
                 $WomensDay=1;
                 $includePath = "kqcode/checkio_Womens_Day.php";
                 include $includePath;
          		 $$QjTimeTemp+=$qjHours; //=$qjHours
	  }while($qjRow1=mysql_fetch_array($qjResult1));
          
	    $QQTime=8-$WorkTime-$qjHours_sum-$tempWorkTime;
	    if ($WomensTime>0){
            //三八妇女节放假
            $WomensDay=2;
            $includePath = "kqcode/checkio_Womens_Day.php";
            include $includePath;
        }              
	    $QQTime=$QQTime<"0"?"0":$QQTime;
		//$QQTime=8-$WorkTime-$qjHours;
		if($QQTime==0){//如果没有缺勤，则不计算迟到早退
			$InLates=0;
			$OutEarlys=0;
			}
		else{
			if($QQTime<1){//缺勤时间在0.5小时内
				$QQTime=0;
				}
			else{//如果缺勤时间大于0.5小时,则按工时扣款，但不计算次数
				$InLates=0;
				$OutEarlys=0;
				}
		}
		//$QjTimeTemp="QjTime".strval($qjType); 
		//$$QjTimeTemp=$qjHours;
		}
	else{//没有请假计算缺勤；
		$QQTime=8-$WorkTime-$tempWorkTime;
                //三八妇女节放假
                $WomensDay=2;
                $includePath = "kqcode/checkio_Womens_Day.php";
                include $includePath;
                
		if($QQTime==8)
		{
			$QQTime=0;
			$KGTime=8;
		}
		else
		{
            if ($QQTime==4 && $GTime==4)
            {
            	$QQTime=0;
				$KGTime=4;
            }
            else
            {
				if($QQTime<1){//缺勤时间在0.5小时内
					$QQTime=0;
				}
				else{//如果缺勤时间大于0.5小时,则按工时扣款，但不计算次数
					$InLates=0;
					$OutEarlys=0;
				}
			}
        }
		}
	}


		}

	////////////////////////////////////////
	//离职或未入职处理
	$checkD=mysql_query("SELECT A.Id,A.ComeIn,B.outDate FROM $DataPublic.staffmain A
						 	Left Join $DataPublic.dimissiondata B On B.Number = A.Number 
						 	WHERE A.Number='$Number' 
						 	and (A.ComeIn>'$CheckDate' OR A.Number IN(SELECT B.Number FROM $DataPublic.dimissiondata WHERE B.Number='$Number' and B.OutDate<'$CheckDate'))",$link_id);
							 	
	if($checkDRow = mysql_fetch_array($checkD))
	{
		$outDate = $checkDRow["outDate"];
		$comeIn = $checkDRow["ComeIn"];
	
		$ddInfo="(离)";
		$AIcolor="";$AOcolor="";$jbTime=0;$AI="";$AO="";$aiTime="";$aoTime="";$aiTime=0;$aoTime=0;$WorkTime=0;$GJTime=0;$XJTime=0;$FJTime=0;$InLates=0;$OutEarlys=0;
		$QjTime1=0;$QjTime2=0;$QjTime3=0;$QjTime4=0;$QjTime5=0;$QjTime6=0;$QjTime7=0;$QjTime8=0;$QQTime=0;$KGTime=0;$YBs=0;
		$WXTime=$DateType=="X"?0:8;
		$GTime=$DateType=="X"?0:8;
	}
	
	if($j==4 && date("Y-m")=='2012-10')
	{			
    	$WXTime=0;
		$GTime=0;
	}
	
	 $dkHour=0;  // 计算抵扣时间
	  $Check2Row=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(dkHour),0)  AS dkHour FROM $DataPublic.staff_dksheet WHERE Number='$Number' AND  Month='$CheckMonth'  ",$link_id));
	  $dkHour=$Check2Row["dkHour"];
	  if ($dkHour==0) {  //说明当月末生成，或无抵扣,则从主表找
		  $Check2Row=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(RemainHour),0)  AS dkHour FROM $DataPublic.staff_dkdate WHERE Number='$Number' AND  RemainHour>0  ",$link_id));
		  $dkHour=$Check2Row["dkHour"];
	  
	  }
	  
	  $TodaydkHour=0;  //是否当天有抵扣，不能算旷工
	  $rqddResult = mysql_query("SELECT Id,dkHour FROM $DataPublic.staff_dkdate WHERE Number='$Number' AND dkDate='$CheckDate'  LIMIT 1",$link_id);
	  if($rqddRow = mysql_fetch_array($rqddResult))
	  {
		    $TodaydkHour=$rqddRow["dkHour"];
			$DateType="D";
			$ddInfo="($TodaydkHour)";
			$DateTypeColor="class='greenB'";
			$sumDk += $TodaydkHour; 
		}	
	 	
	 if($CheckMonth>"2013-04" && $factoryCheck == "on"){//按新的计算方式显示:如果是周日，不计算；验厂模式，周日记录
		if($ddInfo=="(调日)" || $weekDay==0){
			$aiTime=$aoTime=" ";
			$XJTime=0;
			}
		}
	
	
	$grenderSql = mysql_query("Select A.Sex,B.kqSign From $DataPublic.staffsheet A 
							   Left Join $DataPublic.staffmain B On B.Number = A.Number
							   Where A.Number = '$Number'");
	$grenderResult = mysql_fetch_assoc($grenderSql);
	$grender = $grenderResult["Sex"];
	$KqSign = $grenderResult["kqSign"];
	
	if($CheckDate == "2014-03-08" && $grender == "0" && $KqSign < 3)
    {
	     if($XJTime >= 4)
		 {
			 $FJTime = $XJTime - 4;
			 $XJTime = 4;
		 }	        
   }

	
		
	//当天的数据计算结束
	$sumGTime=$sumGTime+$GTime;
	$sumWorkTime=$sumWorkTime+$WorkTime;
	if($factoryCheck == "off")
	{
		$sumGJTime=$sumGJTime+$GJTime-$TodaydkHour;
		$sumXJTime=$sumXJTime+$XJTime;
		$sumFJTime=$sumFJTime+$FJTime;
	}
	$sumInLates=$sumInLates+$InLates;
	$sumOutEarlys=$sumOutEarlys+$OutEarlys;
	$sumQjTime1=$sumQjTime1+$QjTime1;
	$sumQjTime2=$sumQjTime2+$QjTime2;
	$sumQjTime3=$sumQjTime3+$QjTime3;
	$sumQjTime4=$sumQjTime4+$QjTime4;
	$sumQjTime5=$sumQjTime5+$QjTime5;
	$sumQjTime6=$sumQjTime6+$QjTime6;
	$sumQjTime7=$sumQjTime7+$QjTime7;
	$sumQjTime8=$sumQjTime8+$QjTime8;
	$sumQQTime=$sumQQTime+$QQTime;
	$sumKGTime=$sumKGTime+$KGTime;
	$YBs = 0;
	$sumYBs=$sumYBs+$YBs;
	$sumWXTime=$sumWXTime+$WXTime;
	
	$aiTime = ($aiTime != "0")?$aiTime:"";
	$aoTime = ($aoTime != "0")?$aoTime:"";
	$GTime = ($GTime != "0")?$GTime:"";
	$WorkTime = ($WorkTime != "0")?$WorkTime:"";
	$GJTime = ($GJTime != "0")?$GJTime:"";
	$GJTimeIpad = $GJTime;
	$XJTime = ($XJTime != "0")?$XJTime:"";
	$XJTimeIpad = $XJTime;
	$FJTime = ($FJTime != "0")?$FJTime:"";
	$FJTimeIpad = $FJTime;
	$InLates = ($InLates != "0")?$InLates:"";
	$OutEarlys = ($OutEarlys != "0")?$OutEarlys:"";
	$QjTime1 = ($QjTime1 != "0")?$QjTime1:"";
	$QjTime2 = ($QjTime2 != "0")?$QjTime2:"";
	$QjTime3 = ($QjTime3 != "0")?$QjTime3:"";
	$QjTime4 = ($QjTime4 != "0")?$QjTime4:"";
	$QjTime5 = ($QjTime5 != "0")?$QjTime5:"";
	$QjTime5 = ($QjTime5 != "0")?$QjTime5:"";
	$QjTime6 = ($QjTime6 != "0")?$QjTime6:"";
	$QjTime7 = ($QjTime7 != "0")?$QjTime7:"";
	$QjTime8 = ($QjTime8 != "0")?$QjTime8:"";
	$QQTime = ($QQTime != "0")?$QQTime:"";
	$KGTime = ($KGTime - $TodaydkHour != "0")?$KGTime - $TodaydkHour:"";
	//$BKTime = ($BKTime != "0")?$BKTime:"";
	$YBs = ($YBs != "0")?$YBs:"";
	$WXTime = ($WXTime != "0")?$WXTime:"";	
		
	//计算直落
	include "../../public/kqcode/checkio_model_zl.php";
	
	if($j==4 && date("Y-m")=='2012-10' && $ZL_Hours!=0)
	{
    	$XJTimeIpad=$XJTimeIpad+$ZL_Hours;
        $GJTimeIpad=0;
    }
    
	if($CheckDate > '2014-03' && $factoryCheck == "on")
	{
		$sumGJTime=$sumGJTime+$GJTime-$TodaydkHour;
		$sumXJTime=$sumXJTime+$XJTime;
		$sumFJTime=$sumFJTime+$FJTime;
	}
	else{
	switch($DateType)
	{
		case "G":
		{
			if($factoryCheck == "off")
			{
				$sumGJTime += $ZL_Hours;
				$GJTimeIpad = $GJTime+$ZL_Hours;
			}
			else if($factoryCheck == "on")
			{
				$OverTimeG=$GJTime>2?$GJTime-2:0;	//超时计算
				$Sum_OverTimeG+=$OverTimeG;			//超时累计
		 		$GJTimeIpad=$GJTime>2?2:$GJTime;
				$sumGJTime+=$GJTime;
			}
        }
		break;
		case "X":
		case "Y":
		{
			if($factoryCheck == "off")
			{
				$sumXJTime += $ZL_Hours;
				$XJTimeIpad = $XJTime+$ZL_Hours;
				$XJTimeIpad = ($XJTimeIpad == 0)?"":$XJTimeIpad;
			}
			else if($factoryCheck == "on")
			{
				if($weekDay==0 || $ddInfo=="(调日)")
				{//如果是周日(或调为周日)，则全部算超时
					$OverTimeX=$XJTime;	//超时计算
					$Sum_OverTimeX+=$OverTimeX;			//超时累计
					$XJTime = 0;
				}
				else
				{
					$OverTimeX=$XJTime>10?$XJTime-10:0;	//超时计算
					$Sum_OverTimeX+=$OverTimeX;			//超时累计
					$XJTime=$XJTime>10?10:$XJTime;
				}
				$sumXJTime += $XJTime;
				//echo "$sumXJTimeIpad += $XJTimeIpad ()"
			}
		}
		break;
		case "F"://法定假日全部列为超时
		{
			if($factoryCheck == "off")
			{
				if($jbTimes==3)
				{
					$sumFJTime+=$ZL_Hours;
					$sumFJTimeIpad = $sumFJTime;
					$FJTimeAll=$FJTime+$ZL_Hours;
					$FJTimeIpad = $FJTimeAll;
					$FJTimeIpad = ($FJTimeIpad == 0)?"":$FJTimeIpad;
				}
				else
				{
					$sumXJTime+=$ZL_Hours;
					$sumXJTimeIpad = $sumXJTime;
					$XJTimeAll=$XJTime+$ZL_Hours;
					$XJTimeIpad = $XJTimeAll;
				}
			}
			else if($factoryCheck == "on")
			{
				$OverTimeF=$FJTime;							//超时计算
				$Sum_OverTimeF+=$OverTimeF;			//超时累计
				$FJTime=0;
				$sumFJTime+=$FJTime;
			}
		}
		break;
	}}
	
			
	$kqArray[] = array("$j","$weekInfo","$DateType$ddInfo","$aiTime","$aoTime","$GTime","$WorkTime","$GJTimeIpad","$XJTimeIpad","$FJTimeIpad","$InLates","$OutEarlys","$QjTime1","$QjTime2","$QjTime3","$QjTime4","$QjTime5","$QjTime6","$QjTime7","$QjTime8","$QQTime","$KGTime","$YBs","$WXTime $TT");
	}
	}
	
	$kqArray[] = array(""," "," ","抵扣工时"," "," ","","-$sumDk"," ","","","","","","","","","","","","","","","");
	
		
	$kqArray[] = array(""," "," ","合计"," ","$sumGTime","$sumWorkTime","$sumGJTime","$sumXJTime","$sumFJTime","$sumInLates","$sumOutEarlys","$sumQjTime1","$sumQjTime2","$sumQjTime3","$sumQjTime4","$sumQjTime5","$sumQjTime6","$sumQjTime7","$sumQjTime8","$sumQQTime","$sumKGTime","$sumYBs","$sumWXTime");
	
	//print_r($kqArray);
	echo json_encode($kqArray);
		
?>