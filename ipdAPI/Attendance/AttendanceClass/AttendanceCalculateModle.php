<?php

	interface AttendanceCalculateInterface
	{
		function attendanceSetup($DataIn, $DataPublic, $link_id);
		function getWorkSchedule($DataIn, $DataPublic, $link_id);
		function getDateType($DataIn, $DataPublic, $link_id);
		function getWorkHours($DataIn, $DataPublic, $link_id);
		function getOverTimeHours($DataIn, $DataPublic, $link_id);
		function getLeaveHours($DataIn, $DataPublic, $link_id);
		function attendanceStatistic($DataIn, $DataPublic, $link_id);
	}



?>