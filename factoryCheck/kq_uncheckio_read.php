<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;             
$tableMenuS=450;
ChangeWtitle("$SubCompany 忘签考勤记录");
$funFrom="kq_uncheckio";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|部门|60|职位|60|员工ID|60|员工姓名|70|星期|80|出勤时间|150|出勤状态|80|数据来源|60|审核情况|60|操作员|50";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
    $SearchRows ="";
    echo"<select name='chooseMonth' id='chooseMonth' onchange='RefreshPage(\"$nowWebPage\")'>";
    $date_Result = mysql_query("SELECT DATE_FORMAT(CheckTime,'%Y-%m') AS Month FROM d7check.fakecheckinout WHERE dFrom=-1 group by DATE_FORMAT(CheckTime,'%Y-%m') order by CheckTime DESC",$link_id);
    if ($dateRow = mysql_fetch_array($date_Result)) {
        do{
            $dateValue=$dateRow["Month"];
            $chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
            if($chooseMonth==$dateValue){
                echo"<option value='$dateValue' selected>$dateValue</option>";
                $SearchRows.="and DATE_FORMAT(C.CheckTime,'%Y-%m')='$dateValue'";
                }
            else{
                echo"<option value='$dateValue'>$dateValue</option>";                   
                }
            }while($dateRow = mysql_fetch_array($date_Result));
        }
        echo"</select>&nbsp;";
    
    $FormalSign=$FormalSign==""?0:$FormalSign;
        $selStr="selFlag" . $FormalSign;
        $$selStr="selected";
        echo"<select name='FormalSign' id='FormalSign' onchange='RefreshPage(\"$nowWebPage\")'>
             <option value='0' $selFlag0>全部</option>
             <option value='1' $selFlag1>正式工</option>
             <option value='2' $selFlag2>试用期</option>";
        echo "</select>&nbsp;";
        if($FormalSign>0){$SearchRows.=" AND M.FormalSign='$FormalSign'";}
        
        $SearchRows=$SearchRows." and C.dFrom=-1";
    }
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
    $CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT C.Id,C.CheckTime,C.CheckType,C.dFrom,C.Estate,C.Locks,C.Operator,M.Number,M.Name,
    M.BranchId,M.JobId,M.KqSign
    FROM d7check.fakecheckinout C,$DataPublic.staffmain M
    WHERE 1 $SearchRows and C.Number=M.Number AND M.cSign='$Login_cSign'
    ORDER BY C.CheckTime DESC,M.BranchId,M.JobId,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
    do{
        $m=1;
        $Id=$myRow["Id"];
        $CheckTime=$myRow["CheckTime"];
        $MonthTemp=substr($CheckTime,0,7);
        $CheckType=$myRow["CheckType"];
        $dFrom=$myRow["dFrom"]=="0"?"考勤机器":"人事更新";
        $Number=$myRow["Number"];
        $Name=$myRow["Name"];       
        $BranchId=$myRow["BranchId"];               
        $B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
        $Branch=$B_Result["Name"];
        
        $JobId=$myRow["JobId"];     
        $J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
        $Job=$J_Result["Name"];
        $Operator=$myRow["Operator"];
        include "../model/subprogram/staffname.php";
        //星期确定
        $DateTemp=date("Y-m-d",strtotime($CheckTime));  
        $Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");   //星期数组
        $weekTemp=date("w",strtotime($DateTemp));                                   //当天属于星期几 
        $ddSTR="";
//日期类型确定:工作日\休息日\假日(休息日和假日不计迟到早退)
        $DateType=($weekTemp==6 || $weekTemp==0)?"X":"G";       
        $holidayResult = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE Date='$DateTemp'",$link_id);
        if($holidayRow = mysql_fetch_array($holidayResult)){
            switch($holidayRow["Sign"]){
                case "F":
                $DateType="F";
                $ddSTR="<br><div class='yellowB'>$holidayRow[Name]</div>";
                break;
                case "Y":
                $DateType="Y";
                $ddSTR="<br><div class='yellowB'>$holidayRow[Name]</div>";
                break;
                case "W":
                $DateType="W";
                $ddSTR="<br><div class='yellowB'>$holidayRow[Name]</div>";
                break;
                }
            }
//是否存在调班？是,则工作日变休息日,休息日变工作日
        //$rqddResult = mysql_query("SELECT * FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' or XDate='$DateTemp'",$link_id);
        $rqddResult = mysql_query("SELECT * FROM $DataIn.kqrqdd WHERE (GDate='$DateTemp' or XDate='$DateTemp') and Number='$Number'",$link_id);
        if($rqddRow = mysql_fetch_array($rqddResult)){          
            $ddSTR=$DateType=="X"?"<br><div class='yellowB'>调为工作日</div>":"<br><div class='yellowB'>调为休息日</div>";
            $DateType=$DateType=="X"?"G":"X";
            }
            
        $weekDay=$DateType."-"."星期".$Darray[$weekTemp].$ddSTR;                          //用于输出的星期标签
        //星期确定结束
        switch($CheckType){
                case "I":
                $CheckType="<div align='center' class='greenB'>上班签到</div>";
                break;
                case "O":
                $CheckType="<div align='center' class='greenB'>下班签退</div>";
                break;
                case "i":
                $CheckType="<div align='center' class='greenB'>加班签到</div>";
                break;
                case "o":
                $CheckType="<div align='center' class='greenB'>加班签退</div>";
                break;
                default:
                $CheckType="<div align='center' class='redB'>异常记录</div>";
                break;
                }
        $Estate=$myRow["Estate"];
        $Locks=$myRow["Locks"];
        //强制锁定已统计的记录
        $LockRemark="";
        if($Estate==0){
            $checkMonth=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Month='$MonthTemp' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
            if($checkRow = mysql_fetch_array($checkMonth)){
                $LockRemark="该月考勤统计已生成,禁止修改.";
                }
            $Estate="<div class='greenB'>已审核</div>";
            }
        else{
            $Estate="<div class='redB'>未审核</div>";
            }       
        $ValueArray=array(
            array(0=>$Branch,       1=>"align='center'"),
            array(0=>$Job,          1=>"align='center'"),
            array(0=>$Number,       1=>"align='center'"),
            array(0=>$Name,         1=>"align='center'"),
            array(0=>$weekDay,      1=>"align='center'"),
            array(0=>$CheckTime,    1=>"align='center'"),
            array(0=>$CheckType,    1=>"align='center'"),
            array(0=>$dFrom,        1=>"align='center'"),
            array(0=>$Estate,       1=>"align='center'"),
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
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>