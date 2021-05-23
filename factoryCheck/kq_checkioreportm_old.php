<?php
    $path = $_SERVER["DOCUMENT_ROOT"];
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceStatistic.php");
    include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WorkSchedule.php");
    include_once('FactoryClass/AttendanceTimeSetup.php');
    include_once('FactoryClass/AttendanceDatetype.php');
    include_once('FactoryClass/AttendanceInfo.php');
    include_once('FactoryClass/AttendanceCalculate.php');

    //步骤2：
    $nowWebPage ="kq_checkio_count";
    $fromWebPage="kq_checkio_read";
    $_SESSION["nowWebPage"]=$nowWebPage;
    $Parameter="fromWebPage,$fromWebPage";
    $toWebPage="kq_checkio_save";
    $nowMonth=date("Y-m");          //现在的月份，默认
    if($CheckMonth==""){ //如果没有填月份，则为默认的现在月份
        $CheckMonth=substr($CheckDate,0,7);
    }
    $FristDay=$CheckMonth."-01";
    $EndDay=date("Y-m-t",strtotime($FristDay));
    if($CheckMonth==$nowMonth){
        $Days=date("d")-1;
    }
    else{
        $Days=date("t",strtotime($FristDay));
    }
    
    $KqSignStr="";  
    if ($KqSign!="") {
        $KqSignStr=" AND M.KqSign='$KqSign'";
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
                            GROUP BY Q.Number ) A GROUP BY A.Number 
                            ORDER BY A.BranchId,A.JobId,A.Number";
    $CheckStaff= mysql_query($CheckStaffSql,$link_id);
    if($StaffRow = mysql_fetch_array($CheckStaff)){
        $StaffList="<select name='Number' id='Number' onchange='document.form1.submit()'>";
        $k=1;
        do{
            $NumberT=$StaffRow["Number"];
            $Number=$Number==""?$NumberT:$Number;
            $NameT=$StaffRow["Name"];
            $JobName=$StaffRow["JobName"];
            if($Number==$NumberT){
                $StaffList.="<option value='$NumberT' selected>$k $JobName $NameT $NumberT</option>";
            }
            else{
                $StaffList.="<option value='$NumberT' >$k $JobName $NameT $NumberT</option>";
            }
            $k++;
        }while ($StaffRow = mysql_fetch_array($CheckStaff));
        $StaffList.="</select>&nbsp;";
    }
    $SelectCode=$StaffList."
    <input name='CheckMonth' type='text' id='CheckMonth' size='10' maxlength='7' value='$CheckMonth' onchange='javascript:document.form1.submit();'>&nbsp;
    <select name='CountType' id='CountType' onchange='document.form1.submit()'>
    <option value='0' $CountType0>日考勤统计</option>
    <option value='1' $CountType1>月考勤统计</option>
    </select> &nbsp; ";

    $selStr="selFlag" . $KqSign;
    $$selStr="selected";
    $SelectCode=$SelectCode. "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''>
      <option  value=''   $selFlag>--全部--</option> 
      <option  value='1'  $selFlag1>考勤有效</option>
       <option value='2' $selFlag2>考勤参考</option>
      </select>";
    //如果是之前月份检查统计是否存在:如果是当月，则只有离职员工可以保存
    $checkSql = mysql_query("SELECT Id FROM $DataIn.kqdataother WHERE 1 and Number=$Number and Month='$CheckMonth' ORDER BY Id LIMIT 1",$link_id);
    if($checkRow = mysql_fetch_array($checkSql)){
        $SaveSTR="NO";
    }
    $checkSql1 = mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE 1 and Number=$Number and Estate=0 ORDER BY Id LIMIT 1",$link_id);
    if($checkRow1 = mysql_fetch_array($checkSql1)){
        $Days=date("t",strtotime($FristDay));
    }
    include "../model/subprogram/add_model_t.php";
    echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'>";
    echo"<tr class='' align='center'>
        <td width='30' rowspan='2' class='A1111'>日期</td>
        <td width='50' rowspan='2' class='A1101'>星期</td>
        <td width='45' rowspan='2' class='A1101'>类别</td>
        <td height='20' colspan='2' class='A1101'>签卡记录</td>
        <td width='45' rowspan='2' class='A1101'>应到<br>工时</td>
        <td width='45' rowspan='2' class='A1101'>实到<br>工时</td>
        <td width='120' colspan='3' class='A1101'>加点加班工时(+直落)</td>
        <td width='30' rowspan='2' class='A1101'>迟到</td>
        <td width='30' rowspan='2' class='A1101'>早退</td>
        <td colspan='9' class='A1101'>请、休假工时</td>
        <td width='30' rowspan='2' class='A1101'>缺勤<br>工时</td>
        <td width='30' rowspan='2' class='A1101'>旷工<br>工时</td>
        <td width='30' rowspan='2' class='A1101'>无效<br>工时</td>
        <td width='30' rowspan='2' class='A1101'>有薪<br>工时</td>
    </tr>
    <tr class='' align='center'>
        <td width='45' height='20' class='A0101'>签到</td>
        <td width='45' class='A0101'>签退</td>
        <td width='38' class='A0101'>1.5倍</td>
        <td width='38' class='A0101'>2倍</td>
        <td width='38' class='A0101'>3倍</td>
        <td width='25' class='A0101'>事</td>
        <td width='25' class='A0101'>病</td>     
        <td width='25' class='A0101'>无</td>
        <td width='25' class='A0101'>年</td>
        <td width='25' class='A0101'>补</td>
        <td width='25' class='A0101'>婚</td>
        <td width='25' class='A0101'>丧</td>
        <td width='25' class='A0101'>产</td>
        <td width='25' class='A0101'>工</td>
    </tr>";

    /*******************************开始做分析***************************************/
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
            $datetypeInfo = '(离)';
        }
        if($attendanceResult['lackWorkHours'] < 0 && strtotime($attendanceTime['start']) <= strtotime($CheckDate.' '.$sheet->mCheckTime['start'])){
            $attendanceResult['workHours'] += $attendanceResult['lackWorkHours'];
            $attendanceTime['end'] = date('Y-m-d H:i', strtotime($attendanceTime['end'])+$attendanceResult['lackWorkHours']*3600);
            $attendanceResult['lackWorkHours'] = '';
        }
        $attendanceStatistic->statistic($attendanceResult);
        /*******************************************************************/
        echo"<tr align='center'><td class='A0111' $rowBgcolor>$j</td>";
        echo"<td class='A0101'>".$weekInfo."&nbsp;</td>";
        $showDateType = '';
        if($datetype['morning'] === $datetype['afternoon'] || ($datetype['morning'] != '' && $datetype['afternoon'] == '') || ($datetype['morning'] == '' && $datetype['afternoon'] != '')){
            $showDateType = $datetype['morning'] != ''?$datetype['morning']:$datetype['afternoon'];
        }
        else{
            $showDateType = $datetype['morning'].'/'.$datetype['afternoon'];
        }
        echo"<td class='A0101'><div $DateTypeColor>".$showDateType.$datetypeInfo."</div></td>";
        echo"<td class='A0101'><span $AIcolor>".substr($attendanceTime['start'], 11,5)."</span>&nbsp;</td>";
        echo"<td class='A0101'><span $AOcolor>".substr($attendanceTime['end'], 11,5)."</span>&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['defaultWorkHours']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['workHours']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['workdayOt']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['weekdayOt']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['holidayOt']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['beLate']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['beEarly']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['personalLeave']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['sickLeave']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['noPayLeave']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['annualLeave']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['bxLeave']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['marrayLeave']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['deadLeave']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['birthLeave']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['hurtLeave']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['lackWorkHours']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['kgHours']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['noPayHours']."&nbsp;</td>";
        echo"<td class='A0101'>".$attendanceResult['dkHours']."&nbsp;</td>";
        echo"</tr>";
    }
    $totleStatistic = $attendanceStatistic->outputByTag();
    echo"<tr align='center'>
    <td class='A0111' colspan='5' >合计</td>
    <td class='A0101'>".$totleStatistic["defaultWorkHours"]."<input name='Dhours' type='hidden' id='Dhours' value='".$totleStatistic["defaultWorkHours"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["workHours"]."<input name='Whours' type='hidden' id='Whours' value='".$totleStatistic["workHours"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic['workdayOt']."<input name='GOverTime' type='hidden' id='GOverTime' value='".$totleStatistic['workdayOt']."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic['weekdayOt']."<input name='XOverTime' type='hidden' id='XOverTime' value='".$totleStatistic['weekdayOt']."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic['holidayOt']."<input name='FOverTime' type='hidden' id='FOverTime' value='".$totleStatistic['holidayOt']."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["beLate"]."<input name='InLates' type='hidden' id='InLates' value='".$totleStatistic["beLate"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["beEarly"]."<input name='OutEarlys' type='hidden' id='OutEarlys' value='".$totleStatistic["beEarly"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["personalLeave"]."<input name='SJhours' type='hidden' id='SJhours' value='".$totleStatistic["personalLeave"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["sickLeave"]."<input name='BJhours' type='hidden' id='BJhours' value='".$totleStatistic["sickLeave"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["noPayLeave"]."<input name='WXJhours' type='hidden' id='WXJhours' value='".$totleStatistic["noPayLeave"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["annualLeave"]."<input name='YXJhours' type='hidden' id='YXJhours' value='".$totleStatistic["annualLeave"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["bxLeave"]."&nbsp;</td>
    <td class='A0101'>".$totleStatistic["marrayLeave"]."&nbsp;</td>
    <td class='A0101'>".$totleStatistic["deadLeave"]."&nbsp;</td>       
    <td class='A0101'>".$totleStatistic["birthLeave"]."&nbsp;</td>
    <td class='A0101'>".$totleStatistic["hurtLeave"]."&nbsp;</td>
    <td class='A0101'>".$totleStatistic["lackWorkHours"]."<input name='QQhours' type='hidden' id='QQhours' value='".$totleStatistic["lackWorkHours"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["kgHours"]."<input name='KGhours' type='hidden' id='KGhours' value='".$totleStatistic["kgHours"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["noPayHours"]."<input name='WXhours' type='hidden' id='WXhours' value='".$totleStatistic["noPayHours"]."'>&nbsp;</td>
    <td class='A0101'>".$totleStatistic["dkHours"]."<input name='dkhours' type='hidden' id='dkhours' value='".$totleStatistic["dkHours"]."'>&nbsp;</td>
    </tr>";

    echo"<tr class='' align='center'>
        <td width='30' rowspan='2' class='A1111'>日期</td>
        <td width='50' rowspan='2' class='A1101'>星期</td>
        <td width='45' rowspan='2' class='A1101'>类别</td>
        <td height='20' colspan='2' class='A1101'>签卡记录</td>
        <td width='45' rowspan='2' class='A1101'>应到<br>工时</td>
        <td width='45' rowspan='2' class='A1101'>实到<br>工时</td>
        <td width='120' colspan='3' class='A1101'>加点加班工时(+直落)</td>
        <td width='30' rowspan='2' class='A1101'>迟到</td>
        <td width='30' rowspan='2' class='A1101'>早退</td>
        <td colspan='9' class='A1101'>请、休假工时</td>
        <td width='30' rowspan='2' class='A1101'>缺勤<br>工时</td>
        <td width='30' rowspan='2' class='A1101'>旷工<br>工时</td>
        <td width='30' rowspan='2' class='A1101'>无效<br>工时</td>
        <td width='30' rowspan='2' class='A1101'>有薪<br>工时</td>
    </tr>
    <tr class='' align='center'>
        <td width='45' height='20' class='A0101'>签到</td>
        <td width='45' class='A0101'>签退</td>
        <td width='38' class='A0101'>1.5倍</td>
        <td width='38' class='A0101'>2倍</td>
        <td width='38' class='A0101'>3倍</td>
        <td width='25' class='A0101'>事</td>
        <td width='25' class='A0101'>病</td>     
        <td width='25' class='A0101'>无</td>
        <td width='25' class='A0101'>年</td>
        <td width='25' class='A0101'>补</td>
        <td width='25' class='A0101'>婚</td>
        <td width='25' class='A0101'>丧</td>
        <td width='25' class='A0101'>产</td>
        <td width='25' class='A0101'>工</td>
    </tr></table>";

?>