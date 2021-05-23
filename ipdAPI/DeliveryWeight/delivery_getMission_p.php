<?php
    
    include "../../basic/parameter.inc";

    $Floor=6; $Line="D";
    $LineResult=mysql_fetch_array(mysql_query("SELECT C.Id  FROM  $DataIn.qc_scline C  WHERE  C.LineNo='$Line'  AND C.Floor='$Floor' LIMIT 1",$link_id));
    $LineId=$LineResult["Id"]==""?1:$LineResult["Id"];

    $curDate=date("Y-m-d");
    $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS ThisWeek",$link_id));
    $thisWeek = substr($dateResult["ThisWeek"],4,2);

    $myResultSql="SELECT  S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,G.DeliveryDate,S.Qty,S.SendSign,G.POrderId, 
             M.CompanyId,P.Forshort, P.CompanyId,D.StuffCname,D.Picture,D.TypeId,YEARWEEK(G.DeliveryDate,1) AS Weeks,H.DateTime,
           Max(IFNULL(C.Date,Now())) AS QcDate,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks,H.Estate,Max(T.shDate) AS shDate,D.FrameCapacity
            FROM $DataIn.gys_shsheet S 
            LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
            LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
            LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id 
            LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0
            LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId 
            LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id   
            WHERE  S.Estate=2  AND M.Floor='$Floor'  AND S.SendSign IN(0,1)  AND  H.LineId='$LineId' GROUP BY S.Id ORDER BY Estate,QcDate,shDate,H.DateTime,Weeks,ReduceWeeks";

    // $myResultSql="SELECT  S.Id,S.Mid,S.StuffId,S.StockId,SUM((G.AddQty+G.FactualQty)) AS cgQty,G.DeliveryDate,SUM(S.Qty) as Qty,S.SendSign,G.POrderId, 
    //          M.CompanyId,P.Forshort, P.CompanyId,D.StuffCname,D.Picture,D.TypeId,YEARWEEK(G.DeliveryDate,1) AS Weeks,H.DateTime,
    //        Max(IFNULL(C.Date,Now())) AS QcDate,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks,H.Estate,Max(T.shDate) AS shDate,D.FrameCapacity,(S.Qty - IFNULL(SUM(C.Qty), 0)) as restQty
    //         FROM $DataIn.gys_shsheet S 
    //         LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
    //         LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
    //         LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
    //         LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
    //         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
    //         LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id 
    //         LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0
    //         LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId 
    //         LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id   
    //         WHERE  S.Estate=2  
    //         AND M.Floor='6'  
    //         AND S.SendSign IN(0,1)  
    //         AND H.LineId='4' 
    //         GROUP BY S.StuffId 
    //         HAVING restQty > 0
    //         ORDER BY Estate,QcDate,shDate,H.DateTime,Weeks,ReduceWeeks";
    
    //echo $myResultSql;
    $missionList = array();
    $myResult = mysql_query($myResultSql);
    while($myRow = mysql_fetch_assoc($myResult)){
        $StuffId = $myRow['StuffId'];
        $Id = $myRow['Id'];
        $stuffName = $myRow['StuffCname'];
        $shQty = $myRow['Qty'];
        $FrameCapacity = $myRow['FrameCapacity'];
        $Forshort = $myRow['Forshort'];
        $CompanyId = $myRow['CompanyId'];

        $djResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty  FROM $DataIn.qc_cjtj WHERE Sid='$Id' AND StuffId='$StuffId' ",$link_id));
        $DjQty=$djResult["Qty"];

        //获取配件属性
        $propertis =  array();
        $stuffPropertySql = "SELECT Left(B.TypeName, 1) as TypeName From $DataIn.stuffproperty as A 
                            LEFT JOIN $DataIn.stuffpropertytype as B On A.Property = B.Id
                            Where A.StuffId = '$StuffId'";
        $stuffPropertyResult = mysql_query($stuffPropertySql);
        while($propertyRows = mysql_fetch_assoc($stuffPropertyResult)){
            $property =  $propertyRows['TypeName'];
            $propertis[] = $property;
        }

        //获取当前在检配件
        $states = array();
        $getCheckStateSql = "SELECT * FROM $DataIn.qc_currentcheck order by Id";
        $checkStateResult = mysql_query($getCheckStateSql);
        while($stateRow = mysql_fetch_assoc($checkStateResult)){
            $states[] = ($StuffId == $stateRow['stuffId'])?'1':'0';
        }

        if($shQty == $DjQty){
            continue;
        }
        $missionList[] = array('StuffId'=>"$StuffId", 'shId'=>"$Id", 'StuffName'=>"$stuffName", 'shQty'=>"$shQty", 'recordQty'=>"$DjQty", 'frameCapacity'=>"$FrameCapacity", 'forShort'=>"$Forshort", 'companyId'=>"$CompanyId", 'week'=>"$thisWeek", 'property'=>$propertis, 'positions'=>$states);
    }

    echo json_encode($missionList);

?>