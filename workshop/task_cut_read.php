<?php
include "../basic/parameter.inc";
include_once "tasks_function.php";

$SearchRows="";
$workAdd = 1;
switch($Floor){
    case "3A":
    case "6":   
        $Floor=6; $LineId="4";
    break;
    case "17": 
        $Floor=17;
        $workAdd = 2;
        $LineId = "4";
    break;
    default:     
        $Floor=3; 
        $LineId="1,2,3";
    break;
}
  
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
$curWeek=$dateResult["curWeek"];

$curDate=date("Y-m-d");
$today=date("Y-m-d H:i:s");

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(ADDDATE(CURDATE(), INTERVAL 7 DAY) ,1) AS nextWeek",$link_id));
$nextWeek=$dateResult["nextWeek"];

$ListSTR="";   

//获取拉线组长
$leaderNumberSql = "SELECT Name, Number FROM $DataIn.staffmain WHERE GroupId = 824 and JobId = 39";
$leaderResult = mysql_query($leaderNumberSql);
$leaderRow = mysql_fetch_assoc($leaderResult);
$leaderName = $leaderRow['Name'];
$leaderNumber = $leaderRow['Number'];

$m=0;

//部门人数统计
$staffStaticSql = "SELECT count(*) as count, '1' as sign FROM $DataIn.staffmain WHERE workAdd=$workAdd AND GroupId=824 AND Estate=1
                   Union ALL
                   SELECT count(*) as count, '2' as sign FROM $DataIn.kqqjsheet A 
                   LEFT JOIN $DataIn.staffmain B ON B.Number = A.Number 
                   WHERE  (A.StartDate <= '$today' and A.EndDate >= '$today') 
                   AND B.workAdd = $workAdd AND GroupId = 824
                  ";
//echo $staffStaticSql;
$staffStaticResult = mysql_query($staffStaticSql);
$staffCount = 0;$leaveCount = 0;
while($staffStaticRow = mysql_fetch_assoc($staffStaticResult)){
    $tempCount = $staffStaticRow['count'];
    $tempSign = $staffStaticRow['sign'];

    switch ($tempSign){
        case "1":
            $staffCount = $tempCount;
        break;
        case "2":
            $leaveCount = $tempCount;
        break;
    }
}

switch ($scLine) {
    case 'A':
        $cuttype = ' AND C.cutSign = 3';
        break;
    case 'B':
        $cuttype = ' AND C.cutSign not in (1,2,3,4)';
        break;
    case 'C':
        $cuttype = ' AND C.cutSign = 4';
        break;
    case 'D':
        $cuttype = ' AND C.cutSign = 1';
        break;
}

$getBlSql = "SELECT S.sPOrderId,S.mStockId,S.Qty,S.ScQty,G.POrderId,S.StockId,G.StuffId,G.DeliveryWeek,D.StuffCname,
                           D.Picture,IFNULL(Max(L.created),'') AS created,C.CutName,IFNULL(Y.OrderPO,'') AS OrderPO,G.Price  
            FROM  yw1_scsheet S
            LEFT  JOIN cg1_stocksheet  G ON G.StockId=S.mStockId
            LEFT JOIN stuffdata        D ON D.StuffId=G.StuffId
            LEFT JOIN slice_cutdie     E ON E.StuffId=D.StuffId 
            LEFT JOIN pt_cut_data      C ON  C.Id  = E.CutId
            LEFT JOIN  ck5_llsheet     L ON L.sPOrderId=S.sPOrderId 
            LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId  
            WHERE S.WorkShopId='105' AND S.Estate>0 
                  AND getCanStock(S.sPOrderId,3)=3
                  AND S.Qty > S.ScQty
                  $cuttype 
            GROUP BY S.sPOrderId ORDER BY created,DeliveryWeek,sPOrderId"; 
//echo $getBlSql;
//初始化统计数据
$overTotleQty = 0;$overQtyCount = 0; //欠数
$curTotleQty = 0;$curTotleCount = 0; $curDjTotleQty = 0;//生产中
$totleCutQty = 0;
$m=0;
$n=10;
$getBlResult = mysql_query($getBlSql);
while($blRow = mysql_fetch_assoc($getBlResult)){
    $Qty = $blRow['Qty'];
    $name = $blRow['StuffCname'];
    $Weeks = $blRow['DeliveryWeek'];
    $WorkShopId = $blRow['WorkShopId'];
    $sPOrderId = $blRow['sPOrderId'];
    $cutName = $blRow['CutName'];
    $djQty = $blRow['djQty'] == ""?0:$blRow['djQty'];
    $lastDjTime = $blRow['djTime'];
    $StuffId = $blRow['StuffId'];
    $totleCutQty += $Qty;
    //统计欠数等
    if($djQty > 0){
        $curDjTotleQty += $djQty;
        $curTotleQty += $Qty;
        $curTotleCount++;
        if(strtotime($today) - strtotime($lastDjTime) >= 1800 && $Qty - $djQty >0){
            $overTotleQty += ($Qty - $djQty);
            $overQtyCount ++;
        }
    }

    // $lastRkDateSql = "SELECT max(A.created) as lastDate FROM $DataIn.ck5_llsheet A
    //                   LEFT JOIN yw1_stocksheet B On B.StockId = A.StockId
    //                   WHERE B.sPOrderId = $sPOrderId";
    // $lastLlDateResult = mysql_fetch_assoc(mysql_query($lastRkDateSql));
    //$lastDate = $lastLlDateResult['lastDate'];

    $lastDate = $blRow['created'];
    if($lastDate != ''){
        
        $diff = (strtotime($today) - strtotime($lastDate));
    
        if(intval($diff/86400) > 0){
            $diffTime = intval($diff/86400);
            $diffTime.="天前";
        }else if(intval($diff/3600) > 0){
            $diffTime = intval($diff/3600);
            $diffTime.="小时前";
        }else if(intval($diff/60)> 0){
            $diffTime = intval($diff/60);
            $diffTime.="分钟前";
        }else{
            $diffTime="$diff秒前";
        }
    }else{
        continue;
    }
    
    $Week1=substr($Weeks, 4,1);
    $Week2=substr($Weeks, 5,1);
    $WeekColor=$curWeek>$Weeks?'bgcolor_red':'bgcolor_black';
    $tdColor=$curWeek>$Weeks?'red_color':'black_color';
    $WeekClass="week_qc";
    $WeekSTR="<div>$Week1</div><div>$Week2</div>";
    $DateChars="配";
    if($djQty > 0){
        $bgColor = "#01BE56";
        $djColor = "color:#01BE56;";
    }else{
        #$bgColor = $m%2!=0?"#E5F1F7":"#fff";
        $djColor = '';
    }
    if(mb_strlen($name,'utf-8') > 25){
        $shortCname = mb_substr($name, 0, 25, 'utf-8').'...';
    }else{
        $shortCname = $name;
    }

    $logoName = 'image/noIogo.png';
    if(file_exists("../download/stuffIcon/$StuffId.jpg")){
        $logoName = "../download/stuffIcon/$StuffId.jpg";
    }else if(file_exists("../download/stuffIcon/$StuffId.png")){
        $logoName = "../download/stuffIcon/$StuffId.png";
    }

    $diffTime= $factoryCheck=='on'?'':$diffTime;
    
    $ListSTR.=" <table id='ListTable$m' name='ListTable[]' class='$tableClass' style='background-color:$bgColor;vertical-align: middle;'>
                    <tr style='vertical-align:top;'>
                        <td rowspan='2' width='120px' style='padding-top:20px;padding-left:10px;'><img style='width:100px;' src='$logoName'></td>
                        <td width='80' class='$WeekClass  $WeekColor' style='padding-left:10px;padding-top:0px;'>$WeekSTR</td>
                        <td colspan='4' width='580' class='title_qc blue_color' style='word-break:break-all;font-size:42px;padding-top:0px;'>$cutName</td>
                        <td class='time static_qty' width='300' style='padding-top:0px;font-size:45px;padding-right:43px;'><span style='$djColor'>".number_format($djQty)."</span> / ".number_format($Qty)."</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan='4' class='static_qty' style='font-size:35px;'>$shortCname</td>
                        <td class='time_qc float_right'><span style='color:#848888;font-size:30px;'>$diffTime</sapn><div style='margin-top:3px;'><img src='image/cut_fp.png' style='height:40px;'></div></td>
                    </tr>
                </table>";
    $m++;
}

$todayLLSql = "SELECT sum(A.Qty) as sumQty FROM $DataIn.sc1_cjtj A 
               LEFT JOIN $DataIn.yw1_scsheet B On A.sPOrderId = B.sPOrderId
               LEFT JOIN cg1_stocksheet G ON G.StockId=B.mStockId 
               LEFT JOIN stuffdata D ON D.StuffId=G.StuffId 
               LEFT JOIN slice_cutdie E ON E.StuffId=D.StuffId 
               LEFT JOIN pt_cut_data C ON C.Id = E.CutId 
               WHERE date_format(A.created,'%Y-%m-%d') = '$curDate' 
               AND B.WorkShopId = 105
               $cuttype";
//echo $todayLLSql ;
$todayLLTotle = mysql_query($todayLLSql);
$todayLlRow = mysql_fetch_assoc($todayLLTotle);
$TodayQty = $todayLlRow['sumQty']==""?0:$todayLlRow['sumQty'];

$WeekName="<img src='image/cutM$scLine.png' style='height:120px;padding-top:20px;padding-left:0px;'>";
    
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv_qc' style='height:200px;'>
   <div id='linediv_bled' class='float_left' ><?php echo $WeekName; ?></div>
   <ul id='state_bled' class='float_left' style='width:300px;'>
        <li>
            <image class='state_img' src='image/cut<?php echo $scLine;?>_logo.png'><span class='state_span'>开料<?php echo $scLine;?></span>
        </li>
        <li>
            <image class='state_img float_left' src='image/group_staff.png'>
            <div class='float_left state_span' style='margin-left:80px;padding-top:25px;'>
                <span style='font-size:32px;margin-top:10px;color:#848888;'><?php echo $staffCount; ?>人 | </span>
                <span class='red_color' style='font-size:32px;margin-top:10px;'><?php echo $leaveCount; ?></span> 
                <span style='font-size:32px;margin-top:10px;color:#848888;'>人</span> 
            </div>
        </li>
   </ul>
   <ul id='quantity3_qc' class='float_right'>
             <li class='text_left'><span style='color:#848888;font-size:70px;'><?php echo " / ". number_format($totleCutQty); ?></span></li>
             <li class='text_right'><span class='margin_right_15' style='font-size:70px;'><?php echo number_format($TodayQty); ?></span></li>
   </ul>
   <ul id='count_cut' class=''>
            <li >欠数 <div></div>
                <span class='float_right'>
                    <span class='red_color'><?php echo number_format(intval($overTotleQty)); ?> </span>
                    <span class='littleLi'><?php echo  "($overQtyCount)"; ?> </span>
                </span>
            </li>
            <li >生产中
                <span class='float_right'>
                    <span style='color:#01BE56;'><?php echo number_format(intval($curDjTotleQty)); ?></span><span> / <?php echo number_format(intval($curTotleQty)); ?> </span>
                    <span class='littleLi'><?php echo  "($curTotleCount)"; ?> </span>
                </span>
            </li>
    </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1720px;width:1080px;'>
<?php echo $ListSTR;?>
</div>