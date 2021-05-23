<?php 
//mc 内部文件:OK ewen2013-08-03
//按原有方式显示记录
$ActioToS="";
if($CheckDate==""){
	$CheckDate=date("Y-m-d");
	}
$CheckMonth=substr($CheckDate,0,7);
$SelectCode="<input name='CheckDate' type='text' id='CheckDate' size='10' maxlength='10' value='$CheckDate' onchange='javascript:document.form1.submit();'>&nbsp;
<select name='CountType' id='CountType' onchange='document.form1.submit()'><option value='0' $CountType0>日考勤统计</option><option value='1' $CountType1>月考勤统计</option></select>";
$selStr="selFlag" . $KqSign;
$$selStr="selected";
$SelectCode=$SelectCode. "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''>
      <option  value=''   $selFlag>--全部--</option> 
      <option  value='1'  $selFlag1>考勤有效</option>
	   <option value='2' $selFlag2>考勤参考</option>
	  </select>";
$KqSignStr="";	
if ($KqSign!="") {
	$KqSignStr=" AND M.KqSign='$KqSign'";
}

$SaveFun="<span onClick='SaveDaytj()' $onClickCSS>保存</span>&nbsp;";
$CustomFun="<a href='kq_checkio_print.php?CheckDate=$CheckDate' target='_blank' $onClickCSS>列印</a>&nbsp;&nbsp;";
$SaveSTR="NO";
include "../model/subprogram/add_model_t.php";
//步骤4：星期处理
$ToDay=$CheckDate;//计算当天
$NowToDay=date("Y-m-d");//
//2		计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$weekDay=date("w",strtotime($CheckDate));	 
$weekInfo="星期".$Darray[$weekDay];
$DateTypeTemp=($weekDay==6 || $weekDay==0)?"X":"G";
$jbTimes=0;
$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$CheckDate'",$link_id);
if($holidayRow = mysql_fetch_array($holidayResult)){
	$jbTimes=$holidayRow["jbTimes"];
	switch($holidayRow["Type"]){
	case 0:		$DateTypeTemp="W";		break;
	case 1:		$DateTypeTemp="Y";		break;
	case 2:		$DateTypeTemp="F";		break;
		}
	}
echo"<input name='kqList' type='hidden' id='kqList'>";
echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF' id='kqTable'>
<tr><td colspan='27' class='A0011'>当天是：$weekInfo (直落工时的薪酬计算与加班工时一致)<span style='color:#ff0033'>(Number号红色为数据已作修改,黄色为数据未保存)</span></td></tr>";
echo"<tr class=''>
		<td width='30' rowspan='2' class='A1111'><div align='center'>序号</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>编号</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>姓名</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>小组</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>现职</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>日期<br>类别</div></td>
		<td height='19' width='80' colspan='2' class='A1101'><div align='center'>签卡记录</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>应到<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>实到<br>工时</div></td>
		<td width='120' colspan='3' class='A1101'><div align='center'>加点加班工时(+直落)</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>迟到</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>早退</div></td>
		<td width='160' colspan='9' class='A1101'><div align='center'>请、休假工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>缺勤<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>旷工<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>夜班</div></td>
 	</tr>
  	<tr class=''>
		<td width='40' heigh38t='20'  align='center' class='A0101'>签到</td>
		<td width='40' class='A0101' align='center'>签退</td>
		<td width='38' class='A0101' align='center'>1.5倍</td>
		<td width='38' class='A0101' align='center'>2倍</td>
		<td width='38' class='A0101' align='center'>3倍</td>
		<td width='20' class='A0101' align='center'>事</td>
		<td width='20' class='A0101' align='center'>病</td>		
		<td width='20' class='A0101' align='center'>无</td>
		<td width='20' class='A0101' align='center'>年</td>
		<td width='20' class='A0101' align='center'>补</td>
		<td width='20' class='A0101' align='center'>婚</td>
		<td width='20' class='A0101' align='center'>丧</td>
		<td width='20' class='A0101' align='center'>产</td>
		<td width='20' class='A0101' align='center'>工</td>
	</tr>";
//3		读取需统计的有效的员工资料
/*有效员工的条件：
	需要考勤且没有调动记录的（即入职起就需要考勤）；
	或有调动记录,则取考勤月份比调动生效月份大的最小那个月份的调入状态；
	同时员工的入职月份要少于或等于考勤那个月份
	且员工不在离职日期少于考勤月份的员工

*/
$MySql="SELECT M.Number,M.Name,J.Name AS Job,G.GroupName FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId 
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE M.cSign='$Login_cSign' $KqSignStr AND
((M.Number NOT IN (SELECT K.Number FROM $DataPublic.redeployk K GROUP BY K.Number ORDER BY K.Id) and M.KqSign<3)
OR(
	M.Number IN(
		SELECT K.Number FROM $DataPublic.redeployk K 
			INNER JOIN(
				SELECT Number,max(Month) as Month FROM $DataPublic.redeployk group by Number) k2 ON K.Number=k2.Number and K.Month=k2.Month 	
			WHERE K.ActionIn<3 and K.Month<='$CheckMonth'))
OR(
	M.Number IN(
		SELECT Ka.Number FROM $DataPublic.redeployk Ka 
			INNER JOIN(
				SELECT Number,min(Month) as Month FROM $DataPublic.redeployk WHERE Month>'$CheckMonth' group by Number) k2a ON Ka.Number=k2a.Number and Ka.Month=k2a.Month 
			WHERE Ka.ActionOut<3)))
and left(M.ComeIn,7) <='$CheckMonth' 
and M.Number NOT IN (SELECT D.Number FROM $DataPublic.dimissiondata D WHERE D.Number=M.Number and  D.outDate<'$CheckDate')
ORDER BY M.BranchId,M.GroupId,M.Number";

//echo "$MySql";    /////////////////////////////////////////////////////////////
$i=1;
$Qty_SUM=0;
$Amount_SUM=0;
$Result = mysql_query($MySql,$link_id);
if($myrow = mysql_fetch_array($Result)) {
	do{
		$test="";		$AIcolor="";	$AOcolor="";	$jbTime=0;
		$AI="";			$AO="";			$aiTime="";		$aoTime="";		$ddInfo="";
		$aiTime=0;		$aoTime=0;		$GTime=0;		$WorkTime=0;		$GJTime=0;
		$XJTime=0;		$FJTime=0;		$InLates=0;		$OutEarlys=0;		
		$QjTime1=0;		$QjTime2=0;		$QjTime3=0;		$QjTime4=0;		$QjTime5=0;		$QjTime6=0;		$QjTime7=0;		$QjTime8=0;$QjTime9=0;
		$QQTime=0;		$KGTime=0;		$YBs=0;
		$DateType=$DateTypeTemp;$GJTimeAll=0;$XJTimeAll=0;$FJTimeAll=0;
		$Name=$myrow["Name"];
		$Number=$myrow["Number"];
		$GroupName=$myrow["GroupName"];
		$Job=$myrow["Job"];
		//读取班次
		include "kqcode/checkio_model_pb.php";
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
						 Where B.Number = '$Number' and (A.GDate='$CheckDate' OR A.XDate='$CheckDate') Limit 1";
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
				$tempWorkTime = 8 - $GTime;
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

		if($DateType=="G"){//工作日的数据计算
			if($pbType==0){
				include "kqcode/checkio_model_countG.php";
				}
			else{
				include "kqcode/checkio_model_countGL.php";
				}
			}
		else{
			//非工作日的数据计算
			//echo "2:$Number : DateType:$DateType:jbTimes:$jbTimes:pbType:$pbType <br>";
			if($pbType==0){
				//echo "here3:CheckTime";
				include "kqcode/checkio_model_countX.php";}
			else{
				//echo "here4:CheckTime";
				include "kqcode/checkio_model_countXL.php";}//?????
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

		//当天的数据计算结束
		$YXJhours=$QjTime4+$QjTime5+$QjTime6+$QjTime7+$QjTime8;//有薪假总工时
		//直落工时计算：使用原文件，直落工时累计至加班工时
		include "kqcode/checkio1_model_zl.php";
		//将数据存入日统计表
		if(($aiTime!="" || $aoTime!="") || $GTime>0) {
			$GJTimeAll=$GJTimeAll==""?$GJTime:$GJTimeAll;
			$XJTimeAll=$GXTimeAll==""?$XJTime:$XJTimeAll;
			$FJTimeAll=$FJTimeAll==""?$FJTime:$FJTimeAll;
			$inRecode1="INSERT INTO $DataIn.kqrtj 
					SELECT NULL,Number,'$GTime','$WorkTime','$GJTimeAll','$XJTimeAll','$FJTimeAll','$InLates','$OutEarlys','$QjTime1','$QjTime2','$YXJhours','$QjTime3','$QQTime','$YBs','$KGTime','$CheckDate','0',1,0,'$Operator',NOW(),'$Operator',NOW(),'$Operator' 
					FROM $DataPublic.staffmain WHERE Number='$Number' AND '$CheckDate'<'$NowToDay' AND '$CheckDate'>'2010-10-01' AND Number NOT IN (SELECT Number FROM $DataIn.kqrtj WHERE Date='$CheckDate' AND Number='$Number')";
			//echo $inRecode1;
			$inAction1=@mysql_query($inRecode1);
			}

		$aiTime=SpaceValue($aiTime);
		$aoTime=SpaceValue($aoTime);
		$GTime=zerotospace($GTime);			//应到时间
		$WorkTime=zerotospace($WorkTime);	//实到时间
		$GJTime=$gzlSign==1?$GJTime:zerotospace($GJTime);		//工作日加班工时+直落
		$XJTime=$xzlSign==1?$XJTime:zerotospace($XJTime);		//休息日加班工时+直落
        if($CheckDate=="2012-10-04" &&  $ZL_Hours!=0)//2012-10-04这天单独处理。
           {
                 $XJTime=$XJTime+$ZL_Hours;
                  $GJTime=0;
		          $GJTime=zerotospace($GJTime);
              }//特殊情况
		$FJTime=$fzlSign==1?$FJTime:zerotospace($FJTime);		//法定假日加班工时+直落
		$InLates=zerotospace($InLates);		//迟到次数
		$OutEarlys=zerotospace($OutEarlys); //早退次数
		$QjTime1=zerotospace($QjTime1);		//事假
		$QjTime2=zerotospace($QjTime2);		//病假
		$QjTime3=zerotospace($QjTime3);		//无薪假
		$QjTime4=zerotospace($QjTime4);		//年休假
		$QjTime5=zerotospace($QjTime5);		//补休
		$QjTime6=zerotospace($QjTime6);		//婚假
		$QjTime7=zerotospace($QjTime7);		//丧假
		$QjTime8=zerotospace($QjTime8);		//产假
		$QjTime9=zerotospace($QjTime9);		//工伤
		$QQTime=zerotospace($QQTime);		//缺勤工时
		$WXTime=zerotospace($WXTime);		//无薪工时：当月未到职或已离职
		$KGTime=zerotospace($KGTime);		//旷工工时
		$BKTime=zerotospace($BKTime);		//应扣工时，已取消
		$YBs=zerotospace($YBs);				//夜班次数
		
		
		$CheckTable="kqdaytj";
		include "kq_checkio_check.php";//检查是否有记录没有保存，是否有记录已作修改
		
	  $TodaydkHour=0;  //是否当天有抵扣，不能算旷工
	  $rqddResult = mysql_query("SELECT Id,dkHour FROM $DataPublic.staff_dkdate WHERE Number='$Number' AND dkDate='$CheckDate'  LIMIT 1",$link_id);
	  if($rqddRow = mysql_fetch_array($rqddResult)){
		    $TodaydkHour=$rqddRow["dkHour"];
			//$sumdkHour=$sumdkHour+$TodaydkHour;
			$DateType="D";
			$ddInfo="($TodaydkHour)";
			$DateTypeColor="class='greenB'";
		}	  
      		
		
		echo"<tr><td class='A0111' align='center'>$i</td>";
		echo"<td class='A0101' align='center' $NumberColor >$Number</td>";
		echo"<td class='A0101' align='center'>$Name</td>";
		echo"<td class='A0101' align='center'>$GroupName</td>";
		echo"<td class='A0101' align='center'>$Job</td>";
		echo"<td class='A0101' align='center'><div $DateTypeColor>$DateType$ddInfo</div></td>";
		echo"<td class='A0101' align='center'><span $AIcolor>$aiTime</span></td>";
		echo"<td class='A0101' align='center'><span $AOcolor>$aoTime</span></td>";
		echo"<td class='A0101' align='center'>$GTime</td>";
		echo"<td class='A0101' align='center'>$WorkTime</td>";
		echo"<td class='A0101' align='center'>$GJTime</td>";
		echo"<td class='A0101' align='center'>$XJTime</td>";
		echo"<td class='A0101' align='center'>$FJTime</td>";
		echo"<td class='A0101' align='center'>$InLates</td>";
		echo"<td class='A0101' align='center'>$OutEarlys</td>";
		echo"<td class='A0101' align='center'>$QjTime1</td>";
		echo"<td class='A0101' align='center'>$QjTime2</td>";
		echo"<td class='A0101' align='center'>$QjTime3</td>";
		echo"<td class='A0101' align='center'>$QjTime4</td>";
		echo"<td class='A0101' align='center'>$QjTime5</td>";
		echo"<td class='A0101' align='center'>$QjTime6</td>";// $test
		echo"<td class='A0101' align='center'>$QjTime7</td>";
		echo"<td class='A0101' align='center'>$QjTime8</td>";// $qjTest
		echo"<td class='A0101' align='center'>$QjTime9</td>";// $qjTest
		echo"<td class='A0101' align='center'>$QQTime</td>";
		//echo"<td class='A0101' align='center'>$KGTime</td>";
		if($TodaydkHour>0){
			$KGTime=$KGTime-$TodaydkHour;
			$sumKGTime=$sumKGTime-$TodaydkHour;
			$KGTime=zerotospace($KGTime);
			echo "<td class='A0101' align='center'>$KGTime</td>";	
		}
		else {
			echo"<td class='A0101' align='center'>$KGTime</td>";
			}	
			
		echo"<td class='A0101' align='center'>$YBs</td>";
		echo"</tr>";
		$i++;
		}while ($myrow = mysql_fetch_array($Result));
	    
	}
else{
	echo"<tr bgcolor='#FFFFFF'><td colspan='25' scope='col' height='60' class='A0111' align='center'><p>暂时还没有资料。</td></tr>";
	}  
	  
		echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'><input name='TempValue' type='hidden' value='0'>";
		echo"
			<tr class=''>
				<td rowspan='2' class='A0111'><div align='center'>序号</div></td>
				<td rowspan='2' class='A0101'><div align='center'>编号</div></td>
				<td rowspan='2' class='A0101'><div align='center'>姓名</div></td>
				<td rowspan='2' class='A0101'><div align='center'>小组</div></td>
				<td rowspan='2' class='A0101'><div align='center'>现职</div></td>
				<td rowspan='2' class='A0101'><div align='center'>日期<br>类别</div></td>
				<td height='19' colspan='2' class='A0101'><div align='center'>签卡记录</div></td>
				<td rowspan='2' class='A0101'><div align='center'>应到<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>实到<br>工时</div></td>
				<td colspan='3' class='A0101'><div align='center'>加点加班工时(+直落)</div></td>
				<td rowspan='2' class='A0101'><div align='center'>迟到</div></td>
				<td rowspan='2' class='A0101'><div align='center'>早退</div></td>
				<td colspan='9' class='A0101'><div align='center'>请、休假工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>缺勤<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>旷工<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>夜班</div></td>
			</tr>
			<tr class=''>
				<td heigh38t='20'  align='center' class='A0101'>签到</td>
				<td class='A0101' align='center'>签退</td>
				<td class='A0101' align='center'>1.5倍</td>
				<td class='A0101' align='center'>2倍</td>
				<td class='A0101' align='center'>3倍</td>
				<td class='A0101' align='center'>事</td>
				<td class='A0101' align='center'>病</td>		
				<td class='A0101' align='center'>无</td>
				<td class='A0101' align='center'>年</td>
				<td class='A0101' align='center'>补</td>
				<td class='A0101' align='center'>婚</td>
				<td class='A0101' align='center'>丧</td>
				<td class='A0101' align='center'>产</td>
				<td class='A0101' align='center'>工</td>
			</tr></table>";
//步骤5：
include "../model/subprogram/add_model_b.php";
echo"<br>";
//include "../model/subprogram/read_model_menu.php";
?>
<input name="CheckNote" type="hidden" id="CheckNote" value="<?php  echo $CheckNote?>"/>
<script language="javascript" type="text/javascript">
function SaveDaytj(){
        var message=confirm("保存之前请确认相关数据是否正确，点取消可以返回修改。");
		if (message){
		   var kqList="";
		   for(var m=3; m<kqTable.rows.length-2; m++){
		         for(var n=9;n<13;n++){
				 var s=kqTable.rows[m].cells[n].innerText;
				      s=s.replace(/(^\s*)/g,"");
				   if(s.length>0){
		               kqList=kqList+","+
				      kqTable.rows[m].cells[1].innerHTML+"^^"+
					  kqTable.rows[m].cells[9].innerHTML+"^^"+
					  kqTable.rows[m].cells[10].innerHTML+"^^"+
					  kqTable.rows[m].cells[11].innerHTML+"^^"+
					  kqTable.rows[m].cells[12].innerHTML;
					  break;
					  }
				    }
                }	
		    //alert(kqList);
			document.getElementById("kqList").value=kqList;
			document.form1.action="kq_checkio_updated.php?ActionId=kq";
			document.form1.submit();
			}
		else{
			return false;
			}

    
}
</script>
