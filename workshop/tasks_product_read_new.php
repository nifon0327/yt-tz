<?php
//$Line=$Line==""?"C":$Line;
include_once "tasks_function.php";
include "../basic/parameter.inc";
$path = $_SERVER["DOCUMENT_ROOT"];
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceDecorator.php");
include_once("$path/ipdAPI/Attendance_new/AttendanceClass/AttendanceStatistic.php");

  
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
$curWeek=$dateResult["curWeek"];

$SC_TYPE='101';
$curDate=date("Y-m-d");
$today=date("Y-m-d H:i:s");
 
 //$SearchRows=$curDate=="2015-10-16"?" AND M.CompanyId<>'100083' AND M.CompanyId<>'100072' ":"";
$mySql = "  SELECT S.POrderId,S.sPOrderId,S.Qty,Y.ProductId,Y.OrderNumber,Y.ShipType,IFNULL(PI.LeadWeek,PL.LeadWeek) AS LeadWeek,M.OrderDate,M.OrderPO,A.Forshort,P.cName,P.TestStandard,'' AS created,L.Letter AS Line, SUM(SC.Qty) as ScQty, max(SC.created) as ScDate , SL.Letter, S.Remark
            FROM       yw1_scsheet    S 
            LEFT JOIN sc1_cjtj SC On SC.sPOrderId = S.sPOrderId
            INNER JOIN workscline SL ON SL.Id = S.scLineId
            INNER JOIN yw1_ordersheet Y  ON Y.POrderId=S.POrderId 
            INNER JOIN yw1_ordermain M   ON M.OrderNumber=Y.OrderNumber
            INNER JOIN trade_object A    ON A.CompanyId=M.CompanyId 
            INNER JOIN productdata P     ON P.ProductId=Y.ProductId 
            LEFT  JOIN yw3_pisheet PI    ON PI.oId=Y.Id
            LEFT  JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
            LEFT  JOIN workscline L      ON L.Id=S.scLineId 
            WHERE S.WorkShopId='101' AND S.ScFrom in (0,2) AND S.Estate=1 
            Group By S.sPOrderId
            HAVING LeadWeek > 0 and ScQty > 0
            ORDER BY S.ScFrom Desc,LeadWeek,OrderDate";

$myResult = mysql_query($mySql ,$link_id); 
  
$SumScQty=0;$ScCount=0;
$OweQty=0;$OweCount=0;
$SumOweQty=0; 
$ListSTR="";    $m=0;   $TotalCount=0;  $FinishCount=0;
while($myRow = mysql_fetch_assoc($myResult)){

    $Week1=substr($myRow["LeadWeek"], 4,1);
    $Week2=substr($myRow["LeadWeek"], 5,1);
    $WeekColor=$curWeek>$myRow["LeadWeek"]?'bgcolor_red':'bgcolor_black';
    $Forshort=$myRow["Forshort"];
    $cName=$myRow["cName"];
    $ProductId = $myRow["ProductId"];
    $Qty=$myRow["Qty"];
    $ScQty=$myRow["ScQty"] == ''?0:$myRow["ScQty"];
    $ScDate=$myRow["ScDate"];
    $InspectionSign=$myRow["InspectionSign"];
    $sPOrderId = $myRow['sPOrderId'];
    $cName = mb_strlen($cName,'utf-8')>12?mb_substr($cName, 0, 12,'utf-8').'...':$cName;
    $Estate = $myRow['Estate'];
    $DateStr=GetDateTimeOutString($ScDate,'');
    $ScLine=substr($myRow["boxId"], 0,1);
    $Qty = intval($Qty);
    $scFrom = $myRow['ScFrom'];
    $Line = $myRow['Letter'];
    $sgRemark=$myRow["Remark"];
        if ($sgRemark=='' || $sgRemark=='新增业务订单' || $sgRemark=='新增重置' || $sgRemark=='生产工单设置更新')
        {
           $sgRemark=''; 
        }
    $ScQtyRow=mysql_fetch_array(mysql_query("SELECT SUM(A.Qty) AS Qty, MAX(A.created) as maxCreated FROM $DataIn.sc1_cjtj A
                                            LEFT JOIN $DataIn.yw1_scsheet B On B.sPOrderId = A.sPOrderId
                                            WHERE A.sPOrderId='$sPOrderId' AND B.WorkShopId='101'",$link_id));
    $ScQty2=$ScQtyRow["Qty"];
    $Inspection=0;
    if($ScQty2==$Qty && $Estate==1){
        $SumScQty+=$ScQty;
        $ScCount++;
        $tableClass="table_image";
        if ($InspectionSign==1){
            $checkInspectResult=mysql_query("SELECT I.Inspection FROM $DataIn.yw1_productinspection I WHERE I.POrderId='$POrderId' ORDER BY I.Id DESC LIMIT 1",$link_id);
            if ($InspectRow = mysql_fetch_assoc($checkInspectResult)){
                    $Inspection=$InspectRow["Inspection"];
                    if ($Inspection==1)$FinishCount++;
            }
        }else{
              $FinishCount++;
        }

    }else{
        //欠尾数   
        if ((strtotime($today)-strtotime($ScDate))/60>30 && $Qty>$ScQty){
            $OweQty+= $Qty-$ScQty;
            $SumOweQty+=$Qty;
            $OweCount++;
            $tableClass="tb_bgcolor1";
        }else{
            continue;
        }
    }

    
    if ($m<10){
        if ($InspectionSign==1){
            $InspectionImage="<img src='image/inspection_$Inspection.png' style='margin-right:10px;width:42px;'/>";
        }
        else{
            $InspectionImage="";
        }
        $ShipType=$myRow["ShipType"];
        $ShipType=$ShipType===""?"":"<image src='../images/ship$ShipType.png' style='margin:10px 10px 0px 0px;width:48px;height:48px;'/>";
        //$bgColor = $m%2!=0?"#E5F1F7":"#fff";
        
        $LineImage="<img src='image/$Line.png' style='margin-right:0px;width:42px;'/>";
        
       $productIcon ='../download/productIcon/' . $ProductId . '.png';
       if(!file_exists($productIcon)){
	     $productIcon= '../download/productIcon/'  . $ProductId . '.jpg';  
       }
	       
        $remarkIcon = $sgRemark != ""?"<img src='image/remark.png'/>":'';
        $ListSTR.="<table id='ListTable$m' name='ListTable[]' class='$tableClass' style='background-color:$bgColor;'>
                    <tr>
                        <td rowspan='2' width='120'><img style='width:100px;' src='$productIcon'></td>
                        <td colspan='4' width='700' class='title'>
                            <span class='week $WeekColor'><div>$Week1</div><div>$Week2</div></span>
                            <span>$Forshort-</span>$cName</td>
                        <td width='260' class='little-Qty'>
                            <div class='float_left' style='text-align:right;width:218px;'>
                                <span class='otherGreen_color'>$ScQty</span>/$Qty
                            </div>
                            <div style='margin-bottom:5px;' class='float_right'>$InspectionImage</div>
                        </td>
                    </tr>
                    <tr>
                        <td  class='remark_icon' style='height:60px;width:60px;'>$remarkIcon</td>
                        <td colspan='3' class='remark'  style='height:60px;width:760px;'>$sgRemark</td>
                        <td  width='280' class='line2 time2' >$DateStr<div class='' style='margin-bottom:5px;'>$LineImage</div></td>
                    </tr>";
        $ListSTR.="</table>";
    }


    $m++;
}

$TotalCount=$ScCount+$OweCount;

$SumOweQty=number_format($SumOweQty);
$OweQty="<span class='red_color'>" . number_format($OweQty) . "</span><span style='color:#888888;font-size:30pt;'>($OweCount)</span>";
$SumScQty="<span class='green_color'>" . number_format($SumScQty) . "</span><span style='color:#888888;font-size:30pt;'>($ScCount)</span>";

//今日生产数量
$ScedResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS ScQty    
        FROM $DataIn.sc1_cjtj S 
        LEFT JOIN $DataIn.yw1_scsheet Y On S.sPOrderId = Y.sPOrderId
        WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND Y.WorkShopId='$SC_TYPE' ",$link_id));
$TotalScQty=$ScedResult["ScQty"]==""?0:number_format($ScedResult["ScQty"]);

//本月生产数量
$curMonth=date("Y-m");
$ScedResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS ScQty    
        FROM $DataIn.sc1_cjtj S 
        LEFT JOIN $DataIn.yw1_scsheet Y On S.sPOrderId = Y.sPOrderId
        WHERE  DATE_FORMAT(S.Date,'%Y-%m')='$curMonth' AND Y.WorkShopId='$SC_TYPE' ",$link_id));
$MonthScQty=$ScedResult["ScQty"]==""?0:number_format($ScedResult["ScQty"]);

//上班人员统计
include "staff_counts.php";



$WeekName=substr($curWeek, 4,2);
//include "../iphoneAPI/subprogram/worktime_read.php";
$upTime=date("H:i:s");
    // $FinishCount=rand(0,1);

//产值统计
$barHeight = 165;
$kToPx = 4;
$staticListStr = "";
$dateListStr = "";

$dayValue = 0;
$dayforcastValue = 0;
$monthValue = 0;
$monthforcastValue = 0;

$m = $factoryCheck=='on'?1:0;
for($i=6+$m;$i>=0;$i--){
    $dateColor = '';
    $tmpDate = date('Y-m-d', strtotime("$curDate - $i days"));
    if ($factoryCheck=='on' && date('w',strtotime($tmpDate))==0) {
			   continue;
	}
			
    $workPrice = 0;
    if($tmpDate == $curDate){
        $CheckNums=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums FROM $DataIn.checkinout  C 
                                              LEFT JOIN  $DataPublic.staffmain M  ON M.Number=C.Number  
                                              LEFT JOIN $DataIn.staffgroup G  ON G.GroupId=M.GroupId 
                                              WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$tmpDate' 
                                              AND C.CheckType='I'  
                                              AND  G.TypeId='7100'
                                              AND NOT EXISTS(SELECT K.Number 
                                                             FROM $DataIn.checkinout K 
                                                             WHERE DATE_FORMAT(K.CheckTime,'%Y-%m-%d')='$tmpDate' 
                                                             AND K.CheckType='O'  
                                                             AND  K.Number=C.Number )",$link_id));
        $kqNums=$CheckNums["Nums"]==""?0:$CheckNums["Nums"];
        $reduceHours = 0;
        if(strtotime($today) > strtotime($curDate.' 13:00')){
            $reduceHours++;
        }

        if(strtotime($today) > strtotime($curDate.' 18:00')){
            $reduceHours++;
        }

        $workHours = (strtotime($today)-strtotime($curDate.' 08:00')-$reduceHours*3600)/3600;
        $workPrice = $kqNums * $workHours * 20;

    }else{
        $CheckNums=mysql_query("SELECT SUM(SdTime+JbTime+JbTime2+JbTime3) as hours FROM $DataIn.kqdaytj C
                                                  LEFT JOIN $DataIn.staffmain S ON S.Number = C.Number
                                                  LEFT JOIN $DataIn.staffgroup G ON G.GroupId = S.GroupId
                                                  WHERE C.Date = '$tmpDate'
                                                  AND G.TypeId = 7100
                                                  AND S.WorkAdd = 1",$link_id);
        
        $CheckNumsResult = mysql_fetch_assoc($CheckNums);
        $workPrice = ($CheckNumsResult['hours'] * 20);

    }
    
    $realPriceSql = "SELECT SUM(S.Qty * B.Price) as Vaule 
                    FROM $DataIn.sc1_cjtj S 
                    LEFT JOIN $DataIn.yw1_scsheet A On A.sPOrderId = S.sPOrderId
                    LEFT JOIN $DataIn.cg1_stocksheet B On B.StockId = S.StockId
                    Where LEFT(S.created,10)='$tmpDate'
                    AND A.WorkShopId = '101'";
    $realPriceResult = mysql_query($realPriceSql);
    $realPriceRow = mysql_fetch_assoc($realPriceResult);
    $realPrice = $realPriceRow['Vaule'];

    if($curDate == $tmpDate){
        $dateColor = " blue_color";
        $colortype = $workPrice > $realPrice?" red_color":" otherGreen_color";
        $dayValue = number_format($workPrice);
        $dayforcastValue = number_format($realPrice);
    }

    //预估
    $workPrice = number_format($workPrice/1000,1).'k';
    $workPriceHeight = $workPrice * 4;
    $diffWorkPriceHeight = $barHeight - $workPriceHeight;
    $workPriceHeight.='px';
    $diffWorkPriceHeight.='px';

    //实际
    $realPrice = number_format($realPrice/1000,1).'k';
    $realPriceHeight = $realPrice * 4;
    $diffrealPriceHeight = $barHeight - $realPriceHeight;
    $realPriceHeight.='px';
    $diffrealPriceHeight.='px';

    $staticListStr .= " <td style='width:154px;font-size:16px;padding-left:40px;'>
                            <div class='float_left' style='width:42px;height:$workPriceHeight;background-color:#71bede;margin-right:10px;margin-top:$diffWorkPriceHeight;'><span style='margin-top:-20px;position:absolute;text-align:center;'>$workPrice</span></div>
                            <div class='float_left' style='width:42px;height:$realPriceHeight;background-color:#01be56;margin-top:$diffrealPriceHeight;'><span style='margin-top:-20px;position:absolute;'>$realPrice</span></div>
                        </td>";
    $tmpDay = substr($tmpDate, 5,5);
    $dateListStr .= "<td class='topLine $dateColor' style='width:154px;font-size:20px;text-align:center;padding-bottom:20px;'>$tmpDay</td>";                  
}

$monthValue = 0;
$monthForcastValue = 0;
$month = date('Y-m');
$MonthCheckNums=mysql_query("SELECT SUM(SdTime+JbTime+JbTime2+JbTime3) as hours FROM $DataIn.kqdaytj C
                                                  LEFT JOIN $DataIn.staffmain S ON S.Number = C.Number
                                                  LEFT JOIN $DataIn.staffgroup G ON G.GroupId = S.GroupId
                                                  WHERE Left(C.Date, 7) = '$month'
                                                  AND G.TypeId = 7100
                                                  AND S.WorkAdd = 1",$link_id);
$MonthCheckNumsResult = mysql_fetch_assoc($MonthCheckNums);
$monthForcastValue = $MonthCheckNumsResult['hours'] * 20;

$realMonPriceSql = "SELECT SUM(S.Qty * B.Price) as Vaule 
                    FROM $DataIn.sc1_cjtj S 
                    LEFT JOIN $DataIn.yw1_scsheet A On A.sPOrderId = S.sPOrderId
                    LEFT JOIN $DataIn.cg1_stocksheet B On B.StockId = S.StockId
                    Where LEFT(S.created,7)='$month'
                    AND A.WorkShopId = '101'";
//echo $realMonPriceSql;
$realMonthPriceResult = mysql_query($realMonPriceSql);
$realMonthPriceRow = mysql_fetch_assoc($realMonthPriceResult);
$monthValue = $realMonthPriceRow['Vaule'];
$monthColortype = $monthForcastValue > $monthValue ?" red_color":" otherGreen_color";
//echo $monthForcastValue.'  '.$monthValue;
$rateState = "";

if($monthForcastValue - $monthValue > 0){
    $rateState ="<image style='width:15px;margin-top:0px;' src='image/reduce.png'>";
}else{
    $rateState ="<image style='width:15px;margin-top:0px;' src='image/incre.png'>";
}

$rate = intval((abs($monthForcastValue - $monthValue) / $monthForcastValue)*100);
$rateState = $rate.'%'.$rateState;
?>
<input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
<input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
<input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
<input type='hidden' id='FinishCount' name='FinishCount' value='<?php echo $FinishCount; ?>'>

<div id='headdiv' style='height:200px;'>
<div id='weekdiv' class='float_left'><?php echo $WeekName; ?></div>
    <ul id='quantity3'   class='float_right' style='width:750px;'>
        <li class='text_left' style='font-size:58pt;'><span style='color:#007C2D'><?php echo '/'.$MonthScQty; ?></span></li>
        <li class='text_right' style='font-size:58pt;'><span><?php echo $TotalScQty; ?></span></li>
    </ul>
<!--
<ul id='quantity' class='float_right clear_right'>
         <li class='text_right'  style='width:360px;font-size:58pt;'><span><?php echo $TotalScQty;  ?></span><div></div></li>
         <li class='text_left' style='width:400px;font-size:58pt;'><?php echo $MonthScQty; ?></li>
</ul> #D9EAF4
-->
    <ul id='count'>
        <li style='width: 540px;'><div></div><span class='float_left'>欠尾数</span> <span><?php echo $OweQty; ?> </span></li>
        <li style='width: 540px;'><span class='float_left'>待入库</span><span><?php echo $SumScQty; ?></span></li>
    </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1430px;width:1080px;'>
    <?php echo $ListSTR;?>
</div>
<div id='staticDiv' class='topLine' style='height:290px;width:1080px;'>
    <table style='border-collapse: 0px;border-spacing: 0px;'>
        <tr style='height:50px;width'>
            <td colspan = '7'>
                <div class='float_right' style='font-size:15px;width:200px;'>
                    <div style='width:100%'>月产值 <span class='float_right <?echo $monthColortype;?>' style='margin-right:10px;'><?php echo $rateState;?></span></div>
                    <div><?
                    $monthForcastValue = number_format($monthForcastValue);
                    $monthValue = number_format($monthValue);
                    echo "<span class='$monthColortype'>￥$monthValue</span>/￥ $monthForcastValue "; ?></div>
                </div>
                <div class='float_right' style='font-size:15px;width:200px;'>
                    <div>日产值</div>
                    <div><?echo "<span class='$colortype'>￥$dayforcastValue </span>/￥$dayValue"; ?></div>
                </div>
                <div class='float_right' style='font-size:20px;width:120px;margin-right:20px;'>
                    <div class='dotDiv float_left' style='background-color:#01be56;margin-top:5px;margin-left:30px;'></div>实际
                </div>
                <div class='float_right' style='font-size:20px;width:100px;'>
                    <div class='dotDiv float_left' style='background-color:#71bede;margin-top:5px;margin-left:30px;'></div>预估
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
</div>
