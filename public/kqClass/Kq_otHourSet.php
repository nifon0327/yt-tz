<?php
	
	class KqOtHourSet
	{
		public $gHours;
		public $xHours;
		public $fHours;
		public $zlHours;
		public $date;
		
		function __construct($Number, $date, $DataIn, $DataPublic,$link_id)
		{
			$otHoursSql = "Select workDay, weekDay, holiday From $DataIn.kqovertime Where otDate = '$date'";
			$otHoursResult = mysql_query($otHoursSql);
			if($otRows = mysql_fetch_assoc($otHoursResult))
			{
				$this->gHours = $otRows["workDay"];
				$this->xHours = $otRows["weekDay"];
				$this->fHours = $otRows["holiday"];
			}	
			
			$zlResult = mysql_query("SELECT SUM(Hours) AS  Hours FROM $DataPublic.kqzltime  WHERE Number=$Number and Date='$date'",$link_id);
			if($zlResultRow = mysql_fetch_assoc($zlResult))
			{
				$this->zlHours = ($zlResultRow["Hours"] == "")?"0":$zlResultRow["Hours"];
			}
			
		}
		
		public function getOtHours($dateType)
		{
			$otHours = "0";
			switch($dateType)
			{
				case "G":
				{
					$otHours = $this->gHours;
				}
				break;
				case "X":
				case "Y":
				{
					$otHours = $this->xHours;
				}
				break;
				case "F":
				{
					$otHours = $this->fHours;
				}
				break;
			}
			
			return $otHours;
		}
		
		
	}
	
	
?>