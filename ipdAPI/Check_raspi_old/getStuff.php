<?php
    include_once "../../basic/parameter.inc";

    $stockId = $_POST['stockId'];
    //$stockId = '20150504160701';

    $getStuffInfomationSql = "SELECT stuff.StuffId, stuff.StuffCName, stuff.FrameCapacity, stuff.Date, staff.Name, company.Forshort,mission.DateTime
                              FROM $DataIn.cg1_stocksheet AS stock 
                              INNER JOIN $DataIn.stuffdata AS stuff ON stock.StuffId = stuff.StuffId
                              INNER JOIN $DataIn.bps AS b ON b.StuffId = stuff.StuffId
                              INNER JOIN $DataPublic.Staffmain AS staff ON staff.Number = b.BuyerId
                              Left JOIN $DataIn.gys_shsheet AS sh ON sh.StockId = stock.StockId
                              Left JOIN $DataIn.qc_mission AS mission ON mission.SId = sh.Id
                              INNER JOIN $DataIn.trade_object AS company ON company.CompanyId = b.CompanyId
                              Where stock.StockId = $stockId";

    //echo $getStuffInfomationSql;

    $stuffResult = mysql_query($getStuffInfomationSql);
    $stuffRow = mysql_fetch_assoc($stuffResult);

    $stuffname = $stuffRow['StuffCName'];
    $buyer = $stuffRow['Name'];
    $supplier = $stuffRow['Forshort'];
    $FrameCapacity = $stuffRow['Forshort']==0?'未填写':$stuffRow['Forshort'].' PCS';
    $uploadtime = $stuffRow['Date'];
    $stuffId = $stuffRow['StuffId'];
    $datetime = $stuffRow['DateTime'];

    //计算时间差
    $currentTime =date("Y-m-d H:i:s");
    $timeInterval = strtotime($currentTime) - strtotime($datetime);
    if($datetime == ''){
        $timelap = '无记录';
    }
    else if($timeInterval < 60){
        $timelap = $timeInterval+'秒前';
    }else if($timeInterval < 3600){
        $timelap = $timeInterval / 60;
        $timelap = $timelap+'分钟前';
    }else if($timeInterval < 3600 * 24){
        $timelap = $timeInterval / 3600;
        $timelap = $timelap+'小时前';
    }else{
        $timelap = $timeInterval / (3600*24);
        $timelap = $timelap+'天前';
    }


    $getStoreInQtySql = "SELECT SUM(Qty) as Qty, Count(*) as Count FROM $DataIn.ck1_rksheet Where StuffId = '$stuffId'";
    $getStoreInResult = mysql_query($getStoreInQtySql);
    $getStoreInRow = mysql_fetch_assoc($getStoreInResult);

    $qty = number_format($getStoreInRow['Qty']);
    $count = $getStoreInRow['Count'];
    $storeIn = "$qty($count)";

    $result = array('name'=>"$stuffname", 'stuffId'=>"$stuffId", 'supplier'=>"$supplier", 'buyer'=>"$buyer", 'uploadtime'=>$uploadtime, 'storeIn'=>$storeIn, 'frameCapacity'=>"$FrameCapacity", 'timelap'=>"$timelap");

    echo json_encode($result);

?>