<?php
    include_once "../../basic/parameter.inc";

    $stuffId = $_POST['stuffId'];
    if($stuffId == ''){
        $stuffId = $_GET['stuffId'];
    }
    //$stuffId = '162772';

    $myResultSql="SELECT Sum(A.Qty) as Qty, SUM(A.recordQty) as recordQty, A.QcDate, A.shDate, A.FrameCapacity, A.CompanyId, A.Forshort, A.StuffCname, A.Name, A.uploadtime From
            (SELECT  S.StuffId,G.DeliveryDate,S.Qty,IFNULL(SUM(C.Qty), 0) as recordQty, 
            M.CompanyId,IFNULL(K.Name,P.Forshort) AS Forshort,D.StuffCname,D.Picture,D.TypeId,D.Date as uploadtime,
            Min(IFNULL(C.Date,Now())) AS QcDate,Max(T.shDate) AS shDate,D.FrameCapacity, staff.Name
            FROM $DataIn.gys_shsheet S 
            LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
            LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
            LEFT JOIN $DataIn.yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	        LEFT JOIN $DataIn.workshopdata K ON K.Id=A.WorkShopId
            LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0
            LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId 
            LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id   
            LEFT JOIN $DataIn.bps AS b ON b.StuffId = D.StuffId
            LEFT JOIN $DataPublic.Staffmain AS staff ON staff.Number = b.BuyerId
            WHERE S.StuffId = '$stuffId' 
            And  S.Estate=2  
            AND S.SendSign IN(0,1)  
            GROUP BY S.Id) as A";

    $myResult = mysql_query($myResultSql);
    $stuffRow = mysql_fetch_assoc($myResult);
    $stuffname = $stuffRow['StuffCname'];
    $buyer = $stuffRow['Name'];
    $supplier = $stuffRow['Forshort'];
    $FrameCapacity = $stuffRow['FrameCapacity']==0?'未设置':$stuffRow['FrameCapacity'].' PCS';
    $uploadtime = $stuffRow['uploadtime'];
    //$stuffId = $stuffRow['StuffId'];
    $datetime = $stuffRow['QcDate'];
    $shQty = number_format($stuffRow['Qty']);
    $recordQty = number_format($stuffRow['recordQty']);

    //计算时间差
    $currentTime =date("Y-m-d H:i:s");
    // $timeInterval = strtotime($currentTime) - strtotime($datetime);
    // if($datetime == ''){
    //     $timelap = '无记录';
    // }
    // else if($timeInterval < 60){
    //     $timelap = $timeInterval.'秒前';
    // }else if($timeInterval < 3600){
    //     $timelap = intval($timeInterval / 60);
    //     $timelap = $timelap.'分钟前';
    // }else if($timeInterval < 3600 * 24){
    //     $timelap = intval($timeInterval / 3600);
    //     $timelap = $timelap.'小时前';
    // }else{
    //     $timelap = intval($timeInterval / (3600*24));
    //     $timelap = $timelap.'天前';
    // }

    $getStoreInQtySql = "SELECT SUM(Qty) as Qty, Count(*) as Count FROM $DataIn.ck1_rksheet Where StuffId = '$stuffId'";
    $getStoreInResult = mysql_query($getStoreInQtySql);
    $getStoreInRow = mysql_fetch_assoc($getStoreInResult);

    $qty = number_format($getStoreInRow['Qty']);
    $count = $getStoreInRow['Count'];
    $storeIn = "$qty($count)";

    #获取工位状态
    $states = array();
    $getCheckStateSql = "SELECT * FROM $DataIn.qc_currentcheck order by Id";
    $checkStateResult = mysql_query($getCheckStateSql);
    while($stateRow = mysql_fetch_assoc($checkStateResult)){
        $tempStuffId = $stateRow['stuffId'];
        $states[] = ($tempStuffId == $stuffId)?'1':'0';
    }

    //获取属性
    $propertis =  array();
    $stuffPropertySql = "SELECT Property From $DataIn.stuffproperty as A 
                         Where A.StuffId = '$stuffId' AND Property in (1,2,10)";
    $stuffPropertyResult = mysql_query($stuffPropertySql);
    while($propertyRows = mysql_fetch_assoc($stuffPropertyResult)){
        $property =  $propertyRows['Property'];
        $propertis[] = $property;
    }

    $result = array('name'=>"$stuffname", 'stuffId'=>"$stuffId", 'supplier'=>"$supplier", 'buyer'=>"$buyer", 'uploadtime'=>"$uploadtime", 'storeIn'=>$storeIn, 'frameCapacity'=>"$FrameCapacity", 'timelap'=>"$timelap", 'shQty'=>"$shQty", 'recordQty'=>"$recordQty", 'state'=>$states, 'property'=>$propertis);

    echo json_encode($result);

?>