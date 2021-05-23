<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/public/kqClass/Kq_dateItem.php");
	//include_once("Kq_otHourSet.php");
	include_once("$path/public/kqClass/Kq_pbSet.php");
	
	class KqDailyItem extends KqDateItem
	{
		
		public $checkInTime;
		public $checkOutTime;
		public $checkDate;
		public $dateType;
		public $dayOfWeek;
		public $dayInfomation;
		private $defaultWorkHours;
		private $hasLunchTime;
		private $hasDinnerTime;
		private $secondOfHour;
		
		function __construct($inTime, $outTime, $date)
		{
			$this->checkDate = $date;
			$dateArray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
			$weekDay=date("w",strtotime($date));	 
			$this->dayOfWeek="星期".$dateArray[$weekDay];
			$this->secondOfHour = 60 * 60;
			if($inTime != "" || $outTime != "")
			{
				$this->checkInTime = substr($inTime,0, 16);
				$this->checkOutTime = substr($outTime,0, 16);
				$this->defaultWorkHours = 8;
			}
		}
		
		public function setupDateType($date, $number, $DataIn, $DataPublic, $link_id)
		{
			//echo "SELECT Id,XDate,GDate FROM $DataIn.kqrqdd WHERE Number='$number' AND (GDate='$date' OR XDate='$date') LIMIT 1";
			//确定当天是否有对调
			$rqddResult = mysql_query("SELECT Id,XDate,GDate FROM $DataIn.kqrqdd WHERE Number='$number' AND (GDate='$date' OR XDate='$date') LIMIT 1",$link_id);
			if($rqddRow = mysql_fetch_assoc($rqddResult))
			{
				$gDate = $rqddRow["GDate"];
				$xDate = $rqddRow["XDate"];
				
				$exchangeDate = ($date == $gDate)?$xDate:$gDate;
				$this->dateType = $this->isHoliday($exchangeDate, $DataIn, $DataPublic,$link_id);
				
				$dateArray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
				$weekDay=date("w",strtotime($exchangeDate));
				$this->dayInfomation = "调(".$dateArray[$weekDay].")";
				
			}
			else
			{
				$this->dateType = $this->isHoliday($date, $DataIn, $DataPublic,$link_id);
			}
			
			//若为工作时间则确定当天工作时间
			$this->workTime = ($this->dateType == "G")?"8":"0";
		}
		
		public function getDateType($date, $DataIn, $DataPublic, $link_id)
		{
			//echo "SELECT Id,XDate,GDate FROM $DataIn.kqrqdd WHERE (GDate='$date' OR XDate='$date') LIMIT 1";
			//确定当天是否有对调
			$rqddResult = mysql_query("SELECT Id,XDate,GDate FROM $DataIn.kqrqdd WHERE (GDate='$date' OR XDate='$date') LIMIT 1",$link_id);
			if($rqddRow = mysql_fetch_assoc($rqddResult))
			{
				$gDate = $rqddRow["GDate"];
				$xDate = $rqddRow["XDate"];
				
				$exchangeDate = ($date == $gDate)?$xDate:$gDate;
				$this->dateType = $this->isHoliday($exchangeDate, $DataIn, $DataPublic,$link_id);
				
				$dateArray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
				$weekDay=date("w",strtotime($exchangeDate));
				$this->dayInfomation = "调(".$dateArray[$weekDay].")";
				
			}
			else
			{
				$this->dateType = $this->isHoliday($date, $DataIn, $DataPublic,$link_id);
			}
			
		}

		
		private function isHoliday($date, $DataIn, $DataPublic,$link_id)
		{
			$type = "";
			$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$date'",$link_id);
			if($holidayRow = mysql_fetch_assoc($holidayResult))
			{
				$holidayType = $holidayRow["Type"];
				switch($holidayType)
				{
					case 0:		$type="W";		break;
					case 1:		$type="Y";		break;
					case 2:		$type="F";		break;
				}
				
			}
			else
			{
				$weekDay=date("w",strtotime($date));
				$type=($weekDay==6 || $weekDay==0)?"X":"G";
			}
			
			return $type;
		}
		
		
		public function calculateHours($number, $otHours, $zlHours, $pbSheet, $DataIn, $DataPublic, $link_id)
		{
			if($this->checkInTime != "" && $this->checkOutTime != "")
			{
				$morningWorkHours = $this->workHours($pbSheet->mCheckStartTime, $pbSheet->mCheckEndTime, $this->checkInTime, $this->checkOutTime);
				$afternoonWorkHours = $this->workHours($pbSheet->aCheckStartTime, $pbSheet->aCheckEndTime, $this->checkInTime, $this->checkOutTime);

				//重置签退时间		
			
				//计算迟到早退
				if(strtotime($this->checkDate." ".$pbSheet->mCheckStartTime) < strtotime($this->checkInTime))
				{
					$this->beLate = 1;
				}
			
				if(strtotime($this->checkDate." ".$pbSheet->aCheckEndTime) > strtotime($this->checkOutTime))
				{
					$this->leaveEarly = 1;
				}
				
						
				//保存当天的工作工时信息,按日期类型处理
				switch($this->dateType)
				{
					case "G":
					{
						$this->realWorkTime = $morningWorkHours + $afternoonWorkHours;
						$this->jbTime = $otHours;
						
						if(($number == "10037" || $number == "10214" || $number == "10422" || $number == "10422" || $number == "11966") && $this->checkDate == "2014-06-11")
						{
							$this->jbTime = 2.5;
							$otHours = 2.5;
						}
						
					}
					break;
					case "X":
					case "Y":
					{
						$this->sxTime = $morningWorkHours + $afternoonWorkHours;
					}
					break;
					case "F":
					{
						$this->jrTime = $morningWorkHours + $afternoonWorkHours;
					}
					break;
				}
				
			}
			
			$this->leaveHours($number, $this->checkDate, $pbSheet, $DataIn, $DataPublic, $link_id);
			
			$tmpSumLeaveHours = $this->privateLeave + $this->sickLeave + $this->noWageLeave + $this->annualLeave + $this->notBusyLeave + $this->marriageLeave + $this->funeralLeave + $this->maternityLeave + $this->injuryLeave;
			if(($this->checkDate == "2014-06-05" && strtotime($this->checkOutTime) < strtotime($this->checkDate." ".$pbSheet->aCheckEndTime)) || ($this->checkDate == "2014-06-19" && strtotime($this->checkInTime) > strtotime($this->checkDate." ".$pbSheet->mCheckEndTime)))
			{
				$this->workTime = 4;
				if($this->checkDate == "2014-06-05")
				{
					$this->leaveEarly = 0;
				}
				else
				{
					$this->beLate = 0;
				}
				if($tmpSumLeaveHours > 0)
				{
					if($this->privateLeave > 4)
					{
						$this->privateLeave-=4;
					}
					
					if($this->annualLeave > 4)
					{
						$this->annualLeave -= 4;
					}
				}	
			}
			
			if($tmpSumLeaveHours > 0 && $this->checkDate == "2014-06-19")
			{
				$this->workTime = 4;
				if($this->privateLeave > 4)
					{
						$this->privateLeave-=4;
					}
					
					if($this->annualLeave > 4)
					{
						$this->annualLeave -= 4;
					}

			}
			
			//计算旷工数据
			if($this->workTime != 0 && $this->realWorkTime < 8)
			{
				$this->setquitTime($number, $DataIn, $DataPublic, $link_id);
			}
			
			switch($this->dateType)
				{
					case "G":
					if(strtotime($this->checkDate." ".$pbSheet->mCheckEndTime) < strtotime($this->checkOutTime) && strtotime($this->checkOutTime) > strtotime($this->checkDate." ".$pbSheet->otStartTime))
					{
						
						$minNumber = substr($this->checkOutTime, 15, 4);												
						//有加班
						if($otHours == 0)
						{
							$realOtTime = 0;
							$targetDate = $this->checkDate." ".$pbSheet->aCheckEndTime;
						}
						else
						{
							$realOtTime = $otHours;
							$targetDate = $this->checkDate." ".$pbSheet->otStartTime;
						}
						$newCheckTime = date("Y-m-d H:i:s", strtotime($targetDate) + $realOtTime*$this->secondOfHour);
						$this->checkOutTime = substr($newCheckTime, 0, 15).$minNumber;
					}
					else
					{
						$this->jbTime = 0;
						
						if(strtotime($this->checkOutTime) - strtotime($this->checkDate." ".$pbSheet->aCheckEndTime) > 15*30)
						{
							$minNumber = substr($this->checkOutTime, 15, 4);
							$this->checkOutTime = substr($this->checkDate." ".$pbSheet->aCheckEndTime, 0, 15).$minNumber;
						}
					}
					break;
					case "X":
					case "Y":
						$minNumber = substr($this->checkOutTime, 15, 4);
						
						$realOtTime = ($otHours-8 <= 0)?0:$otHours-8;
						if($realOtTime >= 0)
						{
							if($realOtTime == 0)
							{
								$targetDate = $this->checkDate." ".$pbSheet->aCheckEndTime;
							}
							else
							{
								$realOtTime = $otHours;
								$targetDate = $this->checkDate." ".$pbSheet->otStartTime;
							}
							$newCheckTime = date("Y-m-d H:i:s", strtotime($targetDate) + $realOtTime*$this->secondOfHour);
							$this->checkOutTime = substr($newCheckTime, 0, 15).$minNumber;
						}
						
					break;
				}
			
			//妇女节判定
			$this->womensDay($number, $DataIn, $DataPublic, $link_id);
			
		}
				
		private function getTime($checkTime, $pbSheet)
		{
			$sheet = $pbSheet->returnSheet();
			$time = $checkTime;
			foreach($sheet as $sheetTime)
			{
				$targetTime = $this->checkDate." ".$sheetTime;
				if($startTime < $targetTime)
				{
					$startTime = $targetTime;
					break;
				}
			}
			
			return $time;
		}
		
		private function workHours($standardStart, $standardEnd, $startTime, $endTime)
		{
			$standardStartDate = $this->checkDate." ".$standardStart;
			$standardEndDate = $this->checkDate." ".$standardEnd;
			
			$startHolder = "";
			$endHolder = "";
			
			if(strtotime($startTime) >= strtotime($endTime))
			{
				return "error";
			}
			
			//确定开始时间
			if(strtotime($standardEndDate) < strtotime($startTime))
			{
				$startHolder = $standardEndDate;
			}
			else if(strtotime($standardStartDate) < strtotime($startTime) && strtotime($standardEndDate) > strtotime($startTime))
			{
				$startHolder = $this->rounding_in($startTime);
			}
			else
			{
				$startHolder = $standardStartDate;
			}
			
			//确定结束时间
			if(strtotime($standardStartDate) >= strtotime($endTime))
			{
				$endHolder = $standardStartDate;
			}
			else if(strtotime($standardStartDate) < strtotime($endTime) && strtotime($standardEndDate) > strtotime($endTime))
			{
				$endHolder = $this->rounding_out($endTime);
			}
			else
			{
				$endHolder = $standardEndDate;
			}
			
			$workHours = (strtotime($endHolder) - strtotime($startHolder))/$this->secondOfHour;
			return $workHours;
			
		}
		
		private function leaveHours($number, $checkDate, $pbSheet, $DataIn, $DataPublic, $link_id)
		{
			$qjResult1 = mysql_query("SELECT StartDate,EndDate,Type FROM $DataPublic.kqqjsheet WHERE Number=$number and ('$checkDate' between left(StartDate,10) and left(EndDate,10))",$link_id);
			while($qjRow1 = mysql_fetch_assoc($qjResult1))
			{
				$StartDate=$qjRow1["StartDate"];
				$EndDate=$qjRow1["EndDate"];
				$qjType=$qjRow1["Type"];
				
				$partOne = $this->workHours($pbSheet->mCheckStartTime, $pbSheet->mCheckEndTime, $StartDate, $EndDate);
				$partTwo = $this->workHours($pbSheet->aCheckStartTime, $pbSheet->aCheckEndTime, $StartDate, $EndDate);
				
				$this->setLeaveTime($partOne+$partTwo, $qjType);
			}
		}
		
		private function setLeaveTime($leaveTime, $qjType)
		{
			switch($qjType)
			{
				case "1":
				{
					$this->privateLeave += $leaveTime;
				}
				break;
				case "2":
				{
					$this->sickLeave += $leaveTime;
				}
				break;
				case "3":
				{
					$this->noWageLeave += $leaveTime;
				}
				break;
				case "4":
				{
					$this->annualLeave += $leaveTime;
				}
				break;
				case "5":
				{
					$this->notBusyLeave += $leaveTime;
				}
				break;
				case "6":
				{
					$this->marriageLeave += $leaveTime;
				}
				break;
				case "7":
				{
					$this->funeralLeave += $leaveTime;
				}
				break;
				case "8":
				{
					$this->maternityLeave += $leaveTime;
				}
				break;
				case "9":
				{
					$this->injuryLeave += $leaveTime;
				}
				break;
			}
		}
		
		private function setquitTime($number, $DataIn, $DataPublic, $link_id)
		{
			$tmpSumLeaveHours = $this->privateLeave + $this->sickLeave + $this->noWageLeave + $this->annualLeave + $this->notBusyLeave + $this->marriageLeave + $this->funeralLeave + $this->maternityLeave + $this->injuryLeave;
			
			$tmpAbsenteeismHours = $this->workTime - $this->realWorkTime - $tmpSumLeaveHours;
			$this->absenteeismHours = $tmpAbsenteeismHours<0?"0":$tmpAbsenteeismHours;
			if($tmpSumLeaveHours > 0)
			{
				if($this->absenteeismHours == 0)
				{
					$this->beLate = 0;
					$this->leaveEarly = 0;
				}
				else if($this->absenteeismHours < 1)
				{
					$this->absenteeismHours = 0;
				}
				else
				{
					$this->beLate = 0;
					$this->leaveEarly = 0;
				}
			}
			else
			{
				$this->absenteeismHours = $this->workTime - $this->realWorkTime;
				if($this->absenteeismHours == 8)
				{
					$this->absenteeismHours = 0;
					$this->queQingHours = 8;
				}
				else 
				{
					if($this->absenteeismHours ==4 && $this->realWorkTime == 4)
					{
						$this->absenteeismHours = 0;
						$this->queQingHours = 4;
					}
					else
					{
						if($this->absenteeismHours < 1)
						{
							$this->absenteeismHours = 0;
						}
						else
						{
							$this->beLate = 0;
							$this->leaveEarly = 0;
						}
					}
				}
				
			}
			
			//计算抵扣工时
			$CheckDate = $this->checkDate;
			$rqddResult = mysql_query("SELECT Id,dkHour FROM $DataPublic.staff_dkdate WHERE Number='$number' AND dkDate='$CheckDate'  LIMIT 1",$link_id);
			if($rqddRow = mysql_fetch_array($rqddResult))
			{
				$this->payHours = $rqddRow["dkHour"];
				if($this->payHours > 0)
				{
					$this->absenteeismHours = $this->absenteeismHours - $this->payHours;
				}
			}
		}
			
		private function womensDay($number, $DataIn, $DataPublic, $link_id)
		{
			if (date("m-d",strtotime($this->checkDate))=="03-08")
			{
				$womenResult = mysql_query("SELECT Id FROM $DataPublic.staffsheet WHERE Number=$number AND Sex=0 LIMIT 1",$link_id);
				if($womenRow=mysql_fetch_array($womenResult))
				{
					
				}
			}
		}
		
		private function rounding_in($AITemp)
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

		private function rounding_out($AOTemp)
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

			
		public function returnCheckInTime()
		{
			return substr($this->checkInTime, 11, 5);
		}
		
		public function returnCheckOutTime()
		{
			return substr($this->checkOutTime, 11, 5);
		}
			
	}
	
?>