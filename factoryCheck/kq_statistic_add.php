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
    //步骤2：
    ChangeWtitle("$SubCompany 考勤月统计");       //需处理
    $Log_Funtion="保存";
    $fromWebPage=$funFrom."_read";
    $nowWebPage=$funFrom."_save";
    $_SESSION["nowWebPage"]=$nowWebPage;
    $DateTime=date("Y-m-d H:i:s");
    $nowMonth=date("Y-m"); 
    $CheckMonth = $chooseMonth;
    $FristDay=$CheckMonth."-01";
    $EndDay=date("Y-m-t",strtotime($FristDay));
    if($CheckMonth==$nowMonth){
        $Days=date("d")-1;
    }
    else{
        $Days=date("t",strtotime($FristDay));
    }

    $CheckStaffSql = "SELECT A.Number,A.Name,A.JobName FROM (
                            SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId
                            FROM $DataPublic.staffmain M
                            LEFT JOIN $DataIn.checkinout C ON  M.Number=C.Number
                            LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
                            WHERE C.CheckTime LIKE '$CheckMonth%' AND (C.BranchId IN ( 6, 7, 8 ) OR C.JobId = 10) 
                            GROUP BY C.Number 
                            UNION ALL 
                            SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId  
                            FROM $DataPublic.staffmain M
                            LEFT JOIN $DataPublic.kqqjsheet Q ON  M.Number=Q.Number
                            LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
                            LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
                            WHERE (Q.StartDate  LIKE '$CheckMonth%'  OR Q.EndDate  LIKE '$CheckMonth%'  OR  (Q.StartDate<'$CheckMonth-01'  AND Q.EndDate>'$CheckMonth-01'))  AND G.Estate=1 AND M.cSign='$Login_cSign' AND (M.BranchId IN ( 6, 7, 8 ) OR M.JobId = 10) $KqSignStr
                            GROUP BY Q.Number ) A 
                            WHERE A.Number NOT IN (SELECT Number FROM $DataIn.kqdataother WHERE Month = '$CheckMonth')
                            GROUP BY A.Number 
                            ORDER BY A.BranchId,A.JobId,A.Number";
    //echo $CheckStaffSql.'<br>';
    $CheckStaff= mysql_query($CheckStaffSql,$link_id);
    //mysql_query('START TRANSACTION');
    while($StaffRow = mysql_fetch_array($CheckStaff)){
        $Number=$StaffRow["Number"];
        $JobId = $StaffRow['JobId'];
        if($JobId == '38'){
            continue;
        }
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

        $recordStatisticSql = "INSERT INTO $DataIn.kqdataother(Id, Number, Dhours, Whours, Ghours, Xhours, Fhours, InLates, OutEarlys, SJhours, BJhours, YXJhours, WXJhours, QQhours, WXhours, KGhours, dkhours, Month, Locks) 
                            VALUES (NULL, $Number, '".
                            $totleStatistic['defaultWorkHours']."','".
                            $totleStatistic['workHours']."','".
                            $totleStatistic['workdayOt']."','".
                            $totleStatistic['weekdayOt']."','".
                            $totleStatistic['holidayOt']."','".
                            $totleStatistic['beLate']."','".
                            $totleStatistic['beEarly']."','".
                            $totleStatistic['personalLeave']."','".
                            $totleStatistic['sickLeave']."','".
                            $totleStatistic['annualLeave']."','".
                            $totleStatistic['noPayLeave']."','".
                            $totleStatistic['lackWorkHours']."','".
                            $totleStatistic['noPayHours']."','".
                            $totleStatistic['kgHours']."','".
                            $totleStatistic['dkHours']."','".
                            "$CheckMonth', '0')";
        echo $recordStatisticSql.';<br>';
        if(!mysql_query($recordStatisticSql)){
            $Log .= $recordStatisticSql.'<br>';
            //mysql_query('ROLLBACK ');
        }
        else{
            $Log .= $Number."添加成功 <br>";
        }
        
    }
    //mysql_query('COMMIT');
    echo 'end';
    include "../model/logpage.php";
?>