<?php
$path = $_SERVER["DOCUMENT_ROOT"];
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");
class AttendanceTimeSetup{

    private $dataIn;
    private $dataPublic;
    private $linkId;
    function __construct($DataIn, $DataPublic, $link_id){
        $this->dataIn = $DataIn;
        $this->dataPublic = $DataPublic;
        $this->linkId = $link_id;
    }

    public function setupTime($number, $targetDate){
        return $attendanceInfo = $this->getAttendanceInfo($number, $targetDate);
    }

    private function getAttendanceInfo($number, $checkDay){
        $startTime = '';
        $endTime = '';
        $krSign = '';

        $getCheckDataSql = "SELECT CheckTime,CheckType,KrSign 
                            FROM ".$this->dataIn.".fakecheckinout 
                            WHERE Number=$number 
                            and ((CheckTime LIKE '$checkDay%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$checkDay%' and KrSign='1'))
                            order by CheckTime";
        //echo $getCheckDataSql.'<br>';
        $getCheckDataResult = mysql_query($getCheckDataSql, $this->linkId);
        while($getCheckDataRow = mysql_fetch_assoc($getCheckDataResult)){
            $checkType = $getCheckDataRow["CheckType"];
            switch($checkType){
                case "I":{
                    $startTime = substr($getCheckDataRow["CheckTime"], 0, 16);
                }
                break;
                case "O":{
                    $endTime = substr($getCheckDataRow["CheckTime"], 0, 16);
                    $krSign = $getCheckDataRow["KrSign"];
                }
                break;
            }
        }

        return array('start'=>$startTime, 'end'=>$endTime, 'krSign'=>$krSign);
    }
}
?>