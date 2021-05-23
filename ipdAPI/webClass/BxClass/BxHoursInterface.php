<?php

	interface BxHoursInterface
	{
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


	}



?>