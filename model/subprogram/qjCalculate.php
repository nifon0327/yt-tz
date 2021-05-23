<?php
    function qjCalculate($startTime, $endTime){
        $startMonth = date('Y-m', strtotime($startTime));
        $endMonth = date('Y-m', strtotime($endTime));
        $tempMonth = $startMonth;
        $tempTime = $startTime;
        $qjArray = array();
        while($tempMonth <= $endMonth){
            if($tempMonth == $endMonth){
                //echo $tempTime.'   '.$endTime.'<br>';
                $qjArray[] = array($tempTime, $endTime);
            }else if($tempMonth < $endMonth){
                $lastDay = date("Y-m-t",strtotime($tempMonth.'-01'));
                //echo $tempTime.'   '.$lastDay.' 17:00:00'.'<br>';
                $qjArray[] = array($tempTime, $lastDay.' 17:00:00');
            }

            $tempMonth = date("Y-m",strtotime("+1months",strtotime($tempMonth)));
            $tempTime = $tempMonth.'-01 08:00:00';
        }
        return $qjArray;
    }
?>