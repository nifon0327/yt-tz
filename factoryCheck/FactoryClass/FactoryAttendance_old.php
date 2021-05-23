<?php
$path = $_SERVER["DOCUMENT_ROOT"];
include_once "$path/basic/parameter.inc";
include_once("$path/ipdAPI/Attendance/AttendanceClass/StaffAvatar.php");
include_once("$path/ipdAPI/Attendance/AttendanceClass/AttendanceCalculateModle.php");
include_once("$path/ipdAPI/Attendance/AttendanceClass/WorkSchedule.php");

class FactoryAttendanceOld extends StaffAvatar{
    private $targetDate;
    private $startTime;
    private $endTime;
    private $weekDay;
    private $workSchedule;

    private $defaultWorkHours = 0;
    private $overHours = 2;
    private $workHours = 0;
    private $workOtTime = 0;
    private $workOverTime = 0;
    private $weekOtTime = 0;
    private $weekOverTime = 0;
    private $holidayOtTime = 0;
    private $holidayOverTime = 0;

    private $workZlHours = 0;
    private $weekZlHours = 0;
    private $holidayZlHours = 0;
    private $dkHours = 0;
    private $noPayHours = 0;

    private $leaveArray = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0);
    private $totleLeaveHours = 0;
    private $late = 0;private $beLateStandard;
    private $early = 0;private $beEarlyStandard;
    private $secondOfHour = 3600;
    private $lackWorkHours = 0;//缺勤工时
    private $kgHours = 0;//旷工工时
    private $KrSign;
    private $nightShit = 0;

    
}

?>