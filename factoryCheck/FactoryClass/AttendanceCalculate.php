<?php
class AttendanceCalculate{
    private $dataIn;
    private $dataPublic;
    private $linkId;

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

    function __construct($DataIn, $DataPublic, $link_id){
        $this->dataIn = $DataIn;
        $this->dataPublic = $DataPublic;
        $this->linkId = $link_id;
    }

    public function calculateTime($number, $start, $end, $sheet, $targetDate, $datetype){
        $this->defaultWorkHours += $datetype['morning'] == 'G'?4:0;
        $this->defaultWorkHours += $datetype['afternoon'] == 'G'?4:0;
        $this->workHours = $this->workHours($sheet->mCheckTime['start'], $sheet->mCheckTime['end'], $start, $end, $targetDate) + $this->workHours($sheet->aCheckTime['start'], $sheet->aCheckTime['end'], $start, $end, $targetDate);
        $otTime = $this->getOverTimeHours($end, $sheet, $targetDate);
        switch ($datetype['night']) {
            case 'G':
                $this->workdayOt = $otTime;
                break;
            case 'X':
                $this->weekdayOt = $otTime;
                break;
            case 'F':
                $this->holidayOt = $otTime;
                break;
        }
        if($datetype['afternoon'] == 'G' || $datetype['morning'] == 'G'){
            $this->getLeaveHours($number, $targetDate, $sheet);
        }
        $this->attendanceStatistic($number, $targetDate, $datetype);
        return $this->outputByTag();
    }

    private function workHours($standardStart, $standardEnd, $startTime, $endTime, $targetDate){
        $standardStartDate = $targetDate." ".$standardStart;
        $standardEndDate = $targetDate." ".$standardEnd;
        $startHolder = "";
        $endHolder = "";
        if(strtotime($standardStart) > strtotime($standardEnd)){
            $standardEndDate = date('Y-m-d', strtotime($targetDate) + 24*3600)." ".$standardEnd;
        }

        //确定开始时间
        if(strtotime($standardEndDate) <= strtotime($startTime)){
            $startHolder = $standardEndDate;
        }
        else if(strtotime($standardStartDate) < strtotime($startTime) && strtotime($standardEndDate) > strtotime($startTime)){
            $startHolder = $this->rounding_in($startTime);
        }
        else{
            $startHolder = $standardStartDate;
        }
        
        //确定结束时间
        if(strtotime($standardStartDate) >= strtotime($endTime)){
            $endHolder = $standardStartDate;
        }
        else if(strtotime($standardStartDate) < strtotime($endTime) && strtotime($standardEndDate) > strtotime($endTime)){
            $endHolder = $this->rounding_out($endTime);
        }
        else{
            $endHolder = $standardEndDate;
        }
        $workHours = (strtotime($endHolder) - strtotime($startHolder))/$this->secondOfHour;
        //echo $endHolder.' '.$startHolder;
        return $workHours<0?0:$workHours;
    }

    public function getOverTimeHours($end, $sheet, $targetDate){
        if(strtotime($sheet->mCheckTime["start"]) > strtotime($sheet->aCheckTime["end"])){
            $calculateDate = date('Y-m-d', strtotime($targetDate) + 24*3600);
        }
        else{
            $calculateDate = $targetDate;
        }

        $otTime = (strtotime($this->rounding_out($end)) - strtotime($calculateDate." ".$sheet->otStartTime["start"]))/3600;
        $otTime = $otTime<0?0:$otTime;
        return $otTime;
    }

    private function rounding_in($AITemp){
        //向上取整处理
        $m_Temp=substr($AITemp,14,2);//取分钟
        if($m_Temp!=0 && $m_Temp!=30){
            if($m_Temp<30){
                $m_Temp=30-$m_Temp;
            }
            else{
                $m_Temp=60-$m_Temp;
            }
        }
        else{
            $m_Temp=0;
        }
        $ChickIn=date("Y-m-d H:i:00",strtotime("$AITemp")+$m_Temp*60);
        return $ChickIn;
    }

    private function rounding_out($AOTemp){
        //向下取整处理
        $m_Temp=substr($AOTemp,14,2);//取分钟
        if($m_Temp!=0 && $m_Temp!=30){
            if($m_Temp<30){
                $m_Temp=0;
            }
            else{
                $m_Temp=30;
            }
        }
        $m_Temp=$m_Temp==0?":00":":30";
        $ChickOut=substr($AOTemp,0,13).$m_Temp.":00";
        return $ChickOut;
    }

    public function getLeaveHours($number, $targetDate, $sheet){
        //$totleLeavHours = 0;
        $leaveTime = array();
        $qjResult = mysql_query("SELECT StartDate,EndDate,Type FROM ".$this->dataPublic.".kqqjsheet WHERE Number=".$number." and ('".$targetDate."' between left(StartDate,10) and left(EndDate,10))",$this->linkId);
        while($qjRow = mysql_fetch_assoc($qjResult)){
            $leaveStartDate = $qjRow["StartDate"];
            //规范化时间
            if(strtotime($leaveStartDate) == strtotime($targetDate.' '.$sheet->aCheckTime['start'])){
                    $leaveStartDate = $targetDate.' '.$sheet->mCheckTime['end'];
            }

            if(strtotime($leaveStartDate) < strtotime($targetDate.' '.$sheet->mCheckTime['start'])){
                $leaveTime[] = $targetDate.' '.$sheet->mCheckTime['start'];
            }
            else{
                $leaveTime[] = $leaveStartDate;
            }

            $leaveEndDate = $qjRow["EndDate"];
            if(strtotime($leaveEndDate) == strtotime($targetDate.' '.$sheet->mCheckTime['end'])){
                    $leaveEndDate = $targetDate.' '.$sheet->aCheckTime['start'];
                }

            if(strtotime($leaveEndDate) > strtotime($targetDate.' '.$sheet->mCheckTime['end'])){
                $leaveTime[] = $targetDate.' '.$sheet->aCheckTime['end'];
            }
            else{
                $leaveTime[] = $leaveEndDate;
            }

            $leaveType = $qjRow["Type"];
            //计算上午请假时间
            $mLeaveHours = $this->workHours($sheet->mCheckTime["start"], $sheet->mCheckTime["end"], $leaveStartDate, $leaveEndDate, $targetDate);
            $this->leaveArray[$leaveType] += $mLeaveHours;
            $this->totleLeaveHours += $mLeaveHours;
            
            //计算下午请假时间
            $aLeaveHours = $this->workHours($sheet->aCheckTime["start"], $sheet->aCheckTime["end"], $leaveStartDate, $leaveEndDate, $targetDate);
            $this->leaveArray[$leaveType] += $aLeaveHours;
            $this->totleLeaveHours += $aLeaveHours;
        }

        sort($leaveTime);
        $dayLeaveStart = $leaveTime[0];
        $dayLeaveEnd = $leaveTime[count($leaveTime)-1];

        if($this->totleLeaveHours == 8){
            $this->beLateStandard = $targetDate.' '.$sheet->mCheckTime['start'];
            $this->beEarlyStandard = $targetDate.' '.$sheet->aCheckTime['end'];
        }
        else if(strtotime($dayLeaveStart) == strtotime($targetDate.' '.$sheet->mCheckTime['start'])){
            $this->beLateStandard = $dayLeaveEnd;
            $this->beEarlyStandard = $targetDate.' '.$sheet->aCheckTime['end'];
        }
        else if(strtotime($dayLeaveEnd) == strtotime($targetDate.' '.$sheet->aCheckTime['end'])){
            $this->beLateStandard = $targetDate.' '.$sheet->mCheckTime['start'];
            $this->beEarlyStandard = $dayLeaveStart;
        }
    }

    private function attendanceStatistic($number, $targetDate, $datetype){
        //工时抵扣
        $rqddResult = mysql_query("SELECT Id,dkHour FROM ".$this->dataPublic.".staff_dkdate WHERE Number='".$number."' AND dkDate='".$targetDate."'  LIMIT 1",$this->linkId);
        if($rqddRow = mysql_fetch_assoc($rqddResult)){
            $this->dkHours = $rqddRow["dkHour"];
        }
        if($datetype['morning'] === "G" || $datetype['afternoon'] === 'G'){
            $this->lackWorkHours = $this->defaultWorkHours - $this->workHours - $this->totleLeaveHours - $this->dkHour;
            if($this->lackWorkHours == $this->defaultWorkHours){
                $this->kgHours = $this->defaultWorkHours;
                $this->lackWorkHours = '';
            }
        }

        //判断迟到早退
        if($this->beLateStandard !="" && $this->startTime != '' && strtotime($this->startTime) > strtotime($this->beLateStandard)){
            $this->late = 1;
        }

        if($this->beEarlyStandard !="" && $this->endTime != '' && strtotime($this->endTime) < strtotime($this->beEarlyStandard)){
            $this->early = 1;
        }

        if($this->kgHours == $this->defaultWorkHours){
            $this->late = '';
            $this->early = '';
        }
    }

    public function setOutOfWorkState($datetype){
        foreach ($datetype as $key => $value) {
            if($key != 'night'){
                switch($datetype[$key]){
                    case "F":
                        $this->noPayHours+=4;$this->defaultWorkHours+=4;
                        break;
                    case "X":
                        $this->noPayHours=0;$this->workHours=0;
                        break;
                    default:
                        $this->noPayHours+=4;$this->defaultWorkHours+=4;
                        break;
                }
            }
        }  
        return $this->outputByTag();     
    }

    private function outputByTag($type=true){

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