<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";
      
      switch($Floor){
        case "3A":
        case "6":    $Floor=6; $checkSign=1; break;
        case "1A": 
        case "12":  $Floor=12;break;
        case "17":
        case "471": $Floor = 17;break;
        case "14":
        case "471C": $Floor = 14;break;
        default:     $Floor=3;break;
      }

      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     $curYear = date("Y");
     $ListSTR="";   
     
     $SearchRows=$curDate=="2015-10-17"?" AND M.CompanyId!='100167' ":"";
 $m=0;         
 //开单
 $TotleQty = 0;
 $overTimeStandard= 4 * 60;



$shSql = "SELECT  sum(A.OrderQty) AS cgQty, sum(A.Qty) as Qty,A.SendSign,A.Date,A.CompanyId,A.Forshort,count(*) as count FROM 
    (
      SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture,
      D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$curWeek',1,0) AS OveSign ,S.SendSign,M.CompanyId,M.Date
      FROM  gys_shmain M 
      LEFT JOIN gys_shsheet S ON M.Id=S.Mid
      LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId 
      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
      LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
      LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
      WHERE M.Floor='$Floor' AND S.Estate='1' AND S.SendSign<2 AND D.ComboxSign!=1 $SearchRows 
 UNION ALL
   SELECT S.Id,S.StockId,S.StuffId,S.Qty,(G.AddQty+G.FactualQty) AS OrderQty,G.DeliveryWeek,D.StuffCname,D.Picture,
      D.CheckSign,IFNULL(W.Name,B.Forshort) AS Forshort,IF(G.DeliveryWeek<'$curWeek',1,0) AS OveSign ,S.SendSign,M.CompanyId,M.Date
      FROM  gys_shmain M 
      LEFT JOIN gys_shsheet S ON M.Id=S.Mid
      LEFT JOIN cg1_stuffcombox C ON C.StockId=S.StockId 
      LEFT JOIN cg1_stocksheet G ON G.StockId=C.mStockId 
      LEFT JOIN stuffdata D ON D.StuffId=S.StuffId 
      LEFT JOIN yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
      LEFT JOIN workshopdata W ON W.Id=A.WorkShopId 
      LEFT JOIN trade_object B ON B.CompanyId=M.CompanyId  AND B.ObjectSign IN (1,3) 
      WHERE M.Floor='$Floor' AND S.Estate='1' AND S.SendSign<2 AND D.ComboxSign=1 $SearchRows 
   ORDER BY DeliveryWeek) A
   Group By A.CompanyId
   ORDER BY A.Date";
          
// $shSql = "SELECT  S.Id,S.Mid,S.StuffId,S.StockId,sum(G.AddQty+G.FactualQty) AS cgQty, sum(S.Qty) as Qty,S.SendSign,M.Date,M.CompanyId,P.Forshort,count(*) as count
//             FROM $DataIn.gys_shsheet S 
//             LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
//             LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
//             LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
//             LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
//             LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId=S.StockId 
//             LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=C.mStockId
//             WHERE  S.Estate=1  AND M.Floor='$Floor' AND S.SendSign IN (0,1) $SearchRows 
//             Group By M.CompanyId
//             ORDER BY M.Date,M.Id";
//echo $shSql;
$myResult=mysql_query($shSql,$link_id);    
while($myRow = mysql_fetch_assoc($myResult)){
    $m++;
    $CompanyId = $myRow['CompanyId'];
    $qty = $myRow['Qty'];
    $TotleQty += $qty;
    $companyName = $myRow['Forshort'];
    $shCount = $myRow['count'];

    $overDate = '';
    $overCount = 0;
    $overQty = 0;
    $OverResult=mysql_query("SELECT  S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty, IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS DeliveryDate,S.Qty,S.SendSign,
             M.created,M.CompanyId,P.Forshort,GM.PurchaseID,GM.Date AS cgDate,
             YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1) AS Weeks    
            FROM $DataIn.gys_shsheet S 
            LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
            LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
            LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=C.mStockId
            WHERE  S.Estate=1  
            AND M.Floor='$Floor'  
            AND M.CompanyId = $CompanyId
            AND S.SendSign IN (0,1) ORDER BY M.Date,M.Id",$link_id);  

    while($overRow = mysql_fetch_assoc($OverResult)){
        $Weeks=$overRow["Weeks"];
        $Qty=$overRow["Qty"];
        $SendSign=$overRow["SendSign"];
        $DeliveryDate = $overRow['created'];

        if($overDate == ''){
            $overDate = $DeliveryDate;
        }else if($overDate > $DeliveryDate){
            $overDate = $DeliveryDate;
        }

        if ($Weeks<$curWeek || $SendSign==1){
            $overQty+=$Qty;$overCount++;
        }
    }

    $seperatebgColor = '';
    if($m % 2 == 0){
        $seperatebgColor = 'listSperate';
    }

    if($overQty == 0){
        $overQty = '';
        $overCount = '';
    }else{
        $overQty = number_format($overQty);
        $overCount = "$overCount";
    }

    $timeCount = intval((strtotime($today)-strtotime($overDate))/60);

    // if($timeCount > 3*24*60){
    //     //echo "over";
    //     continue;
    // }

    $overtimeStyle = '';
    if($timeCount >= $overTimeStandard){
        $overtimeStyle = 'color:#ED6454;';
    }else{
        $overtimeStyle = 'color:#A4A6A7;';
    }

    if($timeCount < 60){
        $timeTitle = $timeCount.'分钟前';
    }else if($timeCount >= 60 && $timeCount < 1440){
        $timeTitle = intval($timeCount / 60).'小时前';
    }else if($timeCount >= 1440){
        $timeTitle = intval($timeCount / 1440).'天前';
    }

    $totleShQty = 0;
    $totleOverQty = 0;
    $shRateSql = "SELECT sum(T.qty) as qty , '1' as type
                  from (
                        select A.qty,B.rkDate,IF(D.StockId>0,D.DeliveryDate,CG.DeliveryDate) AS DeliveryDate, YEARWEEK(IF(D.StockId>0,D.DeliveryDate,CG.DeliveryDate),1) AS Weeks, YEARWEEK(B.rkDate, 1) as rkWeeks
                        from $DataIn.ck1_rksheet A
                        left join $DataIn.ck1_rkmain B On A.Mid = B.Id
                        left join $DataIn.cg1_stocksheet D On D.stockId = A.stockId
                        LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId=A.StockId 
                        LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=C.mStockId
                        where B.companyId = $CompanyId
                        AND left(B.rkDate, 4) = '$curYear'
                        Having Weeks <= rkWeeks) T
                union 
                select sum(qty) as qty, '2' as type
                from $DataIn.ck1_rksheet A
                left join $DataIn.ck1_rkmain B On A.Mid = B.Id
                where B.companyId = $CompanyId
                AND left(B.rkDate, 4) = '$curYear'";
    
    $shRateResult = mysql_query($shRateSql);
    while ($shRateRow = mysql_fetch_assoc($shRateResult)) {
        $type = $shRateRow['type'];
        switch ($type) {
            case '1':
                $totleOverQty = $shRateRow['qty'];
                break;
            
            case '2':
                $totleShQty = $shRateRow['qty'];
                break;
        }
    }
    //echo "$totleOverQty    $totleShQty <br>";
    if($totleShQty != 0){
        $shRate = intval(($totleOverQty/$totleShQty) * 100);
    }else{
        $shRate = 0;
    }

    $NumsResult=mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(IF(YEARWEEK(M.rkDate,1)>YEARWEEK(G.Deliverydate,1),S.Qty,0)) AS OverQty   
        FROM $DataIn.ck1_rksheet S
        LEFT JOIN $DataIn.ck1_rkmain M  ON M.Id=S.Mid 
        LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId=S.StockId  
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
        LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
        WHERE  M.CompanyId='$CompanyId'  and DATE_FORMAT(M.rkDate,'%Y-%m')='$checkMonth' ",$link_id);
            if($NumsRow = mysql_fetch_array($NumsResult)){
                  $P_Qty=$NumsRow["Qty"];
                  if ($P_Qty>0){
                          $P_OverQty=$NumsRow["OverQty"];
                          $Punc_Value=($P_Qty-$P_OverQty)/$P_Qty*100;
                          $Punc_Value=round($Punc_Value);
                          $Punc_Percent=$Punc_Value>=0?"    " . $Punc_Value ."%":" "; 
            }
        }


    $ListSTR .= "<tr class ='$seperatebgColor'>
                    <td class='sh_list_color listTr' style='font-size:30px;padding-left:20px;'></td>
                    <td class='sh_list_color' style='font-size:50px;'>$companyName</td>
                    <td class='sh_list_color' style='font-size:40px;'>$shRate<span style='font-size:25px;'>%</span></td>
                    <td class='sh_list_color' style='font-size:48px;color:#ED6454;padding-right:20px;'>
                        <div class='float_left' style='width:60%;text-align:right;margin-right:15px;vertical-align: middle;line-height:60px;'>".$overQty."</div>
                        <div class='float_left' style='font-size:40px;text-align:left;vertical-align: bottom;line-height:68px;color: #A4A6A7;padding-left:10px;'> $overCount</div>
                    </td>
                    <td class='sh_list_color' style='font-size:48px;color:#000;'>
                        <div class='float_left' style='width:60%;text-align:right;margin-right:20px;vertical-align: middle;line-height:60px;'>".number_format($qty)."</div>
                        <div class='float_left' style='font-size:40px;text-align:left;vertical-align: bottom;line-height:68px;color: #A4A6A7;padding-left:10px;'> $shCount</div>
                    </td>
                    <td class='sh_list_color' style='font-size:40px;text-align: right;padding-right:10px;$overtimeStyle'>$timeTitle</td>
                </tr>";


}

 
//今日到达数量
$shResult= mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty  FROM $DataIn.gys_shdate H 
                    LEFT JOIN $DataIn.gys_shsheet S  ON S.Id=H.Sid 
                    LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
                    WHERE   M.Floor='$Floor' AND  DATE_FORMAT(H.shDate,'%Y-%m-%d')='$curDate' ",$link_id));
$TodayQty=number_format($shResult["Qty"]);          

?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv_new'>
   <table>
       <tr>
           <td style='font-size:60px;line-height:160px;'>
                <img src='image/sh_cgQty.png' style='width:50px;height:50px;margin-top:50px;'><span style=''><?php echo number_format($TotleQty);?><span></td>
           <td style='width:3px;'>
                <div style='width:2px;height:100%;background-color:#CEE4F0;'></div>
           </td>
           <td style='color:#0fb900;border-width:2px;font-size:60px;line-height:160px;'>
                <img src='image/sh_arriveQty.png' style='width:50px;height:50px;margin-top:50px;'><?php echo $TodayQty;?>
          </td>
       </tr>
   </table>
</div>
<div style='overflow: hidden;width:1080px;'>
    <table id='sh_list'>
        <tr class='sh_list_color' style='background-color:#CEE4F0;height: 75px;'>
            <td style='width=5%;font-size: 35px;'>  </td>
            <td style='width=20%;font-size: 35px;'>供应商</td>
            <td style='width=15%;font-size: 35px;'>准时率</td>
            <td style='width=25%;font-size: 35px;padding-right:10px;padding-left:40px;'>逾期</td>
            <td style='width=25%;font-size: 35px;'>开单</td>
            <td style='width=10%;font-size: 35px; text-align: right; padding-right:10px;'>时间</td>
        <tr>
        <?php echo $ListSTR;?>
    </table>
</div>