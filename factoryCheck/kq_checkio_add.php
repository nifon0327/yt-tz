<?php
    include "../model/modelhead.php";
    $path = $_SERVER["DOCUMENT_ROOT"];
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceStatistic.php");
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");
    include_once('../FactoryCheck/FactoryClass/AttendanceTimeSetup.php');
    include_once('../FactoryCheck/FactoryClass/AttendanceDatetype.php');
    include_once('../FactoryCheck/FactoryClass/AttendanceInfo.php');
    include_once('../FactoryCheck/FactoryClass/AttendanceCalculate.php');

    $Log_Item="考勤";           //需处理
    $fromWebPage=$funFrom."_read";
    $nowWebPage=$funFrom."_save";
    $_SESSION["nowWebPage"]=$nowWebPage;
    $ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
    //新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
    $Log_Funtion="保存";
    $TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
    ChangeWtitle($TitleSTR);

    /*********************************************
    分别导入签到和签退，签到数据不做处理，直接导入。而签退数
    据会根据设定时间计算,先导入，再计算！
    *********************************************/

    //Insert Check In Data
    //step 1-先找出已经插入过的数据
    $insertNumberSql = "SELECT Number,CheckType From d7check.fakecheckinout WHERE ((CheckTime LIKE '$targetDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$targetDate%' and KrSign='1')) and otReason = 'mc'";
    $insertNumberResult = mysql_query($insertNumberSql);
    $checkinNumbers = array();
    $checkoutNumbers = array();
    while($checkinNumberRow = mysql_fetch_assoc($insertNumberResult)){
        if($checkinNumberRow['CheckType'] === 'I'){
            $checkinNumbers[] = $checkinNumberRow['Number'];
        }else{
            $checkoutNumbers[] = $checkinNumberRow['Number'];
        }
    }

    $insertSearch = '';
    if(count($checkinNumbers) > 0){
        $checkinNumbers = implode(',', $checkinNumbers);
        $insertSearch = " AND Number NOT IN ($checkinNumbers)";
    }

    if(count($checkoutNumbers) > 0){
        $checkoutNumbers = implode(',', $checkoutNumbers);
        $insertOutSearch = " AND Number NOT IN ($checkoutNumbers)";
    }
    //step 2-插入数据
    $insertCheckInSql = "INSERT INTO d7check.fakecheckinout (Id, BranchId, JobId, Number, CheckTime, CheckType, dFrom, dFromId, Estate, Locks, ZlSign, KrSign, otReason, Operator)
                         SELECT NULL, BranchId, JobId, Number, CheckTime, CheckType, dFrom, dFromId, Estate, Locks, ZlSign, KrSign, 'mc', Operator 
                         FROM $DataIn.checkinout 
                         WHERE ((CheckTime LIKE '$targetDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$targetDate%' and KrSign='1'))
                         AND CheckType = 'I'
                         $insertSearch";
    
    if(mysql_query($insertCheckInSql)){
        $Log = "$targetDate 签到记录导入成功";
    }else{
        $Log = "$targetDate 签到记录导入失败";
    }

    //导入签退数据
    $insertCheckInSql = "INSERT INTO d7check.fakecheckinout (Id, BranchId, JobId, Number, CheckTime, CheckType, dFrom, dFromId, Estate, Locks, ZlSign, KrSign, otReason, Operator)
                         SELECT NULL, BranchId, JobId, Number, CheckTime, CheckType, dFrom, dFromId, Estate, Locks, ZlSign, KrSign, 'mc', Operator 
                         FROM $DataIn.checkinout 
                         WHERE ((CheckTime LIKE '$targetDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$targetDate%' and KrSign='1'))
                         AND CheckType = 'O'
                         $insertOutSearch";

    if(mysql_query($insertCheckInSql)){
        $Log .= "<br>$targetDate 签退记录导入成功";
    }else{
        $Log .= "<br>$targetDate 签退记录导入失败";
    }
    //修改时间
    $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
    $checkinoutSql = "SELECT * FROM d7check.fakecheckinout WHERE ((CheckTime LIKE '$targetDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$targetDate%' and KrSign='1')) AND checktype = 'O' AND otReason = 'mc' and JobId != 38";
    $checkinoutResult = mysql_query($checkinoutSql);
    while($checkinoutRow = mysql_fetch_assoc($checkinoutResult)){
        $id = $checkinoutRow['Id'];
        $checktime = $checkinoutRow['CheckTime'];
        $checkDate = substr($checktime, 0, 10);
        $CheckMonth = substr($checkDate, 0, 7);
        $Number = $checkinoutRow['Number'];
        if($checkinoutRow['KrSign'] == '1'){
            $checkDate = date('Y-m-d', strtotime($checkDate)-3600*24);
            $CheckMonth = substr($checkDate, 0, 7);
        }
        $sheet = new WorkScheduleSheet($Number, $checkDate, '', '');
        $sheet->setDefault();
        $datetype = $datetypeModle->getDatetype($Number, $checkDate, $sheet);
        $otTime = 0;
        if($CheckMonth >= '2014-03'){
            $otTimeSql = "SELECT workday, weekday, holiday FROM $DataIn.kqovertime WHERE otDate = '$checkDate' Limit 1";
            $otTimeResult = mysql_fetch_assoc(mysql_query($otTimeSql));
            switch ($datetype['night']) {
                case 'G':
                    $otTime = $otTimeResult['workday'];
                    break;
                case 'X':
                    $otTime = $otTimeResult['weekday'];
                    break;
                case 'F':
                    $otTime = $otTimeResult['holiday'];
                    break;
            }
        }

        $willUpdate = false;
        $defaultOutTime = $otTime!=0?date('Y-m-d H:i', strtotime($checkDate.' 18:00:00')+$otTime*3600):date('Y-m-d H:i', strtotime($checkDate.' 17:00:00')+$otTime*3600);
        if(strtotime($checktime) > strtotime($defaultOutTime) || ($otTime === 0 && strtotime($checktime) > strtotime($defaultOutTime))){
            $willUpdate = true;
            $newCheckTime = substr($defaultOutTime, 0, 15).substr($checktime, 15, 4);
        }else if(strtotime($checktime) < strtotime($checkDate.' 18:29:00') && strtotime($checktime) > strtotime($checkDate.' 17:29:00')){
            $willUpdate = true;
            $newCheckTime = $checkDate.' 17:0'.substr($checktime, 15, 4);
        }

        if($willUpdate){
            $updatSql = "UPDATE d7check.fakecheckinout SET CheckTime = '$newCheckTime' ,KrSign=0 WHERE Id = $id;";
            mysql_query($updatSql);
        }
    }
    
    include "../model/logpage.php";
?>