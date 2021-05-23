<?php
$path = $_SERVER["DOCUMENT_ROOT"];
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");
class AttendanceDatetype{
    private $dataIn;
    private $dataPublic;
    private $linkId;

    function __construct($DataIn, $DataPublic, $link_id){
        $this->dataIn = $DataIn;
        $this->dataPublic = $DataPublic;
        $this->linkId = $link_id;
    }

    public function getDatetype($number, $targetDate, $sheet){
        $dateType = array('morning'=>'', 'afternoon'=>'', 'night'=>'');
        $targtMonth = substr($targetDate, 0, 7);
        //旧调班
        $workDateChangeSql = mysql_query("SELECT Id,XDate,GDate FROM $DataIn.kqrqdd WHERE (GDate='".$targetDate."' OR XDate='".$targetDate."') and Number = ".$number." LIMIT 1");
        if($rqddRow = mysql_fetch_assoc($workDateChangeSql)){
        
            $gDate = $rqddRow["GDate"];
            $xDate = $rqddRow["XDate"];

            $exchangeDate = ($targetDate == $gDate)?$xDate:$gDate;
            $type = $this->isHoliday($exchangeDate);
            $dateType['morning']=$type;
            $dateType['afternoon']=$type;
            $dateType['night']=$type;
        }
        else{//新调班
            $nRqddSql = "SELECT A.GDate,A.GTime,A.XDate,A.XTime From ".$this->dataIn.".kq_rqddnew A 
                        LEFT JOIN ".$this->dataIn.".kq_ddsheet B On B.ddItem = A.Id
                        Where B.Number = '".$number."' and (A.GDate='".$targetDate."' OR A.XDate='".$targetDate."')";
            $nRqddResult = mysql_query($nRqddSql);
            $todayDateType = $this->isHoliday($this->targetDate);
            if(mysql_num_rows($nRqddResult) != 0 && (($targtMonth != '2014-06' && $targtMonth != '2014-07'))){
                while($nRqddRow = mysql_fetch_assoc($nRqddResult)){
                    $gDate = $nRqddRow["GDate"];
                    $tDate = $nRqddRow["XDate"];
                    $tTime = $nRqddRow["XTime"];
                    $gTime = $nRqddRow["GTime"];
                    switch($targetDate){
                        case $gDate:{
                            $info = $this->workDateBeExchange($gTime, $gDate, $tTime, $tDate, $sheet);
                            if($info[0] == 'allday'){
                                $dateType['morning']=$info[1];
                                $dateType['afternoon']=$info[1];
                                $dateType['night']=$info[1];
                            }
                            else{
                                $dateType[$info[0]] = $info[1];
                            }
                        }
                        break;
                        case $tDate:{
                            $info = $this->workDateExchange($gTime, $gDate, $tTime, $tDate, $sheet);
                            if($info[0] == 'allday'){
                                $dateType['morning']=$info[1];
                                $dateType['afternoon']=$info[1];
                                $dateType['night']=$info[1];
                            }
                            else{
                                $dateType[$info[0]] = $info[1];
                            }
                        }
                        break;
                    }
                }
                if($dateType['night'] === ''){
                    $dateType['night'] = $todayDateType;
                }
            }
            else{
                $type = $this->isHoliday($targetDate);
                $dateType['morning']=$type;
                $dateType['afternoon']=$type;
                $dateType['night']=$type;
            }
        }

        return $dateType;
    }

    private function isHoliday($date){
        $type = "";
        $holidayResult = mysql_query("SELECT Type,jbTimes FROM ".$this->dataPublic.".kqholiday WHERE Date='$date'");
        if($holidayRow = mysql_fetch_assoc($holidayResult)){
            $holidayType = $holidayRow["Type"];
            switch($holidayType){
                case 0:     $type="W";      break;
                case 1:     $type="Y";      break;
                case 2:     $type="F";      break;
            }
        }
        else{
            $weekDay=date("w",strtotime($date));
            $type=($weekDay==6 || $weekDay==0)?"X":"G";
        }
    
        return $type;
    }

    private function workDateBeExchange($gTime, $gDate, $tTime, $tDate, $sheet){
        $gChangeTime = explode('-', $gTime);
        if(strtotime($gDate.' '.$gChangeTime[1])-strtotime($gDate.' '.$gChangeTime[0]) >= 3600*8){
            //全天
            $type = '';
            if($tDate == ''){
                $type = $this->isHoliday($gDate)==='X'?'G':'X';
            }
            else{
                $type = $this->isHoliday($tDate);
            }
            return array('allday', $type);
        }
        else{
            //半天
            if($gChangeTime[0] === $sheet->mCheckTime['start']){
                return array('morning', $this->isHoliday($tDate));
            }
            else{
                return array('afternoon', $this->isHoliday($tDate));
            }
        }
    }

    private function workDateExchange($gTime, $gDate, $tTime, $tDate, $sheet){
        $tChangeTime = explode('-', $tTime);
        if(strtotime($tDate.' '.$tChangeTime[1])-strtotime($tDate.' '.$tChangeTime[0]) >= 3600*8){
            //全天
            $type = $this->isHoliday($gDate);
            return array('allday', $type);
        }
        else{
            //半天
            if($tChangeTime[0] === $sheet->mCheckTime['start']){
                return array('morning', $this->isHoliday($tDate));
            }
            else{
                return array('afternoon', $this->isHoliday($tDate));
            }
        }
    }
}

?>