<?php 
//自动写入月考勤统计
include "../model/modelhead.php";
include "kqcode/kq_function.php";
$toWebPage="kq_checkio_save";
//第一步：计算要查询的月份及当月考勤天数
$nowMonth="2013-06";			//现在的月份，默认
$CheckMonth=$nowMonth;

$FristDay=$CheckMonth."-01";
$EndDay=date("Y-m-t",strtotime($FristDay));
if($CheckMonth==$nowMonth){
	$Days=date("d")-1;
	}
else{
	$Days=date("t",strtotime($FristDay));
	}
$KqSignStr=" AND M.KqSign='1'";
echo "SELECT A.Number,A.Name,A.JobName FROM (
SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId
FROM $DataPublic.staffmain M
LEFT JOIN $DataIn.checkinout C ON  M.Number=C.Number
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE C.CheckTime LIKE '$CheckMonth%' AND M.GroupId!='517' AND G.Estate=1 AND M.cSign='$Login_cSign'
GROUP BY C.Number 
UNION ALL 
SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId  
FROM $DataPublic.staffmain M
LEFT JOIN $DataPublic.kqqjsheet Q ON  M.Number=Q.Number
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE (Q.StartDate  LIKE '$CheckMonth%'  OR Q.EndDate  LIKE '$CheckMonth%'  OR  (Q.StartDate<'$CheckMonth-01'  AND Q.EndDate>'$CheckMonth-01'))  AND M.GroupId!='517' AND G.Estate=1 AND M.cSign='$Login_cSign' AND M.KqSign=1 $KqSignStr
GROUP BY Q.Number 
) A GROUP BY A.Number 
ORDER BY A.BranchId,A.JobId,A.Number";
/*
$CheckStaff= mysql_query("SELECT A.Number,A.Name,A.JobName FROM (
SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId
FROM $DataPublic.staffmain M
LEFT JOIN $DataIn.checkinout C ON  M.Number=C.Number
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE C.CheckTime LIKE '$CheckMonth%' AND M.GroupId!='517' AND G.Estate=1 AND M.cSign='$Login_cSign' $KqSignStr
GROUP BY C.Number 
UNION ALL 
SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId  
FROM $DataPublic.staffmain M
LEFT JOIN $DataPublic.kqqjsheet Q ON  M.Number=Q.Number
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE (Q.StartDate  LIKE '$CheckMonth%'  OR Q.EndDate  LIKE '$CheckMonth%'  OR  (Q.StartDate<'$CheckMonth-01'  AND Q.EndDate>'$CheckMonth-01'))  AND M.GroupId!='517' AND G.Estate=1 AND M.cSign='$Login_cSign' AND M.KqSign=1 $KqSignStr
GROUP BY Q.Number 
) A GROUP BY A.Number 
ORDER BY A.BranchId,A.JobId,A.Number",$link_id);
if($StaffRow = mysql_fetch_array($CheckStaff)){
	$ll=1;
	do{
		$Number=$StaffRow["Number"];
		$Name=$StaffRow["Name"];

$checkSql1 = mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE 1 and Number=$Number and Estate=0 ORDER BY Id LIMIT 1",$link_id);
if($checkRow1 = mysql_fetch_array($checkSql1)){
	$Days=date("t",strtotime($FristDay));
	}
//统计
$sumGTime=0;$sumWorkTime=0;$sumGJTime=0;$sumXJTime=0;$sumFJTime=0;$sumInLates=0;
$sumOutEarlys=0;$sumQjTime1=0;$sumQjTime2=0;$sumQjTime3=0;$sumQjTime4=0;$sumQjTime5=0;
$sumQjTime6=0;$sumQjTime7=0;$sumQjTime8=0;$sumQQTime=0;$sumKGTime=0;$sumYBs=0;$sumWXTime=0;$sumdkHour=0;
$sumGJTime=$Sum_OverTimeG=$sumZLG=$sumXJTime=$Sum_OverTimeX=$sumZLX=$sumFJTime=$Sum_OverTimeF=$sumZLF=0;
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
		//对调情况落实到个人：因为有时是部分员工对调
		//echo "SELECT Id FROM $DataIn.kqrqdd WHERE Number='$Number' and (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1";
		$rqddResult = mysql_query("SELECT Id,XDate FROM $DataIn.kqrqdd WHERE Number='$Number' AND (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1",$link_id);
		if($rqddRow = mysql_fetch_array($rqddResult)){
			$weekDayTempX=date("w",strtotime($rqddRow["XDate"]));		//调动的休息日
			$weekDayTempG=date("w",strtotime($rqddRow["GDate"]));	//调动的工作日
			
			if($DateType=="G"){
				$ddInfo=$weekDayTempX==0?"(调日)":"(调六)";
				}
			else{
				$ddInfo="(调".$Darray[$weekDayTempG].")";
				}
			$DateType=$DateType=="X"?"G":"X";
			}
		$DateTypeColor=$DateType=="G"?"":"class='greenB'";
		//读取班次
		include "kqcode/checkio_model_pb.php";
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
		if($TodaydkHour>0){
			$KGTime=$KGTime-$TodaydkHour;
			$sumKGTime=$sumKGTime-$TodaydkHour;		//旷工工时
			}
		$TodaydkHour=zerotospace($TodaydkHour);
	}//end for
		//有薪假总工时
		$sumYXJall=$sumQjTime4+$sumQjTime5+$sumQjTime6+$sumQjTime7+$sumQjTime8+$sumQjTime9;
		//写入月统计表
		$inRecode1="INSERT INTO $DataIn.kqdataother 
		SELECT NULL,Number,'$sumGTime','$sumWorkTime',
		'$sumGJTime','$Sum_OverTimeG','$sumZLG',
		'$sumXJTime','$Sum_OverTimeX','$sumZLX',
		'$sumFJTime','$Sum_OverTimeF','$sumZLF',
		'$sumInLates','$sumOutEarlys','$sumQjTime1',
		'$sumQjTime2','$sumYXJall','$sumQjTime3','$sumQQTime','$sumYBs','$sumWXTime','$sumKGTime','$sumdkHour','$CheckMonth','1','10002','1' 
		FROM $DataPublic.staffmain WHERE Number='$Number' AND Number NOT IN (SELECT Number FROM $DataIn.kqdataother WHERE Month='$CheckMonth' and Number='$Number')";
		//echo $Name." -- ".$inRecode1."<br>";
		$inAction1=@mysql_query($inRecode1);
		echo $ll."$Name $inRecode1<br>";
		$ll++;
		}while ($StaffRow = mysql_fetch_array($CheckStaff));
	}*/
?>