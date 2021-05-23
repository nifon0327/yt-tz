<?php
    include_once "../../basic/parameter.inc";

    $shippingMonthSql = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows AND M.Shiptype = '' AND M.Estate=0 GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
    //echo "SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows AND M.Shiptype = '' GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC";
    $monthes = array();
    while($shipMonthesResult = mysql_fetch_assoc($shippingMonthSql)){
        $monthes[] = date("Y-m",strtotime($shipMonthesResult['Date']));
    }

    echo json_encode($monthes);
