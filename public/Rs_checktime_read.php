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
ChangeWtitle("$SubCompany 考勤时间调动记录");
$funFrom="Rs_checktime";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|部门|70|小组|70|员工ID|70|员工姓名|70|考勤时间1|150|考勤时间2|150|调动工时|60|更新日期|100|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,4,7,8,12";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From != 'slist'){

    $selStr="selFlag" . $FormalSign;
    $$selStr="selected";
    echo"<select name='FormalSign' id='FormalSign' onchange='ResetPage(this.name)'>
            <option value='1' $selFlag1>对调工时</option>
            <option value='2' $selFlag2>减少工时</option>";
    echo "</select>&nbsp;";

    echo "<select name='monthSelect' id='monthSelect' onchange='ResetPage(this.name)'>";
    $monthSelectSql = "SELECT distinct LEFT(oldChecktime, 7) as month From $DataIn.checktime_dd WHERE Estate = 1 Order by month Desc";
    $monthResult = mysql_query($monthSelectSql);
    while ($monthRow = mysql_fetch_assoc($monthResult)) {
        $tempMonth = $monthRow['month'];
        $monthSelect = $monthSelect == ''?$tempMonth:$monthSelect;

        if($tempMonth == $monthSelect){
            echo "<option value='$tempMonth' selected>$tempMonth</option>";
            //$SearchRows .= " AND LEFT(oldChecktime, 7) = '$monthSelect'";
        }else{
            echo "<option value='$tempMonth'>$tempMonth</option>";
        }

    }
    echo "</select>";

    echo "<select name='daySelect' id='daySelect' onchange='ResetPage(this.name)'>";
    $daySelectSql = "SELECT distinct SUBSTRING(oldChecktime, 9, 2) as day From $DataIn.checktime_dd WHERE Estate = 1 AND LEFT(oldChecktime, 7) = '$monthSelect' Order by day Desc";
    $daySelectSql = mysql_query($daySelectSql);
    while ($dayRow = mysql_fetch_assoc($daySelectSql)) {
        $tempday = $dayRow['day'];
        $daySelect = $daySelect == ''?$tempday:$daySelect;

        if($tempday == $daySelect){
            echo "<option value='$tempday' selected>$tempday</option>";
            $SearchRows .= " AND LEFT(oldChecktime, 10) = '$monthSelect-$daySelect'";
        }else{
            echo "<option value='$tempday'>$tempday</option>";
        }
    }
    echo "</select>";


    if ($FormalSign == '1' || $FormalSign == '') {
    $SearchRows.=" AND K.checknId != ''";
    }else{
    $SearchRows.=" AND K.checknId ='' ";
    }
}

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT K.Id,M.Number,K.checkioId,K.oldChecktime,K.targetChecktime,K.worktime,K.Date,K.Operator,M.Name,M.BranchId,M.JobId
FROM $DataIn.checktime_dd K 
LEFT JOIN $DataIn.checkinout C On C.Id = K.checkioId
LEFT JOIN $DataIn.staffmain M ON M.Number=C.Number 
WHERE 1 $SearchRows AND K.Estate=1 ORDER BY K.Id DESC,M.Estate";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
    do{
        $m=1;
        $Id=$myRow["Id"];
        $Name=$myRow["Name"];
        $Number=$myRow["Number"];
        $BranchId=$myRow["BranchId"];   
        $B_Query = mysql_query("SELECT Name FROM $DataIn.branchdata where 1 and Id=$BranchId LIMIT 1");     
        if($B_Query)    
        {
            $B_Result = mysql_fetch_array($B_Query);
            $Branch=$B_Result["Name"];  
        }           
        $JobId=$myRow["JobId"];
        $J_Query = mysql_query("SELECT Name FROM $DataIn.jobdata where 1 and Id=$JobId LIMIT 1",$link_id);
        if($J_Query)
        {
            $J_Result = mysql_fetch_array($J_Query);
            $Job=$J_Result["Name"];
        }
       
        
        $Month=$myRow["Month"];
        $Date=$myRow["Date"];
        $Locks=$myRow["Locks"];
        $Operator=$myRow["Operator"];
        include "../model/subprogram/staffname.php";

        $oldChecktime = $myRow['oldChecktime'];
        $targetChecktime = $myRow['targetChecktime'];
        $worktime = $myRow['worktime'];

        $ValueArray=array(
            array(0=>$Branch,       1=>"align='center'"),
            array(0=>$Job,      1=>"align='center'"),
            array(0=>$Number,       1=>"align='center'"),
            array(0=>$Name,         1=>"align='center'"),
            array(0=>$oldChecktime,      1=>"align='center'"),
            array(0=>$targetChecktime,       1=>"align='center'"),
            array(0=>$worktime.'h',       1=>"align='center'"),
            array(0=>$Date,         1=>"align='center'"),
            array(0=>$Operator,     1=>"align='center'")
            );
            
        $checkidValue=$Id;
        include "../model/subprogram/read_model_6.php";
        }while ($myRow = mysql_fetch_array($myResult));
    }
else{
    noRowInfo($tableWidth);
    }
//步骤7：
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>