<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/public/kqClass/Kq_dailyItem.php");
	class KqTotleItem extends KqDateItem
	{
		
		function __construct()
		{}
		
		public function summary(KqDailyItem $dailyItem)
		{
			$this->workTime += $dailyItem->workTime;
			$this->realWorkTime += $dailyItem->realWorkTime;
			$this->jbTime += $dailyItem->jbTime;
			$this->sxTime += $dailyItem->sxTime;
			$this->jrTime += $dailyItem->jrTime;
			$this->beLate += $dailyItem->beLate;
			$this->leaveEarly += $dailyItem->leaveEarly;
			$this->privateLeave += $dailyItem->privateLeave;
			$this->sickLeave += $dailyItem->sickLeave;
			$this->noWageLeave += $dailyItem->noWageLeave;
			$this->annualLeave += $dailyItem->annualLeave;
			$this->notBusyLeave += $dailyItem->notBusyLeave;
			$this->marriageLeave += $dailyItem->marriageLeave;
			$this->funeralLeave += $dailyItem->funeralLeave;
			$this->maternityLeave += $dailyItem->maternityLeave;
			$this->injuryLeave += $dailyItem->injuryLeave;
			$this->absenteeismHours += $dailyItem->absenteeismHours;
			$this->queQingHours += $dailyItem->queQingHours;
			$this->nightShit += $dailyItem->nightShit;
			$this->nopayHours += $dailyItem->nopayHours;
			$this->payHours += $dailyItem->payHours;
		}
		
	}
	
	
?>