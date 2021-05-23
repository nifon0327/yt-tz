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

$position =$position == ''? 'head' : $_GET['position']; 
$lineLimit = '';
if($position != 'head'){
    $lineLimit = ' Limit 12,14 ';
}
  
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
$curWeek=$dateResult["curWeek"];

$curDate=date("Y-m-d");
$today=date("Y-m-d H:i:s");
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(ADDDATE(CURDATE(), INTERVAL 7 DAY) ,1) AS nextWeek",$link_id));
$nextWeek=$dateResult["nextWeek"];

$SearchRows=" AND (G.DeliveryWeek<=$nextWeek  OR  S.WorkShopId=0) ";

$ListSTR="";   

//获取拉线组长
$leaderNumberSql = "SELECT Name, Number FROM $DataIn.staffmain WHERE GroupId = 701 and JobId = 39";
$leaderResult = mysql_query($leaderNumberSql);
$leaderRow = mysql_fetch_assoc($leaderResult);
$leaderName = $leaderRow['Name'];
$leaderNumber = $leaderRow['Number'];

$m=0;

//部门人数统计
$staffStaticSql = "SELECT count(*) as count, '1' as sign FROM $DataIn.staffmain WHERE workAdd=$workAdd AND GroupId=701 AND Estate=1
                   Union ALL
                   SELECT count(*) as count, '2' as sign FROM $DataIn.kqqjsheet A 
                   LEFT JOIN $DataIn.staffmain B ON B.Number = A.Number 
                   WHERE  (A.StartDate <= '$today' and A.EndDate >= '$today') 
                   AND B.workAdd = $workAdd AND GroupId = 701
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

// $getBlSql = "SELECT S.sPOrderId,S.Qty,S.WorkShopId,G.POrderId,G.StockId,G.StuffId,G.DeliveryWeek,D.StuffCname,D.Picture,IFNULL(Y.OrderPO,'') AS OrderPO,G.created,S.blQty,getOrderStockTime(S.sPOrderId) as StockTime 
//             FROM ( 
//                 SELECT A.sPOrderId,A.mStockId,A.StockId,A.Qty,SUM(IF(A.OrderQty>(A.llQty+A.tStockQty),1,0)) AS blSign,SUM(A.OrderQty) AS blQty, SUM(A.llQty) AS llQty,A.WorkShopId
//                 FROM (
//                     SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,G.OrderQty,SUM(IFNULL(L.Qty,0)) AS llQty,K.tStockQty,S.WorkShopId   
//                     FROM(
//                         SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,G.DeliveryWeek,S.WorkShopId
//                         FROM yw1_scsheet S
//                         LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
//                         WHERE S.WorkShopId != '101' AND S.ActionId!= '105' AND S.ScFrom>0 AND S.Estate>0 AND G.DeliveryWeek>0 
//                     ) S 
//                     INNER JOIN yw1_stocksheet G ON G.sPOrderId=S.sPOrderId  
//                     LEFT JOIN  ck5_llsheet    L ON L.StockId=G.StockId AND L.sPOrderId=S.sPOrderId 
//                     LEFT JOIN  ck9_stocksheet K ON  K.StuffId=G.StuffId 
//                     WHERE 1 GROUP BY G.StockId 
//                 ) A GROUP BY A.sPOrderId
//              ) S 
//             LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
//             LEFT JOIN stuffdata       D ON D.StuffId=G.StuffId
//             LEFT JOIN yw1_ordersheet  Y ON Y.POrderId=G.POrderId 
//             WHERE S.blQty>S.llQty AND S.blSign=0 
//             Order By G.DeliveryWeek, StockTime";

$getBlSql = "SELECT B.sPOrderId,B.mStockId,B.Qty,B.WorkShopId,D.StuffCname,D.Picture,B.created,B.DeliveryWeek,B.StockTime,IFNULL(C.ableDate, NOW()) as ableDate
                FROM ( 
                    SELECT A.sPOrderId,A.mStockId,A.Qty ,getCanStock(A.sPOrderId,1) AS canSign,A.POrderId,A.StockId,A.StuffId,A.DeliveryWeek,A.WorkShopId,A.created,getOrderStockTime(A.sPOrderId) as StockTime
                    FROM (
                            SELECT S.sPOrderId,S.mStockId,S.Qty,G.DeliveryWeek AS LeadWeek,G.POrderId,G.StockId,G.StuffId,G.DeliveryWeek,S.WorkShopId,G.created
                            FROM      yw1_scsheet  S 
                            LEFT JOIN cg1_stocksheet G ON G.StockId=S.mStockId  
                            WHERE S.WorkShopId !='101' AND S.ScFrom>0 AND S.Estate>0 AND G.DeliveryWeek>0 AND G.Mid = 0  $SearchRows  
                    )A WHERE 1 
                 )B 
            LEFT JOIN stuffdata  D ON D.StuffId=B.StuffId
            LEFT JOIN ck_bldatetime C On B.sPOrderId = C.sPOrderId
            WHERE B.canSign=1 
            Order by B.DeliveryWeek,ableDate $lineLimit";
//echo $getBlSql;//getOrderStockTime(B.sPOrderId) as StockTime,
//初始化统计数据
$overTotleQty = 0;$overQtyCount = 0;
$curTotleQty = 0;$curTotleCount = 0;
$nextTotleQty = 0;$nextTotleCount = 0;

$m=0;
$n=12;
$getBlResult = mysql_query($getBlSql);
while($blRow = mysql_fetch_assoc($getBlResult)){
    $Qty = $blRow['Qty'];
    $name = $blRow['StuffCname'];
    $Weeks = $blRow['DeliveryWeek'];
    $WorkShopId = $blRow['WorkShopId'];
    $sPOrderId = $blRow['sPOrderId'];
    $mStockId  = $blRow['mStockId'];
    $stockTime = $blRow['ableDate'];
    
    
    $getBlTime = "SELECT ableDate,Estate FROM $DataIn.ck_bldatetime WHERE sPOrderId = '$sPOrderId' LIMIT 1";
    $getBlTiemResult = mysql_fetch_assoc(mysql_query($getBlTime));
    if($getBlTiemResult){
        
        $canEstate   =  $getBlTiemResult['Estate'];
        if ($canSign==1 && $canEstate==0){ //不可备料
             $stockTime = $today;
	         $updateSql =  "UPDATE  $DataIn.ck_bldatetime SET unableDate=ableDate,ableDate='$today',Estate=1,modified='$today' WHERE sPOrderId = '$sPOrderId' ";
	         mysql_query($updateSql);
        }
        else{
	        $stockTime =  $getBlTiemResult['ableDate'];
        }
        
    }else{
        $insertBlTime = "INSERT INTO $DataIn.ck_bldatetime (Id, sPOrderId, ableDate, unableDate,Date,created) VALUES (NULL, $sPOrderId, '$today', '$today','$curDate','$today')";
        mysql_query($insertBlTime);
    }

    
    $Lock_Result=mysql_fetch_array(mysql_query("SELECT StockId,Locks FROM $DataIn.cg1_lockstock  WHERE  StockId ='$mStockId'",$link_id));
    $CheckLockRow=mysql_fetch_array(mysql_query("SELECT Id,Remark FROM $DataIn.yw1_sclock WHERE sPOrderId ='$sPOrderId' AND Locks=0 LIMIT 1",$link_id));
    $ScId=$CheckLockRow["Id"];	
    $Locks=$Lock_Result["Locks"];
    $newStockId=$Lock_Result["StockId"];
    
    //取最上层半成品是否锁定
    $LocksResult=mysql_fetch_array(mysql_query("SELECT getStockIdLock('$mStockId') AS Locks",$link_id));
	$mLocks = $LocksResult['Locks'];
    
    if (( $newStockId!="" &&  $Locks==0 ) || $ScId>0 || $mLocks>0) continue;
        
    $lastRkDateSql = "SELECT * FROM $DataIn.ck1_rksheet A
                      LEFT JOIN yw1_stocksheet B On B.StockId = A.StockId
                      WHERE B.sPOrderId = $sPOrderId";

    $Week1=substr($Weeks, 4,1);
    $Week2=substr($Weeks, 5,1);
    $WeekColor=$curWeek>$Weeks?'bgcolor_red':'bgcolor_black';
    $tdColor=$curWeek>$Weeks?'red_color':'black_color';
    $WeekClass="week_qc";
    $WeekSTR="<div>$Week1</div><div>$Week2</div>";

    $overweek = 1;
    if(date("w",strtotime($curDate))==5){
        $overweek = 2;
    }

    if($Weeks < $curWeek){
        $overTotleQty += $Qty;
        $overQtyCount++;
    }else if($Weeks == $curWeek){
        $curTotleQty += $Qty;
        $curTotleCount++;
    }else if($Weeks - $curWeek <= $overweek){
        $nextTotleQty+=$Qty;
        $nextTotleCount++;
    }else{
        continue;
    }



    //$lastDate = $lastLlDateResult['lastDate'];
    $diff = (strtotime($today) - strtotime($stockTime));

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
    $diffTime= $factoryCheck=='on'?'':$diffTime;
    
    $MonthQty+=$Qty;
    if(mb_strlen($name,'utf-8') > 14){
        $shortCname = mb_substr($name, 0, 14, 'utf-8').'...';
    }else{
        $shortCname = $name;
    }

    $bgColor = $m%2!=0?"#E5F1F7":"#fff";
    $ListSTR.=" <table id='ListTable$m' name='ListTable[]' class='$tableClass' style='background-color:$bgColor;vertical-align: middle;'>
                    <tr style='vertical-align:top;height:138px;'>
                        <td width='120' style='padding-top:40px;padding-left:30px;'><img src='image/w$WorkShopId.png' style='height:60px;'></td>
                        <td width='100' class='$WeekClass  $WeekColor' style='padding-left:10px;padding-top:40px;'>$WeekSTR</td>
                        <td colspan='4' width='500' class='title_qc' style='font-size:35px;padding-top:40px;'>$shortCname</td>
                        <td class='time static_qty' width='200' style='padding-top:45px;font-size:45px;'>".number_format($Qty)."</td>
                        <td class='static_qty' width='200' style='padding-top:50px;font-size:40px;padding-left:30px;color:#848888;'>$diffTime</td>
                   </tr>
                </table>";
    $m++;
}

$todayLLSql = "SELECT sum(A.Qty) as sumQty FROM $DataIn.ck5_llsheet A 
               LEFT JOIN $DataIn.yw1_scsheet B On A.sPOrderId = B.sPOrderId
               WHERE date_format(A.created,'%Y-%m-%d') = '$curDate' 
               AND B.WorkShopId != 101";
$todayLLTotle = mysql_query($todayLLSql);
$todayLlRow = mysql_fetch_assoc($todayLLTotle);
$TodayQty = $todayLlRow['sumQty']==""?0:$todayLlRow['sumQty'];

$WeekName="<img src='../download/staffPhoto/P$leaderNumber.png' style='height:125px;margin-bottom:0px;'>";
    
?>

<?php 
    if($position == 'head'){
?>

 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv_qc' style='height:200px;'>
   <div id='linediv_qc' class='float_left'><?php echo $WeekName; ?></div>
   <ul id='state_qc' class='float_left'>
        <li>
            <image class='state_img' src='image/bl_logo.png'><span class='state_span'>待备</span>
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
             <li class='text_left'><span style='color:#848888;font-size:70px;'><?php echo " / ". number_format($MonthQty); ?></span></li>
             <li class='text_right'><span class='margin_right_15' style='font-size:70px;'><?php echo number_format($TodayQty); ?></span></li>
   </ul>
   <ul id='count_qc' class=''>
            <li >逾期 <div></div>
                <span class='float_right'>
                    <span class='red_color'><?php echo number_format(intval($overTotleQty)); ?> </span>
                    <span class='littleLi'><?php echo  "($overQtyCount)"; ?> </span>
                </span>
            </li>
            <li >本周 <div></div>
                <span class='float_right'>
                    <span ><?php echo number_format(intval($curTotleQty)); ?> </span>
                    <span class='littleLi'><?php echo  "($curTotleCount)"; ?> </span>
                </span>
            </li>
            <li ><?php echo substr($nextWeek,4,2); ?>周+ 
                <span class='float_right'>
                    <span><?php echo number_format(intval($nextTotleQty)); ?></span>
                    <span class='littleLi'><?php echo "($nextTotleCount)"; ?></span>
                </span>
            </li>
    </ul>
</div>
<?php
    }
?>
<div id='listdiv' style='overflow: hidden;height:1720px;width:1080px;'>
<?php echo $ListSTR;?>
</div>