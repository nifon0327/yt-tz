<?php
	$path = $_SERVER["DOCUMENT_ROOT"];
	include "$path/basic/parameter.inc";

	class WorkScheduleSheet
	{

		public $mCheckTime;
		public $aCheckTime;
		public $otTime;
		public $noonRestTime;
		public $aRestTime;

		public function __construct($number, $ToDay, $startTime, $endTime)
		{
			//获取工作地点，用来判断上班时段
			$workAddressSql = "SELECT AttendanceFloor From staffmain Where Number=$number";
			$workAddressResult = mysql_query($workAddressSql);
			$workAddRow = mysql_fetch_assoc($workAddressResult);
			$workAdd = $workAddRow['AttendanceFloor'];

			if($workAdd == 6 || $workAdd == 12) {
				$this->mCheckTime = array("start"=>"08:00", "end"=>"12:00", "state"=>"");
				$this->aCheckTime = array("start"=>"13:30", "end"=>"17:30", "state"=>"");
				$this->otStartTime = array("start" => "18:00", "state"=>"");
			}else{



			$lsbResult = mysql_query("SELECT InTime,OutTime,InLate,OutEarly,TimeType,RestTime FROM $DataIn.kqlspbb WHERE Number=$number and left(InTime,10)='$ToDay' order by Id Limit 1");
			if($number == '11924'  or $number == '10143' or $number == '10487' or $number == '10106' or $number == '11624'){
				$startHour = substr($startTime, 11,2);
				$endHour = substr($endTime, 11,2);
				$lsbRow = mysql_fetch_assoc($lsbResult);
				if($lsbResult && mysql_num_rows($lsbResult) >0 && strtotime($lsbRow["InTime"]) < strtotime($lsbRow["OutTime"])){

					$tmpStart = substr($lsbRow["InTime"], 11, 5);
					$tmpEnd   = substr($lsbRow["OutTime"], 11, 5);

					if(strtotime($tmpStart) < strtotime("12:00")){
						$this->mCheckTime = array("start"=>substr($lsbRow["InTime"], 11, 5), "end"=>"12:00", "state"=>"");
						$this->aCheckTime = array("start"=>"13:00", "end"=>substr($lsbRow["OutTime"], 11, 5), "state"=>"");
						$this->otStartTime = array("start" => substr($lsbRow["OutTime"], 11, 5), "state"=>"");
					}else{
						//临时排班开始时间大于12点
						$this->mCheckTime = array("start"=>substr($lsbRow["InTime"], 11, 5), "end"=>substr($lsbRow["InTime"], 11, 5), "state"=>"");
						$this->aCheckTime = array("start"=>substr($lsbRow["InTime"], 11, 5), "end"=>substr($lsbRow["OutTime"], 11, 5), "state"=>"");
						$this->otStartTime = array("start" => substr($lsbRow["OutTime"], 11, 5), "state"=>"");
					}
					
				}
				else if(substr($startTime, 0, 10) != substr($endTime, 0, 10) && $startHour >= 18){
				
					$this->mCheckTime = array("start"=>"20:00", "end"=>"23:00", "state"=>"");
					$this->aCheckTime = array("start"=>"23:00", "end"=>"04:00", "state"=>"");
					$this->otStartTime = array("start"=>"04:00", "state"=>"");
				}else{
					$this->mCheckTime = array("start"=>"08:00", "end"=>"12:00", "state"=>"");
					$this->aCheckTime = array("start"=>"13:00", "end"=>"17:00", "state"=>"");
					$this->otStartTime = array("start" => "18:00", "state"=>"");
				}
			}
			else if($lsbResult && $lsbRow = mysql_fetch_array($lsbResult)){
				$this->mCheckTime = array("start"=>substr($lsbRow["InTime"], 11, 5), "end"=>"12:00", "state"=>"");
				$this->aCheckTime = array("start"=>"13:00", "end"=>substr($lsbRow["OutTime"], 11, 5), "state"=>"");
				$this->otStartTime = array("start" => "18:00:00", "state"=>"");
			}
			else{
				$this->mCheckTime = array("start"=>"08:00", "end"=>"12:00", "state"=>"");
				$this->aCheckTime = array("start"=>"13:00", "end"=>"17:00", "state"=>"");
				$this->otStartTime = array("start" => "18:00:00", "state"=>"");
			}
			}
		}

		private function setupRestTime()
		{
			$this->noonRestTime = (strtotime($this->aCheckStartTime) - strtotime($this->mCheckEndTime))/60;
			$this->aRestTime = (strtotime($this->otStartTime) - strtotime($this->aCheckEndTime))/60;
			
		}

		public function setDefault(){
			$this->mCheckTime = array("start"=>"08:00", "end"=>"12:00", "state"=>"");
			$this->aCheckTime = array("start"=>"13:00", "end"=>"17:00", "state"=>"");
			$this->otStartTime = array("start" => "18:00:00", "state"=>"");
		}

		public function setWorkDayType()
		{
			
		}

	}


?>