<?php
//$Line=$Line==""?"C":$Line;
include_once "tasks_function.php";
include "../basic/parameter.inc";

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
$curWeek=$dateResult["curWeek"];

$getGroupSql = "SELECT * FROM $DataIn.staffgroup WHERE GroupName = '组装$Line'";
$getGroupResult = mysql_query($getGroupSql);
$groupRow = mysql_fetch_assoc($getGroupResult);
$GroupId=$groupRow["GroupId"];
$leaderNumber=$groupRow["GroupLeader"];

$SC_TYPE='101';
$checkResult=mysql_fetch_array(mysql_query("SELECT sPOrderId 
                                            FROM $DataIn.sc_currentmission S
                                            LEFT JOIN workscline SL On SL.Id = S.LineId
                                            WHERE SL.Letter='$Line' 
                                            LIMIT 1",$link_id));
$CurPOrderId=$checkResult["sPOrderId"];
//MAX(SM.DateTime) AS mDateTime,SC.DateTime,IF(SC.Id>0,1,0) AS LineSign,
$mySql = "SELECT  B.POrderId,B.ProductId,B.sPOrderId,B.Qty,B.LeadWeek,B.ShipType,B.Remark,
        M.OrderDate,M.OrderPO,A.Forshort,P.cName,P.TestStandard,getOrderStockTime(B.sPOrderId) AS bledTime ,B.ScQty, B.ScDate
                FROM ( 
                    SELECT A.POrderId,A.ProductId,A.OrderNumber,A.sPOrderId,A.Qty,A.LeadWeek,A.ShipType,A.Remark,
                    getCanStock(A.sPOrderId,3) AS canSign, A.ScQty, A.ScDate
                    FROM (
                            SELECT S.POrderId,S.sPOrderId,S.Qty,Y.ProductId,Y.OrderNumber,Y.ShipType,
                            IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek, SUM(SC.Qty) as ScQty, MAX(SC.created) as ScDate,S.Remark
                            FROM      yw1_scsheet S 
                            LEFT JOIN sc1_cjtj SC On SC.sPOrderId = S.sPOrderId
                            LEFT JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
                            LEFT JOIN workscline SL On SL.Id = S.scLineId
                            LEFT JOIN yw3_pisheet PI ON PI.oId=Y.Id
                            LEFT JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
                            WHERE S.WorkShopId='101' AND S.ScFrom>0 AND S.Estate>0 And SL.Letter = '$Line'
                            GROUP By S.sPOrderId
                    ) A WHERE 1
                 ) B 
               INNER JOIN yw1_ordermain M ON M.OrderNumber=B.OrderNumber
               INNER JOIN trade_object A ON A.CompanyId=M.CompanyId 
               INNER JOIN productdata P ON P.ProductId=B.ProductId  
               WHERE B.canSign=3 
               ORDER BY B.ScQty Desc,LeadWeek,OrderDate";
//echo $mySql.'<br>';
$myResult = mysql_query($mySql,$link_id); 
$today=date("Y-m-d H:i:s");

$SumQty=0;$SumScQty=0;
$OweQty=0;$OweCount=0;
$WaitQty=0;$WaitCount=0;
$ListSTR="";$m=0; 
$TotalCount=0;  $FinishCount=0;

$overTotleQty = 0;
$overQtyCount = 0;
$standardHeight = 210;
while($myRow = mysql_fetch_assoc($myResult)){
    
    $POrderId=$myRow["POrderId"];
    $Week1=substr($myRow["LeadWeek"], 4,1);
    $Week2=substr($myRow["LeadWeek"], 5,1);
    $WeekColor=$curWeek>$myRow["LeadWeek"]?'bgcolor_red':'bgcolor_black';
    $Forshort=$myRow["Forshort"];
    $cName=$myRow["cName"];$cName = mb_strlen($cName,'utf-8')>12?mb_substr($cName, 0, 12,'utf-8').'...':$cName;
    $OrderPO=$myRow["OrderPO"];
    $Qty=$myRow["Qty"];
    $sPOrderId = $myRow["sPOrderId"];
    $ProductId = $myRow['ProductId'];
    $ScQty=$myRow["ScQty"]=''?0:$myRow["ScQty"];
    $ScDate=$myRow["ScDate"];
    //逾期
    if($myRow['LeadWeek'] < $curWeek){
        $overTotleQty += $Qty;
        $overQtyCount++;
    }

    $sgRemark=$myRow["Remark"];
    if ($sgRemark=='' || $sgRemark=='新增业务订单' || $sgRemark=='新增重置' || $sgRemark=='生产工单设置更新' || $sgRemark == '新单重置')
    {
       $sgRemark=''; 
    }
    
    $tableClass = '';
    $ScDateColor="";$ScDateStr="";
    $scMinute=(strtotime($today)-strtotime($ScDate))/60;
    if ($ScDate != '' && $scMinute>30){
        $OweQty+=$Qty-$ScQty;
        $OweCount++;
        $ScDateStr=GetDateTimeOutString($ScDate,'');
        $tableClass=" tb_bgcolor1 ";
    
        
        $FinishCount+=$scMinute<60 && $sgRemark==""?1:0;
        $ScDateColor=$scMinute<60?"orange_color":" red_color ";
        $SumQty+=$Qty; 
        $SumScQty+=$ScQty;
    }else{
       //生产中
        if ($ScQty>0){
            $SumQty+=$Qty; 
            $SumScQty+=$ScQty;
            $tableClass=" tb_bgcolor2";
        }else{
            $WaitQty+=$Qty;
            $WaitCount++;
            //$tableClass=" tb_bgcolor0";
        }
    }
    
    $TotalCount++;
    $TotalQty+=$Qty-$ScQty;
    $BlDate = $myRow["bledTime"];
    
    $isVisiable = '';
    if($ScQty == 0){
        $DateChars = "<img src='image/bled_state.png'>";
        $DateStr=GetDateTimeOutString($BlDate,'');
        $blHours=(strtotime($today)-strtotime($BlDate))/3600;
        $blColors=$blHours>24?"red_color":"";
        $blDateStr="";$blDateStr="";
    }else{
        $DateStr=GetDateTimeOutString($ScDate,'');
        $isVisiable ='visibility:hidden;';
    }


    $remarkIcon = $sgRemark != ""?"<img src='image/remark.png'/>":'';
    $curSign=$CurPOrderId==$sPOrderId?"<img src='image/qr.png' style='width:30px;height:30px;'/>":"";
    $tableClass=$CurPOrderId==$POrderId?" tb_bgcolor2":$tableClass;
     
    $Qty=number_format($Qty);
    $ScQty=number_format($ScQty);
    if ($m<12){
        //出货方式
        $ShipType=$myRow["ShipType"];
        // if($tableClass == ''){
        //     $bgColor = $m%2!=0?"#E5F1F7":"#fff";
        // }
        if($curSign == ''){
            $ShipType=$ShipType===""?"":"<image src='../images/ship$ShipType.png' style='width:40px;height:40px;'/>";
        }else{
            $ShipType = $curSign;
        }

        
        $logoName = 'image/noIogo.png';
        
        $productIcon ='../download/productIcon/' . $ProductId . '.png';
        if(file_exists($productIcon)){
              $logoName=$productIcon;
         }
         else{
	       if(file_exists("../download/productIcon/$ProductId.jpg")){
               $logoName = "../download/productIcon/$ProductId.jpg";
           }  
       }
       

        $ListSTR.="<table id='ListTable$m' name='ListTable[]' class='$tableClass' style='background-color:$bgColor;'>
                    <tr>
                        <td rowspan='2' width='120'><img style='width:100px;' src='$logoName'></td>
                        <td colspan='4' width='700' class='title'>
                            <span class='week $WeekColor'><div>$Week1</div><div>$Week2</div></span>
                            <span>$Forshort-</span>$cName</td>
                        <td width='260' class='little-Qty'><div class='float_left' style='text-align:right;width:218px;'><span class='otherGreen_color'>$ScQty</span>/$Qty<span></div><div style='margin-bottom:-10px;margin-right:15px;' class='float_right'>$ShipType</div></span></td>
                    </tr>
                    <tr>
                        <td  class='remark_icon'  style='height:60px;width:60px;'>$remarkIcon</td>
                        <td colspan='3' class='remark'  style='height:60px;width:760px;'>$sgRemark</td>
                        <td  width='280' class='time time2 $blColors' >$DateStr<div style='margin-bottom:5px;$isVisiable'>$DateChars</div></td>
                    </tr>";
        $ListSTR.="</table>";
   }
   $m++;
}

if ($ListSTR!=""){
    $curDate=date("Y-m-d");
    //组装总人数
    $BranchResult =mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts   
        FROM $DataIn.staffgroup G 
        LEFT JOIN $DataIn.sc1_memberset  S ON S.GroupId=G.GroupId AND S.Date='$curDate' 
        LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
        WHERE  G.GroupId = $GroupId  AND M.Estate=1 AND M.cSign=7 ",$link_id));
    $BranchNums=$BranchResult["Counts"];  
   
    //请假人数
    $OverTime=date("Y-m-d") . " 17:00:00";
    $LeaveResult =mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts  FROM (SELECT K.Number   
        FROM $DataPublic.kqqjsheet K
        LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
        WHERE (K.StartDate<NOW() AND (K.EndDate>=NOW() OR K.EndDate>='$OverTime')) AND M.GroupId='$GroupId' AND M.cSign=7 AND M.Estate=1  GROUP BY K.Number)A ",$link_id));
    $leaveCount=$LeaveResult["Counts"];
  
  
    $ScedResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS ScQty,0 AS OrderQty  FROM $DataIn.sc1_cjtj S  
      WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND S.boxId like '$Line%'  
",$link_id));//AND S.GroupId='$GroupId' 
  
    $TotalScQty=$ScedResult["ScQty"]==""?0:$ScedResult["ScQty"];
  
    //$TotalQty=$ScedResult["OrderQty"]==""?0:$ScedResult["OrderQty"];
    //$TotalQty+=$WaitQty;
  
    $TotalScQty=number_format($TotalScQty);
    $TotalQty=number_format($TotalQty);
  
    $OweQty=number_format($OweQty) ;
    $WaitQty=number_format($WaitQty) ;
  
    $AlignClass=$Align=="R"?"float_right":"float_left";
    $LeaderClass=$Align=="R"?"leader_right":"leader_left";
    $MarginClass=$Align=="R"?"margin_right_15":"margin_left_15";
  
    $GroupClass=$Align=="R"?"float_left margin_left":"float_right";
    $ClearClass=$Align=="R"?"clear_left":"clear_right";
  
   include "../iphoneAPI/subprogram/worktime_read.php";
   $upTime=date("H:i:s");

   $WeekName="<img src='../download/staffPhoto/P$leaderNumber.png' style='height:125px;margin-bottom:0px;'>";


   //统计产值
//    for($i=9;$i>=0;$i--){
//     $dateColor = '';
//     $tmpDate = date('Y-m-d', strtotime("$curDate - $i days"));
//     $workPrice = 0;

//     $realPriceSql = "SELECT SUM(S.Qty*B.Price) as Vaule, '1' as sign
//                     FROM $DataIn.sc1_cjtj S 
//                     LEFT JOIN $DataIn.yw1_scsheet A On A.sPOrderId = S.sPOrderId
//                     LEFT JOIN $DataIn.cg1_stocksheet B On B.StockId = S.StockId
//                     INNER JOIN $DataIn.workscline SL On SL.Id = A.scLineId
//                     Where S.created >='$tmpDate 08:00' AND S.created <='$tmpDate 12:00'
//                     AND A.WorkShopId = '101'
//                     AND SL.Letter = '$Line'
//                     Union ALL
//                     SELECT SUM(S.Qty*B.Price) as Vaule, '2' as sign
//                     FROM $DataIn.sc1_cjtj S 
//                     LEFT JOIN $DataIn.yw1_scsheet A On A.sPOrderId = S.sPOrderId
//                     LEFT JOIN $DataIn.cg1_stocksheet B On B.StockId = S.StockId
//                     INNER JOIN $DataIn.workscline SL On SL.Id = A.scLineId
//                     Where S.created >='$tmpDate 13:00' AND S.created <='$tmpDate 17:00'
//                     AND A.WorkShopId = '101'
//                     AND SL.Letter = '$Line'
//                     Union ALL
//                     SELECT SUM(S.Qty*B.Price) as Vaule, '3' as sign
//                     FROM $DataIn.sc1_cjtj S 
//                     LEFT JOIN $DataIn.yw1_scsheet A On A.sPOrderId = S.sPOrderId
//                     LEFT JOIN $DataIn.cg1_stocksheet B On B.StockId = S.StockId
//                     INNER JOIN $DataIn.workscline SL On SL.Id = A.scLineId
//                     Where S.created >='$tmpDate 18:00' AND S.created <='$tmpDate 23:59'
//                     AND A.WorkShopId = '101'
//                     AND SL.Letter = '$Line'";
//     //echo $realPriceSql.'<br>';
//     $morningValue = 0;
//     $afternoonValue = 0;
//     $nightValue = 0;

//     $realPriceResult = mysql_query($realPriceSql);
//     while($realPriceRow = mysql_fetch_assoc($realPriceResult)){
//         $sign = $realPriceRow['sign'];
//         switch($sign){
//             case '1':
//                 $morningValue = $realPriceRow['Vaule']==''?0:$realPriceRow['Vaule'];
//             break;
//             case '2':
//                 $afternoonValue = $realPriceRow['Vaule']==''?0:$realPriceRow['Vaule'];
//             break;
//             case '3':
//                 $nightValue = $realPriceRow['Vaule']==''?0:$realPriceRow['Vaule'];
//             break;
//         }
//     }
//     $range = 0.015;
//     $morningHeight = intval(($morningValue<1?1:$morningValue* $range) ).'px';
//     $afternoonHeight = intval(($afternoonValue<1?1:$afternoonValue* $range) ).'px';
//     $nightHeight = intval(($nightValue<=0?0:$nightValue* $range) ).'px';

//     $tmpDayTotole = number_format($morningValue + $afternoonValue + $nightValue);
//     $tmpTotleHeight = $morningHeight + $afternoonHeight + $nightHeight;
//     if($tmpTotleHeight >= $standardHeight){
//         $tmpTotleHeight = $standardHeight - 30;
//     }

//     $dateColor = '';
//     if($tmpDate == $curDate){
//         $dateColor = 'blue_color';
//     }else{
//         $weekDay=date("w",strtotime($tmpDate));
//         $holidayResult = mysql_query("SELECT Type,jbTimes FROM ".$DataPublic.".kqholiday WHERE Date='$tmpDate'");
//         if(mysql_num_rows($holidayResult) == 1 || $weekDay==6 || $weekDay==0){
//             $dateColor = 'red_color';
//         }
//     }

//     $diffHeight = ($standardHeight - $tmpTotleHeight-4 ).'px';
//     $staticListStr .= " <td style='width:154px;font-size:16px;padding-left:40px;'>
//                             <div style='width:42px;height:$nightHeight;background-color:#01be56;margin-right:10px;margin-top:$diffHeight;'><span style='margin-top:-20px;position:absolute;text-align:center;'>$tmpDayTotole</span></div>
//                             <div style='width:42px;height:$afternoonHeight;background-color:#01be56;margin-top:2px;'></div>
//                             <div style='width:42px;height:$morningHeight;background-color:#01be56;margin-top:2px;'></div>
//                         </td>";
//     $tmpDay = substr($tmpDate, 5,5);
//     $dateListStr .= "<td class='topLine $dateColor' style='width:154px;font-size:20px;text-align:center;padding-bottom:20px;'>$tmpDay</td>";                  
// }

$AlignClass=$Align=="R"?"float_right":"float_left";
$quantityClass = $Align=="R"?"quantity3_task":"quantity3_qc";
$AlignClassL = $Align=="R"?"float_left":"float_right";

?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 <input type='hidden' id='FinishCount' name='FinishCount' value='<?php echo $FinishCount; ?>'>
 
<div id='headdiv_qc' style='height:200px;'>
   <div id='linediv_qc' class='<?php echo $AlignClass;?>'><?php echo $WeekName; ?></div>
   <ul id='state_qc' class='<?php echo $AlignClass;?>'>
        <li>
            <image class='state_img' src='image/<?php echo "Line$Line";?>.png'><span class='state_span'>组装</span>
        </li>
        <li>
            <image class='state_img float_left' src='image/group_staff.png'>
            <div class='float_left state_span' style='margin-left:80px;padding-top:25px;'>
                <span style='font-size:32px;margin-top:10px;color:#848888;'><?php echo $BranchNums; ?>人 | </span>
                <span class='red_color' style='font-size:32px;margin-top:10px;'><?php echo $leaveCount; ?></span> 
                <span style='font-size:32px;margin-top:10px;color:#848888;'>人</span> 
            </div>
        </li>
   </ul>
   <ul id='<?php echo $quantityClass;?>' class='<?php echo $AlignClass;?>'>
        <?php if ($Align=="R"){ ?>
            <li class='float_left'><span style='color:#848888'><?php echo "  $TotalQty /"; ?></span></li>
            <li class='float_left'><span class='margin_right_15'><?php echo $TotalScQty; ?></span></li>
            
        <?php }else {?>
            <li class='text_left'><span style='color:#848888'><?php echo " / $TotalQty"; ?></span></li>
            <li class='text_right'><span class='margin_right_15'><?php echo $TotalScQty; ?></span></li>
        <?php }?>
   </ul>
   <ul id='count_qc' class=''>
            <li >逾期 <div></div>
                <span class='float_right'>
                    <span class='red_color'><?php echo number_format(intval($overTotleQty)); ?> </span>
                    <span class='littleLi'><?php echo  "($overQtyCount)"; ?> </span>
                </span>
            </li>
            <li >已登 <div></div>
                <span class='float_right'>
                    <span style='color:#01be56'><?php echo number_format(intval($SumScQty)); ?> </span>
                    <span ><?php echo '/'.$SumQty; ?> </span>
                </span>
            </li>
            <li >已配 
                <span class='float_right'>
                    <span><?php echo $WaitQty; ?></span>
                    <span class='littleLi'><?php echo "($WaitCount)"; ?></span>
                </span>
            </li>
    </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1720px;width:1080px;'>
<?php echo $ListSTR;?>
</div>
<!-- <div id='staticDiv' class='topLine' style='height:290px;width:1080px;'>
    <table style='border-collapse: 0px;border-spacing: 0px;'>
        <tr style='height:210px;' class='tableBottomLine' >
            <?php echo $staticListStr;?>
        </tr>
        <tr style='height:60px;'>
            <?php echo $dateListStr;?>
        </tr>
    </table>
</div> -->
<?php } ?>