<?php
include_once "tasks_function.php";
include "../basic/parameter.inc";

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
$curWeek=$dateResult["curWeek"];

$SC_TYPE=7100;//组装加工类型
$curDate=date("Y-m-d");
$today=date("Y-m-d H:i:s");
$nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
$nextWeek=$dateResult["NextWeek"];
$nextWeek=substr($nextWeek, 4,2);
$ListSTR="";   
   
 //已备料数量 //getOrderStockTime->   getLastStockTime->最后一个配件备料
$mySql = "SELECT  B.POrderId,B.ProductId,B.sPOrderId,B.Qty,B.LeadWeek,B.ShipType,
        M.OrderDate,M.OrderPO,A.Forshort,P.cName,P.TestStandard,getOrderStockTime(B.sPOrderId) AS created , B.Letter, getLastStockTime(B.sPOrderId) as lastBlTime, B.missionTime,B.Operator
                FROM ( 
                    SELECT A.POrderId,A.ProductId,A.OrderNumber,A.sPOrderId,A.Qty,A.LeadWeek,A.ShipType,
                    getCanStock(A.sPOrderId,2) AS canSign , A.Letter , A.missionTime,A.Operator 
                    FROM (
                            SELECT S.POrderId,S.sPOrderId,S.Qty,Y.ProductId,Y.OrderNumber,Y.ShipType,A.Name AS Operator,
                            IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek, SL.Letter , M.DateTime as missionTime
                            FROM      yw1_scsheet    S 
                            LEFT JOIN workscline SL ON SL.Id = S.scLineId
                            LEFT JOIN sc1_mission M On M.sPorderId = S.sPorderId
                            LEFT JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
                            LEFT JOIN yw3_pisheet PI ON PI.oId=Y.Id
                            LEFT JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
                            LEFT JOIN staffmain A ON A.Number=Y.Operator 
                            WHERE S.WorkShopId='101' AND S.ScFrom>0 AND S.Estate>0 AND S.scLineId>=0 GROUP BY S.sPOrderId  
                    )A WHERE 1 
                 )B 
               INNER JOIN yw1_ordermain M ON M.OrderNumber=B.OrderNumber
               INNER JOIN trade_object A ON A.CompanyId=M.CompanyId 
               INNER JOIN productdata P ON P.ProductId=B.ProductId  
               WHERE B.canSign=2 ORDER BY LeadWeek,OrderDate";   

$myResult=mysql_query($mySql,$link_id);
//按周以PI交期分组读取未出订单
$TotalOverQty=0; $OverCount=0;//逾期
$CurWeekQty=0;  $CurWeekCount=0;//本周
$NextWeekQty=0;  $NextWeekCount=0;//下周+
$SumQty=0;
$m=0;
while($myRow = mysql_fetch_array($myResult)) {
    $Weeks=$myRow["LeadWeek"];
    $Qty=$myRow["Qty"];
    $cName = mb_strlen($cName,'utf-8')>12?mb_substr($cName, 0, 12,'utf-8').'...':$cName;
    if ($Weeks<$curWeek){
        $TotalOverQty+=$Qty;$OverCount++;
    }else{
        if ($Weeks==$curWeek){
            $CurWeekQty+=$Qty;$CurWeekCount++;
        }else{
            $NextWeekQty+=$Qty;$NextWeekCount++;
        }
    }
    $sPOrderId = $myRow['sPOrderId'];
    $canZyDate = $today;
    $getBlTime = "SELECT ableDate FROM $DataIn.ck_bldatetime WHERE sPOrderId = $sPOrderId LIMIT 1";
    //echo $getBlTime;
    $getBlTiemResult = mysql_fetch_assoc(mysql_query($getBlTime));
    if($getBlTiemResult){
        $canZyDate =  $getBlTiemResult['ableDate'];
    }else{
        $insertBlTime = "INSERT INTO $DataIn.ck_bldatetime (Id, sPOrderId, ableDate, unableDate) VALUES (NULL, $sPOrderId, '$today', '$today')";
        mysql_query($insertBlTime);
    }
    
    $ScLine=$myRow["Letter"]==""?"":$myRow["Letter"];
    if ($ScLine==""){
	    $SumQty+=$Qty;
     }

    if ($m<12){
        $Week1=substr($Weeks, 4,1);
        $Week2=substr($Weeks, 5,1);
        $WeekColor=$curWeek>$Weeks?'bgcolor_red':'bgcolor_black';
        $tdColor=$curWeek>$Weeks?'red_color':'black_color';

        $Forshort=$myRow["Forshort"];
        $cName=$myRow["cName"];$cName = mb_strlen($cName,'utf-8')>12?mb_substr($cName, 0, 12,'utf-8').'...':$cName;
        $OrderPO=$myRow["OrderPO"];
        $sgRemark=$myRow["sgRemark"];
        $ProductId = $myRow['ProductId'];
        
        $TeststandardSign=$myRow["TestStandard"]!=1?"color:#FFFFFF;background-color:#FF0000;":"";
        $missionTime = $myRow['missionTime'];
        $lastBlTime = $myRow['lastBlTime'];
        $getLastBledSql = "SELECT MAX(created) as last FROM ck5_llsheet WHere sPOrderId = $sPOrderId";
        $getLastResult = mysql_fetch_assoc(mysql_query($getLastBledSql));
        $lastBl = $myRow['last'];
        if ($ScLine==""){ //未分配
           // $SumQty+=$Qty;

            $firstDateCalss = $lblTextColor; $firstDate = GetDateTimeOutString($lastBlTime, '', 0);
            $firstChartStyle = $TeststandardSign; $firstChart = "<img class='stateLogo' src='image/waitAss.png'>";

            $KblDate = GetDateTimeOutString($canZyDate, $lastBl,0);
            $secondDateCalss = $kblTextColor; $secondDate = str_replace('前', '', $KblDate);
            $secondChartStyle = ''; $secondChart = "<img class='stateLogo'  src='image/zhanyong.png'>";


        }else{

            $firstDateCalss = $AllotTextColor; 
            $asstime = GetDateTimeOutString($missionTime,"",0);
            $firstDate = $asstime;
            $firstChartStyle = 'background-color:#888888;'; 
            $firstChart = "<img class='stateLogo' src='image/waitBled.png'>";

            $diffAss = GetDateTimeOutString($lastBlTime, $missionTime, 0);
            $secondDateCalss = $lblTextColor; 
            $secondDate = str_replace('前', '', $diffAss);
            $secondChartStyle = $TeststandardSign; $secondChart = "<img class='stateLogo' src='image/$ScLine.png'>";
        }


    $Qty=number_format($Qty);
    //出货方式
    $logoName = 'image/noIogo.png';
    $Operator = $myRow["Operator"];
    $OperatorSTR = "";$auditImage="";
    
     $POrderId = $myRow["POrderId"];
      $checkResult=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id DESC LIMIT  1");
	  $upSign = mysql_num_rows($checkResult)>0?1:0;
	    
    if($myRow["TestStandard"]==1 && $upSign==0){
        $productIcon ='../download/productIcon/' . $ProductId . '.png';
        if(file_exists($productIcon)){
              $logoName=$productIcon;
         }
         else{
	       if(file_exists("../download/productIcon/$ProductId.jpg")){
               $logoName = "../download/productIcon/$ProductId.jpg";
           }  
       }
    }else{
	    $OperatorSTR="<div style='width:100px;text-align:center;position:absolute;margin:-20px 0 0 0px;font-size:22px;color:#888888'>$Operator</div>";
	    if ($myRow["TestStandard"]==2){
		    $auditImage="<img style='width:40px;height:40px;position:absolute;margin:5px 0 0 -50px;' src='image/wait_audit.png'/>";
	    }else{
		    $cName="<span style='color:#800080;'>$cName</span>";
	    }
    } 
    
    $ShipType=$myRow["ShipType"];
    //$ShipType=1;
    $ShipType=$ShipType===""?"":"<image src='../images/ship$ShipType.png' style='width:30px;height:30px;'/>";
    //$bgColor = $m%2!=0?"#E5F1F7":"#fff";
    $ListSTR.=" <table id='ListTable$m' name='ListTable[]' class='$tableClass qc_table' style='background-color:$bgColor;'>
                    <tr>
                        <td rowspan='2' width='120px'><img style='width:100px;' src='$logoName'>$auditImage $OperatorSTR</td>
                        <td colspan='4' width='600px' class='title'>
                            <span class='week $WeekColor'><div>$Week1</div><div>$Week2</div></span>
                            <span>$Forshort-</span>$cName </td>
                        <td width='140px' class='little-Qty'>
                            <div class='float_left' style='text-align:right;'>
                                <span class=''>$Qty</span>
                            </div>
                        </td>
                        <td  width='170px' class='time2 $firstDateCalss' style='font-size:30px;'>
                            <div class='' style=''>$firstDate</div>
                            <div style='position:absolute;margin-left:120px;margin-top:-40px;'>$firstChart</div>
                        </td>
                    </tr>
                    <tr>
                        <td class='remark_icon'  style='height:60px;width:60px;'>$remarkIcon</td>
                        <td colspan='3' class='remark'  style='height:60px;width:860px;'>$sgRemark</td>
                        <td class='remark'  style='height:60px;'>$sgRemark</td>
                        <td class='time2 $secondDateCalss' style='font-size:30px;'>
                            <div class='float_left' style='margin-bottom:5px;'>$secondDate</div>
                            <div style='position:absolute;margin-left:120px;'>$secondChart</div>
                        </td>
                    </tr>";
    $ListSTR.="</table>";
    $m++;
    }
         
}
                   
//上班人员统计
include "staff_counts.php";

$TotalQty=$TotalOverQty+$CurWeekQty+$NextWeekQty;
$TotalCount=$OverCount+$CurWeekCount+$NextWeekCount;
$SumQty=number_format($SumQty);

$TotalQty=number_format($TotalQty);
$TotalOverQty=number_format($TotalOverQty);
$CurWeekQty=number_format($CurWeekQty);
$NextWeekQty=number_format($NextWeekQty);

$WeekName=substr($curWeek, 4,2);

include "../iphoneAPI/subprogram/worktime_read.php";
$upTime=date("H:i:s");
 
$LeaderNumber=10200;
$LeaderName="谢雪梅";
$WeekName="<img src='../download/staffPhoto/P$LeaderNumber.png' style='height:125px;margin-bottom:0px;'>";

// $standardHeight = 160;
// for($i=6;$i>=0;$i--){
//     $dateColor = '';
//     $tmpDate = date('Y-m-d', strtotime("$curDate - $i days"));
//     $workPrice = 0;
    
//     $realPriceSql = "SELECT SUM(S.Qty) as Vaule, '1' as sign
//                     FROM $DataIn.sc1_cjtj S 
//                     LEFT JOIN $DataIn.yw1_scsheet A On A.sPOrderId = S.sPOrderId
//                     LEFT JOIN $DataIn.yw1_ordersheet B On B.POrderId = A.POrderId
//                     LEFT JOIN $DataIn.productdata C On C.ProductId = B.ProductId
//                     Where S.created >='$tmpDate 08:00' AND S.created <='$tmpDate 12:00'
//                     AND A.WorkShopId = '101'
//                     AND C.TypeId != 8061
//                     Union 
//                     SELECT SUM(S.Qty ) as Vaule, '2' as sign
//                     FROM $DataIn.sc1_cjtj S 
//                     LEFT JOIN $DataIn.yw1_scsheet A On A.sPOrderId = S.sPOrderId
//                     LEFT JOIN $DataIn.yw1_ordersheet B On B.POrderId = A.POrderId
//                     LEFT JOIN $DataIn.productdata C On C.ProductId = B.ProductId
//                     Where S.created >='$tmpDate 13:00' AND S.created <='$tmpDate 17:00'
//                     AND A.WorkShopId = '101'
//                     AND C.TypeId != 8061
//                     Union 
//                     SELECT SUM(S.Qty) as Vaule, '3' as sign
//                     FROM $DataIn.sc1_cjtj S 
//                     LEFT JOIN $DataIn.yw1_scsheet A On A.sPOrderId = S.sPOrderId
//                     LEFT JOIN $DataIn.yw1_ordersheet B On B.POrderId = A.POrderId
//                     LEFT JOIN $DataIn.productdata C On C.ProductId = B.ProductId
//                     Where S.created >='$tmpDate 18:00' AND S.created <='$tmpDate 23:59'
//                     AND A.WorkShopId = '101'
//                     AND C.TypeId != 8061";
  
//     $morningValue = 0;
//     $afternoonValue = 0;
//     $nightValue = 0;
//     $tmpDayTotole = 0;
//     $tempCount = 0;
//     $realPriceResult = mysql_query($realPriceSql);
//     while($realPriceRow = mysql_fetch_assoc($realPriceResult)){
//         $sign = $realPriceRow['sign'];
//         $tmpDayTotole += $realPriceRow['Vaule'];

//         //echo $tmpDate.'  '.$

//         switch($sign){
//             case '1':
//                 $morningValue = $realPriceRow['Vaule']==''?0:number_format($realPriceRow['Vaule']/1000,1);
//             break;
//             case '2':
//                 $afternoonValue = $realPriceRow['Vaule']==''?0:number_format($realPriceRow['Vaule']/1000,1);
//             break;
//             case '3':
//                 $nightValue = $realPriceRow['Vaule']==''?0:number_format($realPriceRow['Vaule']/1000,1);
//             break;
//         }
//     }

//     $ratePersent = 0.5;
//     $morningHeight = intval(($morningValue==0?0:$morningValue* $ratePersent) ).'px';
//     $afternoonHeight = intval(($afternoonValue==0?0:$afternoonValue* $ratePersent) ).'px';
//     $nightHeight = intval(($nightValue==0?0:$nightValue* $ratePersent) ).'px';
//     $tmpDayTotole = number_format($tmpDayTotole/1000,1).'k';
//     $diffHeight = ($standardHeight - $morningHeight - $afternoonHeight - $nightHeight-4 ).'px';

//     //备料统计
//     $blStaticSql = "SELECT sum(B.Qty) as totleblQty
//                     FROM ( 
//                         SELECT A.sPOrderId,A.Qty,A.scLineId,getCanStock(A.sPOrderId,3) AS canSign, A.LastReceived,A.ProductId
//                         FROM (
//                             SELECT S.sPOrderId,S.Qty,S.scLineId, MAX(L.Received) as LastReceived,Y.ProductId
//                             FROM      yw1_scsheet    S 
//                             LEFT JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
//                             LEFT JOIN ck5_llsheet L On L.sPOrderId = S.sPOrderId
//                             WHERE S.WorkShopId='101'
//                             GROUP By S.sPOrderId
//                         )A WHERE 1 
//                     )B 
//                     LEFT JOIN productdata P On P.ProductId = B.ProductId
//                     WHERE B.canSign=3
//                     AND P.TypeId != 8061
//                     AND LEFT(B.lastReceived, 10) = '$tmpDate'";
    


//     $blStaticResult = mysql_fetch_assoc(mysql_query($blStaticSql));
//     $blTotle = $blStaticResult['totleblQty'] == ''?'0k':number_format($blStaticResult['totleblQty']/1000,1).'k';

//     $blHeight = intval(($blTotle<1?1:$blTotle* $ratePersent) ).'px';
//     $diffBlHeight = ($standardHeight - $blHeight).'px';

//     $staticListStr .= " <td style='width:154px;font-size:16px;padding-left:40px;'>
//                             <div class='float_left' style='width:42px;height:$blHeight;background-color:#71bede;margin-right:10px;margin-top:$diffBlHeight ;'><span style='margin-top:-20px;position:absolute;text-align:center;'>$blTotle</span></div>
//                             <div class='float_left' style='width:42px;height:$tmpDayTotole;margin-top:$diffHeight;'>
//                                 <div style='width:42px;height:$nightHeight;background-color:#01be56;margin-right:10px;margin-top:0px;'><span style='margin-top:-20px;position:absolute;text-align:center;'>$tmpDayTotole</span></div>
//                                 <div style='width:42px;height:$afternoonHeight;background-color:#01be56;margin-top:2px;'></div>
//                                 <div style='width:42px;height:$morningHeight;background-color:#01be56;margin-top:2px;'></div>
//                             </div>
//                         </td>";
//     $tmpDay = substr($tmpDate, 5,5);
//     $dateListStr .= "<td class='topLine $dateColor' style='width:154px;font-size:20px;text-align:center;padding-bottom:20px;'>$tmpDay</td>";
// }


?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv_qc' style='height:200px;'>
   <div id='linediv_qc' class='float_left'><?php echo $WeekName; ?></div>
   <ul id='state_qc' class='float_left'>
        <li>
            <image class='state_img' src='image/waitFB.png'><span class='state_span'>组装</span>
        </li>
        <li>
            <image class='state_img float_left' src='image/group_staff.png'>
            <div class='float_left state_span' style='margin-left:80px;padding-top:25px;'>
                <span style='font-size:32px;margin-top:10px;color:#848888;'><?php echo $workNums; ?>人 | </span>
                <span class='red_color' style='font-size:32px;margin-top:10px;'><?php echo $LeaveNums; ?></span> 
                <span style='font-size:32px;margin-top:10px;color:#848888;'>人</span> 
            </div>
        </li>
   </ul>
   <ul id='quantity3_qc' class='float_left'>
        <li class='text_left'><span style='color:#848888'><?php echo " / $SumQty"; ?></span></li>
        <li class='text_right'><span class='margin_right_15' style='color:#ff0000;'><?php echo $TotalOverQty; ?></span></li>
   </ul>
   <ul id='count_qc' class=''>
            <li >逾期 <div></div>
                <span class='float_right'>
                    <span class='red_color'><?php echo $TotalOverQty; ?> </span>
                    <span class='littleLi'>(<?php echo $OverCount; ?>) </span>
                </span>
            </li>
            <li >本周 <div></div>
                <span class='float_right'>
                    <span ><?php echo $CurWeekQty; ?> </span>
                    <span class='littleLi'><?php echo  "($CurWeekCount)"; ?> </span>
                </span>
            </li>
            <li ><?php echo $nextWeek; ?>周+ 
                <span class='float_right'>
                    <span><?php echo $NextWeekQty; ?></span>
                    <span class='littleLi'><?php echo "($NextWeekCount)"; ?></span>
                </span>
            </li>
    </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1720px;width:1080px;'>
<?php echo $ListSTR;?>
</div>
<!-- <div id='staticDiv' class='topLine' style='height:290px;width:1080px;'>
    <table style='border-collapse: 0px;border-spacing: 0px;'>
        <tr style='height:50px;width'>
            <td colspan = '7' style='width:1080px;'>
                <div class='float_right' style='font-size:20px;width:120px;margin-right:20px;'>
                    <div class='dotDiv float_left' style='background-color:#01be56;margin-top:5px;margin-left:30px;'></div>生产
                </div>
                <div class='float_right' style='font-size:20px;width:100px;'>
                    <div class='dotDiv float_left' style='background-color:#71bede;margin-top:5px;margin-left:30px;'></div>已备
                </div>
            </td>
        </tr>
        <tr style='height:160px;' class='tableBottomLine' >
            <?php echo $staticListStr;?>
        </tr>
        <tr style='height:60px;'>
            <?php echo $dateListStr;?>
        </tr>
    </table>
</div> -->