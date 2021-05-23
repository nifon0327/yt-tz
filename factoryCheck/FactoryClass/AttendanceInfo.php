<?php
class AttendanceInfo{

    private $defaultWorkHours = 0;
    private $workHours = 0;
    private $workdayOt = 0;
    private $weekdayOt = 0;
    private $holidayOt = 0;
    private $dkHours = 0;
    private $noPayHours = 0;

    private $leaveArray = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0);
    private $totleLeaveHours = 0;
    private $late = 0;private $beLateStandard;
    private $early = 0;private $beEarlyStandard;
    private $secondOfHour = 3600;
    private $lackWorkHours = 0;//缺勤工时
    private $kgHours = 0;//旷工工时

    public function statistic($infomation)
    {
        $this->defaultWorkHours += $infomation["defaultWorkHours"];
        $this->workHours += $infomation["workHours"];
        $this->workdayOt += $infomation["workdayOt"];
        $this->weekdayOt += $infomation["weekdayOt"];
        $this->holidayOt += $infomation["holidayOt"];
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

    function outputByTag($type=true){
        return array("defaultWorkHours"=> $this->spaceInsteadZero($this->defaultWorkHours, $type),
                     "workHours"=> $this->spaceInsteadZero($this->workHours, $type),
                     "workdayOt"=>$this->spaceInsteadZero($this->workdayOt, $type),
                     "weekdayOt"=>$this->spaceInsteadZero($this->weekdayOt, $type),
                     "holidayOt"=>$this->spaceInsteadZero($this->holidayOt, $type),
                     "beLate"=>$this->spaceInsteadZero($this->late, $type),
                     "beEarly"=>$this->spaceInsteadZero($this->early, $type),
                     "personalLeave"=>$this->spaceInsteadZero($this->leaveArray["1"], $type),
                     "sickLeave"=>$this->spaceInsteadZero($this->leaveArray["2"], $type),
                     "noPayLeave"=>$this->spaceInsteadZero($this->leaveArray["3"], $type),
                     "annualLeave"=>$this->spaceInsteadZero($this->leaveArray["4"], $type),
                     "bxLeave"=>$this->spaceInsteadZero($this->leaveArray["5"], $type),
                     "marrayLeave"=>$this->spaceInsteadZero($this->leaveArray["6"], $type),
                     "deadLeave"=>$this->spaceInsteadZero($this->leaveArray["7"], $type),
                     "birthLeave"=>$this->spaceInsteadZero($this->leaveArray["8"], $type),
                     "hurtLeave"=>$this->spaceInsteadZero($this->leaveArray["9"], $type),
                     "lackWorkHours"=>$this->spaceInsteadZero($this->lackWorkHours, $type),
                     "kgHours"=>$this->spaceInsteadZero($this->kgHours, $type),
                     "noPayHours"=>$this->spaceInsteadZero($this->noPayHours, $type),
                     "dkHours"=>$this->spaceInsteadZero($this->dkHours, $type));
    }

    function spaceInsteadZero($number, $type){
        if(!$type){
            return $number == ""?'0':$number."";
        }
        else{
            return ($number=="0" )?"":$number."";
        }
    }
}
?>