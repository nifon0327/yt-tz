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

		public function __construct($number, $ToDay, $startTime='', $endTime='')
		{
			$lsbResult = mysql_query("SELECT InTime,OutTime,InLate,OutEarly,TimeType,RestTime FROM $DataIn.kqlspbb WHERE Number=$number and left(InTime,10)='$ToDay' order by Id Limit 1");
			if($number == '11923' or $number == '10744'){
				if(substr($startTime, 0, 10) == substr($endTime, 0, 10)){
					$this->mCheckTime = array("start"=>"08:00", "end"=>"12:00", "state"=>"");
					$this->aCheckTime = array("start"=>"13:00", "end"=>"17:00", "state"=>"");
					$this->otStartTime = array("start" => "18:00", "state"=>"");
				}
				else{
					$this->mCheckTime = array("start"=>"20:00", "end"=>"23:00", "state"=>"");
					$this->aCheckTime = array("start"=>"23:00", "end"=>"04:00", "state"=>"");
					$this->otStartTime = array("start" => "04:00", "state"=>"");
				}
			}
			else if($lsbResult && $lsbRow = mysql_fetch_array($lsbResult)){
				$this->mCheckStartTime = $lsbRow["InTime"];
				$this->aCheckEndTime = $lsbRow["OutTime"];
			}
			else{
				$this->mCheckTime = array("start"=>"08:00", "end"=>"12:00", "state"=>"");
				$this->aCheckTime = array("start"=>"13:00", "end"=>"17:00", "state"=>"");
				$this->otStartTime = array("start" => "18:00:00", "state"=>"");
			}

		}

		private function setupRestTime()
		{
			$this->noonRestTime = (strtotime($this->aCheckStartTime) - strtotime($this->mCheckEndTime))/60;
			$this->aRestTime = (strtotime($this->otStartTime) - strtotime($this->aCheckEndTime))/60;
			
		}


		public function setWorkDayType()
		{}

	}


?>