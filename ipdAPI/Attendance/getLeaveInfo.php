<?php 

	$ipadTag = "yes";
	include "../../basic/parameter.inc";
	include "../../model/kq_YearHolday.php";
	include("getStaffNumber.php");
	
	$Number = $_POST["id"];
	if(strlen($Number) != 5)
	{
		$Number = getStaffNumber($Number, $DataPublic);
	}
	
	$chooseYear=$chooseYear==""?date("Y"):$chooseYear;
	$StartDate = $chooseYear;
	$LastYear = $chooseYear-1;
	
	$mySql="SELECT M.Name,M.BranchId,M.ComeIn,B.Name AS Branch
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataPublic.branchdata B ON M.BranchId=B.Id
	WHERE M.Number='$Number' AND M.Estate=1  ORDER BY M.BranchId,M.GroupId,M.JobId,M.Number";
		
	$myResult = mysql_query($mySql."",$link_id);
	
	if(mysql_num_rows($myResult) == 0)
	{
		$error = array("error"=>"err");
		echo json_encode($error);
		exit();
	}
	
	if($myRow = mysql_fetch_assoc($myResult))
	{
		$Name = $myRow["Name"];
		$ComeInee = $myRow["ComeIn"];
	}
	
	$currentDate = date("Y-m-d");
	$comeInYear = intval((strtotime($currentDate)- strtotime($ComeInee))/3600/24/365);
		
	//$AnnualLeave1 = GetYearHolDayDays($Number,$chooseYear,$EndDate,$DataIn,$DataPublic,$link_id);
	//***************
	
	$mySql="SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.KqSign,B.Name AS Branch,J.Name AS Job,G.GroupName 
	FROM $DataPublic.staffmain M
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
	LEFT JOIN $DataPublic.branchdata B ON M.BranchId=B.Id
	LEFT JOIN $DataPublic.jobdata J ON M.JobId=J.Id
	WHERE M.Estate=1  
	And M.Number = '$Number'
	ORDER BY 
	M.BranchId,M.GroupId,M.JobId,M.Number";
	
	$SearchRows="";
	$myResult = mysql_query($mySql."",$link_id);
	if($myRow = mysql_fetch_array($myResult))
	{
		$m=1;
		$KqSign=$myRow["KqSign"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$ComeIn=$myRow["ComeIn"];
		$GroupName=$myRow["GroupName"];
		//入职当年
		$ComeInY=substr($ComeIn,0,4);
		//年份间隔:间隔在2以上的，全休，间隔1实计；间隔0无
		$ValueY=$chooseYear-$ComeInY;
				
		$DefaultLastM=$chooseYear."-12-01";
		$ThisEndDay=$chooseYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
		$CountDays=date("z",strtotime($ThisEndDay));	//年假当年总天数
		
		//计算休假工时  1~9年:5天,10~19:10天,20年以上的 15天
		//计算本年请假的时间(除年休)，超过15天以上的要扣除
		$sumQjTime=0;
		$qjTimeSql=mysql_query("SELECT StartDate,EndDate FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type  IN (1,3) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')",$link_id);
/*echo "SELECT StartDate,EndDate FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type  IN (1,3) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')";*/
		if($qjTimeRow=mysql_fetch_array($qjTimeSql))
		{//垮年处理
		    do{
		       $StartDate=$qjTimeRow["StartDate"];
		       $EndDate=$qjTimeRow["EndDate"];
			   $frist_Year=substr($StartDate,0,4);
			   $end_Year=substr($EndDate,0,4);
			   if($frist_Year<$LastYear)$StartDate=$LastYear."-01-01 08:00:00";
			   if($end_Year>$chooseYear)$EndDate=$chooseYear."-12-31 17:00:00";
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
					              $holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
					              if($holiday_Row = mysql_fetch_array($holiday_Result)){
						            $HolidayTemp=$HolidayTemp+1;
						            $isHolday=1;
						        }
					     }            
				         //分析是否有工作日对调
				         if($isHolday==1){  //节假日上班，所以其休息时间要减
					        $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'",$link_id);
					         if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							  $HolidayTemp=$HolidayTemp-1;
					          }				
			           	}			
				else{  //非节假日调班，则其休息时间要加,
					     $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'",$link_id);
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
		       }
		    while($qjTimeRow=mysql_fetch_array($qjTimeSql));
		    }
			if($sumQjTime<15)$sumQjTime=0;//少于15天忽略。
			//echo $sumQjTime;
			if($ValueY>1){	//年份间隔在2以上的
				$inDays=$CountDays-$sumQjTime;
				$AnnualLeave=intval((5*8*$inDays)/$CountDays);
				if($ValueY>=10){
					$AnnualLeave=intval((10*8*$inDays)/$CountDays);
					}
				if($ValueY>=20){
					$AnnualLeave=intval((15*8*$inDays)/$CountDays);
					}			
				}
			else{
					if($ValueY==1)
					{
						$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays-$sumQjTime;
						
						$AnnualLeave=intval((5*8*$inDays)/$CountDays);
					}
					else
					{
						$AnnualLeave=0;
						$inDays=0;
					}
	}
		}
	//***************
	$qjAllDays = HaveYearHolDayDays($Number,$chooseYear."-01-01 00:00:00",$chooseYear."-01-01 00:00:00",$DataIn,$DataPublic,$link_id);
	
	echo json_encode(array($Name, intval($AnnualLeave/8), $qjAllDays, $comeInYear));
?>