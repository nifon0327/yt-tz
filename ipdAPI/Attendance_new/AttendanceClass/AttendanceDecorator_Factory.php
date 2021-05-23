<?php
$path = $_SERVER["DOCUMENT_ROOT"];
include_once "$path/basic/parameter.inc";
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/StaffAvatar.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceCalculateModle.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");

class AttendanceAvatar_Factory extends AttendanceAvatar {
    public function setupAttendanceData($number, $checkDay, $DataIn, $DataPublic, $link_id){
            $dateArray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
            $this->weekDay="星期".$dateArray[date("w",strtotime($checkDay))];
            $this->nightShit = ($this->KrSign == "1")?"1":"";

            $this->defaultWorkHours = 8;
            $this->targetDate = $checkDay;
            $getCheckDataSql = "SELECT CheckTime,CheckType,KrSign,Id
                                FROM $DataIn.checkinout A
                                WHERE Number=$number 
                                and ((A.CheckTime LIKE '$checkDay%' and A.KrSign='0') OR (DATE_SUB(A.CheckTime,INTERVAL 1 DAY) LIKE '$checkDay%' and A.KrSign='1'))
                                and Left(A.CheckTime, 10) not in (select checkId from $DataIn.disableCheckId)
                                order by CheckTime";
            
            $getCheckDataResult = mysql_query($getCheckDataSql);
            while($getCheckDataRow = mysql_fetch_assoc($getCheckDataResult)){
                $checkType = $getCheckDataRow["CheckType"];
                switch($checkType){
                    case "I":{
                        $this->startTime = substr($getCheckDataRow["CheckTime"], 0, 16);

                        if($this->sex == 0 && substr($this->startTime, 5,5)=='03-08' && strtotime(substr($this->startTime, 11,5)) < strtotime('12:00')){
                            
                            $this->startTime = date('Y-m-d h:i:s', strtotime($this->startTime)+5*3600);
                        }

                    }
                    break;
                    case "O":{
                        $originalTime = $getCheckDataRow["CheckTime"];
                        $checkStandardTime = (date("w",strtotime($checkDay))== 0 || date("w",strtotime($checkDay))== 6 || strtotime($originalTime)<strtotime($checkDay.' 18:30'))?"17":"20";
                        if(strtotime($originalTime) > strtotime($checkDay." $checkStandardTime:10:00")){
                            $mintuesValue = substr($originalTime, 15, 1);
                            $endtimeHolder = "$checkDay $checkStandardTime:0$mintuesValue:00";
                        }else{
                            $endtimeHolder = $originalTime;
                        }
                        $this->endTime = substr($endtimeHolder, 0, 16);
                        $this->KrSign = $getCheckDataRow["KrSign"];
                    }
                    break;
                }
            }
        }

    public function getZlHours($DataIn, $DataPublic, $link_id){
        $ZL_Result = mysql_query("SELECT sum(Hours) as Hours FROM $DataPublic.kqzltime  WHERE Number=".$this->getStaffNumber()." and Date='".$this->targetDate."' and Date not in (select checkId from $DataIn.disableCheckId)",$link_id);
        if($zlRow = mysql_fetch_assoc($ZL_Result)){
            $zlHours = $zlRow["Hours"];
            switch ($this->workSchedule->otStartTime["state"]) {
                case "G":{
                    $this->workZlHours = $zlHours;
                }
                break;
                case "X":
                case "Y":{
                    $this->weekZlHours = $zlHours;
                }
                break;
                case "F":{
                    $this->holidayZlHours = $zlHours;
                }
                break;
            }
        }

    }
}

?>