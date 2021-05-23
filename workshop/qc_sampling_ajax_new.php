<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";

      //$Floor=$Floor==""?6:$Floor;//送货楼层
      $Floor=6;
      $LineNo=$LineNo==""?"D":$LineNo;
      $Line=$Line==""?"1":$Line;
      
      $LineResult=mysql_fetch_array(mysql_query("SELECT C.Id  FROM  $DataIn.qc_scline C  WHERE  C.LineNo='$LineNo'  AND C.Floor='$Floor' LIMIT 1",$link_id));
      $LineId=$LineResult["Id"]==""?1:$LineResult["Id"];
      
      $stuffidResult=mysql_fetch_array(mysql_query("SELECT StuffId  FROM  $DataIn.qc_currentcheck   WHERE  Id='$Line' LIMIT 1",$link_id));
      $checkStuffId=$stuffidResult["StuffId"]==""?0:$stuffidResult["StuffId"];
      
      
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";   

     $m=0;
    
    $TotalCount=0;
    $WaitQty=0;//未品检总数
    
    //员工编号
    $staffnumber = '';
    $staffname = '';
    $getStaffSql = "SELECT A.Number, A.stuffId, B.Name From $DataPublic.qc_currentcheck A
                    LEFT JOIN $DataPublic.staffmain B ON A.Number = B.Number
                    WHERE A.Id = $Line";

    //echo $getStaffSql;

    $staffResult = mysql_query($getStaffSql);
    $staffRow = mysql_fetch_assoc($staffResult);
    $staffname = $staffRow['Name'];
    $staffnumber = $staffRow['Number'];
    $currentStuffId = $staffRow['stuffId'];

    //待检总数量
    $myResult=mysql_query("SELECT SUM(A.Qty) AS Qty,SUM(A.djQty) AS djQty
    FROM (
    SELECT  S.StuffId,S.Qty,IFNULL(SUM(C.Qty),0) AS djQty
                FROM $DataIn.gys_shsheet S 
                LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
                LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId 
                WHERE  S.Estate=2 AND S.SendSign IN(0,1)  AND M.Floor='$Floor' GROUP BY S.Id 
    )A  
    ORDER BY FIELD(A.StuffId,$checkStuffId) DESC",$link_id); 
    $myRow = mysql_fetch_assoc($myResult);
    $Qty=$myRow["Qty"];
    $DjQty=$myRow["djQty"];
    $WaitQty+= $Qty-$DjQty;

    //当天登记总数
    if($staffnumber != ''){
        $todayDjSql = "SELECT sum(Qty) as qty From $DataIn.qc_cjtj WHERE Operator=$staffnumber AND LEFT(Date, 10) = '$curDate'";
        $todayDjResult = mysql_query($todayDjSql);
        $todayDjRow = mysql_fetch_assoc($todayDjResult);
        $todayDjQty = $todayDjRow['qty'];
    }else{
        $todayDjQty = 0;
    }

    $stuffResult=mysql_query("SELECT A.StuffId,SUM(A.cgQty) AS cgQty,SUM(A.Qty) AS Qty,A.Forshort,A.StuffCname,A.Picture,SUM(A.djQty) AS djQty,Max(A.QcDate) AS QcDate,K.datetime ,A.FrameCapacity
    FROM (
    SELECT  S.StuffId,(G.AddQty+G.FactualQty) AS cgQty,S.Qty, 
                 P.Forshort,D.StuffCname,D.Picture,IFNULL(SUM(C.Qty),0) AS djQty,Max(IFNULL(C.Date,'0000-00-00')) AS QcDate,D.FrameCapacity
                FROM $DataIn.gys_shsheet S 
                LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
                LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
                LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
                LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId 
                WHERE  S.Estate=2 AND S.SendSign IN(0,1)  AND M.Floor='$Floor' AND S.StuffId = '$currentStuffId' GROUP BY S.Id 
    )A  
    LEFT JOIN (SELECT StuffId,MAX(datetime) AS datetime FROM $DataIn.qc_currentcheck GROUP BY StuffId) K ON K.StuffId=A.StuffId     
    GROUP BY A.StuffId  ORDER BY FIELD(A.StuffId,$checkStuffId) DESC,datetime DESC,QcDate DESC",$link_id); 
    if($stuffRow = mysql_fetch_assoc($stuffResult)){
        $stuffname = $currentStuffId.'-'.$stuffRow['StuffCname'];
        $company = $stuffRow['Forshort'];
        $stuffQty=$stuffRow["Qty"];
        $stuffDjQty=$stuffRow["djQty"];
        $frameCapacity = intval($stuffRow['FrameCapacity']);
        if($frameCapacity != 0){
            $totleBox = $stuffQty%$frameCapacity == 0? intval($stuffQty/$frameCapacity):intval($stuffQty/$frameCapacity)+1;
            //$recordBox =  $stuffDjQty%$frameCapacity == 0? intval($stuffDjQty/$frameCapacity):intval($stuffDjQty/$frameCapacity)+1;
            $intvalBox = intval($stuffQty/$frameCapacity);
            if($stuffDjQty > $intvalBox * $frameCapacity){
                $recordBox = $totleBox;
            }else{
                $recordBox = intval($stuffDjQty/$frameCapacity);
            }
        }else{
            $totleBox = '-- --';
            $recordBox = '';
        }
    }



    $subResult=mysql_query("SELECT mStuffId,Relation FROM $DataIn.stuffcombox_bom WHERE StuffId='$currentStuffId' LIMIT 1 ",$link_id);  
    if($subRow = mysql_fetch_array($subResult)) {
        $mStuffId=$subRow["mStuffId"];
        $Relation=$subRow["Relation"];

        $staticSql = "SELECT concat('2') AS Sign,SUM(G.FactualQty+G.AddQty) * $Relation AS Qty, count(*) as other
                      FROM $DataIn.cg1_stocksheet G
                      WHERE G.StuffId = $mStuffId";

    }else{
        $staticSql = "SELECT concat('2') AS Sign,SUM(G.FactualQty+G.AddQty) AS Qty, count(*) as other
                  FROM $DataIn.cg1_stocksheet G
                  WHERE G.StuffId = $currentStuffId";
    }

    $staticSql.=" UNION ALL
                SELECT concat('4') AS Sign,SUM(R.Qty) AS Qty, count(*) as other 
                FROM $DataIn.ck1_rksheet R
                WHERE R.StuffId=$currentStuffId
                UNION ALL
                SELECT concat('5') AS Sign,SUM(Qty) AS Qty , '' as other 
                FROM $DataIn.ck7_bprk WHERE StuffId='$currentStuffId' AND  Estate=0
                UNION ALL
                SELECT concat('6') AS Sign,SUM(Qty) AS Qty , '' as other 
                FROM $DataIn.ck2_thsheet WHERE StuffId='$currentStuffId' AND  Estate=0
                UNION ALL
                SELECT concat('6') AS Sign,SUM(Qty) AS Qty , '' as other 
                FROM $DataIn.ck12_thsheet WHERE StuffId='$currentStuffId' AND  Estate=0";

    //echo $staticSql;

    $staticResult = mysql_query($staticSql);
    $staticCgQty = 0;$cgCount = 0;
    $staticRkQty = 0;$rkCount = 0;
    $staticBpQty = 0;$bpRate = 0;
    $staticThQty = 0;$thRate = 0;

    while($staticRow = mysql_fetch_assoc($staticResult)){
        $sign = $staticRow['Sign'];
        $qty = intval($staticRow['Qty']);
        $other = $staticRow['other'];

        switch ($sign) {
            case '2':{
                $staticCgQty += $qty;
                $cgCount += $other; 
            }
            break;
            case '4':{
                $staticRkQty += $qty;
                $rkCount += $other;
            }
            break;
            case '5':{
                $staticBpQty += $qty;
            }
            break;
            case '6':{
                $staticThQty += $qty;
            }
            break;
        }
    }

    if($staticRkQty != 0){
        $bpRate = number_format(($staticBpQty/$staticRkQty)*100,1);
        $thRate = number_format(($staticThQty/$staticRkQty)*100,1);
    }


    //重量显示
    $frameNo = 1;
    $weightGetSql = "SELECT stuffId,weight FROM $DataIn.qc_currentcheck WHERE Id in (1,2,3,4)";
    $weightReust = mysql_query($weightGetSql);
    while($weightRow = mysql_fetch_assoc($weightReust)){
        $tmpStuffId = $weightRow['stuffId'];
        if($tmpStuffId == $currentStuffId){
            $tmpWeight = $weightRow['weight']."<span style='font-size:45px;'>g</span>";
            $bgColor = 'sameFrame';
            $positionStyle = $frameNo.'l';
        }else{
            $tmpWeight = '--';
            $bgColor = 'differentFrame';
            $positionStyle = $frameNo;
        }

        //$seprateline = "<div class='seprateline float_right'></div>";

        $currentBg = '';
        if($frameNo == $Line){
            $currentBg = "class='currentNo'";
        }else{
        }


        $weightFrames .= "<td $currentBg class='rightLine bottomLine'>
                            <div class='float_left frameno'><img style='width:140px;height:100%;' src='image/weight_$positionStyle.png'></div>
                            <div class='weight_txt float_left'>$tmpWeight</div>
                            $seprateline
                          </td>";
        if($frameNo % 2 != 0){
            $weightFrames = "<tr>".$weightFrames;
        }else{
            $weightFrames = $weightFrames."<tr>";
        }

        $frameNo++;

    }

    $ListSTR="<tr>
                    <td style='width:10%' class='recordNo'></td>
                    <td style='width:30%' class='bottomLine'>
                        <img style='width:40px;' src='image/record_icon.png'>
                        <img style='width:40px;' src='image/recording.gif'></td>
                    <td style='width:40%' class='bottomLine'></td>
                    <td style='width:20%' class='bottomLine'></td>
                   </tr>";
    $qcRecordSql = "SELECT A.qty,A.date,B.Name, A.operator
                    From $DataIn.qcdatarecord A
                    INNER JOIN $DataPublic.staffmain B ON A.operator=B.Number
                    WHERE A.StuffId = $currentStuffId
                    AND Left(A.date, 10) = '$curDate'
                    Order By A.date Desc";
    $qcRecordResult = mysql_query($qcRecordSql);
    $recordListStyle='width: 35px;height: 35px;margin-right: -8px;';
    $count = mysql_num_rows($qcRecordResult);
    while($recordRow = mysql_fetch_assoc($qcRecordResult)){
        $qty = $recordRow['qty'];
        $name = $recordRow['Name'];
        $tmpNumber = $recordRow['operator'];
        $date = intval((strtotime($today)-strtotime($recordRow['date']))/60);

        if($date < 60){
            $timeTitle = $date == 0?'1分钟前':$date.'分钟前';
        }else if($date >= 60 && $date < 1440){
            $timeTitle = intval($date / 60).'小时前';
        }else if($date >= 1440){
            $timeTitle = intval($date / 1440).'天前';
        }


        $currentBg = '';
        if($tmpNumber == $staffnumber){
            $currentBg = "sameTextColor'";
        }

        $numListTitle = '';
        if($count >9){
            $countInStr = "$count";
            $len = strlen($countInStr);
            for($i=0; $i<$len; $i++){
                $listNumber = substr($countInStr, $i, 1);
                $numListTitle .= "<img style='$recordListStyle' src='image/list_$listNumber.png'>";
            }
        }else{
            $numListTitle .= "<img style='$recordListStyle' src='image/list_0.png'><img  style='$recordListStyle' src='image/list_$count.png'>";
        }
        $numListTitle .= "<img style='$recordListStyle' src='image/list_dot.png'>";
        $ListSTR.="<tr style='color: #888888;'>
                    <td style='width:10%' class='recordNo'>$numListTitle</td>
                    <td style='width:30%' class='bottomLine $currentBg'><img style='width:40px;' src='image/recorded_icon.png'>$qty</td>
                    <td style='width:40%' class='bottomLine $currentBg'>$name</td>
                    <td style='width:20%' class='bottomLine'>$timeTitle</td>
                   </tr>";
        $count--;
    }

    if ($Page!=2 || $TotalCount>0){//第二个页面为空时不显示
    
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv' style='height:200px;'>
    <div id='linediv_new' class='float_left'><?php echo $Line; ?></div>
    <div class='float_left'>
        <?php
            $imageSrc = $staffnumber == ''?'image/working_staff.png':"../download/staffPhoto/P$staffnumber.png";
            $staffnameTitle = $staffname == ''?'-- --':$staffname;
        ?>
        <img id='staff_avator' style='width:150px;height:200px;' src='<?php echo $imageSrc;?>'>
        <div id='staff_name' style='width:150px;height:40px;margin-top:-53px;position:absolute;'><?php echo $staffnameTitle;?></div>
    </div>
    <ul id='quantity3_new' class='float_right'>
        <li class='text_left'><?php echo number_format($WaitQty); ?></li>
        <li style='width:24px;'><div></div></li>
        <li class='text_right'><span class='margin_right_15'><?php echo number_format($todayDjQty); ?></span></li>
    </ul>
</div>

    <table id='stuff_info'>
        <tr style='height:5%;width:100%' >
            <td colspan="3"><div class='float_right' style='margin-top:10px;margin-right:15px;'>
                <img src='image/framein.png' style='width:40px;margin-right:5px;'>
                <span style='color:#22AC38;font-size:50px;'><?php echo $recordBox;?></span>
                <span style='font-size:35px;'><?php
                        if($totleBox != '-- --') {
                            echo '/';
                            echo number_format($totleBox); 
                        }
                        else{ 
                            echo $totleBox;
                        }
                    ?></span></div>
            <td>
        </tr>
        <tr style='width:100%'>
            <td style='width:60%' class='stuffInfo_text'><span style='color:#72b2d4;'><?php echo $company;?>/</span> <?php echo $stuffname; ?></td>
            <td style='width:2%' class='stuffInfo_text'><div id='stuff_div'></div></td>
            <td style='text-align: center;'>
                <span style='color:#22AC38;font-size:60px;font-family: "helveticaneue-light"'><?php echo number_format($stuffDjQty); ?></span>
                <span style='font-family:"helveticaneue-light"'>/<?php echo number_format($stuffQty); ?></span>
            </td>
        </tr>
    </table>

<table>
    <tr id='state_bar'>
        <td>
            <img src='image/cg_icon.png'>
            <div class='state_title'>采购</div>
            <div class='state_title'><?php echo exchangeNumber(number_format($staticCgQty));?>
                <span class='static_min'>(</span>
                <span class='static_min static_min_color'><?php echo number_format($cgCount);?></span>
                <span class='static_min'>)</span>
            </div>
        </td>
        <td>
            <img src='image/rk_icon.png'>
            <div class='state_title'>入库</div>
            <div class='state_title'><?php echo exchangeNumber(number_format($staticRkQty));?>
            <span class='static_min'>(</span>
            <span class='static_min static_min_color'><?php echo number_format($rkCount);?></span>
            <span class='static_min'>)</span>
            </div>
        </td>
        <td>
            <img src='image/bp_icon.png'>
            <div class='state_title'>备品</div>
            <div class='state_title'><?php echo exchangeNumber(number_format($staticBpQty));?>
                <span class='static_min'>(</span>
                <span class='static_min static_min_color'><?php echo $bpRate.'%';?></span>
                <span class='static_min'>)</span>
            </div>
        </td>
        <td>
            <img src='image/th_icon.png'>
            <div class='state_title' style='color:#FF4D4D'>退货</div>
            <div class='state_title'><?php echo exchangeNumber(number_format($staticThQty));?>
                <span class='static_min'>(</span>
                <span class='static_min static_min_color'><?php echo $thRate.'%';?></span>
                <span class='static_min'>)</span>
            </div>
        </td>
    </tr>
</table>
<table id='weight_bar' style='color: #888888;'>
        <?php
            echo $weightFrames;
        ?>
</table>
<div id='listdiv' style='overflow: hidden;height:1690px;width:1080px;'>
<table id='record_list'  cellpadding=0 cellspacing=0>
    <?php echo $ListSTR;?>
</table>
</div>
<?php } ?>

<?php
function exchangeNumber($number){
    $numberArray = explode(',', $number);
    $shortName = '';
    switch(count($numberArray)){
        case 3:
            $shortName = 'k';
        break;
        case 4:
            $shortName = 'm';
        break;
    }



    return count($numberArray)==1?$number : $numberArray[0].','.$numberArray[1].$shortName;
}

?>