<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployk
$DataPublic.staffmain
$DataPublic.jobdata
$DataPublic.branchdata
$DataPublic.kqtype
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;             
$tableMenuS=600;
ChangeWtitle("$SubCompany 考勤时间调动统计");
$funFrom="Rs_checktime";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|部门|70|职位|70|职位|70|员工ID|70|员工姓名|70|1.5倍调动总工时|100|1.5倍金额|80|2倍调动总工时|100|2倍金额|80|3倍调动总工时|100|3倍金额|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,4,7,8,12";
$sumCols="8";
//步骤3：
include "../model/subprogram/read_model_3.php";
$workMoney = 17.5;
echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
$date_Result = mysql_query("SELECT DATE_FORMAT(oldChecktime,'%Y-%m') AS Month FROM $DataIn.checktime_dd group by DATE_FORMAT(oldChecktime,'%Y-%m') order by oldChecktime DESC",$link_id);
if ($dateRow = mysql_fetch_array($date_Result)){
    do{
        $dateValue=$dateRow["Month"];
        $chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
        if($chooseMonth==$dateValue){
            echo"<option value='$dateValue' selected>$dateValue</option>";
            $SearchRows.="and DATE_FORMAT(oldChecktime,'%Y-%m')='$dateValue'";
        }else{
            echo"<option value='$dateValue'>$dateValue</option>";
        }
    }while($dateRow = mysql_fetch_array($date_Result));
}
echo"</select>&nbsp;";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$sumMoney = 0;
// $mySql="SELECT K.Id,M.Number,K.checkioId,K.oldChecktime,K.targetChecktime,sum(K.worktime) as worktime, K.rate,K.Date,K.Operator,M.Name,M.BranchId,M.JobId
//     FROM $DataIn.checktime_dd K 
//     LEFT JOIN $DataIn.checkinout C On C.Id = K.checkioId
//     LEFT JOIN $DataPublic.staffmain M ON M.Number=C.Number 
//     WHERE K.targetChecktime = '' AND M.KqSign=1 AND K.Estate=1 $SearchRows Group By M.Number ORDER BY K.Id DESC,M.Estate";

$mySql="SELECT extra.worktime as oneworktime, extra.rate as oneRate, doubleRate.worktime as doubleworktime, doubleRate.rate as doubleRate, threeRate.worktime as threeworktime, threeRate.rate as threeRate, extra.Number,K.Name,K.BranchId,K.JobId,K.GroupId
        From 
        (select sum(A.worktime) as worktime, A.rate, A.Date, C.Number 
        From $DataIn.checktime_dd A
        LEFT JOIN $DataIn.checkinout C On C.Id = A.checkioId
        LEFT JOIN $DataPublic.staffmain M On C.Number = M.Number
        Where A.targetChecktime = '' and A.Estate = 1 $SearchRows AND A.type = 2 AND M.KqSign=1
        Group by C.Number
        ) extra
        left Join
        (select sum(A.worktime) as worktime, A.rate, A.Date, C.Number 
        From $DataIn.checktime_dd A
        LEFT JOIN $DataIn.checkinout C On C.Id = A.checkioId
        LEFT JOIN $DataPublic.staffmain M On C.Number = M.Number
        Where A.targetChecktime = '' and A.Estate = 1 $SearchRows AND A.type = 3 AND M.KqSign=1
        Group by C.Number
        ) doubleRate On doubleRate.Number = extra.Number 
        left Join
        (select sum(A.worktime) as worktime, A.rate, A.Date, C.Number 
        From $DataIn.checktime_dd A
        LEFT JOIN $DataIn.checkinout C On C.Id = A.checkioId
        LEFT JOIN $DataPublic.staffmain M On C.Number = M.Number
        Where A.targetChecktime = '' and A.Estate = 1 $SearchRows AND A.type = 4 AND M.KqSign=1
        Group by C.Number
        ) threeRate On threeRate.Number = extra.Number 
        LEFT JOIN $DataPublic.staffmain K ON K.Number=extra.Number
        ORDER BY K.BranchId ,K.GroupId ,K.Estate DESC";

$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
    do{
        $m=1;
        $Id=$myRow["Id"];
        $Name=$myRow["Name"];
        $Number=$myRow["Number"];
        $BranchId=$myRow["BranchId"];   
        $GroupId = $myRow['GroupId'];  
        //$rate = $myRow['rate']==""?"17.5":$myRow['rate'];
        //1.5倍
        $oneworktime = $myRow['oneworktime'];
        $oneRate = $myRow['oneRate']==""?"17.5":$myRow['oneRate'];
        //2倍
        $doubleworktime = $myRow['doubleworktime'];
        $doubleRate = $myRow['doubleRate']==""?"22.33":$myRow['doubleRate'];
        //3倍
        $threeworktime = $myRow['threeworktime'];
        $threeRate = $myRow['threeRate']==""?"35.00":$myRow['threeRate'];

        //
        $B_Query = mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1");     
        if($B_Query)    
        {
            $B_Result = mysql_fetch_array($B_Query);
            $Branch=$B_Result["Name"];  
        }           
        $JobId=$myRow["JobId"];
        $J_Query = mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id);
        if($J_Query)
        {
            $J_Result = mysql_fetch_array($J_Query);
            $Job=$J_Result["Name"];
        }
        
        $G_Query = mysql_query("SELECT GroupName FROM $DataIn.staffgroup where 1 and GroupId=$GroupId LIMIT 1",$link_id);
        if($G_Query)
        {
            $G_Result = mysql_fetch_array($G_Query);
            $GroupName=$G_Result["GroupName"];
        }
        
        $Month=$myRow["Month"];
        $Date=$myRow["Date"];
        $Locks=$myRow["Locks"];
        $Operator=$myRow["Operator"];
        include "../model/subprogram/staffname.php";

        // $oldChecktime = $myRow['oldChecktime'];
        // $targetChecktime = $myRow['targetChecktime'];
        // $worktime = $myRow['worktime'];

        $ValueArray=array(
            array(0=>$Branch,       1=>"align='center'"),
            array(0=>$Job,      1=>"align='center'"),
            array(0=>$GroupName,      1=>"align='center'"),
            array(0=>$Number,       1=>"align='center'"),
            array(0=>$Name,         1=>"align='center'"),
            array(0=>$oneworktime,       1=>"align='center'"),
            array(0=>$oneworktime*$oneRate,       1=>"align='center'"),
            array(0=>$doubleworktime,       1=>"align='center'"),
            array(0=>$doubleworktime*$doubleRate == 0?'':$doubleworktime*$doubleRate,       1=>"align='center'"),
            array(0=>$threeworktime,       1=>"align='center'"),
            array(0=>$threeworktime*$threeRate == 0?'':$doubleworktime*$doubleRate,       1=>"align='center'")
            );
        $thisTotle = $oneworktime*$oneRate + $doubleworktime*$doubleRate + $threeworktime*$threeRate;
        $sumMoney += $thisTotle;   
        $checkidValue=$Id;
        include "../model/subprogram/read_model_6.php";
        }while ($myRow = mysql_fetch_array($myResult));

        $ValueArray=array(
            array(0=>""  ),
            array(0=>""   ),
            array(0=>""   ),
            array(0=>""   ),
            array(0=>""   ),
            array(0=>$sumMoney, 1=>"align='center'")
        );
        $ShowtotalRemark="合计(RMB)";
        $isTotal=1;
        include "../model/subprogram/read_model_total.php"; 
    }
else{
    noRowInfo($tableWidth);
    }
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>