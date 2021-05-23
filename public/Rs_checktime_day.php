<?php
$insetArray = array();
$disableIds = array();

if($_POST['ListId']){
    $Counts=count($_POST['ListId']);
    $Ids="";
    for($i=0;$i<$Counts;$i++){
        $Number=$_POST[ListId][$i];

        $Check0Sql="SELECT C.Id,C.CheckTime,C.CheckType 
                     FROM $DataIn.checkinout C 
                     WHERE C.Number='$Number' and ( (C.CheckTime LIKE '$time0%' and C.KrSign='0') OR (DATE_SUB(C.CheckTime,INTERVAL 1 DAY) LIKE '$time0%' and C.KrSign='1') )  ORDER BY C.CheckTime";
        $Check0Result = mysql_query($Check0Sql);
        $inTimeId = '';
        $outTimeId = '';
        while ($ioRow = mysql_fetch_assoc($Check0Result)) {
            $CheckTime=$ioRow["CheckTime"];
            $CheckType=$ioRow["CheckType"];
            $KrSign=$ioRow["KrSign"];
            switch($CheckType){
                case "I":
                    $inTimeId = $ioRow["Id"];
                    $AI=date("Y-m-d H:i:00",strtotime("$CheckTime"));
                    $aiTime=date("H:i",strtotime("$CheckTime"));                        
                    break;
                case "O":
                    $outTimeId = $ioRow["Id"];
                    $AO=date("Y-m-d H:i:00",strtotime("$CheckTime"));   
                    $aoTime=date("H:i",strtotime("$CheckTime"));                        
                    break;
            }
        }

        $ToDay = $time0;
        include "kqcode/checkio_model_pb.php";
        if($pbType==0){
            include "kqcode/checkio_model_countX.php";
        }else{
            include "kqcode/checkio_model_countXL.php";//?????
        }

        $ZL_Result = mysql_query("SELECT SUM(Hours) AS  Hours FROM $DataPublic.kqzltime  WHERE Number=$Number and Date='$time0'",$link_id);
        if($ZL_Row = mysql_fetch_array($ZL_Result)){
            $zlhour = $ZL_Row['Hours'];
            $XJTime += $zlhour;
        }

        if($XJTime != 0){
            $insetArray[] = "(NULL, '$inTimeId,$outTimeId', '$time0', '$checknId', '$targetChecktime', '$XJTime' , '$DateTime', '$Operator', '$rate', '$typeI')";
            $disableIds[] = "(NULL, $inTimeId),(NULL, $outTimeId)";
        }

    }
}

if(count($insetArray)>0){
    $checktimeddSql = "INSERT INTO $DataIn.checktime_dd (Id, checkioId, oldChecktime, checknId, targetChecktime, worktime, Date, Operator, rate, type) VALUES ".implode(',', $insetArray) ;
    $disableIdsStr = implode(',', $disableIds);
    $disableCheckSql = "INSERT INTO $DataIn.disableCheckId (Id, checkId) VALUES ".$disableIdsStr;

    mysql_query("BEGIN");
    if(mysql_query($checktimeddSql) && mysql_query($disableCheckSql)){
         $Log="<div class='redB'>考勤时间更新成功<div><br>";
        mysql_query("COMMIT");
    }else{
        $Log="<div class='redB'>&nbsp;&nbsp; $TitleSTR 失败! $checktimeddSql </br> $reduceChecktimeSql </br> $addChecktimeSql </div></br>";
        mysql_query("ROOLBACK");
    }
}
?>