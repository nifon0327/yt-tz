<?php
    include "../model/modelhead.php";
    $path = $_SERVER["DOCUMENT_ROOT"];
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator.php");
    $From=$From==""?"read":$From;
    $otherDayOverTimeStarndard = 8;
    //需处理参数
    $ColsNumber=23;             
    $tableMenuS=500;
    ChangeWtitle("$SubCompany 考勤月统计");
    $funFrom="kq_statistic";
    $nowWebPage=$funFrom."_read";
    $sumCols="";        //求和列
    $Th_Col="选项|40|序号|30|月份|50|部门|50|职位|50|员工ID|50|姓名|50|应到|40|实到|40|1.5倍|50|2倍工时|50|3倍工时|50|迟到次数|40|迟到次数|40|事假|40|病假|40|有薪|40|无薪|40|缺勤工时|40|无效工时|40|旷工工时|40|有薪工时|40";

    $Pagination=$Pagination==""?1:$Pagination;
    $Page_Size = 200;
    $ActioToS="1,2,4,11, 26, 175";//26,

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
    $SearchRows ="";
    $date_Result = mysql_query("SELECT Month FROM $DataIn.kqdata WHERE 1 group by month order by Id DESC",$link_id);
    if ($dateRow = mysql_fetch_array($date_Result)){
        echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
        do{
            $dateValue=$dateRow["Month"];
            $chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
            if($chooseMonth==$dateValue){
                echo"<option value='$dateValue' selected>$dateValue</option>";
                $SearchRows.="and K.Month='$chooseMonth'";
                }
            else{
                echo"<option value='$dateValue'>$dateValue</option>";
                }
            }while($dateRow = mysql_fetch_array($date_Result));
            echo"</select>&nbsp;";
        }   
        $FormalSign=$FormalSign==""?0:$FormalSign;
        $selStr="selFlag" . $FormalSign;
        $$selStr="selected";
        echo"<select name='FormalSign' id='FormalSign' onchange='RefreshPage(\"$nowWebPage\")'>
             <option value='0' $selFlag0>全部</option>
             <option value='1' $selFlag1>正式工</option>
             <option value='2' $selFlag2>试用期</option>";
        echo "</select>&nbsp;";
        if($FormalSign>0)$SearchRows.=" AND M.FormalSign='$FormalSign'";    
        
        $SelectTB="M";$SelectFrom=1; 
        //选择地点
        include "../model/subselect/WorkAdd.php"; 
        
        $selStr="selFlag" . $KqSign;
        $$selStr="selected";
        echo "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''>
              <option  value=''   $selFlag>--全部--</option> 
              <option  value='1'  $selFlag1>考勤有效</option>
               <option value='2' $selFlag2>考勤参考</option>
              </select>";
        $KqSignStr="";  
        if ($KqSign!="") {
            $KqSignStr=" AND M.KqSign='$KqSign'";
        }       
    }
    echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
    $CencalSstr";
    //步骤5：
    include "../model/subprogram/read_model_5.php";
    //List_Title($Th_Col,"1",0);
?>
 <table border='0'    cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border-top-width:0px' >
    <tr height='20' class='' align='center'>
      <td width='40' rowspan='2' class='A1111'>选项</td>
      <td width='30' rowspan='2' class='A1101'>序号</td>
      <td width='50' rowspan='2' class='A1101'>月份</td>
      <td width='50' rowspan='2' class='A1101'>部门</td>
      <td width='50' rowspan='2' class='A1101'>职位</td>
      <td width='50' rowspan='2' class='A1101'>员工ID</td>
      <td width='50' rowspan='2' class='A1101'>姓名</td>
      <td width='40' rowspan='2' class='A1101'>应到<br>工时</td>
      <td width='40' rowspan='2' class='A1101'>实到<br>工时</td>
      <td width='50' rowspan="2" class='A1101'>1.5倍薪<br>工时</td>
     <td width='50' rowspan="2" class='A1101'>2倍薪<br>工时</td>
     <td width='50' rowspan="2" class='A1101'>3倍薪<br>工时</td>
      <td width='40' rowspan='2' class='A1101'>迟到<br>次数</td>
      <td width='40' rowspan='2' class='A1101'>早退<br>次数</td>
      <td colspan='4' class='A1101'>请、休假工时</td>
      <td width='40' rowspan='2' class='A1101'>缺勤<br>工时</td>
      <td width='40' rowspan='2' class='A1101'>无薪<br>工时</td>
      <td width='40' rowspan='2' class='A1101'>旷工<br>工时</td>
      <td width='40' rowspan='2' class='A1101'>有薪<br>工时</td>        
    </tr>
    <tr class='' align='center'>
      <td width='40' class='A0101' height='20'>事假</td>
      <td width='40' class='A0101'>病假</td>
      <td width='40' class='A0101'>有薪</td>
      <td width='40' class='A0101'>无薪</td>
    </tr>
  </table>
<?php
    $i=1;
    $j=($Page-1)*$Page_Size+1;
       $mySql = "SELECT K.*,M.Name,M.Estate,B.Name AS Branch,J.Name AS Job
FROM $DataIn.kqdata K 
LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
LEFt JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE 1 $SearchRows AND M.JobId!=38 AND M.WorkAdd!=6 AND M.Number != '10744' ORDER BY K.Month DESC,M.Estate DESC,M.BranchId,M.JobId,K.Number";
    
    $myResult = mysql_query($mySql." $PageSTR",$link_id);
    if($myRow = mysql_fetch_array($myResult)){
    do{
        $m=1;
        $Id=$myRow["Id"];
        $Month=$myRow["Month"];
        $Number=$myRow["Number"];
        $Name=$myRow["Name"];
        $Branch=$myRow["Branch"];       
        $Job=$myRow["Job"];   

        //减去隐藏的双休
        $descHours = 0;
        if($chooseMonth == '2015-10'){
            $hiddenMonthSql = "SELECT checkid FROM $DataIn.disablecheckid WHERE left(checkid, 7)='$Month'"; 
            $hiddenMonthResult = mysql_query($hiddenMonthSql);
            while($hiddenMontRow = mysql_fetch_assoc($hiddenMonthResult)){

                $CheckDate = $hiddenMontRow['checkid'];

                //echo $CheckDate.'<br>';

                $staff = new AttendanceAvatar($Number, $DataIn, $DataPublic, $link_id);
                $staff->setupAttendanceData($Number, $CheckDate, $DataIn, $DataPublic, $link_id);
                $staff->attendanceSetup($DataIn, $DataPublic, $link_id);

                $dayAttendanceInfomation =  $staff->getInfomationByTag();
                //print_r($dayAttendanceInfomation);
                //echo $CheckDate.'  '.$dayAttendanceInfomation["weekOtTime"].'<br>';
                $descHours += $dayAttendanceInfomation["weekOtTime"]>$otherDayOverTimeStarndard?$otherDayOverTimeStarndard:$dayAttendanceInfomation["weekOtTime"];
                //$descHours += $dayAttendanceInfomation["weekZlHours"];
            }
        }
        //2倍加班工时
        if($Month<"2013-05"){
            $XhoursResult=mysql_fetch_array(mysql_query("SELECT xHours,fHours FROM $DataIn.hdjbsheet WHERE Number=$Number and Month='$Month'",$link_id));
            $Xhours=$XhoursResult["xHours"];
            $FHours=$XhoursResult["fHours"];
        }
        else{
            $Xhours=$myRow["Xhours"];
            $Fhours=$myRow["Fhours"];
        }
        //echo $Month;
        //include "../public/kqcode/checkio1_model_zl.php";
        $Dhours=zerotospace($myRow["Dhours"]);      //应到工时
        $Whours=zerotospace($myRow["Whours"]);      //实到工时
        //$Ghours=zerotospace($myRow["Ghours"]+$myRow["GDropTime"]);      //1.5倍工时
        //$Xhours=zerotospace($Xhours-$descHours+$myRow["XDropTime"]);       //2倍工时
        $Ghours=zerotospace($myRow["Ghours"]);      //1.5倍工时
        //echo "$Xhours-$descHours<br>";
        $XhoursReal = $Xhours-$descHours < 0 ? 0 : $Xhours-$descHours;
        $Xhours=zerotospace($XhoursReal);       //2倍工时
        //echo $descHours.'<br>';
        $Fhours=zerotospace('');       //3倍工时
        $InLates=zerotospace($myRow["InLates"]);    //迟到次数
        $OutEarlys=zerotospace($myRow["OutEarlys"]);//早退次数
        $SJhours=zerotospace($myRow["SJhours"]);    //事假工时
        $BJhours=zerotospace($myRow["BJhours"]);    //病假工时
        $BXhours=zerotospace($myRow["BXhours"]);    //补休工时 
        $YXJhours=zerotospace($myRow["YXJhours"]);  //有薪假工时:婚、丧等有薪假
        $WXJhours=zerotospace($myRow["WXJhours"]);  //无薪假工时
        $QQhours=zerotospace($myRow["QQhours"]);    //缺勤工时
        $WXhours=zerotospace($myRow["WXhours"]);    //无效工时
        $KGhours=zerotospace($myRow["KGhours"]);    //旷工工时
        $dkhours=zerotospace($myRow["dkhours"]);    //有薪工时
        $Estate=$myRow["Estate"];
        $Locks=$myRow["Locks"];
                
        //分离职和未离职
        if($Estate==1){//在职
            $Name="<a href='kq_checkio_count.php?Number=$Number&CheckMonth=$Month&CountType=1&needSign=no' target='_blank'><span class='greenB'>$Name</span></a>";
        }
        else{           //离职
            $Name="<a href='kq_checkio_count.php?Number=$Number&CheckMonth=$Month&CountType=1&needSign=no' target='_blank'><span class='redB'>$Name</span></a>";
        }
        //是否已生成薪资，是，则强制锁定
        $LockRemark="";
        // $checkMonth=mysql_query("SELECT Id FROM $DataIn.cwxzsheet WHERE Month='$chooseMonth' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
        // if($checkRow = mysql_fetch_array($checkMonth)){
        //     $LockRemark="该月已生成薪资,禁止修改.";
        // }
        $ValueArray=array(
            array(0=>$Month,    1=>"align='center'"),
            array(0=>$Branch,   1=>"align='center'"),
            array(0=>$Job,      1=>"align='center'"),
            array(0=>$Number,   1=>"align='center'"),
            array(0=>$Name,     1=>"align='center'"),
            array(0=>$Dhours,   1=>"align='center'"),
            array(0=>$Whours,   1=>"align='center'"),
            array(0=>$Ghours,   1=>"align='center'"),
            array(0=>$Xhours,   1=>"align='center'"),
            array(0=>$Fhours,   1=>"align='center'"),
            array(0=>$InLates,  1=>"align='center'"),
            array(0=>$OutEarlys,1=>"align='center'"),
            array(0=>$SJhours,  1=>"align='center'"),
            array(0=>$BJhours,  1=>"align='center'"),
            array(0=>$YXJhours, 1=>"align='center'"),
            array(0=>$WXJhours, 1=>"align='center'"),
            array(0=>$QQhours,  1=>"align='center'"),
            array(0=>$WXhours,  1=>"align='center'"),
            array(0=>$KGhours,  1=>"align='center'"),
            array(0=>$dkhours,  1=>"align='center'")
            );
        $checkidValue=$Id;
        include "../model/subprogram/read_model_6.php";
        }while ($myRow = mysql_fetch_array($myResult));
    }
else{
    noRowInfo($tableWidth);
    }
?>
 <table border='0'    cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border-top-width:0px' >
    <tr height='20' class='' align='center'>
      <td width='40' rowspan='2' class='A0111'>选项</td>
      <td width='30' rowspan='2' class='A0101'>序号</td>
      <td width='50' rowspan='2' class='A0101'>月份</td>
      <td width='50' rowspan='2' class='A0101'>部门</td>
      <td width='50' rowspan='2' class='A0101'>职位</td>
      <td width='50' rowspan='2' class='A0101'>员工ID</td>
      <td width='50' rowspan='2' class='A0101'>姓名</td>
      <td width='40' rowspan='2' class='A0101'>应到<br>工时</td>
      <td width='40' rowspan='2' class='A0101'>实到<br>工时</td>
      <td width='50' rowspan="2" class='A0101'>1.5倍薪<br>工时</td>
     <td width='50' rowspan="2" class='A0101'>2倍薪<br>工时</td>
     <td width='50' rowspan="2" class='A0101'>3倍薪<br>工时</td>
      <td width='40' rowspan='2' class='A0101'>迟到<br>次数</td>
      <td width='40' rowspan='2' class='A0101'>早退<br>次数</td>
      <td width='40' class='A0101'>事假</td>
      <td width='40' class='A0101'>病假</td>
      <td width='40' class='A0101'>有薪</td>
      <td width='40' class='A0101'>无薪</td>
      <td width='40' rowspan='2' class='A0101'>缺勤<br>工时</td>
      <td width='40' rowspan='2' class='A0101'>无薪<br>工时</td>
      <td width='40' rowspan='2' class='A0101'>旷工<br>工时</td>
      <td width='40' rowspan='2' class='A0101'>有薪<br>工时</td>        
    </tr>
    <tr class='' align='center'>
      <td colspan='4' class='A0101' height='20'>请、休假工时</td>
    </tr>
  </table><?php 
//步骤7：
echo '</div>';
$myResult0 = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult0);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

