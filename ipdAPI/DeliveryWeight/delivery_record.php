<?php

    include "../../basic/parameter.inc";
    include "../../workshop/socket.php";

    $StuffId = $_POST['stuffId'];
    $recordNumber = $_POST['recordNumber'];
    $position = $_POST['position'];
    $operator = $_POST['operator']==''?'999':$_POST['operator'];
    $relation = $_POST['relation']==''?'0':$_POST['relation'];

    // $StuffId = '155436';
    // $recordNumber = '1000';
    // $operator = '11008';
    include 'operateRecord.php';
    
    if(substr($position, 0, 3) == '471'){
        $Floor=17; $Line="A";
    }else{
        $Floor=6; $Line="D";
    }
    
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
            AND H.Id is not null
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
            $thisRecord = $recordNumber;
        }

        $recordNumber -= $thisRecord;
        $date = date('Y-m-d H:i:s');
        $insertSql[] = "(NULL, $Id, $StockId, $StuffId, $thisRecord, $LineId, '', '$date', 1, 0, '$operator', '$targetId')";

        if($recordNumber <= 0){
            break;
        }
    }

    $recordSql = "INSERT INTO $DataIn.qc_cjtj (Id, Sid, StockId, StuffId, Qty, LineId, Remark, Date, Estate, Locks, Operator, recordId)
                      VALUES ".implode(',', $insertSql);
    //echo  $recordSql ;
    if(mysql_query($recordSql) && mysql_affected_rows() > 0){
        $isSuccess = true;
        if($position != ''){
            //display_send_msg($position, 'reload');
        }
    }

    if ($isSuccess && $recordNumber > 0) {
        $bpQty  = $recordNumber;
        $bpRkSql = "INSERT INTO $DataIn.ck7_bprk (Id, StuffId, Qty, Remark, Date, Locks, Estate, Operator, PLocks, creator, created, modifier, modified) VALUES (NULL, '$StuffId', '$bpQty', '品检备品登记转入', '$date ', 1, 1, '$operator', '0', '$operator', '$date', '$operator', '$date')";
        //$bpStockSql = "UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$bpQty, oStockQty=oStockQty+$bpQty WHere StuffId = $StuffId";

        mysql_query($bpRkSql);
        //mysql_query($bpStockSql);
    }

    $result = $isSuccess?array('result'=>'Y', 'message'=>'登记成功', 'bp'=>"$bpQty", 'insertId'=>"$targetId"):array('result'=>'N', 'message'=>'登记失败', 'bp'=>"0");
    echo json_encode($result);

?>