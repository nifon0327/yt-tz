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

$ListSTR="";   
 $m=0;

$TotalQty=0; $TotalCount=0;//品检总数
$CurQty=0;  $CurCount=0;//品检中
$QcedQty=0;  $QcedCount=0;//待处理

$WaitQty=0; $WaitCount=0;   
//待入库
$mySql= "SELECT  S.Id,S.Mid,S.StuffId,S.StockId,G.POrderId,(G.AddQty+G.FactualQty) AS cgQty,
             IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS DeliveryDate,S.Qty AS shQty,S.SendSign,
             M.CompanyId,P.Forshort,D.StuffCname,D.Picture,YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1) AS Weeks,B.Date  
            FROM $DataIn.qc_mission H 
            LEFT JOIN $DataIn.gys_shsheet S ON H.Sid=S.Id
            LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
            LEFT JOIN $DataIn.qc_badrecord B ON B.shMid=S.Mid AND B.StockId=S.StockId AND B.StuffId=S.StuffId  
            LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=C.mStockId
            LEFT JOIN $DataIn.qc_scline L ON L.Id=H.LineId
            WHERE  H.rkSign=1 AND S.Estate=0 AND S.SendSign IN (0,1) AND M.Floor='$Floor'  GROUP BY S.Id  ORDER BY DeliveryDate, S.Id";
//
$myResult=mysql_query($mySql,$link_id);  

//按周以PI交期分组读取未出订单
$TotalOverQty=0; $OverCount=0;//逾期
$CurWeekQty=0;  $CurWeekCount=0;//本周
$NextWeekQty=0;  $NextWeekCount=0;//下周+
while($myRow = mysql_fetch_assoc($myResult)) {
    $Id=$myRow["Id"];
    $Weeks=$myRow["Weeks"];
    $SendSign=$myRow["SendSign"];

    // $checkResult=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS Qty   FROM  $DataIn.qc_cjtj  C WHERE  C.Sid='$Id'",$link_id));    
    // $Qty=$checkResult["Qty"];
    // if ($Qty<=0) continue;
    $djColor = $Qty > 0?"#01be56":"#000000";

    if ($Weeks<$curWeek || $SendSign==1){
        $TotalOverQty+=$Qty;$OverCount++;
    }else{
        if ($Weeks==$curWeek){
            $CurWeekQty+=$Qty;$CurWeekCount++;
        }else{
            $NextWeekQty+=$Qty;$NextWeekCount++;
        }
    }

    if ($m<10){
        if ($SendSign==0){
            $Week1=substr($Weeks, 4,1);
            $Week2=substr($Weeks, 5,1);
            $WeekColor=$curWeek>$Weeks?'bgcolor_red':'bgcolor_black';
            $tdColor=$curWeek>$Weeks?'red_color':'black_color';
            $WeekClass="week_qc";
            $WeekSTR="<div>$Week1</div><div>$Week2</div>";
        }else{
            $WeekClass="week2_qc";
            $WeekSTR="<div>补</div>";
        }

        $StuffId=$myRow["StuffId"];
        $StuffCname=$myRow["StuffCname"];
        $Forshort=$myRow["Forshort"];
        $cgQty=$myRow["cgQty"];
        $shQty=$myRow["shQty"];

        $Date=$myRow["Date"];
        $DateChars="检";
        if ($Date!=""){
            $DateStr=GetDateTimeOutString($Date,'');
            $checkHours=(strtotime($today)-strtotime($Date))/60;
            $checkHours=$checkHours>30?"red_color":"";
        }else{
            $DateStr="";$shColors="";
        }
    $LineNo=$myRow["LineNo"] == ""? "":"<div>$LineNo</div>";
    //配件属性
    include "stuff_property.php";
    
    $Qty=number_format($Qty);
    $cgQty=number_format($cgQty);
    $shQty=number_format($shQty);
    $Forshort=" <span class='blue_color'>$Forshort</span>";
    $ListSTR.="<table id='ListTable$m' name='ListTable[]' class='$tableClass' style='background-color:$bgColor;'>
                    <tr style='vertical-align:top;'>
                        <td width='120' class='$WeekClass  $WeekColor' style='padding-left:15px;padding-top:5px;'>$WeekSTR</td>
                        <td colspan='4' width='610' class='title_qc' style='word-break:break-all;'>$Forshort-$StuffCname</td>
                        <td class='time static_qty' width='350' style='padding-top:20px;'><span style='color:$checkHours;'>$Qty</span> / $shQty $LineNo</td>
                   </tr>";
//$ListSTR.="<tr><td colspan='5' style='height:40px;line-height:40px;'>&nbsp;</td></tr>";                 
//$ListSTR.="</table>";

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
        $ListSTR.="<tr>
                    <td></td>
                    <td colspan='4' class='remark'><img src='image/remark.png'/>$bpRemark $Remark</td>
                    <td class='time_qc float_right'><span class='$typeColor'>$DateStr</sapn><div>$DateChars</div></td>
                </tr>";
    }else{
        $ListSTR.="<tr><td colspan='4' style='height:40px;line-height:40px;'>&nbsp;</td>
                        <td  class='remark text_right'><span class='$QcDateColor'>$QcDateStr</span>&nbsp;</td>
                        <td class='time_qc float_right'><span class='$checkHours'>$DateStr</span><div>$DateChars</div></td>
                    </tr>";
    }                            
      
    $ListSTR.="</table>";

    $m++;
    }

}
                   
//$WaitQty=$TotalQty-$CurQty-$QcedQty;
//$WaitCount=$TotalCount-$CurCount-$QcedCount;
$TotalQty=number_format($TotalQty);
$CurQty=number_format($CurQty);
$QcedQty=number_format($QcedQty);
$WaitQty=number_format($WaitQty);

 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
 
 //今日品检数量
 $QtyResult =mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS Qty 
           FROM $DataIn.qc_cjtj C   
           LEFT JOIN $DataIn.gys_shsheet S ON C.Sid=S.Id
            WHERE  DATE_FORMAT(C.Date,'%Y-%m-%d')='$curDate' AND C.LineId IN ($LineId) AND S.Estate=0 
",$link_id));
$TodayQty=$QtyResult["Qty"]==""?0:number_format($QtyResult["Qty"]);

 //本月品检数量
$curMonth=date("Y-m");
$QtyResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty   
            FROM $DataIn.qc_cjtj S  
            LEFT JOIN $DataIn.gys_shsheet G On G.Id = S.Sid
            LEFT JOIN $DataIn.gys_shmain M On M.Id = G.Mid
            WHERE  DATE_FORMAT(S.Date,'%Y-%m')='$curMonth' 
            AND M.Floor = $Floor 
",$link_id));
$MonthQty=$QtyResult["Qty"]==""?0:number_format($QtyResult["Qty"]);

$WeekName="<img src='image/storage.png' style='height:80px;margin-bottom:0px;'>";
    
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv_qc' style='height:200px;'>
   <div id='linediv_qc' class='float_left'><?php echo $WeekName; ?></div>
   </ul>
   <ul id='quantity3_qc' class='float_right'>
             <li class='text_left'><span style='color:#848888'><?php echo " / ".$MonthQty; ?></span></li>
             <li class='text_right'><span class='margin_right_15'><?php echo $TodayQty; ?></span></li>
   </ul>
   <ul id='count_rk' class=''>
            <li >待处理 <div></div>
                <span class='float_right'>
                    <span class=''><?php echo number_format(intval($overTotleQty)); ?> </span>
                    <span class='littleLi'><?php echo  "($overQtyCount)"; ?> </span>
                </span>
            </li>
            <li >待入
                <span class='float_right'>
                    <span ><?php echo number_format(intval($curTotleQty)); ?> </span>
                    <span class='littleLi'><?php echo  "($curTotleCount)"; ?> </span>
                </span>
            </li>
    </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1720px;width:1080px;'>
<?php echo $ListSTR;?>
</div>