<?php
    include_once "../../basic/parameter.inc";

    $shippingMonthSql = mysql_query("SELECT M.DeliveryDate FROM $DataIn.ch1_deliverymain M  GROUP BY DATE_FORMAT(M.DeliveryDate,'%Y-%m') ORDER BY M.DeliveryDate DESC",$link_id);
    
    $monthes = array();
    while($shipMonthesResult = mysql_fetch_assoc($shippingMonthSql)){
        $monthes[] = date("Y-m",strtotime($shipMonthesResult['DeliveryDate']));
    }

    echo json_encode($monthes);
