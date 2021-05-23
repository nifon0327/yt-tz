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
        $Floor=17; 
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
$leaderNumberSql = "SELECT Name, Number FROM $DataIn.staffmain WHERE GroupId = 604 and JobId = 42";
$leaderResult = mysql_query($leaderNumberSql);
$leaderRow = mysql_fetch_assoc($leaderResult);
$leaderName = $leaderRow['Name'];
$leaderNumber = $leaderRow['Number'];
$leaderNumber =  "10124";
 $m=0;

$TotalQty=0; $TotalCount=0;//品检总数
$CurQty=0;  $CurCount=0;//品检中
$QcedQty=0;  $QcedCount=0;//待处理

$WaitQty=0; $WaitCount=0;   
 //品检任务
$SearchRows = "";
if($sceen == 'body'){
    $SearchRows = 'Limit 13,30';
}
$mySql = "SELECT * FROM 
            ((SELECT  '1' AS SortSign,S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,S.Qty,S.SendSign,G.POrderId, 
            IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS  DeliveryDate,
            M.CompanyId,P.Forshort,D.StuffCname,D.Picture,D.TypeId,YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1) AS Weeks,H.DateTime,
           Max(IFNULL(C.Date,Now())) AS QcDate,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks,H.Estate,L.LineNo,Max(S.shDate) AS shDate ,SUM(C.Qty) as qcQty,Max(C.created) as scDate,MAX(H.DateTime) as MissionDate
            FROM $DataIn.gys_shsheet S 
            INNER JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            INNER JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
            LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
            LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id 
            LEFT JOIN $DataIn.qc_scline L ON L.Id=H.LineId 
            LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0
            LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId  
            LEFT JOIN $DataIn.cg1_stuffcombox CS ON CS.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=CS.mStockId
            WHERE  S.Estate=2  AND M.Floor='$Floor'  AND S.SendSign IN(0,1)
            GROUP BY S.Id 
            HAVING qcQty > 0
            ORDER BY scDate desc)
            Union  
            (SELECT  '2' AS SortSign,S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,S.Qty,S.SendSign,G.POrderId, 
            IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS  DeliveryDate,
            M.CompanyId,P.Forshort,D.StuffCname,D.Picture,D.TypeId,YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1) AS Weeks,H.DateTime,
           Max(IFNULL(C.Date,Now())) AS QcDate,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks,H.Estate,L.LineNo,Max(S.shDate) AS shDate ,SUM(C.Qty) as qcQty,Max(C.created) as scDate,MAX(H.DateTime) as MissionDate
            FROM $DataIn.gys_shsheet S 
            INNER JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            INNER JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
            LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
            LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id 
            LEFT JOIN $DataIn.qc_scline L ON L.Id=H.LineId 
            LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0
            LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId  
            LEFT JOIN $DataIn.cg1_stuffcombox CS ON CS.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=CS.mStockId
            WHERE  S.Estate=2  AND M.Floor='$Floor'  AND S.SendSign IN(0,1) AND L.LineNo is not null
            GROUP BY S.Id 
            HAVING qcQty is null
            ORDER BY MissionDate desc)
            Union 
            (SELECT  '3' AS SortSign,S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,S.Qty,S.SendSign,G.POrderId, 
            IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS  DeliveryDate,
            M.CompanyId,P.Forshort,D.StuffCname,D.Picture,D.TypeId,YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1) AS Weeks,H.DateTime,
           Max(IFNULL(C.Date,Now())) AS QcDate,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks,H.Estate,L.LineNo,Max(S.shDate) AS shDate ,SUM(C.Qty) as qcQty,Max(C.created) as scDate,MAX(H.DateTime) as MissionDate
            FROM $DataIn.gys_shsheet S 
            INNER JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            INNER JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
            LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
            LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id 
            LEFT JOIN $DataIn.qc_scline L ON L.Id=H.LineId 
            LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0
            LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId  
            LEFT JOIN $DataIn.cg1_stuffcombox CS ON CS.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=CS.mStockId
            WHERE  S.Estate=2  AND M.Floor='$Floor'  AND S.SendSign IN(0,1) AND L.LineNo is null
            GROUP BY S.Id 
            ORDER BY shDate desc)) Z
            Order BY SortSign,shDate $SearchRows ";
$myResult=mysql_query($mySql,$link_id);
//echo $mySql;
while($myRow = mysql_fetch_array($myResult)) {
         $Id=$myRow["Id"];
         $Mid=$myRow["Mid"];
         $Weeks=$myRow["Weeks"];
         $Qty=$myRow["Qty"];
        $CompanyId = $myRow['CompanyId'];
         $TotalQty+=$Qty; $TotalCount++;
         
        //已登记数量
        $StuffId=$myRow["StuffId"];
        $djResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty  FROM $DataIn.qc_cjtj WHERE Sid='$Id' AND StuffId='$StuffId' ",$link_id));
        $DjQty=$djResult["Qty"];
         
         $QcDateColor="";$QcDateStr="";
         $tableClass=" tb_bgcolor0";//默认
         if ($DjQty>0){
              $QcDate=$myRow["QcDate"];
              $QcDateStr=GetDateTimeOutString($QcDate,'');
              $QcMinutes=(strtotime($today)-strtotime($QcDate))/60;
               if ($QcMinutes>30 || $DjQty==$Qty){
                     $QcedQty+=$DjQty;  $QcedCount++;
                     $tableClass=" tb_bgcolor1 ";  
                     $QcDateColor=$QcMinutes>30? " red_color ":"";
              }
              else{
                    $CurQty+=$Qty;  $CurCount++;
                    $tableClass=" tb_bgcolor2";
              }
         }
         else{
              $WaitQty+=$Qty;$WaitCount++;
         }
         $Lines = $sceen != 'body'?12:13;
         if ($m<$Lines){
                  if ($Weeks>0){
                        $StockId=$myRow["StockId"];
                        $Week1=substr($Weeks, 4,1);
                        $Week2=substr($Weeks, 5,1);
                        $WeekColor=$curWeek>$Weeks?'bgcolor_red':'bgcolor_black';
                        $tdColor=$curWeek>$Weeks?'red_color':'black_color';
                        $WeekClass="week_qc";
                        $WeekSTR="<div>$Week1</div><div>$Week2</div>";
                 }
                 else{
                        $WeekClass="week2_qc";
                        $WeekSTR="<div>补</div>";
                 }
                 
                $StuffCname = $myRow["StuffCname"];
                $Forshort=$myRow["Forshort"];
                if($myRow["CompanyId"] == '2270' || $myRow["CompanyId"] == '100300'){
                    $innerForshortSql = "SELECT B.Name FROM yw1_scsheet A 
                                        LEFT JOIN workshopdata B ON A.WorkShopId = B.Id
                                        WHERE A.mStockId = $StockId";
                    $innerForshortResult = mysql_fetch_assoc(mysql_query($innerForshortSql));
                    $Forshort = $innerForshortResult['Name'];
                }
                  $cgQty=$myRow["cgQty"];
                 
                $shDate=$myRow["shDate"];
                $scDate = $myRow['scDate'];
                $MissionDate = $myRow['MissionDate'];
                
                $shColors="";
                 $typeColor = $QcDateColor==""?$shColors:$QcDateColor;
              
                 //订单交期与采购交期同周
                 $cg_bgColor=$myRow["ReduceWeeks"]==1?"":" style='background-color:#D9EAF4' ";
                 
                 //最后一个配件
                 $POrderId=$myRow["POrderId"];
                include "stuff_blcheck.php";       
                
                $logo = '';
                $DateStr = '';
                $LineNo = $myRow['LineNo'];   
                if($DjQty > 0){
                    $DateStr=GetDateTimeOutString($scDate,'',1);
                    //$DateStr="前".$DateStr;
                    $logo = "<img src='image/little$LineNo.png' style='height:40px;'>";
                }else if($LineNo != ''){
                    $DateStr=GetDateTimeOutString($MissionDate,'',1);
                    //$DateStr="前".$DateStr;
                    $logo = "<img src='image/little$LineNo.png' style='height:40px;'>";
                }else{
                    $DateStr=GetDateTimeOutString($shDate,'',1);
                    //$DateStr="前".$DateStr;
                    $logo = "<img src='image/daoda_state.png' style='height:40px;'>";
                }



                 //配件属性
                include "stuff_property.php";
                if($Qty > 1){
                    $Qty=number_format($Qty);
                    $cgQty=number_format($cgQty);
                    $DjQty=$DjQty==0?"0":number_format($DjQty);    
                }
                 
                 $djColor = $DjQty > 0?"#01be56":"#000000";

                 //$LineNo=$myRow["LineNo"] == ""? "":"<img style='width:35px;height:35px;margin-top:12px;' src='image/little$LineNo.png'>";;
                 $Forshort="<span class='blue_color'>$Forshort</span>";
                 //背景色
                // if($DjQty == 0){
                //     $bgColor = $m%2!=0?"#E5F1F7":"#fff";
                // }
                if(mb_strlen($StuffCname,'utf-8') > 10){
                    $shortCname = mb_substr($StuffCname, 0, 10, 'utf-8').'...';
                }else{
                    $shortCname = $StuffCname;
                }
                $logoName = 'image/noIogo.png';
                if(file_exists("../download/stuffIcon/$StuffId.png")){
                    $logoName = "../download/stuffIcon/$StuffId.png";
                }else if(file_exists("../download/stuffIcon/$StuffId.jpg")){
                    $logoName = "../download/stuffIcon/$StuffId.jpg";
                }    

                 $ListSTR.="<table id='ListTable$m' name='ListTable[]' class='$tableClass qc_table' style='background-color:$bgColor;'>
                    <tr style='vertical-align:top;'>
                        <td rowspan='2' width='120px' style='padding-top:20px;padding-left:10px;'><img style='width:100px;' src='$logoName'></td>
                        <td width='80' class='$WeekClass  $WeekColor' style='padding-left:15px;padding-top:5px;'>$WeekSTR</td>
                        <td colspan='4' width='560' class='title_qc' style='word-break:break-all;'>$Forshort-$shortCname</td>
                        <td class='time static_qty' width='340' style='padding-top:-5px;padding-right:55px;font-size:38px;'><span style='color:$djColor;'>$DjQty</span> / $Qty </td>
                   </tr>";
                   // <tr>
                   //      <td class='qty' ><img src='image/order.png'/><span $cg_bgColor>$cgQty</span></td>
                   //      <td class='qty'><img src='image/register.png'/><span $LastBgColor>$Qty</span></td>
                   //      <td class='qty blue_color'><img src='image/djQtyIcon.png'/>$DjQty</td>
                   //      <td class='time $shColors'>$DateStr</td>
                   //      <td class='time'><div>$DateChars</div></td>
                   // </tr>
                   
             //备注 
             $Remark="";
             $RemarkResult=mysql_query("SELECT Remark  FROM $DataIn.qc_remark WHERE  Sid='$Id' ORDER BY Date DESC LIMIT 1",$link_id);
             if($RemarkRow = mysql_fetch_array($RemarkResult)) {
                  $Remark=$RemarkRow["Remark"];
             }

            //同一张单相同配件的备品 
             $Mid=$myRow["Mid"];
             $bpRemark="";
            $bpResult=mysql_query("SELECT S.Qty,S.StockId,S.SendSign  FROM $DataIn.gys_shsheet S WHERE  S.Mid='$Mid' AND S.StuffId='$StuffId' AND S.Estate=2  AND S.SendSign=2",$link_id);
             if($bpRow = mysql_fetch_array($bpResult)) {
                  $bpQty=number_format($bpRow["Qty"]);
                    $sameResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums FROM $DataIn.gys_shsheet S WHERE  S.Mid='$Mid' AND S.StuffId='$StuffId' AND S.Estate>0  AND S.SendSign=0 ",$link_id));
                   $Nums=$sameResult["Nums"];
                   $bpRemark=$bpQty . "pcs备品($Nums);";
             }
            if ($bpRemark!="" || $Remark!=""){
                    //<td  class='remark text_right'><span class='$QcDateColor'>$QcDateStr</span>&nbsp;</td>
                    $DateStr= $factoryCheck=='on'?'':$DateStr;
                    $ListSTR.="<tr>
                                    <td></td>
                                    <td colspan='4' class='remark_qc'><img src='image/remark.png'/>$bpRemark $Remark</td>
                                    <td class='time_qc float_right'><span class='$typeColor'>$DateStr</sapn><div>$DateChars</div></td>
                                </tr>";
            }
            else{
                   $DateStr= $factoryCheck=='on'?'':$DateStr;
                    $ListSTR.="<tr><td colspan='4' style='height:30px;line-height:40px;'>&nbsp;</td>
                                    <td  class='remark_qc text_right'>
                                        
                                    </td>
                                    <td class='time_qc float_right'><span class='$typeColor' style='font-size:30px;'>
                                        $DateStr</span><div style='margin-top:3px;'>$logo
                                    </td>
                                    </tr>";
            }                        
                                      
             $ListSTR.="</table>";
             $m++;
        }
}

if ($ListSTR==""){
    if ($bfListSTR[2]!=""){//补货待处理
                     $ListSTR.=$bfListSTR[2];$bfListSTR[2]="";
              }
                     
              if ($bfListSTR[1]!=""){//补货品检中
                     $ListSTR.=$bfListSTR[1];$bfListSTR[1]="";
              }
                     
              if ($bfListSTR[0]!=""){//补货待处理
                     $ListSTR.=$bfListSTR[0];$bfListSTR[0]="";
              }

}
                   
//$WaitQty=$TotalQty-$CurQty-$QcedQty;
//$WaitCount=$TotalCount-$CurCount-$QcedCount;
$todayCheckSql = "SELECT sum(A.shQty) as checkQty from qc_badrecord A
                  LEFT JOIN gys_shmain B ON B.Id = A.shMid
                  where date_format(A.created, '%Y-%m-%d') = '$curDate'
                  AND B.Floor = $Floor";
//echo $todayCheckSql;
$todayResult = mysql_fetch_assoc(mysql_query($todayCheckSql));

$TodayQty = $todayResult['checkQty'] == ""?0:number_format($todayResult['checkQty']);

$TotalQty=number_format($TotalQty);
$CurQty=number_format($CurQty);
$QcedQty=number_format($QcedQty);
$WaitQty=number_format($WaitQty);

 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
 
 //今日品检数量
//  $QtyResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty   
//             FROM $DataIn.qc_cjtj S  
//             LEFT JOIN $DataIn.gys_shsheet G On G.Id = S.Sid
//             LEFT JOIN $DataIn.gys_shmain M On M.Id = G.Mid
//             WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' 
//             AND M.Floor = $Floor
// ",$link_id));
// $TodayQty=$QtyResult["Qty"]==""?0:number_format($QtyResult["Qty"]);

 //本月品检数量
// $curMonth=date("Y-m");
// $QtyResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty   
//             FROM $DataIn.ck1_rksheet S  
//             LEFT JOIN $DataIn.gys_shsheet G On G.StockId = S.StockId
//             LEFT JOIN $DataIn.gys_shmain M On M.Id = G.Mid
//             WHERE  DATE_FORMAT(S.Date,'%Y-%m')='$curMonth' 
//             AND M.Floor = $Floor 
// ",$link_id));
// $MonthQty=$QtyResult["Qty"]==""?0:number_format($QtyResult["Qty"]);

//逾期，本周等统计
$qtyStaticSql = "SELECT SUM(S.Qty) as Qty, Count(*) as count, '1' as Sign
                 From $DataIn.gys_shsheet S 
                 INNER JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
                 LEFT JOIN $DataIn.cg1_stuffcombox CS ON CS.StockId=S.StockId 
                 LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=CS.mStockId
                 LEFT JOIN $DataIn.stuffdata SF ON SF.StuffId = G.StuffId
                 WHERE YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1) < $curWeek
                 AND S.Estate = 2 and SF.SendFloor = '$Floor'
                 Union 
                 SELECT SUM(S.Qty) as Qty, Count(*), '2' as Sign
                 From $DataIn.gys_shsheet S
                 INNER JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
                 LEFT JOIN $DataIn.cg1_stuffcombox CS ON CS.StockId=S.StockId 
                 LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=CS.mStockId
                 LEFT JOIN $DataIn.stuffdata SF ON SF.StuffId = G.StuffId
                 WHERE YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1) = $curWeek
                 AND S.Estate = 2 and SF.SendFloor = '$Floor'
                 Union
                 SELECT SUM(S.Qty) as Qty, Count(*), '3' as Sign
                 From $DataIn.gys_shsheet S
                 INNER JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
                 LEFT JOIN $DataIn.cg1_stuffcombox CS ON CS.StockId=S.StockId 
                 LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=CS.mStockId
                 LEFT JOIN $DataIn.stuffdata SF ON SF.StuffId = G.StuffId
                 WHERE YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1) > $curWeek
                 AND S.Estate = 2 and SF.SendFloor = '$Floor'";
$qtyStateResult = mysql_query($qtyStaticSql);
$overTotleQty = 0;$overQtyCount = 0;
$curTotleQty = 0;$curTotleCount = 0;
$nextTotleQty = 0;$nextTotleCount = 0;
while($qtyStateRow = mysql_fetch_assoc($qtyStateResult)){
    $qtyTemp = $qtyStateRow['Qty'];
    $countTemp = $qtyStateRow['count'];
    $sign = $qtyStateRow['Sign'];
    switch ($sign) {
        case '1':
            $overTotleQty = $qtyTemp == ""?$overTotleQty:$qtyTemp;
            $overQtyCount = $countTemp;
            break;
        case '2':
            $curTotleQty = $qtyTemp == ""?$curTotleQty:$qtyTemp;
            $curTotleCount = $countTemp;
            break;
        case '3':
            $nextTotleQty = $qtyTemp == ""?$nextTotleQty:$qtyTemp;
            $nextTotleCount = $countTemp;
            break;
        default:
            # code...
            break;
    }
}

//部门人数统计
$staffStaticSql = "SELECT count(*) as count, '1' as sign FROM $DataIn.staffmain WHERE workAdd=$workAdd AND GroupId=604
                   Union ALL
                   SELECT count(*) as count, '2' as sign FROM $DataIn.kqqjsheet A 
                   LEFT JOIN $DataIn.staffmain B ON B.Number = A.Number 
                   WHERE  (A.StartDate <= '$today' and A.EndDate >= '$today') 
                   AND B.workAdd = $workAdd AND GroupId = 604
                  ";
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

$WeekName="<img src='../download/staffPhoto/P$leaderNumber.png' style='height:125px;margin-bottom:0px;'>";
    
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
<?php
    if($sceen != 'body'){
?>
<div id='headdiv_qc' style='height:200px;'>
   <div id='linediv_qc' class='float_left'><?php echo $WeekName; ?></div>
   <ul id='state_qc' class='float_left'>
        <li>
            <image class='state_img' src='image/qc_logo.png'><span class='state_span'>待检</span>
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
             <li class='text_left'><span style='color:#848888'><?php echo " / ".$TotalQty; ?></span></li>
             <li class='text_right'><span class='margin_right_15'><?php echo $TodayQty; ?></span></li>
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
    $height = $sceen == 'head'?1720:1920;
}
?>
<div id='listdiv' style='overflow: hidden;height:<?php echo $height;?>px;width:1080px;'>
<?php echo $ListSTR;?>
</div>