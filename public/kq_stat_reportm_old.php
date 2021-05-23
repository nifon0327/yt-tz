<?php 
//内部模式:OK ewen2013-07-30
include "../model/modelhead.php";
include "kqcode/kq_function.php";
//步骤2：
$nowWebPage ="kq_stat_reportm";
$fromWebPage="kq_stat_reportm";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage";
//步骤3：
$tableMenuS=600;$tableWidth=1060;
ChangeWtitle("$SubCompany 月考勤统计");//需处理
$toWebPage="checkinout_save";
//第一步：计算要查询的月份及当月考勤天数
$nowMonth=date("Y-m");			//现在的月份，默认
if($CheckMonth==""){			//如果没有填月份，则为默认的现在月份
	$CheckMonth=substr($CheckDate,0,7);
	}
$FristDay=$CheckMonth."-01";
$EndDay=date("Y-m-t",strtotime($FristDay));
if($CheckMonth==$nowMonth){
	$Days=date("d")-1;
	}
else{
	$Days=date("t",strtotime($FristDay));
	}
//如果是之前月份检查统计是否存在:如果是当月，则只有离职员工可以保存
$checkSql = mysql_query("SELECT Id FROM $DataIn.kqdata WHERE 1 and Number=$Number and Month='$CheckMonth' ORDER BY Id LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkSql)){
	$SaveSTR="NO";
	}
$checkSql1 = mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE 1 and Number=$Number and Estate=0 ORDER BY Id LIMIT 1",$link_id);
if($checkRow1 = mysql_fetch_array($checkSql1)){
	$Days=date("t",strtotime($FristDay));
	}
//统计
$sumGTime=0;$sumWorkTime=0;$sumGJTime=0;$sumXJTime=0;$sumFJTime=0;$sumInLates=0;
$sumOutEarlys=0;$sumQjTime1=0;$sumQjTime2=0;$sumQjTime3=0;$sumQjTime4=0;$sumQjTime5=0;
$sumQjTime6=0;$sumQjTime7=0;$sumQjTime8=0;$sumQQTime=0;$sumKGTime=0;$sumYBs=0;$sumWXTime=0;$sumdkHour=0;
$sumGJTime=$Sum_OverTimeG=$sumZLG=$sumXJTime=$Sum_OverTimeX=$sumZLX=$sumFJTime=$Sum_OverTimeF=$sumZLF=0;
echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'>";
echo"<tr class='' align='center'>
		<td width='30' rowspan='2' class='A1111'>日期</td>
		<td width='50' rowspan='2' class='A1101'>星期</td>
		<td width='45' rowspan='2' class='A1101'>类别</td>
		<td height='20' colspan='2' class='A1101'>签卡记录</td>
		<td width='45' rowspan='2' class='A1101'>应到<br>工时</td>
		<td width='45' rowspan='2' class='A1101'>实到<br>工时</td>
		<td colspan='3' class='A1101'>1.5倍薪工时</td>
		<td colspan='3' class='A1101'>2倍薪工时</td>
		<td colspan='3' class='A1101'>3倍薪工时</td>
		<td width='30' rowspan='2' class='A1101'>迟到</td>
		<td width='30' rowspan='2' class='A1101'>早退</td>
		<td colspan='9' class='A1101'>请、休假工时</td>
		<td width='30' rowspan='2' class='A1101'>缺勤<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>旷工<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>夜班</td>
		<td width='30' rowspan='2' class='A1101'>无效<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>有薪<br>工时</td>
 	</tr>
  	<tr class='' align='center'>
		<td width='45' height='20' class='A0101'>签到</td>
		<td width='45' class='A0101'>签退</td>
		<td width='25' class='A0101'>标</td>
		<td width='25' class='A0101' >超</td>
		<td width='25' class='A0101' >直</td>
		<td width='25' class='A0101'>标</td>
		<td width='25' class='A0101' >超</td>
		<td width='25' class='A0101' >直</td>
		<td width='25' class='A0101'>标</td>
		<td width='25' class='A0101' >超</td>
		<td width='25' class='A0101' >直</td>
		<td width='25' class='A0101'>事</td>
		<td width='25' class='A0101'>病</td>		
		<td width='25' class='A0101'>无</td>
		<td width='25' class='A0101'>年</td>
		<td width='25' class='A0101'>补</td>
		<td width='25' class='A0101'>婚</td>
		<td width='25' class='A0101'>丧</td>
		<td width='25' class='A0101'>产</td>
		<td width='25' class='A0101'>工</td>
	</tr>";
for($i=0;$i<$Days;$i++){//日循环
	$j=$i+1;
	$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
	//步骤4：星期处理
	$ToDay=$CheckDate;
	//2		计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
	$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
	$weekDay=date("w",strtotime($CheckDate));
	$weekInfo="星期".$Darray[$weekDay];
	$DateTypeTemp=($weekDay==6 || $weekDay==0)?"X":"G";
	$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$CheckDate'",$link_id);
	if($holidayRow = mysql_fetch_array($holidayResult)){
		$jbTimes=$holidayRow["jbTimes"];
		switch($holidayRow["Type"]){
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
		$QjTime1=0;		$QjTime2=0;		$QjTime3=0;		$QjTime4=0;		$QjTime5=0;		$QjTime6=0;		$QjTime7=0;		$QjTime8=0;$QjTime9=0;
		$QQTime=0;		$KGTime=0;		$YBs=0;			$WXTime=0;   
		$OverTimeG=$OverTimeX=$OverTimeF=0;
		$DateType=$DateTypeTemp;
		//读取班次
		include "kqcode/checkio_model_pb.php";
		
		//对调情况落实到个人：因为有时是部分员工对调
		//echo "SELECT Id FROM $DataIn.kqrqdd WHERE Number='$Number' and (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1";
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
							}
							$DateType = "X";
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
						if($temppartTime == 8 && $Number == "10132" || ($CheckDate == "2014-08-16" && $Number == "10348") || ($CheckDate == "2014-08-17" && $Number == "10348")){
							$DateType = "G";
						}
					}
					
				}
			}
						 
		}
		$DateTypeColor=$DateType=="G"?"":"class='greenB'";
		
		//读取签卡记录:签卡记录必须是已经审核的
		$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign 
		FROM $DataIn.checkinout 
		WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1'))
		 order by CheckTime",$link_id);
		if($ioRow = mysql_fetch_array($ioResult)) {
			do{
				$CheckTime=$ioRow["CheckTime"];
				$CheckType=$ioRow["CheckType"];
				$KrSign=$ioRow["KrSign"];
				switch($CheckType){
					case "I":
						$AI=date("Y-m-d H:i:00",strtotime("$CheckTime"));
						$aiTime=date("H:i",strtotime("$CheckTime"));						
						break;
					case "O":
				
						$AO=date("Y-m-d H:i:00",strtotime("$CheckTime"));			
						$aoTime=date("H:i",strtotime("$CheckTime"));						
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
				
		//当天的数据计算开始

		if($DateType=="G"){
			//工作日的数据计算
			if($pbType==0){
				include "kqcode/checkio_model_countG.php";}
			else{
				include "kqcode/checkio_model_countGL.php";}
				
			}
		else{
			//非工作日的数据计算			
			if($pbType==0){
				include "kqcode/checkio_model_countX.php";
				}
			else{
				include "kqcode/checkio_model_countXL.php";}
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
						 WHERE A.Number='$Number' and (A.ComeIn>'$CheckDate' OR A.Number IN(SELECT B.Number FROM $DataPublic.dimissiondata WHERE B.Number='$Number' and B.OutDate<'$CheckDate'))",$link_id);
	
	if($checkDRow = mysql_fetch_array($checkD)){
	
		$outDate = $checkDRow["outDate"];
		$comeIn = $checkDRow["ComeIn"];
		//if(($outDate < $comeIn && $CheckDate < $comeIn) || ($outDate > $comeIn && $CheckDate > $outDate))
		{
			$ddInfo="(离)";
			$AIcolor="";$AOcolor="";$jbTime=0;$AI="";$AO="";$aiTime="";$aoTime="";	$aiTime=0;$aoTime=0;$WorkTime=0;$GJTime=0;$XJTime=0;$FJTime=0;$InLates=0;$OutEarlys=0;
			$QjTime1=0;$QjTime2=0;$QjTime3=0;$QjTime4=0;$QjTime5=0;$QjTime6=0;$QjTime7=0;$QjTime8=0;$QjTime9=0;$QQTime=0;$KGTime=0;$YBs=0;
			$WXTime=$DateType=="X"?0:8;
			$GTime=$DateType=="X"?0:8;
		}
	}
	 if($j==4 && substr($CheckDate,0,7)=='2012-10'){			
			   $WXTime=0;
				$GTime=0;
	}

	  $TodaydkHour=0;  //是否当天有抵扣，不能算旷工
	  $rqddResult = mysql_query("SELECT Id,dkHour FROM $DataPublic.staff_dkdate WHERE Number='$Number' AND dkDate='$CheckDate'  LIMIT 1",$link_id);
	  if($rqddRow = mysql_fetch_array($rqddResult)){
		    $TodaydkHour=$rqddRow["dkHour"];
			$sumdkHour=$sumdkHour+$TodaydkHour;
			$DateType="D";
			$ddInfo="($TodaydkHour)";
			$DateTypeColor="class='greenB'";
		}	  
      
     
	//当天的数据计算结束
	$sumGTime=$sumGTime+$GTime;
	$sumWorkTime=$sumWorkTime+$WorkTime;
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
	$sumQjTime9=$sumQjTime9+$QjTime9;
	$sumQQTime=$sumQQTime+$QQTime;
	$sumKGTime=$sumKGTime+$KGTime;
	$sumYBs=$sumYBs+$YBs;
	$sumWXTime=$sumWXTime+$WXTime;
		$aiTime=SpaceValue($aiTime);
		$aoTime=SpaceValue($aoTime);
		$GTime=zerotospace($GTime);
		$WorkTime=zerotospace($WorkTime);
		
		$InLates=zerotospace($InLates);
		$OutEarlys=zerotospace($OutEarlys);
		$QjTime1=zerotospace($QjTime1);
		$QjTime2=zerotospace($QjTime2);
		$QjTime3=zerotospace($QjTime3);
		$QjTime4=zerotospace($QjTime4);
		$QjTime5=zerotospace($QjTime5);
		$QjTime6=zerotospace($QjTime6);
		$QjTime7=zerotospace($QjTime7);
		$QjTime8=zerotospace($QjTime8);
		$QjTime9=zerotospace($QjTime9);
		$QQTime=zerotospace($QQTime);		
		$KGTime=zerotospace($KGTime);
		$BKTime=zerotospace($BKTime);
		$YBs=zerotospace($YBs);
		$WXTime=zerotospace($WXTime);		
		//直落工时计算
		include "kqcode/checkio_model_zl.php";
		if($j==4 && substr($CheckDate,0,7)=='2012-10'  && $ZL_Hours!=0){
		$XJTime=$XJTime+$ZL_Hours;
                  $GJTime=0;
		          $GJTime=zerotospace($GJTime);
                 $sumXJTime=$sumXJTime+$ZL_Hours;
         }
		//加班工时分析
		switch($DateType){
			case "G":
        		$OverTimeG=$GJTime>2?$GJTime-2:0;	//超时计算
				$Sum_OverTimeG+=$OverTimeG;			//超时累计
		 		$GJTime=$GJTime>2?2:$GJTime;
				$sumGJTime+=$GJTime;
			break;
			case "X":
			case "Y":
				if($weekDay==0 || $ddInfo=="(调日)"){//如果是周日(或调为周日)，则全部算超时
					$OverTimeX=$XJTime;	//超时计算
					$Sum_OverTimeX+=$OverTimeX;			//超时累计
					$XJTime=0;
					}
				else{
					$OverTimeX=$XJTime>8?$XJTime-8:0;	//超时计算
					$Sum_OverTimeX+=$OverTimeX;			//超时累计
					$XJTime=$XJTime>8?8:$XJTime;
					}
				$sumXJTime+=$XJTime;
			break;
			case "F"://法定假日全部列为超时
					$OverTimeF=$FJTime;							//超时计算
					$Sum_OverTimeF+=$OverTimeF;			//超时累计
					$FJTime=0;
				$sumFJTime+=$FJTime;
				
			break;
		 	}
		echo"<tr align='center'><td class='A0111' $rowBgcolor>$j</td>";
		echo"<td class='A0101'>$weekInfo</td>";
		echo"<td class='A0101'><div $DateTypeColor>$DateType$ddInfo</div></td>";
		echo"<td class='A0101'><span $AIcolor>$aiTime</span></td>";
		echo"<td class='A0101'><span $AOcolor>$aoTime</span></td>";
		echo"<td class='A0101'>$GTime</td>";
		echo"<td class='A0101'>$WorkTime</td>";
		echo"<td class='A0101'>".zerotospace($GJTime)."</td>";
		echo"<td class='A0101' bgcolor='#CCCCCC'>".zerotospace($OverTimeG)."</td>";
		echo"<td class='A0101' bgcolor='#CCCCCC'>$ZLG</td>";
		echo"<td class='A0101'>".zerotospace($XJTime)."</td>";
		echo"<td class='A0101' bgcolor='#CCCCCC'>".zerotospace($OverTimeX)."</td>";
		echo"<td class='A0101' bgcolor='#CCCCCC'>$ZLX</td>";
		echo"<td class='A0101'>".zerotospace($FJTime)."</td>";
		echo"<td class='A0101' bgcolor='#CCCCCC'>".zerotospace($OverTimeF)."</td>";
		echo"<td class='A0101' bgcolor='#CCCCCC'>$ZLF</td>";
		echo"<td class='A0101'>$InLates</td>";
		echo"<td class='A0101'>$OutEarlys</td>";
		echo"<td class='A0101'>$QjTime1</td>";
		echo"<td class='A0101'>$QjTime2</td>";
		echo"<td class='A0101'>$QjTime3</td>";
		echo"<td class='A0101'>$QjTime4</td>";
		echo"<td class='A0101'>$QjTime5</td>";
		echo"<td class='A0101'>$QjTime6</td>";//  $test
		echo"<td class='A0101'>$QjTime7</td>";
		echo"<td class='A0101'>$QjTime8</td>";// $qjTest
		echo"<td class='A0101'>$QjTime9</td>";
		echo"<td class='A0101'>$QQTime</td>";
		
		if($TodaydkHour>0){
			$KGTime=$KGTime-$TodaydkHour;
			$sumKGTime=$sumKGTime-$TodaydkHour;
			$KGTime=zerotospace($KGTime);
			echo "<td class='A0101' align='center'>$KGTime</td>";	
		}
		else {
			echo"<td class='A0101' align='center'>$KGTime</td>";
			}
		$TodaydkHour=zerotospace($TodaydkHour);
		echo"<td class='A0101' align='center'>$YBs</td>";
		echo"<td class='A0101' align='center'>$WXTime $TT</td>";
		echo"<td class='A0101' align='center'>$TodaydkHour</td>";
		echo"</tr>";
		
	}//end for
/*
$tmpsumGJTime=$sumGJTime;
if($sumGJTime>$dkHour) {
	$sumGJTime=$sumGJTime-$dkHour;  // add by zx 2013-05-22 减去顶扣工时
}
else {
	$dkHour=$sumGJTime; //不够抵扣，则就是1.5倍加班全部时间
	$sumGJTime=0;
	
}
$strdkHour="";
if ($dkHour>0) {
	$strdkHour="-";
}
*/
$sumYXJall=$sumQjTime4+$sumQjTime5+$sumQjTime6+$sumQjTime7+$sumQjTime8+$sumQjTime9;

//<td class='A0101' align='center'>".zerotospace($sumGJTime)."<input name='Ghours' type='hidden' id='Ghours' value='$sumGJTime'></td> 
echo"<tr align='center'>
	<td class='A0111' colspan='5' >合计</td>
	<td class='A0101'>".zerotospace($sumGTime)."<input name='Dhours' type='hidden' id='Dhours' value='$sumGTime'></td>
	<td class='A0101'>".zerotospace($sumWorkTime)."<input name='Whours' type='hidden' id='Whours' value='$sumWorkTime'></td>
	<td class='A0101'>".zerotospace($sumGJTime)."<input name='Ghours' type='hidden' id='Ghours' value='$sumGJTime'></td>
	<td class='A0101'  bgcolor='#CCCCCC'>".zerotospace($Sum_OverTimeG)."<input name='GOverTime' type='hidden' id='GOverTime' value='$Sum_OverTimeG'></td>
	<td class='A0101' bgcolor='#CCCCCC'>".zerotospace($sumZLG)."<input name='GDropTime' type='hidden' id='GDropTime' value='$sumZLG'></td>
	<td class='A0101'>".zerotospace($sumXJTime)."<input name='Xhours' type='hidden' id='Xhours' value='$sumXJTime'></td>
	<td class='A0101' bgcolor='#CCCCCC'>".zerotospace($Sum_OverTimeX)."<input name='XOverTime' type='hidden' id='XOverTime' value='$Sum_OverTimeX'></td>
	<td class='A0101' bgcolor='#CCCCCC'>".zerotospace($sumZLX)."<input name='XDropTime' type='hidden' id='XDropTime' value='$sumZLX'></td>
	<td class='A0101'>".zerotospace($sumFJTime)."<input name='Fhours' type='hidden' id='Fhours' value='$sumFJTime'></td>
	<td class='A0101' bgcolor='#CCCCCC'>".zerotospace($Sum_OverTimeF)."<input name='FOverTime' type='hidden' id='FOverTime' value='$Sum_OverTimeF'></td>
	<td class='A0101' bgcolor='#CCCCCC'>".zerotospace($sumZLF)."<input name='FDropTime' type='hidden' id='FDropTime' value='$sumZLF'></td>
	<td class='A0101'>".zerotospace($sumInLates)."<input name='InLates' type='hidden' id='InLates' value='$sumInLates'></td>
	<td class='A0101'>".zerotospace($sumOutEarlys)."<input name='OutEarlys' type='hidden' id='OutEarlys' value='$sumOutEarlys'></td>
	<td class='A0101'>".zerotospace($sumQjTime1)."<input name='SJhours' type='hidden' id='SJhours' value='$sumQjTime1'></td>
	<td class='A0101'>".zerotospace($sumQjTime2)."<input name='BJhours' type='hidden' id='BJhours' value='$sumQjTime2'></td>
	<td class='A0101'>".zerotospace($sumQjTime3)."<input name='WXJhours' type='hidden' id='WXJhours' value='$sumQjTime3'></td>
	<td class='A0101'>".zerotospace($sumQjTime4)."<input name='YXJhours' type='hidden' id='YXJhours' value='$sumYXJall'></td>
	<td class='A0101'>".zerotospace($sumQjTime5)."</td>
	<td class='A0101'>".zerotospace($sumQjTime6)."</td>
	<td class='A0101'>".zerotospace($sumQjTime7)."</td>		
	<td class='A0101'>".zerotospace($sumQjTime8)."</td>
	<td class='A0101'>".zerotospace($sumQjTime9)."</td>
	<td class='A0101'>".zerotospace($sumQQTime)."<input name='QQhours' type='hidden' id='QQhours' value='$sumQQTime'></td>
	<td class='A0101'>".zerotospace($sumKGTime)."<input name='KGhours' type='hidden' id='KGhours' value='$sumKGTime'></td>
	<td class='A0101'>".zerotospace($sumYBs)."<input name='YBs' type='hidden' id='YBs' value='$sumYBs'></td>
	<td class='A0101'>".zerotospace($sumWXTime)."<input name='WXhours' type='hidden' id='WXhours' value='$sumWXTime'></td>
	<td class='A0101'>".zerotospace($sumdkHour)."<input name='dkhours' type='hidden' id='dkhours' value='$sumdkHour'></td>
	</tr>";
echo"<tr class='' align='center'>
	<td rowspan='2' class='A0111'>日期</td>
	<td rowspan='2' class='A0101'>星期</td>
	<td rowspan='2' class='A0101'>类别</td>
	<td height='20' class='A0101'>签到</td>
	<td class='A0101'>签退</td>
	<td rowspan='2' class='A0101'>应到<br>工时</td>
	<td rowspan='2' class='A0101'>实到<br>工时</td>
	<td class='A0101'>标</td>
	<td class='A0101' >超</td>
	<td class='A0101' >直</td>
	<td class='A0101' >标</td>
	<td class='A0101' >超</td>
	<td class='A0101' >直</td>
	<td class='A0101' >标</td>
	<td class='A0101' >超</td>
	<td class='A0101' >直</td>
	<td rowspan='2' class='A0101'>迟到</td>
	<td rowspan='2' class='A0101'>早退</td>
	
	<td class='A0101' >事</td>
	<td class='A0101' >病</td>		
	<td class='A0101' >无</td>
	<td class='A0101' >年</td>
	<td class='A0101' >补</td>
	<td class='A0101' >婚</td>
	<td class='A0101' >丧</td>
	<td class='A0101' >产</td>
	<td class='A0101' >工</td>
	<td rowspan='2' class='A0101'>缺勤<br>工时</td>
	<td rowspan='2' class='A0101'>旷工<br>工时</td>
	<td rowspan='2' class='A0101'>夜班</td>
	<td rowspan='2' class='A0101'>无效<br>工时</td>
	<td rowspan='2' class='A0101'>有薪<br>工时</td>
	</tr>
	<tr class=''  align='center'>
	<td height='20' colspan='2' class='A0101'>签卡记录</td>
	<td colspan='3' class='A0101'>1.5倍薪工时</td>
	<td colspan='3' class='A0101'>2倍薪工时</td>
	<td colspan='3' class='A0101'>3倍薪工时</td>
	<td colspan='9' class='A0101'>请、休假工时</td>
	</tr></table>";
?>