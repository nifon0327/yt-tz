<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/prototype/StaffPrototype.php");
	include_once("$path/model/kq_YearHolday.php");
	
	class StaffBxStatisticsItem extends StaffPrototype
	{
		private $totleBxHours;
		private $usedBxHours;
		private $leftBxHours;
		
		public function setupStatisticBxItem($statisticItem, $DataIn, $DataPublic, $link_id)
		{ 
			$this->number = $statisticItem["Number"];
			$this->totleBxHours = $statisticItem["hours"];
			
			//计算已用补休
			$this->usedBxHours = $this->calculateUseHours($this->number, $DataIn, $DataPublic, $link_id);
			$this->leftBxHours = $this->totleBxHours - $this->usedBxHours;
		}
		
		public function calculateUseHours($Number,$DataIn,$DataPublic,$link_id)
		{
			$usedBx = 0;
			$bxQjCheckSql = "Select * From $DataPublic.kqqjsheet Where Number = '$Number' and Type= '5' AND DATE_FORMAT(StartDate,'%Y')>='2013'";
			$bxQjCheckResult = mysql_query($bxQjCheckSql);
			while($bxQjCheckRow = mysql_fetch_assoc($bxQjCheckResult))
			{
				$startTime = $bxQjCheckRow["StartDate"];
				$endTime = $bxQjCheckRow["EndDate"];
				$bcType = $bxQjCheckRow["bcType"];
				
				$time = GetBetweenDateDays($Number,$startTime,$endTime,$bcType,$DataIn,$DataPublic,$link_id);
				$usedBx += $time;
			}
			
			return $usedBx;
		}
		
		public function getTotleBxHours()
		{
			return $this->totleBxHours;
		}
		
		public function getUsedBxHours()
		{
			return $this->usedBxHours;
		}
		
		public function getLeftBxHours()
		{
			return $this->leftBxHours;
		}
		
		
		public function __clone(){}
		
	}
	
?>