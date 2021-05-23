<?php
    include_once "../../basic/parameter.inc";
    $statisticAccept = "SELECT * FROM $DataIn.qc_scline A WHERE A.Estate = 1 AND A.Id != 4";
    $statisticAcceptResult = mysql_query($statisticAccept);

    $result = array();
    while($rows = mysql_fetch_assoc($statisticAcceptResult)){
        $lineId = $rows['Id'];
        $lineName = $rows['Name'];

        $countSql = "SELECT  B.Qty as shQty, A.Sid, C.Date,  A.LineId
                    From $DataIn.qc_mission A
                    LEFT JOIN $DataIn.gys_shsheet B On A.Sid = B.Id
                    LEFT JOIN (SELECT max(Date) as Date, Sid From $DataIn.qc_cjtj Group by Sid) C On C.Sid = A.Sid
                    WHERE ((A.Estate = 0) OR (C.Date <= DATE_SUB(NOW(), INTERVAL 30 MINUTE))) and B.Estate = 2
                    AND A.LineId = $lineId
                    Group by A.Sid
                    Order by C.Date";
        $countResult = mysql_query($countSql);
        $count = mysql_num_rows($countResult);

        $result[] = array('lineId'=>"$lineId", 'count'=>"$count", 'lineName'=>$lineName);
    }

    echo json_encode($result);
?>