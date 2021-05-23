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

    if($CheckDate==""){
        $CheckDate=date("Y-m-d");
    }
    $CheckMonth=substr($CheckDate,0,7);
    $SelectCode="<input name='CheckDate' type='text' id='CheckDate' size='10' maxlength='10' value='$CheckDate' onchange='javascript:document.form1.submit();'>&nbsp;
<select name='CountType' id='CountType' onchange='document.form1.submit()'><option value='0' $CountType0>日考勤统计</option><option value='1' $CountType1>月考勤统计</option></select>";
    $selStr="selFlag" . $KqSign;
    $$selStr="selected";
    $SelectCode=$SelectCode. "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''>
      <option  value=''   $selFlag>--全部--</option> 
      <option  value='1'  $selFlag1>考勤有效</option>
       <option value='2' $selFlag2>考勤参考</option>
      </select>";
    $KqSignStr="";  
    if ($KqSign!="") {
        $KqSignStr=" AND M.KqSign='$KqSign'";
    }
    $SaveFun="&nbsp;";
    $CustomFun="<a href='kq_checkio_print.php?CheckDate=$CheckDate' target='_blank' $onClickCSS>列印</a>&nbsp;&nbsp;";
    $SaveSTR="NO";
    include "../model/subprogram/add_model_t.php";

    //步骤4：星期处理
    $ToDay=$CheckDate;//计算当天
    $NowToDay=date("Y-m-d");//
    //2     计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
    $Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
    $weekDay=date("w",strtotime($CheckDate));    
    $weekInfo="星期".$Darray[$weekDay];   

    echo"<input name='kqList' type='hidden' id='kqList'>";
    echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF' id='kqTable'>
    <tr><td colspan='27' class='A0011'>当天是：$weekInfo</td></tr>";
    echo"<tr class=''>
            <td width='30' rowspan='2' class='A1111'><div align='center'>序号</div></td>
            <td width='40' rowspan='2' class='A1101'><div align='center'>编号</div></td>
            <td width='50' rowspan='2' class='A1101'><div align='center'>姓名</div></td>
            <td width='100' rowspan='2' class='A1101'><div align='center'>小组</div></td>
            <td width='50' rowspan='2' class='A1101'><div align='center'>现职</div></td>
            <td width='50' rowspan='2' class='A1101'><div align='center'>日期<br>类别</div></td>
            <td height='19' width='80' colspan='2' class='A1101'><div align='center'>签卡记录</div></td>
            <td width='30' rowspan='2' class='A1101'><div align='center'>应到<br>工时</div></td>
            <td width='30' rowspan='2' class='A1101'><div align='center'>实到<br>工时</div></td>
            <td width='120' colspan='3' class='A1101'><div align='center'>加点加班工时(+直落)</div></td>
            <td width='30' rowspan='2' class='A1101'><div align='center'>迟到</div></td>
            <td width='30' rowspan='2' class='A1101'><div align='center'>早退</div></td>
            <td width='160' colspan='9' class='A1101'><div align='center'>请、休假工时</div></td>
            <td width='30' rowspan='2' class='A1101'><div align='center'>缺勤<br>工时</div></td>
            <td width='30' rowspan='2' class='A1101'><div align='center'>旷工<br>工时</div></td>
            <td width='30' rowspan='2' class='A1101'>无效<br>工时</td>
            <td width='30' rowspan='2' class='A1101'>有薪<br>工时</td>
        </tr>
        <tr class=''>
            <td width='40' heigh38t='20'  align='center' class='A0101'>签到</td>
            <td width='40' class='A0101' align='center'>签退</td>
            <td width='38' class='A0101' align='center'>1.5倍</td>
            <td width='38' class='A0101' align='center'>2倍</td>
            <td width='38' class='A0101' align='center'>3倍</td>
            <td width='20' class='A0101' align='center'>事</td>
            <td width='20' class='A0101' align='center'>病</td>      
            <td width='20' class='A0101' align='center'>无</td>
            <td width='20' class='A0101' align='center'>年</td>
            <td width='20' class='A0101' align='center'>补</td>
            <td width='20' class='A0101' align='center'>婚</td>
            <td width='20' class='A0101' align='center'>丧</td>
            <td width='20' class='A0101' align='center'>产</td>
            <td width='20' class='A0101' align='center'>工</td>
        </tr>";

    $MySql="SELECT M.Number,M.Name,J.Name AS Job,G.GroupName FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId 
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE M.cSign='$Login_cSign' $KqSignStr AND
((M.Number NOT IN (SELECT K.Number FROM $DataPublic.redeployk K GROUP BY K.Number ORDER BY K.Id) and M.KqSign<3)
OR(
    M.Number IN(
        SELECT K.Number FROM $DataPublic.redeployk K 
            INNER JOIN(
                SELECT Number,max(Month) as Month FROM $DataPublic.redeployk group by Number) k2 ON K.Number=k2.Number and K.Month=k2.Month     
            WHERE K.ActionIn<3 and K.Month<='$CheckMonth'))
OR(
    M.Number IN(
        SELECT Ka.Number FROM $DataPublic.redeployk Ka 
            INNER JOIN(
                SELECT Number,min(Month) as Month FROM $DataPublic.redeployk WHERE Month>'$CheckMonth' group by Number) k2a ON Ka.Number=k2a.Number and Ka.Month=k2a.Month 
            WHERE Ka.ActionOut<3)))
and left(M.ComeIn,7) <='$CheckMonth' 
and M.Number NOT IN (SELECT D.Number FROM $DataPublic.dimissiondata D WHERE D.Number=M.Number and  D.outDate<'$CheckDate')
AND (M.BranchId IN ( 6, 7, 8 ) OR M.JobId = 10 )
ORDER BY M.BranchId,M.GroupId,M.Number";
    $mySqlResult = mysql_query($MySql);
    $attendanceStatistic = new AttendanceInfo();
    $timeSetup = new AttendanceTimeSetup('d7check', $DataPublic, $link_id);
    $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
    while ($myRow = mysql_fetch_assoc($mySqlResult)) {
        $Number = $myRow['Number'];
        $Name = $myRow['Name'];
        $GroupName = $myRow['GroupName'];
        $Job = $myRow['Job'];
        $attendanceTime = $timeSetup->setupTime($Number, $CheckDate);
        //$sheet = new WorkScheduleSheet($Number, $CheckDate, $attendanceTime['start'], $attendanceTime['end']);
        $datetype = $datetypeModle->getDatetype($Number, $CheckDate, $sheet);
        //获取时间
        //根据加班时间确定考勤签退时间
        $checkIsOutOfWorkResult=mysql_query("SELECT A.Id,A.ComeIn,B.outDate FROM $DataPublic.staffmain A
                         Left Join $DataPublic.dimissiondata B On B.Number = A.Number 
                         WHERE A.Number='".$Number."' and (A.ComeIn>'$CheckDate' OR A.Number IN(SELECT B.Number FROM $DataPublic.dimissiondata WHERE B.Number='".$Number."' and B.OutDate<'$CheckDate'))",$link_id);
        $attendanceTime = $timeSetup->setupTime($Number, $CheckDate);
        $sheet = new WorkScheduleSheet($Number, $CheckDate, $attendanceTime['start'], $attendanceTime['end']);
        $sheet->setDefault();
        $datetype = $datetypeModle->getDatetype($Number, $CheckDate, $sheet);
        if(!$checkDRow = mysql_fetch_array($checkIsOutOfWorkResult)){
            //获取时间
            //根据加班时间确定考勤签退时间
            if($CheckMonth >= '2014-03'){
                $otTime = 0;
                $otTimeSql = "SELECT workday, weekday, holiday FROM $DataIn.kqovertime WHERE otDate = '$CheckDate' Limit 1";
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
            else{
                $otTime = 2;
            }
            $datetypeInfo = '';
            if(($datetype['morning'] === 'G' || $datetype['afternoon'] === 'G') && $attendanceTime['start'] != ''){
                if(strtotime($attendanceTime['end']) - strtotime($CheckDate.' '.$sheet->aCheckTime['end']) > 90*60 && $otTime != 0){
                    $attendanceTime['end'] = substr(date('Y-m-d H:i', strtotime($CheckDate.' '.$sheet->otStartTime['start'])+$otTime*3600), 0, 15).substr($attendanceTime['end'], 15, 2);
                }
                else if(strtotime($attendanceTime['end']) > strtotime($CheckDate.' '.$sheet->aCheckTime['end'])){
                    $attendanceTime['end'] = substr(date('Y-m-d H:i', strtotime($CheckDate.' '.$sheet->aCheckTime['end'])), 0, 15).substr($attendanceTime['end'], 15, 2);
                }
            }
            else{
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
        $showDateType = '';
        if($datetype['morning'] === $datetype['afternoon'] || ($datetype['morning'] != '' && $datetype['afternoon'] == '') || ($datetype['morning'] == '' && $datetype['afternoon'] != '')){
            $showDateType = $datetype['morning'] != ''?$datetype['morning']:$datetype['afternoon'];
        }
        else{
            $showDateType = $datetype['morning'].'/'.$datetype['afternoon'];
        }

        echo"<tr align='center'><td class='A0111'>$i</td>";
        echo"<td class='A0101'>$Number</td>";
        echo"<td class='A0101' align='left'>$Name</td>";
        echo"<td class='A0101' align='left'>$GroupName</td>";
        echo"<td class='A0101' align='left'>$Job</td>";
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

    echo"
        <tr class=''>
            <td rowspan='2' class='A0111'><div align='center'>序号</div></td>
            <td rowspan='2' class='A0101'><div align='center'>编号</div></td>
            <td rowspan='2' class='A0101'><div align='center'>姓名</div></td>
            <td rowspan='2' class='A0101'><div align='center'>小组</div></td>
            <td rowspan='2' class='A0101'><div align='center'>现职</div></td>
            <td rowspan='2' class='A0101'><div align='center'>日期<br>类别</div></td>
            <td height='19' colspan='2' class='A0101'><div align='center'>签卡记录</div></td>
            <td rowspan='2' class='A0101'><div align='center'>应到<br>工时</div></td>
            <td rowspan='2' class='A0101'><div align='center'>实到<br>工时</div></td>
            <td colspan='3' class='A0101'><div align='center'>加点加班工时(+直落)</div></td>
            <td rowspan='2' class='A0101'><div align='center'>迟到</div></td>
            <td rowspan='2' class='A0101'><div align='center'>早退</div></td>
            <td colspan='9' class='A0101'><div align='center'>请、休假工时</div></td>
            <td rowspan='2' class='A0101'><div align='center'>缺勤<br>工时</div></td>
            <td rowspan='2' class='A0101'><div align='center'>旷工<br>工时</div></td>
            <td width='30' rowspan='2' class='A1101'>无效<br>工时</td>
            <td width='30' rowspan='2' class='A1101'>有薪<br>工时</td>
        </tr>
        <tr class=''>
            <td heigh38t='20'  align='center' class='A0101'>签到</td>
            <td class='A0101' align='center'>签退</td>
            <td class='A0101' align='center'>1.5倍</td>
            <td class='A0101' align='center'>2倍</td>
            <td class='A0101' align='center'>3倍</td>
            <td class='A0101' align='center'>事</td>
            <td class='A0101' align='center'>病</td>     
            <td class='A0101' align='center'>无</td>
            <td class='A0101' align='center'>年</td>
            <td class='A0101' align='center'>补</td>
            <td class='A0101' align='center'>婚</td>
            <td class='A0101' align='center'>丧</td>
            <td class='A0101' align='center'>产</td>
            <td class='A0101' align='center'>工</td>
        </tr></table>";

?>