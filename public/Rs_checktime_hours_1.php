<?php

$insetArray = array();
$reduceIds = array();
$addIds = array();

if($_POST['ListId']){//如果指定了操作对象
    $Counts=count($_POST['ListId']);
    $Ids="";
    for($i=0;$i<$Counts;$i++){
        $Number=$_POST[ListId][$i];
        
        $Check0Sql="SELECT C.Id,C.CheckTime,C.CheckType 
                     FROM $DataIn.checkinout C 
                     WHERE C.CheckType = 'O' ANd C.Number='$Number' and ( (C.CheckTime LIKE '$time0%' and C.KrSign='0') OR (DATE_SUB(C.CheckTime,INTERVAL 1 DAY) LIKE '$time0%' and C.KrSign='1') )  ORDER BY C.CheckTime";
        $Check0Result = mysql_query($Check0Sql);
        $Check0Row = mysql_fetch_assoc($Check0Result);
        $checkioId = $Check0Row['Id'];
        $oldChecktime = $Check0Row['CheckTime'];
        $reduceIds[] = $checkioId;


        if ($time2 != '') {
            $Check2Sql="SELECT C.Id,C.CheckTime,C.CheckType 
                     FROM $DataIn.checkinout C 
                     WHERE C.CheckType = 'O' ANd C.Number='$Number' and ( (C.CheckTime LIKE '$time2%' and C.KrSign='0') OR (DATE_SUB(C.CheckTime,INTERVAL 1 DAY) LIKE '$time2%' and C.KrSign='1') )  ORDER BY C.CheckTime";
            $Check2Result = mysql_query($Check2Sql);
            $Check2Row = mysql_fetch_assoc($Check2Result);
            $checknId = $Check2Row['Id'];
            $targetChecktime = $Check2Row['CheckTime'];
            $addIds[] = $checknId;
        }

        $insetArray[] = "(NULL, '$checkioId', '$oldChecktime', '$checknId', '$targetChecktime', '$worktime' , '$DateTime', '$Operator', '$rate', '$typeI')";

    }
}

if(count($insetArray)>0){
    $checktimeddSql = "INSERT INTO $DataIn.checktime_dd (Id, checkioId, oldChecktime, checknId, targetChecktime, worktime, Date, Operator, rate, type) VALUES ".implode(',', $insetArray) ;
   
    $reduceIdStr = implode(',', $reduceIds);
    $reTime = 0;
    if($reduceTime!=''){
        $reTime = 1;
    }
    $newSubtime = exchangeTime($worktime+$reTime);
    $reduceChecktimeSql = "UPDATE $DataIn.checkinout Set CheckTime=DATE_SUB(CheckTime,  interval '$newSubtime' HOUR_MINUTE) WHERE Id IN ($reduceIdStr)";

    if (count($addIds) > 0) {
        $addIdStr = implode(',', $addIds);
        $exTime = 0;
        if($addTime!=''){
           $exTime = 1;
        }
        $newAddTime = exchangeTime($worktime+$exTime);
        $addChecktimeSql = "UPDATE $DataIn.checkinout Set CheckTime=DATE_ADD(CheckTime,  interval '$newAddTime' HOUR_MINUTE) WHERE Id IN ($addIdStr)";
    }
    //echo $reduceChecktimeSql.'<br>';
    //exit();

    mysql_query("BEGIN");
    if (count($addIds) > 0) {
        if(mysql_query($checktimeddSql) && mysql_query($reduceChecktimeSql) && mysql_query($addChecktimeSql)){
            $Log="<div class='redB'>考勤时间更新成功<div><br>";
            mysql_query("COMMIT");
        }else{
            $Log="<div class='redB'>&nbsp;&nbsp; $TitleSTR 失败! $checktimeddSql </br> $reduceChecktimeSql </br> $addChecktimeSql </div></br>";
            mysql_query("ROOLBACK");
        }
    }else{
        if(mysql_query($checktimeddSql) && mysql_query($reduceChecktimeSql)){
            $Log="<div class='redB'>考勤时间更新成功<div><br>";
            mysql_query("COMMIT");
        }else{
            $Log="<div class='redB'>&nbsp;&nbsp; $TitleSTR 失败! $checktimeddSql </br> $reduceChecktimeSql </br> $addChecktimeSql </div></br>";
            mysql_query("ROOLBACK");
        }
    }
   
}else{
    $Log="<div class='redB'>&nbsp;&nbsp; $TitleSTR 失败! $checktimeddSql </br> $reduceChecktimeSql </br> $addChecktimeSql </div></br>";
}


?>