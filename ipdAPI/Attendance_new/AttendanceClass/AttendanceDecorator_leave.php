<?php
$path = $_SERVER["DOCUMENT_ROOT"];
include_once "$path/basic/parameter.inc";
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/StaffAvatar.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceCalculateModle.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");

class AttendanceAvatar_leave extends AttendanceAvatar {
        public function attendanceStatistic($DataIn, $DataPublic, $link_id){
            //工时抵扣
            $dkYXSql = "SELECT dkHours From $DataPublic.staff_dkdate Where dkDate='".$this->targetDate."' Limit 1";
            //echo $dkYXSql.'<br>';
            $dkYXResult = mysql_query($dkYXSql);
            if($dkYxRow = mysql_fetch_assoc($dkYXResult)){
                $dkHour = $dkYxRow['dkHours'];
                if($this->kgHours > 0 || $this->lackWorkHours > 0){
                    $this->dkHours = $dkHour - $this->workHours;
                    //echo "$dkHour - $this->workHours <br>";
                    $this->kgHours = 0;
                    $this->lackWorkHours = 0;
                }
            }

            if($this->workSchedule->mCheckTime["state"] == "G" || $this->workSchedule->aCheckTime["state"] == "G"){
                $this->lackWorkHours = $this->defaultWorkHours - $this->workHours - $this->totleLeaveHours - $this->dkHour;
                $this->lackWorkHours = $this->lackWorkHours < 0?0:$this->lackWorkHours;
                if($this->lackWorkHours == $this->defaultWorkHours){
                    $this->kgHours = $this->defaultWorkHours;
                    $this->lackWorkHours = 0;
                    $this->beLateStandard = '';
                    $this->beEarlyStandard = '';
                }
            }

            //判断迟到早退
            // if ($this->targetDate == '2015-11-25'){
            //  echo $this->beLateStandard.'      '.$this->beEarlyStandard."<br>";
            // }
            if($this->workSchedule->mCheckTime["state"] == "G" || $this->workSchedule->aCheckTime["state"] == "G"){
                //echo $this->targetDate.'       '.$this->beLateStandard.'   '.$this->lackWorkHours.'<br>';
                if($this->beLateStandard !="" && strtotime($this->startTime) > strtotime($this->beLateStandard) && $this->lackWorkHours == 0.5){
                    $this->late = 1;
                    $this->lackWorkHours = "";
                    $this->workHours += 0.5;
                }

                if($this->beEarlyStandard !="" && strtotime($this->endTime) < strtotime($this->beEarlyStandard) && $this->lackWorkHours == 0.5){
                    $this->early = 1;
                    $this->lackWorkHours = "";
                    $this->workHours += 0.5;
                }
            }
        }

        public function attendanceSetup($DataIn, $DataPublic, $link_id){
            $this->getWorkSchedule($DataIn, $DataPublic, $link_id);
            //echo $this->attendanceFloor;
            if ($this->attendanceFloor == 6 || $this->attendanceFloor == 12){
                $this->workSchedule->mCheckTime['start'] = '08:00';
                    $this->workSchedule->mCheckTime['end'] = '12:00';
                    $this->workSchedule->aCheckTime['start'] = '13:30';
                    $this->workSchedule->aCheckTime['end'] = '17:30';
            }


            // if($this->kqSign > 1 && ($this->branchId != 6 & $this->branchId != 7 & $this->branchId != 8 && $this->jobId != 10 )) {
            //         $this->workSchedule->mCheckTime['start'] = '08:00';
            //         $this->workSchedule->mCheckTime['end'] = '12:00';
            //         $this->workSchedule->aCheckTime['start'] = '13:30';
            //         $this->workSchedule->aCheckTime['end'] = '17:30';
            // }

            $this->getDateType($DataIn, $DataPublic, $link_id);
            $this->getWorkHours($DataIn, $DataPublic, $link_id);
            $this->getZlHours($DataIn, $DataPublic, $link_id);
            $this->getOverTimeHours($DataIn, $DataPublic, $link_id);
            //$this->getLeaveHours($DataIn, $DataPublic, $link_id);
            $this->attendanceStatistic($DataIn, $DataPublic, $link_id);
        }

        public function getWorkSchedule($DataIn, $DataPublic, $link_id){
            $this->workSchedule = new WorkScheduleSheet($this->getStaffNumber(), $this->targetDate, $this->startTime, $this->endTime);
            //确定迟到早退标准
            $this->beLateStandard = $this->targetDate.' '.$this->workSchedule->mCheckTime["start"];
            $this->beEarlyStandard = $this->targetDate.' '.$this->workSchedule->aCheckTime["end"];
        }

}

?>