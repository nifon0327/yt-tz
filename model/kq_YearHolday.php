<?php 
//电信-ZX  2012-08-01
//传过来参数有$Number,$StartDate,$EndDate
if(($ipadTag != "yes") && ($iPhoneTag != "yes"))
{
	include "../basic/parameter.inc";
	$path = $_SERVER['DOCUMENT_ROOT'];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator_leave.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceStatistic.php");
}

function calculateDateToDateHours($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id){
	$start = substr($StartDate, 0, 10);
	$end = substr($EndDate, 0, 10);

	$staff = new AttendanceAvatar_leave($Number, $DataIn, $DataPublic, $link_id);
	$days = ((strtotime($end) - strtotime($start)) / (24 * 3600)) + 1;
	$qjHours = 0;
	//echo $days;
	for($i=0; $i<$days; $i++){
		$tempDate = date("Y-m-d",strtotime("$start +$i Day"));
		$tempHours = 0;
		
		$tmpCheckIn = $tempDate == $start?$StartDate:"";
		$tmpCheckOut = $tempDate == $end?$EndDate:"";
		$staff->setupAttendanceData($Number, $tempDate, $tmpCheckIn, $tmpCheckOut, $DataIn, $DataPublic, $link_id);

		$staff->attendanceSetup($DataIn, $DataPublic, $link_id);
		$result = $staff->getInfomationByTag();

		$qjHours += $result['workHours'];
	}
	return $qjHours;
}

function  GetYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id)   //取得$StartDate年假的天数
{

	
	$chooseYear=substr($StartDate,0,4); 
	$AnnualLeave1=0;
	$NextYear=$chooseYear+1;
	$LastYear = $chooseYear -1;
	$mySql="SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.KqSign,B.Name AS Branch,J.Name AS Job
		FROM $DataIn.staffmain M
		LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
		LEFT JOIN $DataIn.jobdata J  ON J.Id=M.JobId
		WHERE M.Number=$Number";   //取得年假时长
	//echo "$mySql";	
	
	$myResult = mysql_query($mySql."",$link_id);
	if($myRow = mysql_fetch_array($myResult)){
		$KqSign=$myRow["KqSign"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$ComeIn=$myRow["ComeIn"];		
		//入职当年
		$ComeInY=substr($ComeIn,0,4);
		
		$StartHolday=date("Y-m-d",strtotime("$ComeIn   +1   Year"));  //满一年才能请假
		$AnnualLeave=0;
		if($StartDate>=$StartHolday){
			//年份间隔:间隔在2以上的，全休，间隔1实计；间隔0无
			$ValueY=$chooseYear-$ComeInY;
			if (substr($ComeIn,5,5)=="01-01") $ValueY+=1;	
					
			$DefaultLastM=$chooseYear."-12-01";
			$ThisEndDay=$chooseYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
			$CountDays=date("z",strtotime($ThisEndDay))+1;	//年假当年总天数
			
			//计算本年请假的时间(除年休)，超过15天以上的要扣除
		$sumQjTime=0;
		$qjTimeSql=mysql_query("SELECT StartDate,EndDate,Type FROM $DataIn.kqqjsheet WHERE Number=$Number AND Type  IN (1) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')",$link_id);
		if($qjTimeRow=mysql_fetch_array($qjTimeSql)){//垮年处理
		    do{
		       $StartDate=$qjTimeRow["StartDate"];
		       $EndDate=$qjTimeRow["EndDate"];
		       $type = $qjTimeRow["Type"];
			   $frist_Year=substr($StartDate,0,4);
			   $end_Year=substr($EndDate,0,4);
			   if($frist_Year<$LastYear)$StartDate=$LastYear."-01-01 08:00:00";
			   //if($end_Year>$chooseYear)$EndDate=$chooseYear."-12-31 17:00:00";
			    if($end_Year>$LastYear)$EndDate=$LastYear."-12-31 17:00:00";
				
			   	$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
		        $Days=intval($HoursTemp/24);//取整求相隔天数
			    //分析请假时间段包括几个休息日/法定假日/公司有薪假日
			    $HolidayTemp=0;     //初始假日数
			    //分析是否有休息日
			     $isHolday=0;  //0 表示工作日
			     $DateTemp=$StartDate;
			     $DateTemp=date("Y-m-d",strtotime("$DateTemp-1 days"));
			      for($n=0;$n<=$Days;$n++){
				         $isHolday=0;  //0 表示工作日
				         $DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				         $weekDay=date("w",strtotime("$DateTemp"));	 
				         if($weekDay==6 || $weekDay==0){
					             $HolidayTemp=$HolidayTemp+1;
					             $isHolday=1;
					     }
				         else{
					           //读取假日设定表
					              $holiday_Result = mysql_query("SELECT * FROM $DataIn.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
					              if($holiday_Row = mysql_fetch_array($holiday_Result)){
						            $HolidayTemp=$HolidayTemp+1;
						            $isHolday=1;
						        }
					     }            
				         //分析是否有工作日对调
				         $newRqTime = 0;
				         if($isHolday==1){  //节假日上班，所以其休息时间要减
					        $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'
														  UNION 
												          SELECT XDate FROM $DataIn.kqrqdd_pt WHERE XDate='$DateTemp' AND  Number='$Number'
														 ",$link_id);
					         if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							  $HolidayTemp=$HolidayTemp-1;
					          }
					          	
			           	}			
					   	else
					   	{  //非节假日调班，则其休息时间要加,
					    $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'
					    UNION 
				        SELECT XDate FROM $DataIn.kqrqdd_pt WHERE GDate='$DateTemp' AND  Number='$Number'
					  ",$link_id);
					      if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp+1;
					     }
			        }              
		      	}
		      	
		      			      	
		      	 
			     //计算请假工时
			     $Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
			     $HourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时
			     $HourTotal=$HourTotal<0?0:$HourTotal;   //有时假，只请半天，但调假一天，所以要去掉

			     $sumQjTime+=$HourTotal/8;

			     //echo ($HourTotal).'<br>';

		       }while($qjTimeRow=mysql_fetch_array($qjTimeSql));
		    }

		    //echo $sumQjTime;
			if($sumQjTime<15)$sumQjTime=0;//少于15天忽略。
			
				if($ValueY>1){	//年份间隔在2以上的
					$inDays=$CountDays-$sumQjTime;
					$AnnualLeave=intval((5*8*$inDays)/$CountDays);
					if($ValueY>10){
						$AnnualLeave=intval((10*8*$inDays)/$CountDays);
						}
					if($ValueY>20){
						$AnnualLeave=intval((15*8*$inDays)/$CountDays);
						}					
					}
				else{
					if($ValueY==1){
						$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays-$sumQjTime;
						$AnnualLeave=intval((5*8*$inDays)/$CountDays);
						}
					else{
						$AnnualLeave=0;
						$inDays=0;
						}
					}					
		}
		
		//明年休假计算
		$ValueY=$NextYear-$ComeInY;
		if (substr($ComeIn,5,5)=="01-01") $ValueY+=1;	
		
		$DefaultLastM=$NextYear."-12-01";
		$NextEndDay=$NextYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
		$NextCountDays=date("z",strtotime($NextEndDay))+1;	//年假当年总天数
			if($ValueY>1){	//年份间隔在2以上的
				$NextAnnualLeave=5*8;
				if($ValueY>10){
					$NextAnnualLeave=10*8;
					}
				if($ValueY>20){
					$NextAnnualLeave=15*8;
					}					
				}
			else{
				if($ValueY==1){
					$NextinDays=abs(strtotime($NextEndDay)-strtotime($ComeIn))/3600/24-$NextCountDays;
					$NextAnnualLeave=intval((5*8*$NextinDays)/$NextCountDays);
					}
				else{
					$NextAnnualLeave=0;
					}
				}					
		
				
		$AnnualLeave1=intval($AnnualLeave/8);   //当年的假
		//$NextAnnualLeave=intval($NextAnnualLeave/8);  //下一年的假
	}
	return $AnnualLeave1;
}

function  GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id)  //两个日期之间的休假天数,注意，没有去掉节假日的，所以请假最好要有节假日的要避开
{
	
	    $HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
	    $Days=intval($HoursTemp/24);//取整求相隔天数
		//分析请假时间段包括几个休息日/法定假日/公司有薪假日
		//初始假日数
		$HolidayTemp=0;
		//分析是否有休息日
		$isHolday=0;  //0 表示工作日
		
		$DateTemp=$StartDate;
		$DateTemp=date("Y-m-d",strtotime("$DateTemp-1 days"));
		$gTempTime = 0;
		$xTempTime = 0;
		$tTempTime = 0;
		$gTempTime = 0;
		for($n=0;$n<=$Days;$n++){
			$isHolday=0;  //0 表示工作日
			$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
			$weekDay=date("w",strtotime("$DateTemp"));
			//分析是否有调班	 
			if($weekDay==6 || $weekDay==0){
				$HolidayTemp=$HolidayTemp+1;
				$isHolday=1;
				}
			else{
				//读取假日设定表
				$holiday_Result = mysql_query("SELECT * FROM $DataIn.kqholiday WHERE 1 and Date='$DateTemp'",$link_id);
				if($holiday_Row = mysql_fetch_array($holiday_Result)){
					//echo 'here';
					$HolidayTemp=$HolidayTemp+1;
					$isHolday=1;
					}
				}
			//分析是否有工作日对调
			if($isHolday==1){  //节假日上班，所以其休息时间要减
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'
				   UNION 
				   SELECT XDate FROM $DataIn.kqrqdd_pt WHERE XDate='$DateTemp' AND  Number='$Number'",$link_id);			
					
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp-1;
					}			
				}			
				else{  //非节假日调班，则其休息时间要加,
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'
					UNION 
				   SELECT XDate FROM $DataIn.kqrqdd_pt WHERE GDate='$DateTemp' AND  Number='$Number'",$link_id);
				
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp+1;
					}
			   }
			   
				$exChangeType = "";
				$nRqddSql = "Select A.GDate,A.GTime,A.XDate,A.XTime From $DataIn.kq_rqddnew A 
						 Left Join $DataIn.kq_ddsheet B On B.ddItem = A.Id
						 Where B.Number = '$Number' and (A.GDate='$DateTemp' OR A.XDate='$DateTemp')";
				//echo $nRqddSql.'<br>';
				$nRqddResult = mysql_query($nRqddSql);
				while($nRqddRow = mysql_fetch_assoc($nRqddResult)){
					$gDate = $nRqddRow["GDate"];
					$gTime = $nRqddRow["GTime"];
					$tDate = $nRqddRow["XDate"];
					$tTime = $nRqddRow["XTime"];
					
					if($gDate == $DateTemp)
					{
						$exChangeType = "m";
						
						$gDateArray = explode("-", $gTime);
						$startTime = $gDateArray[0];
						$endTime = $gDateArray[1];
						
						$temppartTime = (strtotime($endTime)-strtotime($startTime))/3600;
						$temppartTime = ($temppartTime > 8)?8:$temppartTime;
						
						//echo "$StartDate  ".$gDate." ".$endTime;
						if(strtotime($StartDate) > strtotime($gDate." ".$endTime) || strtotime($EndDate) < strtotime($gDate." ".$startTime))
						{
							//echo "rest";
							$temppartTime = 0;
						}
						
						$gTempTime += $temppartTime;
						//echo "gTempTime:$gTempTime";
					}
					else{
						$exChangeType = "a";
						$tDateArray = explode("-", $tTime);
						$startTime = $tDateArray[0];
						$endTime = $tDateArray[1];
						$temppartTime = (strtotime($endTime)-strtotime($startTime))/3600;
						$temppartTime = $temppartTime > 8?8:$temppartTime;

						if(strtotime($StartDate) > strtotime($tDate." ".$endTime) || strtotime($EndDate) < strtotime($tDate." ".$startTime))
						{
							$temppartTime = 0;
						}
						
						$tTempTime += $temppartTime;
						$HolidayTemp -= 0.5;
					}
			    }				
         }
		
		//计算请假工时
		//echo "holiday:$HolidayTemp <br>";
		$Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
		//echo "6. $Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数 "."<br>";
		//如果是临时班，则按实际计算
		if($bcType==0){
			$Hours=$Hours>4?($Hours<=9?$Hours-1:8):$Hours;//相隔小时数的实际工时
			}else{
			  $Hours=$Hours>5?$Hours-1:$Hours;	
			}
		$HourTotal=$Days*8-$HolidayTemp*8+$Hours + $tTempTime - $gTempTime;//总工时
		//echo "$HourTotal=$Days*8-$HolidayTemp*8+$Hours + $tTempTime - $gTempTime";
		//echo "gTempTime:$gTempTime <br>";
		$targetYear = substr($StartDate, 0,4);
		if(strtotime(substr($StartDate, 0, 10))<=strtotime($targetYear.'-03-08') && strtotime(substr($EndDate, 0, 10))>=strtotime($targetYear.'-03-08')){
			//echo 'here';
			$checkSex= mysql_query("SELECT B.sex,A.KqSign  
									FROM $DataIn.staffmain A
									INNER JOIN $DataIn.staffsheet B On A.Number = B.Number 
									WHERE A.Number='$Number'",$link_id);	
			if($checkRow = mysql_fetch_assoc($checkSex)){
				$sex = $checkRow["sex"];
				$KqSign = $checkRow["KqSign"];
				$woweekDay=date("w",strtotime($targetYear.'-03-08'));
				if ($sex==0  && ($woweekDay!=6 && $woweekDay!=0)  && $KqSign == 1) {
					$HourTotal-=4;
				}
			}	
		}
		$HourTotal=$HourTotal<0?0:$HourTotal;  //有时假，只请半天，但调假一天，所以要去掉
		return $HourTotal;		
}

function  HaveYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id)   //取得已经休假的年假天数天数
{

	$chooseYear=substr($StartDate,0,4); 
	//$qjResult1 = mysql_query("SELECT StartDate,EndDate,Type FROM $DataPublic.kqqjsheet WHERE Number=$Number and ('$CheckDate' between left(StartDate,10) and left(EndDate,10))",$link_id);
	//未处理跨年的，如2010-12-31 请假到2011-01-03 前面的属于2010的年假，后面的属于2011年年假

		
	$qjAllHours=0;$n=0;$dataArray=array('date');
	$qjResult1 = mysql_query("SELECT StartDate,EndDate,Type,bcType FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type=4 and substring(StartDate,1,4)='$chooseYear'",$link_id);  //取得已批准的年假
	if($qjRow1=mysql_fetch_array($qjResult1)){//有请假
			
		do {	
			//做成一个函数，更好	
			$StartDate=$qjRow1["StartDate"];
			$EndDate=$qjRow1["EndDate"];
			$bcType=$qjRow1["bcType"];
			array_push($dataArray,$StartDate);
			
			$ThisHoldDays=GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);  //本次请假换算小时数
			$qjAllHours=$qjAllHours+$ThisHoldDays;
			$n++;
		}while ($qjRow1 = mysql_fetch_array($qjResult1));
	 }

	 
	 $qjAllHours=$qjAllHours+$qjAllHours5;
	 $qjAllHours=round($qjAllHours);
	
	 $Mod=$qjAllHours%8;
	 if($Mod>0 && $Mod!=4){
		$Mod=$Mod<4?4-$Mod:8-$Mod;
	 }
	 else{
		$Mod=0;
	 }
	
	
	return $qjAllHours+$Mod;
}

function  GetYearHolDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id)
{
     $YearDays=GetYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id);
     $haveHours=HaveYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id);
     $haveDays=$haveHours/8;
     return $YearDays-$haveDays;
}

//$StartDate请年假是否需审核
function  IsAuditHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id)   
{
   $AuditSign=1;
	
   $myResult= mysql_query("SELECT M.Number,M.Name,M.BranchId,M.cSign  FROM $DataPublic.staffmain M  WHERE M.Number='$Number' LIMIT 1",$link_id);
   if($myRow = mysql_fetch_array($myResult)){
         $BranchId=$myRow["BranchId"];

         
         $checkBranchSql=mysql_fetch_array(mysql_query("SELECT COUNT(*)  AS Nums FROM $DataPublic.staffmain M  WHERE M.BranchId='$BranchId'  GROUP BY M.BranchId",$link_id));
         $BranchNums=$checkBranchSql["Nums"];
         $Days=round((strtotime($EndDate)-strtotime($StartDate))/3600/24);
         $sDate=date("Y-m-d",strtotime($StartDate));
         for ($i=0;$i<$Days;$i++){
             $CheckDate=date("Y-m-d",strtotime("$sDate  +$i day"));
	         $checkQjSql=mysql_fetch_array(mysql_query("SELECT COUNT(*)  AS Nums FROM $DataPublic.kqqjsheet S 
	         LEFT JOIN $DataPublic.staffmain M  ON S.Number=M.Number 
	         WHERE M.BranchId='$BranchId'  AND S.StartDate<='$CheckDate' AND S.EndDate>='$CheckDate' ",$link_id));
	         $qjNums=$checkQjSql["Nums"]==""?0:$checkQjSql["Nums"];
	         
	         $qjScale=($qjNums/$BranchNums)*100;
	         if ($qjScale>=5) {$AuditSign=0;break;}//echo "$CheckDate >请假人数占部分人数比例: ($qjNums/$BranchNums)*100  = ". $qjScale . "%";
         }
   }
     return $AuditSign;
}

function getTakeDeferredHolidays($Number,$DataIn,$DataPublic,$link_id)
{
    $hours=0;
    $myResult= mysql_query("SELECT Hours  FROM $DataPublic.bxTimeCount   WHERE Number='$Number' LIMIT 1",$link_id);
   if($myRow = mysql_fetch_array($myResult)){
         $hours=$myRow["Hours"];
         
         $usedBx=0;
         $bxQjCheckSql = "SELECT StartDate,EndDate,bcType FROM $DataPublic.kqqjsheet
                                   WHERE Number = '$Number' and Type= '5' AND DATE_FORMAT(StartDate,'%Y')>='2013'";
		 $bxQjCheckResult = mysql_query($bxQjCheckSql);
			while($bxQjCheckRow = mysql_fetch_assoc($bxQjCheckResult))
			{
				$startTime = $bxQjCheckRow["StartDate"];
				$endTime = $bxQjCheckRow["EndDate"];
				$bcType = $bxQjCheckRow["bcType"];
				
				$times = GetBetweenDateDays($Number,$startTime,$endTime,$bcType,$DataIn,$DataPublic,$link_id);
				$usedBx += $times;
			}
          $hours-= $usedBx;
   }
   return $hours;
}

function calculateHours($StartDate,$EndDate)
{
	$workHours = 0;
	$targetDate = substr($StartDate, 0, 10);
	if(strtotime($StartDate) > strtotime($targetDate." 18:00")){

		$workHours = (strtotime($EndDate) - strtotime($StartDate))/3600;
	}
	else{
		$workHours += workHours("08:00", "12:00", rounding_inin($StartDate), rounding_outout($EndDate), $targetDate);
		$workHours += workHours("13:00", "17:00", rounding_inin($StartDate), rounding_outout($EndDate), $targetDate);
		$workHours += getOverTimeHours(rounding_outout($EndDate), $targetDate);
	}

	return $workHours;
}

function workHours($standardStart, $standardEnd, $startTime, $endTime, $targetDate){
	$standardStartDate = $targetDate." ".$standardStart;
	$standardEndDate = $targetDate." ".$standardEnd;
	$startHolder = "";
	$endHolder = "";
	//确定开始时间
	if(strtotime($standardEndDate) < strtotime($startTime)){
		$startHolder = $standardEndDate;
	}
	else if(strtotime($standardStartDate) < strtotime($startTime) && strtotime($standardEndDate) > strtotime($startTime)){
		$startHolder = $startTime;
	}
	else{
		$startHolder = $standardStartDate;
	}
	
	//确定结束时间
	if(strtotime($standardStartDate) >= strtotime($endTime)){
		$endHolder = $standardStartDate;
	}
	else if(strtotime($standardStartDate) < strtotime($endTime) && strtotime($standardEndDate) > strtotime($endTime)){
		$endHolder = $endTime;
	}
	else{
		$endHolder = $standardEndDate;
	}

	$workHours = (strtotime($endHolder) - strtotime($startHolder))/3600;
	return $workHours<0?0:$workHours;
}

function getOverTimeHours($workTime, $calculateDate){
	$otTime = (strtotime($workTime) - strtotime($calculateDate." 18:00"))/3600;
	$otTime = $otTime<0?0:$otTime;
	return $otTime;
}


function getLastYearLeave($Number, $DataIn,$link_id){
	$thisYear = date('Y');
	$lastYear = date("Y",strtotime("$thisYear -1 year"));
	
	$getComeInYearSql = "SELECT DATEDIFF(DATE_FORMAT(Now(), '%Y-%m-%d'), ComeIn) as Diff 
						FROM staffmain 
						WHERE Number = $Number";
	$getComeInResult = mysql_fetch_assoc(mysql_query($getComeInYearSql));
	$yearLeaveDay = 5;
	$maxSick = 0;
	$monthDay = 21.75;
	$comeIn = number_format($getComeInResult['Diff'] / 365 - 0.05, 1);
	if($comeIn < 1){
		return 0;
	}else if($comeIn >= 1 && $comeIn < 10){
		$maxSick = 2*$monthDay;
	}else if($comeIn >= 10 && $comeIn < 20){
		$maxSick = 3*$monthDay;
	}else if($comeIn >= 20){
		$maxSick = 4*$monthDay;
	}

	$getSickLeaveSql = "SELECT StartDate, EndDate, bcType FROM kqqjsheet WHERE Number = $Number AND (LEFT(StartDate, 4) = $lastYear OR LEFT(EndDate, 4) = $lastYear) AND Type = 2";
	$sickLeaveDay = 0;
	$getSickResult = mysql_query($getSickLeaveSql);
	while($sickRow = mysql_fetch_assoc($getSickResult)){
		$StartDate = $sickRow['StartDate'];
		$EndDate = $sickRow['EndDate'];
		$bcType = $sickRow['bcType'];
		$tmpHours = GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataIn,$link_id);
		$sickLeaveDay += $tmpHours;
	}
	$sickLeaveDay = $sickLeaveDay / 8;
	if($maxSick <= $sickLeaveDay){
		return 0;
	}else{
		return 1;
	}
}



function rounding_inin($AITemp)
{//向上取整处理
	$m_Temp=substr($AITemp,14,2);//取分钟
	if($m_Temp!=0 && $m_Temp!=30)
	{
		if($m_Temp<30){
			$m_Temp=30-$m_Temp;
			}
		else{
			$m_Temp=60-$m_Temp;
		}
	}
	else{
		$m_Temp=0;
	}
	$ChickIn=date("Y-m-d H:i:00",strtotime("$AITemp")+$m_Temp*60);
	return $ChickIn;
}

function rounding_outout($AOTemp)
{//向下取整处理
	$m_Temp=substr($AOTemp,14,2);//取分钟
	if($m_Temp!=0 && $m_Temp!=30){
		if($m_Temp<30){
			$m_Temp=0;
		}
	else{
		$m_Temp=30;
		}
	}
	$m_Temp=$m_Temp==0?":00":":30";
	$ChickOut=substr($AOTemp,0,13).$m_Temp.":00";
	return $ChickOut;
}


?>