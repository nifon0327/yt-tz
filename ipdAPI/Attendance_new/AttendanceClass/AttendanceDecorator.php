<?php

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once "$path/basic/parameter.inc";
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/StaffAvatar.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceCalculateModle.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule_old.php");

	class AttendanceAvatar extends StaffAvatar implements AttendanceCalculateInterface
	{
		public $targetDate;
		public $startTime;
		public $endTime;
		public $weekDay;
		public $workSchedule;

		public $defaultWorkHours = 0;
		public $workHours = 0;
		public $workOtTime = 0;
		public $weekOtTime = 0;
		public $weekOtTimeOver = 0;
		public $holidayOtTime = 0;

		public $workZlHours = 0;
		public $weekZlHours = 0;
		public $holidayZlHours = 0;
		public $dkHours = 0;
		public $noPayHours = 0;

		public $leaveArray = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0);
		public $totleLeaveHours = 0;
		public $late = 0;public $beLateStandard;
		public $early = 0;public $beEarlyStandard;
		public $secondOfHour = 3600;
		public $lackWorkHours = 0;//缺勤工时
		public $kgHours = 0;//旷工工时
		public $KrSign;
		public $nightShit = 0;

		public $hasZlhours = 'N';

		public function clearData(){
			$this->startTime = '';
			$this->endTime = '';

			$this->workHours = 0;
			$this->workOtTime = 0;
			$this->weekOtTime = 0;
			$this->holidayOtTime = 0;

			$this->workZlHours = 0;
			$this->weekZlHours = 0;
			$this->holidayZlHours = 0;
			$this->dkHours = 0;
			$this->noPayHours = 0;

			$this->totleLeaveHours = 0;
			$this->late = 0;
			$this->early = 0;
			$this->kgHours = 0;
			$this->nightShit = 0;
			$this->hasZlhours = 'N';
		}

		public function setupAttendanceData($number, $checkDay, $DataIn, $DataPublic, $link_id){
			$this->defaultWorkHours = 8;
			$this->targetDate = $checkDay;

			$getCheckDataSql = "SELECT CheckTime,CheckType,KrSign,Id
								FROM $DataIn.checkinout 
								WHERE Number=$number 
								and ((CheckTime LIKE '$checkDay%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$checkDay%' and KrSign='1'))
								and Id not in (Select checkId From $DataIn.disableCheckId)
								order by CheckTime";
			$getCheckDataResult = mysql_query($getCheckDataSql);
			while($getCheckDataRow = mysql_fetch_assoc($getCheckDataResult)){
				$checkType = $getCheckDataRow["CheckType"];
				switch($checkType){
					case "I":{
						$this->startTime = substr($getCheckDataRow["CheckTime"], 0, 16);
					}
					break;
					case "O":{
						$this->endTime = substr($getCheckDataRow["CheckTime"], 0, 16);
						$this->KrSign = $getCheckDataRow["KrSign"];
					}
					break;
				}
			}
			$dateArray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
			$this->weekDay="星期".$dateArray[date("w",strtotime($checkDay))];
			$this->nightShit = ($this->KrSign == "1")?"1":"";
		}
		
		//获取排班
		public function getWorkSchedule($DataIn, $DataPublic, $link_id){
			if($this->targetDate < '2016-05-28'){
				$this->workSchedule = new WorkScheduleSheet_old($this->getStaffNumber(), $this->targetDate, $this->startTime, $this->endTime);
			}else{
				$this->workSchedule = new WorkScheduleSheet($this->getStaffNumber(), $this->targetDate, $this->startTime, $this->endTime);
			}
			//确定迟到早退标准
			$this->beLateStandard = $this->targetDate.' '.$this->workSchedule->mCheckTime["start"];
			$this->beEarlyStandard = $this->targetDate.' '.$this->workSchedule->aCheckTime["end"];
		}

		//确定当天的
		public function getDateType($DataIn, $DataPublic, $link_id){
			//旧调班
			$workDateChangeSql = mysql_query("SELECT Id,XDate,GDate FROM $DataIn.kqrqdd WHERE (GDate='".$this->targetDate."' OR XDate='".$this->targetDate."') and Number = ".$this->getStaffNumber()." and Estate=1 LIMIT 1");
			if($rqddRow = mysql_fetch_assoc($workDateChangeSql)){
			
				$gDate = $rqddRow["GDate"];
				$xDate = $rqddRow["XDate"];

				$exchangeDate = ($this->targetDate == $gDate)?$xDate:$gDate;
				$dateType = $this->isHoliday($exchangeDate, $DataIn, $DataPublic,$link_id);

				$this->workSchedule->mCheckTime["state"] = "$dateType";
				$this->workSchedule->aCheckTime["state"] = "$dateType";
			}else{
				//新调班
				$nRqddSql = "Select A.GDate,A.GTime,A.XDate,A.XTime From $DataIn.kq_rqddnew A 
						 	Left Join $DataIn.kq_ddsheet B On B.ddItem = A.Id
						 	Where B.Number = '".$this->getStaffNumber()."' and (A.GDate='".$this->targetDate."' OR A.XDate='".$this->targetDate."')";
				$isTarget = '';
				//echo $nRqddSql.'<br>';
				$nRqddResult = mysql_query($nRqddSql);
				$todayDateType = $this->isHoliday($this->targetDate, $DataIn, $DataPublic ,$link_id);
				$this->workSchedule->mCheckTime["state"] = $todayDateType;
				$this->workSchedule->aCheckTime["state"] = $todayDateType;
				if(mysql_num_rows($nRqddResult) != 0){
					while($nRqddRow = mysql_fetch_assoc($nRqddResult)){
						$gDate = $nRqddRow["GDate"];
						$tDate = $nRqddRow["XDate"];

						if($gDate == '2015-01-13' or $tDate == '2015-01-16'){
							$isTarget = 'y';
						}

						switch($this->targetDate){
							case $gDate:{
								$gTime = $nRqddRow["GTime"];
								$this->workDateBeExchange($gTime, $gDate, $tDate, $DataIn, $DataPublic,$link_id);
							}
							break;
							case $tDate:{
								$tTime = $nRqddRow["XTime"];
								$this->workDateExchange($tTime, $gDate, $tDate, $DataIn, $DataPublic,$link_id);
							}
							break;
							default:{
								//echo 'here';
							}
							break;
						}
					}
				}else{
					$this->workSchedule->mCheckTime["state"] = $this->isHoliday($this->targetDate, $DataIn, $DataPublic ,$link_id);
					$this->workSchedule->aCheckTime["state"] = $this->isHoliday($this->targetDate, $DataIn, $DataPublic ,$link_id);
				}
			}

			//设定加班状态
			if($this->targetDate == '2015-01-13' && $isTarget != ''){
				$this->workSchedule->mCheckTime["state"] = 'G';
				$this->workSchedule->aCheckTime["state"] = 'T';
				$this->workSchedule->otStartTime["state"] = 'G';
			}else if($this->targetDate == '2015-01-16' && $isTarget != ''){
				$this->workSchedule->mCheckTime["state"] = 'G';
				$this->workSchedule->aCheckTime["state"] = 'G';
				$this->workSchedule->otStartTime["state"] = 'T';
			}else if(substr($this->targetDate,5,5) == '03-08' && $this->getSex() != 1 && date("w",strtotime($checkDay)) != 6  && date("w",strtotime($checkDay)) != 0 ){
				$this->workSchedule->mCheckTime["state"] = 'F';
				$this->workSchedule->otStartTime["state"] = $this->isHoliday($this->targetDate, $DataIn, $DataPublic,$link_id);
			}
			else{
				$this->workSchedule->otStartTime["state"] = $this->isHoliday($this->targetDate, $DataIn, $DataPublic,$link_id);
			}

			//再确定当天的标准工时
			$this->defaultWorkHours = 0;
			$mDefalutWorkHours = ($this->workSchedule->mCheckTime["state"]=="G")?(strtotime($this->workSchedule->mCheckTime["end"])-strtotime($this->workSchedule->mCheckTime["start"]))/3600:0;
			$this->defaultWorkHours += $mDefalutWorkHours;
			
			$aDefaultWorkHours = ($this->workSchedule->aCheckTime["state"]=="G")?(strtotime($this->workSchedule->aCheckTime["end"])-strtotime($this->workSchedule->aCheckTime["start"]))/3600:0;
			
			if($aDefaultWorkHours < 0 ){
				if($this->targetDate != substr($this->endTime, 0, 10)){
					$aDefaultWorkHours += 24;
				}else{
					$aDefaultWorkHours = 0;
				}
				
			}
		     
			$this->defaultWorkHours += $aDefaultWorkHours;
			$this->defaultWorkHours += ($this->workSchedule->otStartTime["state"]=="T")?4:0;

			// if($this->targetDate == '2016-05-26'){
			// 	echo $this->workSchedule->aCheckTime["end"]."   ".$this->workSchedule->aCheckTime["start"].'<br>';
			// 	echo "$mDefalutWorkHours    $aDefaultWorkHours<br>";
			// }

			//若有调班，再次重置迟到早退
			if($this->defaultWorkHours == 0){
				$this->beLateStandard = "";
				$this->beEarlyStandard = "";
			}else if($this->workSchedule->mCheckTime["state"] == "G" && $this->workSchedule->aCheckTime["state"] != "G"){
				$this->beEarlyStandard = $this->targetDate.' '.$this->workSchedule->mCheckTime["end"];
			}else if($this->workSchedule->mCheckTime["state"] != "G" && $this->workSchedule->aCheckTime["state"] == "G"){
				$this->beLateStandard = $this->targetDate.' '.$this->workSchedule->aCheckTime["start"];
			}	
		}


		private function workDateBeExchange($exchangeTime, $gDate, $tDate, $DataIn, $DataPublic,$link_id){
			$changeTime = explode("-", $exchangeTime);
			$startTime = $changeTime[0];
			$endTime = $changeTime[1];
			$gDateType = $this->isHoliday($gDate, $DataIn, $DataPublic,$link_id);
			$tDateType = $this->isHoliday($tDate, $DataIn, $DataPublic,$link_id);
			if($tDate != ""){
				$tDateType = $this->isHoliday($tDate, $DataIn, $DataPublic,$link_id);
			}else{
				$tDateType = $gDateType!="G"?"G":"X";
			}

			if($endTime == $this->workSchedule->aCheckTime["end"] and $startTime == $this->workSchedule->mCheckTime["start"]){
				//调全天
				$this->workSchedule->mCheckTime["state"] = "$tDateType";
				$this->workSchedule->aCheckTime["state"] = "$tDateType";
			}else{
				//调半天
				if(strtotime($startTime) == strtotime($this->workSchedule->mCheckTime["start"]) && strtotime($endTime) == strtotime($this->workSchedule->mCheckTime["end"])){
					$this->workSchedule->mCheckTime["state"] = "$tDateType";
				}else{
					$this->workSchedule->aCheckTime["state"] = "$tDateType";
				}
			}
		}

		private function workDateExchange($exchangeTime, $gDate, $tDate, $DataIn, $DataPublic,$link_id){
			$changeTime = explode("-", $exchangeTime);
			$startTime = $changeTime[0];
			$endTime = $changeTime[1];

			$gDateType = $this->isHoliday($gDate, $DataIn, $DataPublic,$link_id);
			$tDateType = $this->isHoliday($tDate, $DataIn, $DataPublic,$link_id);
			if($endTime == $this->workSchedule->aCheckTime["end"] and $startTime == $this->workSchedule->mCheckTime["start"]){
				//调全天
				$this->workSchedule->mCheckTime["state"] = "$gDateType";
				$this->workSchedule->aCheckTime["state"] = "$gDateType";
			}else{
				//调半天
				if($startTime == $this->workSchedule->mCheckTime["start"] and $endTime == $this->workSchedule->mCheckTime["end"]){
					//同一时间段
					$this->workSchedule->mCheckTime["state"] = "$gDateType";
				}else{
					//不同时间段
					$this->workSchedule->aCheckTime["state"] = "$gDateType";
				}
			}
		}

		private function isHoliday($date, $DataIn, $DataPublic,$link_id){
				$type = "";
				$holidayResult = mysql_query("SELECT Type,jbTimes FROM ".$DataPublic.".kqholiday WHERE Date='$date'");
				if($holidayRow = mysql_fetch_assoc($holidayResult)){
					$holidayType = $holidayRow["Type"];
					switch($holidayType){
						case 0:		$type="W";		break;
						case 1:		$type="Y";		break;
						case 2:		$type="F";		break;
					}
				}else{
					$weekDay=date("w",strtotime($date));
					$type=($weekDay==6 || $weekDay==0)?"X":"G";
				}
			
				return $type;
		}

		//计算当天该员工的工时
		public function getWorkHours($DataIn, $DataPublic, $link_id){
			//上午时段
			$mWorkHours = $this->workHours($this->workSchedule->mCheckTime["start"], $this->workSchedule->mCheckTime["end"], $this->startTime, $this->endTime);
			$this->addHoursByType($mWorkHours, $this->workSchedule->mCheckTime["state"]);

			$aWorkHours = $this->workHours($this->workSchedule->aCheckTime["start"], $this->workSchedule->aCheckTime["end"], $this->startTime, $this->endTime);
			$this->addHoursByType($aWorkHours, $this->workSchedule->aCheckTime["state"]);
		}

		private function workHours($standardStart, $standardEnd, $startTime, $endTime){
			$standardStartDate = $this->targetDate." ".$standardStart;
			$standardEndDate = $this->targetDate." ".$standardEnd;
			$startHolder = "";
			$endHolder = "";
			
			if(strtotime($standardStart) > strtotime($standardEnd)){
				$standardEndDate = date('Y-m-d', strtotime($this->targetDate) + 24*3600)." ".$standardEnd;
			}

			//echo "$standardStartDate|$standardEndDate|$startTime|$endTime <br>";
			//确定开始时间
			if(strtotime($standardEndDate) <= strtotime($startTime)){
				$startHolder = $standardEndDate;
			}
			else if(strtotime($standardStartDate) < strtotime($startTime) && strtotime($standardEndDate) > strtotime($startTime)){
				$startHolder = $this->rounding_in($startTime);
			}
			else{
				$startHolder = $standardStartDate;
			}
			
			//确定结束时间
			if(strtotime($standardStartDate) >= strtotime($endTime)){
				$endHolder = $standardStartDate;
			}
			else if(strtotime($standardStartDate) < strtotime($endTime) && strtotime($standardEndDate) > strtotime($endTime)){
				$endHolder = $this->rounding_out($endTime);
			}
			else{
				$endHolder = $standardEndDate;
			}

			// if($this->targetDate == '2016-05-26'){
			// 	echo "$endHolder    $startHolder<br>";
			// }
			$workHours = (strtotime($endHolder) - strtotime($startHolder))/$this->secondOfHour;
		
			return $workHours<0?0:$workHours;
		}

		private function addHoursByType($hours, $type, $isOtTag=false){
			switch ($type) {
				case 'T':
					if($hours - 4 > 0){
						$this->workHours += 4;
						$overtime = $hours - 4;
						$this->workOtTime += $overtime;
					}else{
						$this->workHours += $hours;
					}
					break;
				case 'G':
					$this->workHours += $hours;	
				break;
				case "X":
				case "Y":
					if($isOtTag){
						$this->weekOtTimeOver = $hours;
					}
					$this->weekOtTime += $hours;

				break;
				case "F":
					$this->holidayOtTime += $hours;
				break;
			}
		
		}

		private function rounding_in($AITemp){//向上取整处理
			$m_Temp=substr($AITemp,14,2);//取分钟
			if($m_Temp!=0 && $m_Temp!=30){
				if($m_Temp<30){
					$m_Temp=30-$m_Temp;
				}else{
					$m_Temp=60-$m_Temp;
				}
			}else{
				$m_Temp=0;
			}
			$ChickIn=date("Y-m-d H:i:00",strtotime("$AITemp")+$m_Temp*60);
			return $ChickIn;
		}

		private function rounding_out($AOTemp){//向下取整处理
			$m_Temp=substr($AOTemp,14,2);//取分钟
			if($m_Temp!=0 && $m_Temp!=30){
				if($m_Temp<30){
					$m_Temp=0;
				}else{
					$m_Temp=30;
				}
			}
			$m_Temp=$m_Temp==0?":00":":30";
			$ChickOut=substr($AOTemp,0,13).$m_Temp.":00";
			return $ChickOut;
		}

		public function getOverTimeHours($DataIn, $DataPublic, $link_id){
			if(strtotime($this->workSchedule->mCheckTime["start"]) > strtotime($this->workSchedule->aCheckTime["end"])){
				$calculateDate = date('Y-m-d', strtotime($this->targetDate) + 24*3600);
			}
			else{
				$calculateDate = $this->targetDate;
			}

			$otTime = (strtotime($this->rounding_out($this->endTime)) - strtotime($calculateDate." ".$this->workSchedule->otStartTime["start"]))/3600;
			$otTime = $otTime<0?0:$otTime;
			if($this->workSchedule->otStartTime["state"]=="G"){
				$this->workOtTime += $otTime;
			}else{
				$this->addHoursByType($otTime, $this->workSchedule->otStartTime["state"], true);
			}
		}

		public function getLeaveHours($DataIn, $DataPublic, $link_id){
			//$totleLeavHours = 0;
			$leaveTime = array();
			$earlistTime = '';
			$qjResult = mysql_query("SELECT StartDate,EndDate,Type FROM $DataPublic.kqqjsheet WHERE Number=".$this->getStaffNumber()." and ('".$this->targetDate."' between left(StartDate,10) and left(EndDate,10))",$link_id);
			while($qjRow = mysql_fetch_assoc($qjResult)){
				$leaveStartDate = $qjRow["StartDate"];
				if(strtotime($leaveStartDate) == strtotime($this->targetDate.' '.$this->workSchedule->aCheckTime['start'])){
					$leaveStartDate = $this->targetDate.' '.$this->workSchedule->mCheckTime['end'];
				}

				if(strtotime($leaveStartDate) < strtotime($this->targetDate.' '.$this->workSchedule->mCheckTime['start'])){
                	$leaveTime[] = $this->targetDate.' '.$this->workSchedule->mCheckTime['start'];
            	}
            	else{
                	$leaveTime[] = $leaveStartDate;
            	}

            	$leaveEndDate = $qjRow["EndDate"];
            	if(strtotime($leaveEndDate) == strtotime($this->targetDate.' '.$this->workSchedule->mCheckTime['end'])){
					$leaveEndDate = $this->targetDate.' '.$this->workSchedule->aCheckTime['start'];
				}

           		if(strtotime($leaveEndDate) > strtotime($this->targetDate.' '.$this->workSchedule->mCheckTime['end'])){
                	$leaveTime[] = $this->targetDate.' '.$this->workSchedule->aCheckTime['end'];
            	}
            	else{
                	$leaveTime[] = $leaveEndDate;
            	}
				$leaveType = $qjRow["Type"];
				//计算上午请假时间
				if($this->workSchedule->mCheckTime["state"] === "G"){
					$mLeaveHours = $this->workHours($this->workSchedule->mCheckTime["start"], $this->workSchedule->mCheckTime["end"], $leaveStartDate, $leaveEndDate);

					$this->leaveArray[$leaveType] += $mLeaveHours;
					$this->totleLeaveHours += $mLeaveHours;
				}
				//计算下午请假时间
				if($this->workSchedule->aCheckTime["state"] === "G"){
					$aLeaveHours = $this->workHours($this->workSchedule->aCheckTime["start"], $this->workSchedule->aCheckTime["end"], $leaveStartDate, $leaveEndDate);
					$this->leaveArray[$leaveType] += $aLeaveHours;
					$this->totleLeaveHours += $aLeaveHours;
				}
			}

			sort($leaveTime);
			$dayLeaveStart = $leaveTime[0];
			//$dayLeaveStart = strtotime($dayLeaveStart) == strtotime($this->targetDate.' '.$this->workSchedule->mCheckTime['end'])?$this->targetDate.' '.$this->workSchedule->aCheckTime['start']:$dayLeaveStart;
        	$dayLeaveEnd = $leaveTime[count($leaveTime)-1];
        	if($this->totleLeaveHours == $this->defaultWorkHours){
            	$this->beLateStandard = '';
            	$this->beEarlyStandard = '';
        	}
        	else if(strtotime($dayLeaveStart) == strtotime($this->targetDate.' '.$this->workSchedule->mCheckTime['start'])){
            	$this->beLateStandard = $dayLeaveEnd;
            	$this->beEarlyStandard = $this->targetDate.' '.$this->workSchedule->aCheckTime['end'];
        	}
        	else if(strtotime($dayLeaveEnd) == strtotime($this->targetDate.' '.$this->workSchedule->aCheckTime['end'])){
            	$this->beLateStandard = $this->targetDate.' '.$this->workSchedule->mCheckTime['start'];
           		$this->beEarlyStandard = $dayLeaveStart;
        	}
		}

		public function getZlHours($DataIn, $DataPublic, $link_id){
			$ZL_Result = mysql_query("SELECT sum(Hours) as Hours FROM $DataPublic.kqzltime  WHERE Number=".$this->getStaffNumber()." and Date='".$this->targetDate."'",$link_id);
			$zlRow = mysql_fetch_assoc($ZL_Result);
			$zlHours = $zlRow["Hours"];
			if($zlHours > 0){
				$type = $this->isHoliday($this->targetDate, $DataIn, $DataPublic,$link_id);
				
				switch ($type) {
					case "G":{
						$this->workZlHours = $zlHours;
						$this->hasZlhours = "G|$zlHours";
					}
					break;
					case "X":
					case "Y":{
						$this->weekZlHours = $zlHours;
						$this->hasZlhours = "X|$zlHours";
					}
					break;
					case "F":{
						$this->holidayZlHours = $zlHours;
						$this->hasZlhours = "F|$zlHours";
					}
					break;
				}
			}

		}

		public function attendanceStatistic($DataIn, $DataPublic, $link_id){
			//工时抵扣
			// $rqddResult = mysql_query("SELECT Id,dkHour FROM $DataPublic.staff_dkdate WHERE Number='".$this->getStaffNumber()."' AND dkDate='".$this->targetDate."'  LIMIT 1",$link_id);
			// if($rqddRow = mysql_fetch_assoc($rqddResult)){
			// 	$this->dkHours = $rqddRow["dkHour"];
			// }

			if($this->workSchedule->mCheckTime["state"] == "G" || $this->workSchedule->aCheckTime["state"] == "G"){
				$this->lackWorkHours = $this->defaultWorkHours - $this->workHours - $this->totleLeaveHours - $this->dkHour;
				$this->lackWorkHours = $this->lackWorkHours < 0?0:$this->lackWorkHours;
				if($this->lackWorkHours == $this->defaultWorkHours){
					$this->kgHours = $this->defaultWorkHours;
					$this->lackWorkHours = 0;
					$this->beLateStandard = '';
					$this->beEarlyStandard = '';
				}
			}


			//是否用有薪假抵扣工时
			$dkYXSql = "SELECT dkHours From $DataPublic.staff_dkdate Where dkDate='".$this->targetDate."' Limit 1";
			//echo $dkYXSql.'<br>';
			$dkYXResult = mysql_query($dkYXSql);
			if($dkYxRow = mysql_fetch_assoc($dkYXResult)){
				$dkHour = $dkYxRow['dkHours'];
				if($this->kgHours > 0 || $this->lackWorkHours > 0){
					$this->dkHours = $dkHour - $this->workHours;
					//echo "$dkHour - $this->workHours <br>";
					$this->kgHours = 0;
					$this->lackWorkHours = 0;
				}
			}




			//判断迟到早退
			// if ($this->targetDate == '2015-11-25'){
			// 	echo $this->beLateStandard.'      '.$this->beEarlyStandard."<br>"; 
			// }
			if($this->workSchedule->mCheckTime["state"] == "G" || $this->workSchedule->aCheckTime["state"] == "G"){
				//echo $this->targetDate.'       '.$this->beLateStandard.'   '.$this->lackWorkHours.'<br>';
				if($this->beLateStandard !="" && strtotime($this->startTime) > strtotime($this->beLateStandard) && $this->lackWorkHours <= 0.5){
					$this->late = 1;
					$this->lackWorkHours = "";
				}

				if($this->beEarlyStandard !="" && strtotime($this->endTime) < strtotime($this->beEarlyStandard)){
					$this->early = 1;
					//$this->lackWorkHours = "";
				}
			}
		}

		public function setOutOfWorkState($DataIn, $DataPublic, $link_id){
			$this->getWorkSchedule($DataIn, $DataPublic, $link_id);
			$this->getDateType($DataIn, $DataPublic, $link_id);

			switch($this->workSchedule->mCheckTime["state"]){
				case "X":
				case "Y":
						$this->noPayHours=0;$this->workHours=0;
					break;
				case "F":
						$this->noPayHours=8;$this->defaultWorkHours=8;
					break;
				default:
						$this->noPayHours=8;$this->workHours=0;
					break;
			}		
			$this->workSchedule->mCheckTime["state"] = "离";
			$this->workSchedule->aCheckTime["state"] = "离";
		}

		public function attendanceSetup($DataIn, $DataPublic, $link_id){
			$this->getWorkSchedule($DataIn, $DataPublic, $link_id);
			$this->getDateType($DataIn, $DataPublic, $link_id);
			$this->getWorkHours($DataIn, $DataPublic, $link_id);
			$this->getZlHours($DataIn, $DataPublic, $link_id);
			$this->getOverTimeHours($DataIn, $DataPublic, $link_id);
			$this->getLeaveHours($DataIn, $DataPublic, $link_id);
			$this->attendanceStatistic($DataIn, $DataPublic, $link_id);
		}

		public function getInfomationByArray(){
			$dateType = $this->workSchedule->mCheckTime["state"] == $this->workSchedule->aCheckTime["state"]?$this->workSchedule->mCheckTime["state"]:$this->workSchedule->mCheckTime["state"]."/".$this->workSchedule->aCheckTime["state"];
			return array( 
							$this->targetDate,
							$this->weekDay,
							$dateType,
							substr($this->startTime, 12, 5)."",
							substr($this->endTime, 12, 5)."",
							$this->defaultWorkHours."",
							$this->workHours."",
							$this->workOtTime."",
							$this->workZlHours."",
							$this->weekOtTime."",
							$this->weekZlHours."",
							$this->holidayOtTime."",
							$this->holidayZlHours."",
							$this->late."",
							$this->early."",
							$this->leaveArray["1"]."",
							$this->leaveArray["2"]."",
							$this->leaveArray["3"]."",
							$this->leaveArray["4"]."",
							$this->leaveArray["5"]."",
							$this->leaveArray["6"]."",
							$this->leaveArray["7"]."",
							$this->leaveArray["8"]."",
							$this->leaveArray["9"]."",
							$this->lackWorkHours."",
							$this->kgHours."",
							$this->nightShit."",
							$this->noPayHours."",
							$this->dkHours."",
							$this->weekOtTimeOver.""
						);
		}

		public function getInfomationByTag(){
			$dateType = $this->workSchedule->mCheckTime["state"] == $this->workSchedule->aCheckTime["state"]?$this->workSchedule->mCheckTime["state"]:$this->workSchedule->mCheckTime["state"]."/".$this->workSchedule->aCheckTime["state"];
			return array( 
							"checkDay" => $this->targetDate,
							"weekDay" => $this->weekDay,
							"state"=> $dateType ,
							"startTime"=>substr($this->startTime, 11, 5),
							"endTime"=>substr($this->endTime, 11, 5),
							"defaultWorkHours"=> $this->spaceInsteadZero($this->defaultWorkHours),
							"workHours"=> $this->spaceInsteadZero($this->workHours),
							"workOtHours"=>$this->spaceInsteadZero($this->workOtTime),
							"workZlHours"=>$this->spaceInsteadZero($this->workZlHours),
							"weekOtTime"=>$this->spaceInsteadZero($this->weekOtTime),
							"weekZlHours"=>$this->spaceInsteadZero($this->weekZlHours),
							"holidayOtHours"=>$this->spaceInsteadZero($this->holidayOtTime),
							"holidayZlHours"=>$this->spaceInsteadZero($this->holidayZlHours),
							"beLate"=>$this->spaceInsteadZero($this->late),
							"beEarly"=>$this->spaceInsteadZero($this->early),
							"personalLeave"=>$this->spaceInsteadZero($this->leaveArray["1"]),
							"sickLeave"=>$this->spaceInsteadZero($this->leaveArray["2"]),
							"noPayLeave"=>$this->spaceInsteadZero($this->leaveArray["3"]),
							"annualLeave"=>$this->spaceInsteadZero($this->leaveArray["4"]),
							"bxLeave"=>$this->spaceInsteadZero($this->leaveArray["5"]),
							"marrayLeave"=>$this->spaceInsteadZero($this->leaveArray["6"]),
							"deadLeave"=>$this->spaceInsteadZero($this->leaveArray["7"]),
							"birthLeave"=>$this->spaceInsteadZero($this->leaveArray["8"]),
							"hurtLeave"=>$this->spaceInsteadZero($this->leaveArray["9"]),
							"lackWorkHours"=>$this->spaceInsteadZero($this->lackWorkHours),
							"kgHours"=>$this->spaceInsteadZero($this->kgHours),
							"nightShit"=>$this->spaceInsteadZero($this->nightShit),
							"noPayHours"=>$this->spaceInsteadZero($this->noPayHours),
							"dkHours"=>$this->spaceInsteadZero($this->dkHours),
							"weekOtTimeOver" => $this->spaceInsteadZero($this->weekOtTimeOver)
						);
		}

		function spaceInsteadZero($number){
		return ($number=="0")?"":$number."";
	}

	}

	

?>