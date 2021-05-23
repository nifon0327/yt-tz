<?php

    include "../../basic/parameter.inc";

    $StuffId = $_POST['stuffId'];
    $StuffId = '155436';
    $recordNumber = $_POST['recordNumber'];
    $operator = $_POST['operator']==''?'999':$_POST['operator'];
    $recordNumber = '580';
    $Floor=6; $Line="D";
    $LineResult=mysql_fetch_array(mysql_query("SELECT C.Id  FROM  $DataIn.qc_scline C  WHERE  C.LineNo='$Line'  AND C.Floor='$Floor' LIMIT 1",$link_id));
    $LineId=$LineResult["Id"]==""?1:$LineResult["Id"];

    $isSuccess = false;
    $myResultSql="SELECT  S.Id,S.Mid,S.StuffId,S.StockId,G.DeliveryDate,S.Qty,S.SendSign,G.POrderId,
             YEARWEEK(G.DeliveryDate,1) AS Weeks,H.DateTime,
           Max(IFNULL(C.Date,Now())) AS QcDate,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks,H.Estate, (S.Qty - IFNULL(SUM(C.Qty), 0)) as restQty
            FROM $DataIn.gys_shsheet S 
            LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
            LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id 
            LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0
            LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId   
            WHERE S.Estate = 2   
            AND M.Floor='$Floor'  
            AND S.SendSign IN(0,1)  
            AND H.LineId='$LineId' 
            AND S.StuffId = $StuffId
            GROUP BY S.Id
            HAVING restQty > 0
            ORDER BY Estate,QcDate,H.DateTime,Weeks,ReduceWeeks";
    $myResult = mysql_query($myResultSql);
    $insertSql = array();
    $bpQty = 0;
    while($myRow = mysql_fetch_assoc($myResult)){

        $Id = $myRow['Id'];
        $shQty = $myRow['Qty'];
        $StockId = $myRow['StockId'];

        $thisRecord = $myRow['restQty'];
        if($recordNumber - $thisRecord < 0){
            //$bpQty = $thisRecord-$recordNumber;
            $thisRecord = $recordNumber;
        }

        $recordNumber -= $thisRecord;
        $date = date('Y-m-d H:i:s');
        $insertSql[] = "(NULL, $Id, $StockId, $StuffId, $thisRecord, $LineId, '', '$date', 1, 0, '$operator')";
        if($recordNumber <= 0){
            break;
        }
    }
    
    $recordSql = "INSERT INTO $DataIn.qc_cjtj (Id, Sid, StockId, StuffId, Qty, LineId, Remark, Date, Estate, Locks, Operator)
                      VALUES ".implode(',', $insertSql);
    echo $recordSql.'<br>';
    echo $recordNumber.'<br>';
    if ($recordNumber > 0) {
        $bpQty  = $recordNumber;
        $bpRkSql = "INSERT INTO $DataIn.ck7_bprk (Id, StuffId, Qty, Remark, Date, Locks, Estate, Operator, PLocks, creator, created, modifier, modified) VALUES (NULL, '$StuffId', '$bpQty', '品检备品登记转入', '$date ', 1, 0, '$operator', '0', '$operator', '$date', '$operator', '$date')";
        echo $bpRkSql;
    }

    // if(mysql_query($recordSql) && mysql_affected_rows() > 0){
    //     $isSuccess = true;
    // }


    $result = $isSuccess?array('result'=>'Y', 'message'=>'登记成功'):array('result'=>'N', 'message'=>'登记失败');
    echo json_encode($result);

?>