<?php

	class AttendanceStatistic
	{
		private $defaultWorkHours = 0;
		private $workHours = 0;
		private $workOtTime = 0;
		private $weekOtTime = 0;
		private $holidayOtTime = 0;

		private $workZlHours = 0;
		private $weekZlHours = 0;
		private $holidayZlHours = 0;
		private $dkHours = 0;
		private $noPayHours = 0;

		private $leaveArray = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0);
		private $late = 0;
		private $early = 0;

		private $lackWorkHours = 0;//缺勤工时
		private $kgHours = 0;//旷工工时
		private $nightShit = 0;

		public function statistic($infomation)
		{
			$this->defaultWorkHours += $infomation["defaultWorkHours"];
			$this->workHours += $infomation["workHours"];
			$this->workOtTime += $infomation["workOtHours"];
			$this->workZlHours += $infomation["workZlHours"];

			$this->weekOtTime += $infomation["weekOtTime"];
			$this->weekZlHours += $infomation["weekZlHours"];

			$this->holidayOtTime += $infomation["holidayOtHours"];
			$this->holidayZlHours += $infomation["holidayZlHours"];

			$this->late += $infomation["beLate"];
			$this->early += $infomation["beEarly"];

			$this->leaveArray["1"] += $infomation["personalLeave"];
			$this->leaveArray["2"] += $infomation["sickLeave"];
			$this->leaveArray["3"] += $infomation["noPayLeave"];
			$this->leaveArray["4"] += $infomation["annualLeave"];
			$this->leaveArray["5"] += $infomation["bxLeave"];
			$this->leaveArray["6"] += $infomation["marrayLeave"];
			$this->leaveArray["7"] += $infomation["deadLeave"];
			$this->leaveArray["8"] += $infomation["birthLeave"];
			$this->leaveArray["9"] += $infomation["hurtLeave"];

			$this->lackWorkHours += $infomation["lackWorkHours"];
			$this->kgHours += $infomation["kgHours"];
			$this->nightShit += $infomation["nightShit"];
			$this->noPayHours += $infomation["noPayHours"];
			$this->dkHours += $infomation["dkHours"];

		}

		public function getStatisticByTag(){
			return array(
						"defaultWorkHours"=> $this->spaceInsteadZero($this->defaultWorkHours.""),
							"workHours"=> $this->spaceInsteadZero($this->workHours.""),
							"workOtHours"=>$this->spaceInsteadZero($this->workOtTime.""),
							"workZlHours"=>$this->spaceInsteadZero($this->workZlHours.""),
							"weekOtTime"=>$this->spaceInsteadZero($this->weekOtTime.""),
							"weekZlHours"=>$this->spaceInsteadZero($this->weekZlHours.""),
							"holidayOtHours"=>$this->spaceInsteadZero($this->holidayOtTime.""),
							"holidayZlHours"=>$this->spaceInsteadZero($this->holidayZlHours.""),
							"beLate"=>$this->spaceInsteadZero($this->late.""),
							"beEarly"=>$this->spaceInsteadZero($this->early.""),
							"personalLeave"=>$this->spaceInsteadZero($this->leaveArray["1"].""),
							"sickLeave"=>$this->spaceInsteadZero($this->leaveArray["2"].""),
							"noPayLeave"=>$this->spaceInsteadZero($this->leaveArray["3"].""),
							"annualLeave"=>$this->spaceInsteadZero($this->leaveArray["4"].""),
							"bxLeave"=>$this->spaceInsteadZero($this->leaveArray["5"].""),
							"marrayLeave"=>$this->spaceInsteadZero($this->leaveArray["6"].""),
							"deadLeave"=>$this->spaceInsteadZero($this->leaveArray["7"].""),
							"birthLeave"=>$this->spaceInsteadZero($this->leaveArray["8"].""),
							"hurtLeave"=>$this->spaceInsteadZero($this->leaveArray["9"].""),
							"lackWorkHours"=>$this->spaceInsteadZero($this->lackWorkHours.""),
							"kgHours"=>$this->spaceInsteadZero($this->kgHours.""),
							"nightShit"=>$this->spaceInsteadZero($this->nightShit.""),
							"noPayHours"=>$this->spaceInsteadZero($this->noPayHours.""),
							"dkHours"=>$this->spaceInsteadZero($this->dkHours."")
				);
		} 

		public function getInfomationByArray(){
			return array($this->spaceInsteadZero($this->defaultWorkHours),
							$this->spaceInsteadZero($this->workHours),
							$this->spaceInsteadZero($this->workOtTime),
							$this->spaceInsteadZero($this->workZlHours),
							$this->spaceInsteadZero($this->weekOtTime),
							$this->spaceInsteadZero($this->weekZlHours),
							$this->spaceInsteadZero($this->holidayOtTime),
							$this->spaceInsteadZero($this->holidayZlHours),
							$this->spaceInsteadZero($this->late),
							$this->spaceInsteadZero($this->early),
							$this->spaceInsteadZero($this->leaveArray["1"]),
							$this->spaceInsteadZero($this->leaveArray["2"]),
							$this->spaceInsteadZero($this->leaveArray["3"]),
							$this->spaceInsteadZero($this->leaveArray["4"]),
							$this->spaceInsteadZero($this->leaveArray["5"]),
							$this->spaceInsteadZero($this->leaveArray["6"]),
							$this->spaceInsteadZero($this->leaveArray["7"]),
							$this->spaceInsteadZero($this->leaveArray["8"]),
							$this->spaceInsteadZero($this->leaveArray["9"]),
							$this->spaceInsteadZero($this->lackWorkHours),
							$this->spaceInsteadZero($this->kgHours),
							$this->spaceInsteadZero($this->nightShit),
							$this->spaceInsteadZero($this->noPayHours),
							$this->spaceInsteadZero($this->dkHours));
		}

		private function spaceInsteadZero($number){
			return ($number=="0")?0:$number."";
		}

	}



