<?php
    include_once "tasks_function.php";
    include "../basic/parameter.inc";
      
    switch($Floor){
        case "3A":
        case "6":    $Floor=6;break;
        case "1A": 
        case "12":   $Floor=12;break;
        case "17":   $Floor=17;break;
        default:     $Floor=3;break;
    }

    $totleStockeSql = "SELECT SUM(K.tStockQty) AS tStockQty,
                        SUM(K.tStockQty*D.Price*C.Rate) AS tPrice,
                        SUM(K.oStockQty*D.Price*C.Rate) AS oPrice,
                        COUNT(*) as count
                        FROM $DataIn.ck9_stocksheet K
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
                        LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
                        LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
                        LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
                        LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
                        WHERE  K.tStockQty>0 AND T.mainType<2  AND P.CompanyId!='2166' AND D.SendFloor = $Floor";

    $totleStockResult = mysql_query($totleStockeSql);
    $totleRows = mysql_fetch_assoc($totleStockResult);
    $tStockQty = $totleRows['tStockQty'];
    //$oStockQty = $totleRows['oStockQty'];
    $count = $totleRows['count'];
    $tPrice = $totleRows['tPrice'];
    $oPrice = $totleRows['oPrice'];

    $lastYear=date("Y")-1;


    //有订单的在库
    $oInStoreSql = "SELECT SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty*D.Price*C.Rate,X.OrderQty*D.Price*C.Rate)) AS dAmount,SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty,X.OrderQty)) AS dQty
                    FROM (
                        SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty FROM(
                                    SELECT K.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty   
                                                FROM $DataIn.ck9_stocksheet K
                                                LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
                                                LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
                                                LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
                                                LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0  AND  G.ywOrderDTime>'$lastYear-01-01' 
                                                WHERE  K.tStockQty>0  AND T.mainType<2 AND D.SendFloor = $Floor Group by K.StuffId  
                                   UNION ALL 
                                                SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty  
                                                FROM $DataIn.ck9_stocksheet K
                                                LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
                                                LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
                                                LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
                                                LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0   AND  G.ywOrderDTime>'$lastYear-01-01'  
                                                LEFT JOIN $DataIn.ck5_llsheet R ON R.StockId=G.StockId 
                                                WHERE  K.tStockQty>0  AND T.mainType<2 AND D.SendFloor = $Floor Group by K.StuffId)A GROUP BY A.StuffId 
                        )X 
                        LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=X.StuffId  
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
                        LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
                        LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
                        LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency";
    //echo $oInStoreSql;
    $oInStoreResult = mysql_query($oInStoreSql);
    $oInStoreRow = mysql_fetch_assoc($oInStoreResult);
    $oStockQty = $oInStoreRow['dQty'];
    $tRate = intval((($oInStoreRow['dAmount']/$tPrice)*100));

    //在库时间
    // $InStoreTimeSql = "SELECT SUM(IF(DATEDIFF(DATE_FORMAT(NOW(), '%Y-%m-%d'), B.rkDate) < 30, A.Qty*C.Price , 0)) as Qty1, SUM(IF(DATEDIFF(DATE_FORMAT(NOW(), '%Y-%m-%d'), B.rkDate) < 90 and DATEDIFF(DATE_FORMAT(NOW(), '%Y-%m-%d'), B.rkDate) >= 30, A.Qty*C.Price , 0)) as Qty2, SUM(IF(DATEDIFF(DATE_FORMAT(NOW(), '%Y-%m-%d'), B.rkDate) >=90, A.Qty*C.Price , 0)) as Qty3,SUM(A.Qty*C.Price) as Totle
    //                     FROM $DataIn.ck1_rksheet A
    //                     LEFT JOIN $DateIn.ck1_rkmain B ON A.Mid=B.Id
    //                     inner join $DateIn.stuffdata C On C.StuffId = A.StuffId 
    //                     WHERE A.StockId NOT IN (SELECT StockId From $DataIn.ck5_llsheet)
    //                     AND C.Estate = 1
    //                     AND C.SendFloor = $Floor";


    $InStoreTimeSql = "SELECT SUM(A.tStockQty) AS Totle,SUM(A.tStockQty*D.Price*C.Rate) AS Amount,
                        SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now()) <1,A.tStockQty,0)) as Qty1,
                        SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now()) <1 ,A.tStockQty*D.Price*C.Rate,0)) AS Qty1Amount,
                        SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now())>=1 and TIMESTAMPDIFF(MONTH,A.DTime,Now())<=3 ,A.tStockQty,0)) AS Qty2,
                        SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now())>=1 and TIMESTAMPDIFF(MONTH,A.DTime,Now())<=3,A.tStockQty*D.Price*C.Rate,0)) AS Qty2Amount, 
                        SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now())>3,A.tStockQty,0)) AS Qty3,
                        SUM(IF(TIMESTAMPDIFF(MONTH,A.DTime,Now())>3,A.tStockQty*D.Price*C.Rate,0)) AS Qty3Amount 
                      FROM (
                        SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
                        FROM $DataIn.ck9_stocksheet K
                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
                        LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
                        LEFT JOIN $DataIn.bps B ON B.StuffId=K.StuffId   
                        LEFT JOIN $DataIn.cg1_stocksheet S ON S.StuffId=K.StuffId
                        LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
                        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
                        LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber  
                        LEFT JOIN $DataIn.stuffovertime O ON O.StuffId=K.StuffId 
                        WHERE  K.tStockQty>0  AND T.mainType<2  AND D.SendFloor = $Floor GROUP BY K.StuffId 
                    )A 
                    LEFT JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
                    LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId 
                    LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
                    WHERE  DATEDIFF(DATE_FORMAT(NOW(), '%Y-%m-%d'), A.DTime) > 0";
    //echo $InStoreTimeSql;


    $InStoreTimeResult = mysql_query($InStoreTimeSql);
    $InStoreRow = mysql_fetch_assoc($InStoreTimeResult);
    $totleQty = $InStoreRow['Totle'];
    $Qty1 = $InStoreRow['Qty1'];
    $Qty2 = $InStoreRow['Qty2'];
    $Qty3 = $InStoreRow['Qty3'];

    $Amount = $InStoreRow['Amount'];
    $Qty1Amount = $InStoreRow['Qty1Amount'];
    $Qty2Amount = $InStoreRow['Qty2Amount'];
    $Qty3Amount = $InStoreRow['Qty3Amount'];

    $rate1 = number_format(($Qty1Amount/$Amount)*100,1);
    

    $rate2 = number_format(($Qty2Amount/$Amount)*100,1);
    //$rate3= number_format(($Qty3Amount/$Amount)*100,1);
    if($rate2 == 0){
        $rate2Visiable = "visibility:hidden;";
    }

    $rate3 = 100 - ($rate1 + $rate2);
    if($rate3 == 0){
        $rate3Visiable = "visibility:hidden;";
    }

    $monthName = strtolower(date('M'));
    $currentMonth = date('Y-m');
    $today = date('Y-m-d');

    //今日入库
    $todayRkSql = "SELECT SUM(A.Qty) as qty, COUNT(*) as count FROM $DataIn.ck1_rksheet A 
                   LEFT JOIN $DataIn.ck1_rkmain B ON B.Id = A.Mid
                   inner join $DataIn.stuffdata C On C.StuffId = A.StuffId
                   LEFT JOIN $DataIn.stufftype T ON T.TypeId = C.TypeId
                   Where B.Date='$today'
                   AND T.mainType<2
                   AND A.StockId not in (Select distinct mStockId From $DataIn.cg1_stuffcombox)
                   AND C.SendFloor = $Floor";
    $todayRkResult = mysql_query($todayRkSql);
    $todayRkRow = mysql_fetch_assoc($todayRkResult);
    $rkQty = number_format($todayRkRow['qty']);
    $rkCount = number_format($todayRkRow['count']);

    //今日备料
    $todayBlSql = "SELECT SUM(A.Qty) as qty, COUNT(*) as count FROM $DataIn.ck5_llsheet A 
                   inner join $DataIn.stuffdata C On C.StuffId = A.StuffId 
                   LEFT JOIN $DataIn.stufftype T ON T.TypeId = C.TypeId
                   Where Left(A.Date, 12)='$today' 
                   AND C.SendFloor = $Floor 
                   AND T.mainType<2";
    //echo $todayBlSql ;
    $todayBlResult = mysql_query($todayBlSql);
    $todayBlRow = mysql_fetch_assoc($todayBlResult);
    $blQty = number_format($todayBlRow['qty']==""?0:$todayBlRow['qty']);
    $blCount = number_format($todayBlRow['count']);

    //补料单
    $blSql = "SELECT SUM(A.Qty) as qty, COUNT(*) as count, '1' as type FROM $DataIn.ck13_replenish A
              inner join $DataIn.stuffdata C On C.StuffId = A.StuffId
              LEFT JOIN $DataIn.stufftype T ON T.TypeId = C.TypeId
              WHERE A.Estate=1 and A.Lid = 0 
              AND T.mainType<2
              AND C.SendFloor = $Floor
              Union
              SELECT SUM(A.Qty) as qty, COUNT(*) as count, '2' as type FROM $DataIn.ck13_replenish A
              inner join $DataIn.stuffdata C On C.StuffId = A.StuffId
               LEFT JOIN $DataIn.stufftype T ON T.TypeId = C.TypeId
              WHERE A.Estate=0 
              and A.Lid > 0
              AND T.mainType<2
              and Left(A.Date, 7) = '$currentMonth'
              AND C.SendFloor = $Floor";
    $blResult = mysql_query($blSql);
    $blWait = 0;$blWaitCount=0;
    $monthBl = 0;$monthblcount=0;
    while($blRow=mysql_fetch_assoc($blResult)){
        $type=$blRow['type'];
        switch ($type) {
            case '1':{
                $blWait = number_format($blRow['qty']);
                $blWaitCount = number_format($blRow['count']);
            }
            break;
            case '2':{
                $monthBl = number_format($blRow['qty']);
                $monthblcount = number_format($blRow['count']);
            }
            break;
        }
    }

    //备品
    $bpSql = "SELECT SUM(A.Qty) as qty, COUNT(*) as count, '1' as type FROM $DataIn.ck7_bprk A
              inner join $DataIn.stuffdata C On C.StuffId = A.StuffId
              LEFT JOIN $DataIn.stufftype T ON T.TypeId = C.TypeId
              WHERE A.Date = '$today'
              AND C.SendFloor = $Floor
              AND T.mainType<2
              Union
              SELECT SUM(A.Qty) as qty, COUNT(*) as count, '2' as type FROM $DataIn.ck7_bprk A
              inner join $DataIn.stuffdata C On C.StuffId = A.StuffId
              LEFT JOIN $DataIn.stufftype T ON T.TypeId = C.TypeId
              WHERE Left(A.Date, 7) = '$currentMonth'
              AND C.SendFloor = $Floor
              AND T.mainType<2";
    $bpResult = mysql_query($bpSql);
    $bpQty = 0;$bpCount=0;
    $monthbp = 0;$monthbpcount=0;
    while($bpRow=mysql_fetch_assoc($bpResult)){
        $type=$bpRow['type'];
        switch ($type) {
            case '1':{
                $bpQty = number_format($bpRow['qty']);
                $bpCount = number_format($bpRow['count']);
            }
            break;
            case '2':{
                $monthbp = number_format($bpRow['qty']);
                $monthbpcount = number_format($bpRow['count']);
            }
            break;
        }
    }

    //报废
    $bfSql = "SELECT SUM(A.Qty) as qty, COUNT(*) as count, '1' as type FROM $DataIn.ck8_bfsheet A
              inner join $DataIn.stuffdata C On C.StuffId = A.StuffId
              LEFT JOIN $DataIn.stufftype T ON T.TypeId = C.TypeId
              WHERE A.Date = '$today' And A.Estate != 1 AND C.SendFloor = $Floor AND T.mainType<2
              Union
              SELECT SUM(A.Qty) as qty, COUNT(*) as count, '2' as type FROM $DataIn.ck8_bfsheet A
              inner join $DataIn.stuffdata C On C.StuffId = A.StuffId
              LEFT JOIN $DataIn.stufftype T ON T.TypeId = C.TypeId
              WHERE Left(A.Date, 7) = '$currentMonth' And A.Estate != 1 AND C.SendFloor = $Floor AND T.mainType<2";
    //echo $bfSql;
    $bfResult = mysql_query($bfSql);
    $bfQty = 0;$bfCount=0;
    $monthbf = 0;$monthbfcount=0;
    while($bfRow=mysql_fetch_assoc($bfResult)){
        $type=$bfRow['type'];
        switch ($type) {
            case '1':{
                $bfQty = number_format($bfRow['qty']);
                $bfCount = number_format($bfRow['count']);
            }
            break;
            case '2':{
                $monthbf = number_format($bfRow['qty']);
                $monthbfcount = number_format($bfRow['count']);
            }
            break;
        }
    }

?>

<link rel='stylesheet' href='rk_count.css'>
<input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
<input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
<input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
<input type='hidden' id='rate1' name='rate1' value='<?php echo $rate1?>'>
<input type='hidden' id='rate2' name='rate2' value='<?php echo $rate2?>'>
<input type='hidden' id='rate3' name='rate3' value='<?php echo $rate3?>'>
<input type='hidden' id='tRate' name='tRate' value='<?php echo $tRate?>'>
 
<div id='rk_head'>
    <table>
        <tr>
            <td style='width:540px;'><img  src='image/InStore/storeInQty.png'><span><?php echo number_format($tStockQty);?></span><span style='font-size:45px;'>pcs</span></td>
            <td style='width:2px;background-color:#7AB5D7;'></td>
            <td style='width:540px;'><img  src='image/InStore/stuffKinds.png'><span><?php echo number_format($count);?></span><span style='font-size:35px;'></span></td>
        </tr>
    </table>
</div>
<div id='dataHolder'>
    <div style='height:18%;'>
         <div id='qty1Board' class='board'>
            <div style='color:#7ab5d7;'><?php echo $rate1?><span>%</span></div>
            <div style='font-size:40px;margin-top:-10px;'><?php echo "<1个月(".exchangeNumber(number_format($Qty1));?>)</div>
            <div style='width:5px;height:200px;background-color:#7ab5d7;position:absolute;margin-left:60px;'></div>
            <div style='width:133px;height:5px;background-color:#7ab5d7;position:absolute;margin-top:200px;margin-left:60px;'></div>
        </div>
    </div>
    <div id='dataDraw' style='height:50%;position:absolute;'></div>
    <div id='qty2Board' class='board float_left' style='<?php echo $rate2Visiable;?>'>
            <div style='color:#7ab5d7;'><?php echo $rate2?><span>%</span></div>
            <div style='font-size:40px;'>1-3个月(<?php echo exchangeNumber(number_format($Qty2));?>)</div>
            <div style='width:145px;height:5px;background-color:#7ab5d7;position:absolute;margin-top:100px;margin-left:-13px;'></div>
            <div style='width:5px;height:105px;background-color:#7ab5d7;position:absolute;margin-left:130px;margin-top:0px;'></div>
    </div>
    <div style='height:25%;'>
        <div id='qty3Board' class='board float_left' style='<?php echo $rate3Visiable;?>'>
            <div style='color:#fd3131;'><?php echo $rate3?><span>%</span></div>
            <div style='font-size:40px;margin-top:-20px;'>>3个月(<?php echo exchangeNumber(number_format($Qty3));?>)</div>
            <div style='width:5px;height:50px;background-color:#7ab5d7;position:absolute;margin-left:50px;margin-top:-170px;'></div>
        </div>
        <div id='qty4Board' class='board float_left'>
            <div style='color:#4bdd32;'><?php echo $tRate?><span>%</span></div>
            <div style='font-size:40px;margin-top:-20px;'>有单(<?php echo exchangeNumber(number_format($tStockQty-$oStockQty));?>)</div>
        </div>
    </div>
</div>
<div>
    <table id='rk_list' style='border-bottom: 0px  solid #E5F1F7;'>
        <tr>
            <td class='bottomLine' style='width:400px;'><img class='title_img' src='image/InStore/todayRk.png'><span class='table_title'>今日入库</span></td>
            <td class='table_state bottomLine'></td>
            <td class='table_qty bottomLine'>
                <div class='float_left listContLeft'><?php echo $rkQty; ?></div>
                <div class='float_left listContRight'><span><?php echo "($rkCount)"; ?></span></div>
            </td>
        </tr>
         <tr>
            <td class='bottomLine'><img class='title_img' src='image/InStore/todayBl.png'><span class='table_title'>今日备料</span></td>
            <td class='table_state bottomLine'></td>
            <td class='table_qty bottomLine'>
                <div class='float_left listContLeft'><?php echo $blQty; ?></div>
                <div class='float_left listContRight'><span><?php echo "($blCount)"; ?></span></div>
            </td>
        </tr>
         <tr>
            <td class='bottomLine'><img class='title_img' src='image/InStore/bl.png'><span class='table_title' style='color:#fd3131;'>补料单</span></td>
            <td class='table_state bottomLine'>
                <div style=''><img src='image/InStore/waitBl.png'></div>
                <div style='margin-top:-10px;'><img src='image/InStore/bled.png'></div>
            </td>
            <td class='table_qty bottomLine'>
                <div style='padding-bottom:10px;'>
                    <div class='float_left' style='width:350px;text-align:right;'><?php echo $blWait; ?></div>
                    <div class='float_left'><span style='text-align:left;'><?php echo "($blWaitCount)"; ?></span></div>
                </div>
                <div  style='padding-top:10px;'>
                    <div class='float_left' style='width:350px;text-align:right;'><?php echo $monthBl; ?></div>
                    <div class='float_left'><span style='text-align:left;'><?php echo "($monthblcount)"; ?></span></div>
                </div>
            </td>
        </tr>
        <tr>
            <td class='bottomLine'><img class='title_img' src='image/InStore/bp.png'><span class='table_title'>备品</span></td>
            <td class='table_state bottomLine'>
                <div><img src='image/InStore/today.png'></div>
                <div style='margin-top:-10px;'><img src='image/InStore/<?php echo $monthName;?>.png'></div>
            </td>
            <td class='table_qty bottomLine'>
                <div>
                    <div class='float_left' style='width:350px;text-align:right;'><?php echo $bpQty; ?></div>
                    <div class='float_left'><span style='text-align:left;'><?php echo "($bpCount)"; ?></span></div>
                </div>
                <div><div class='float_left' style='width:350px;text-align:right;'><?php echo $monthbp; ?></div>
                    <div class='float_left'><span style='text-align:left;'><?php echo "($monthbpcount)"; ?></span></div>
                </div>
            </td>
        </tr>
         <tr>
            <td class=''><img class='title_img' src='image/InStore/bf.png'><span class='table_title'>报废</span></td>
            <td class='table_state'>
                <div><img src='image/InStore/today.png'></div>
                <div style='margin-top:-10px;'><img src='image/InStore/<?php echo $monthName;?>.png'></div>
            </td>
            <td class='table_qty bottomLine'>
                <div>
                    <div class='float_left' style='width:350px;text-align:right;'><?php echo $bfQty; ?></div>
                    <div class='float_left'><span style='text-align:left;'><?php echo "($bfCount)"; ?></span></div>
                </div>
                <div><div class='float_left' style='width:350px;text-align:right;'><?php echo $monthbf; ?></div>
                    <div class='float_left'><span style='text-align:left;'><?php echo "($monthbfcount)"; ?></span></div>
                </div>
            </td>
        </tr>
    </table>
</div>
<div style='overflow: hidden;width:1080px;'>
    
</div>
<?php
function exchangeNumber($number){
    $numberArray = explode(',', $number);
    $shortName = '';
    switch(count($numberArray)){
        case 2:
            $shortName = 'k';
        break;
        case 3:
            $shortName = 'M';
        break;
    }
    return count($numberArray)==1?$number : $numberArray[0].$shortName;
}
?>
<script>
    function drawCircle(){
        var width = 1080;
        var height = 840;
        var rate1 = document.getElementById('rate1').value;
        var rate2 = document.getElementById('rate2').value;
        var rate3 = document.getElementById('rate3').value;
        var tRate = document.getElementById('tRate').value;
        var oRate = 100 - tRate;
        var dataset = [rate1, rate2, rate3];
        var innerDataset = [tRate, oRate];
        
        var svg = d3.select("#dataDraw")
                    .append("svg")
                    .attr("width", width)
                    .attr("height", height);
        
        var pie = d3.layout.pie();

        var piedata = pie(dataset);
        var innerPiedata = pie(innerDataset);

        piedata.push(innerPiedata[0]);
        piedata.push(innerPiedata[1]);
        
        //console.log(piedata);

        var outerRadius = 340;  //外半径
        var innerRadius = 250;  //内半径，为0则中间没有空白

        var iInnerRadius = 240;
        var iOutterRadius = 190;

        var i=0;
        var arc = d3.svg.arc()  //弧生成器
                    .innerRadius(function(d){
                        return i<3?innerRadius:iInnerRadius;
                    })  //设置内半径
                    .outerRadius(function(d){
                        var oRadius = i<3?outerRadius:iOutterRadius;
                        i++;
                        return oRadius;
                    }); //设置外半径
        
        var color = d3.scale.category5();
        
        var arcs = svg.selectAll("g")
                      .data(piedata)
                      .enter()
                      .append("g")
                      .attr("transform","translate("+ (width/2) +","+ (height/2) +")");
                      
        arcs.append("path")
            .attr("fill",function(d,i){
                return color(i);
            })
            .attr("d",function(d){
                return arc(d);
            });
        // console.log(dataset);
        // console.log(piedata);
    }
 </script> 
