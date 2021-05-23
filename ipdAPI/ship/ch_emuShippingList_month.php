<?php
    include_once "../../basic/parameter.inc";
    $shippingMonthSql = mysql_query("SELECT M.Date FROM $DataIn.ch0_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
    $monthes = array();
    while($shipMonthesResult = mysql_fetch_assoc($shippingMonthSql)){
        $monthes[] = date("Y-m",strtotime($shipMonthesResult['Date']));
    }

    echo json_encode($monthes);
?>