<?php
    $path = $_SERVER["DOCUMENT_ROOT"];
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceStatistic.php");
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");
    include_once('FactoryClass/AttendanceTimeSetup.php');
    include_once('FactoryClass/AttendanceDatetype.php');
    include_once('FactoryClass/AttendanceInfo.php');
    include_once('FactoryClass/AttendanceCalculate.php');
    include "../model/modelhead.php";
    $From=$From==""?"read":$From;

    $_SESSION["nowWebPage"]=$nowWebPage;
    $DateTime=date("Y-m-d H:i:s");
    $nowMonth=date("Y-m"); 
    $CheckMonth = $chooseMonth;
    echo "<input type='hidden' name='chooseMonth' id='chooseMonth' vaule=$chooseMonth>";

    $FristDay=$CheckMonth."-01";
    $EndDay=date("Y-m-t",strtotime($FristDay));
    if($CheckMonth==$nowMonth){
        $Days=date("d")-1;
    }
    else{
        $Days=date("t",strtotime($FristDay));
    }

    $NumberTargetSql = mysql_query("SELECT Number FROM $DataIn.kqdataother WHERE Id = $Id");
    $NumberTargetResult = mysql_fetch_assoc($NumberTargetSql);
    $Number = $NumberTargetResult['Number'];


    /***************************************************/
    $attendanceStatistic = new AttendanceInfo();
    $timeSetup = new AttendanceTimeSetup('d7check', $DataPublic, $link_id);
    $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
    for($i=0;$i<$Days;$i++){
        $j=$i+1;
        $CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
        $Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
        $weekDay=date("w",strtotime($CheckDate));    
        $weekInfo="星期".$Darray[$weekDay];

        $checkIsOutOfWorkResult=mysql_query("SELECT A.Id,A.ComeIn,B.outDate FROM $DataPublic.staffmain A
                                             Left Join $DataPublic.dimissiondata B On B.Number = A.Number 
                                             WHERE A.Number='".$Number."' and (A.ComeIn>'$CheckDate' OR A.Number IN(SELECT B.Number FROM $DataPublic.dimissiondata WHERE B.Number='".$Number."' and B.OutDate<'$CheckDate'))",$link_id);
        $attendanceTime = $timeSetup->setupTime($Number, $CheckDate);
        $sheet = new WorkScheduleSheet($Number, $CheckDate, $attendanceTime['start'], $attendanceTime['end']);
        $sheet->setDefault();
        $datetype = $datetypeModle->getDatetype($Number, $CheckDate, $sheet);
        if(!$checkDRow = mysql_fetch_array($checkIsOutOfWorkResult)){
            //获取时间

            //获取时间
            $datetypeInfo = '';
            if(($datetype['morning'] !== 'G' && $datetype['afternoon'] !== 'G') && $attendanceTime['start'] != ''){
                $attendanceTime['start'] = '';
                $attendanceTime['end'] = '';
            }
            $infoCalculator = new AttendanceCalculate($DataIn, $DataPublic, $link_id);
            $attendanceResult = $infoCalculator->calculateTime($Number, $attendanceTime['start'], $attendanceTime['end'], $sheet, $CheckDate, $datetype);
        }
        else{
            $infoCalculator = new AttendanceCalculate($DataIn, $DataPublic, $link_id);
            $attendanceResult = $infoCalculator->setOutOfWorkState($datetype);
            $datetype .= '(离)';
        }
        if($attendanceResult['lackWorkHours'] < 0 && strtotime($attendanceTime['start']) <= strtotime($CheckDate.' '.$sheet->mCheckTime['start'])){
        $attendanceResult['workHours'] += $attendanceResult['lackWorkHours'];
        $attendanceTime['end'] = date('Y-m-d H:i', strtotime($attendanceTime['end'])+$attendanceResult['lackWorkHours']*3600);
        $attendanceResult['lackWorkHours'] = '';
        }
        $attendanceStatistic->statistic($attendanceResult);
    }
    /***************************************************/
    $totleStatistic = $attendanceStatistic->outputByTag(false);
    //echo $totleStatistic['workdayOt'];
    //exit();
    $recordStatisticSql = "UPDATE $DataIn.kqdataother SET ".
                            "Dhours =". $totleStatistic['defaultWorkHours'] .
                            ",Whours =". $totleStatistic['workHours'] .
                            ",Ghours =". $totleStatistic['workdayOt'] .
                            ",Xhours =". $totleStatistic['weekdayOt'] .
                            ",Fhours =". $totleStatistic['holidayOt'] .
                            ",InLates =". $totleStatistic['beLate'] .
                            ",OutEarlys =" . $totleStatistic['beEarly'] .
                            ",SJhours =". $totleStatistic['personalLeave'] .
                            ",BJhours =". $totleStatistic['sickLeave'] .
                            ",YXJhours =". $totleStatistic['annualLeave'] .
                            ",WXJhours =". $totleStatistic['noPayLeave'] .
                            ",QQhours =". $totleStatistic['lackWorkHours'] .
                            ",WXhours =". $totleStatistic['noPayHours'] .
                            ",KGhours =". $totleStatistic['kgHours'] .
                            ",dkhours =". $totleStatistic['dkHours'] .
                            " Where Id = $Id";
    if(!mysql_query($recordStatisticSql)){
        $Log .= $recordStatisticSql.'<br>';
    }
    else{
        $Log .= $Number."重置成功 <br>";
    }
    
    $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
    $IN_res=@mysql_query($IN_recode);
    include "../model/logpage.php";
?>