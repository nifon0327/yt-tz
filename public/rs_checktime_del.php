<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployk
$DataPublic.staffmain
$DataIn.cwxzsheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="考勤时间调动记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;

for($i=0;$i<count($checkid);$i++){
    $Id=$checkid[$i];
    if ($Id!=""){
        
        $checktimeddSql = "SELECT * FROM $DataIn.checktime_dd WHERE Id=$Id";
        $checktimeddResult = mysql_query($checktimeddSql);
        $checktimeRow = mysql_fetch_assoc($checktimeddResult);

        $checkoId = $checktimeRow['checkioId'];
        $checktimeArray = explode(',', $checkoId);
        if(count($checktimeArray) == 1){
            $oldChecktime = $checktimeRow['oldChecktime'];
            $checknId = $checktimeRow['checknId'];
            $targetChecktime = $checktimeRow['targetChecktime'];

            $restoreOldChecktime = "UPDATE $DataIn.checkinout SET CheckTime='$oldChecktime' WHERE Id=$checkoId";

            if($checknId != ''){
                $restoreNewChecktime = "UPDATE $DataIn.checkinout SET CheckTime='$targetChecktime' WHERE Id=$checknId";
            }
            $delChecktimedd = "UPDATE $DataIn.checktime_dd SET Estate=0 WHERE Id=$Id";

            if($checknId != ''){
                if (mysql_query($restoreOldChecktime) && mysql_query($restoreNewChecktime) && mysql_query($delChecktimedd)) {
                    $Log.="<div class='redB'>&nbsp;$nbsp;考勤时间调动复原成功. $UpSql</div><br>";$OperationResult="Y";
                }else{
                    $Log.="<div class='redB'>&nbsp;$nbsp;考勤时间调动复原失败. $restoreOldChecktime <br> $restoreNewChecktime <br> $delChecktimedd </div><br><br>";$OperationResult="N";
                }
            }else{
                if (mysql_query($restoreOldChecktime) && mysql_query($delChecktimedd)) {
                    $Log.="<div class='redB'>&nbsp;$nbsp;考勤时间调动复原成功. $UpSql</div><br>";$OperationResult="Y";
                }else{
                    $Log.="<div class='redB'>&nbsp;$nbsp;考勤时间调动复原失败. $restoreOldChecktime  <br> $delChecktimedd </div><br><br>";$OperationResult="N";
                }
            }
        }else if(count($checktimeArray) == 2){
            $recoverTimeSql = "DELETE From $DataIn.disablecheckid WHERE checkId in ($checkoId)";
            $deleteSql = "DELETE FROM $DataIn.checktime_dd WHERE Id = $Id";
            if(mysql_query($recoverTimeSql) && mysql_query($deleteSql)){
                $Log.="<div class='redB'>&nbsp;$nbsp;考勤时间调动复原成功. $UpSql</div><br>";$OperationResult="Y";
            }else{
                $Log.="<div class='redB'>&nbsp;$nbsp;考勤时间调动复原失败. $restoreOldChecktime <br> $restoreNewChecktime <br> $delChecktimedd </div><br><br>";$OperationResult="N";
            }
        }

    }
}
//条件：生效月份之后的薪资没有结付的且为最后一条记录,删除成功需改为人事表中的部门ID
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.redeployk");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>