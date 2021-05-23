<?php
include_once($path.'/factoryCheck/FactoryClass/AttendanceDatetype.php');

function skipData($Number, $Date, $DataIn, $DataPublic, $link_id){
    $result = false;
    $sheet = new WorkScheduleSheet($Number, $Date, $attendanceTime['start'], $attendanceTime['end']);
    $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
    $datetype = $datetypeModle->getDatetype($Number, $Date, $sheet);
    if($datetype['morning'] != 'G' && $datetype['afternoon'] != 'G'){
        $result = true;
    }
    return $result;
}

?>