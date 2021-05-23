<?php

     include_once "../../basic/parameter.inc";
     $month = $_POST['month'];
     //$month = '2015-06';
     $firstDate = $month.'-01';
     $lastDate = date('Y-m-t', strtotime($month));

     //shipping list main title data
     $shippinglist = array();

     $shippinglistSql="SELECT M.Id,M.CompanyId,M.DeliveryNumber,M.DeliveryDate,M.Estate,M.Remark,M.ShipType,M.Operator,C.Forshort
            FROM $DataIn.ch1_deliverymain M
            INNER JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
            WHERE  (M.DeliveryDate BETWEEN '$firstDate' AND '$lastDate')
            ORDER BY M.DeliveryDate DESC";
    //echo $shippinglistSql;
    
    $shippingResult = mysql_query($shippinglistSql);
    while($shipRow = mysql_fetch_assoc($shippingResult)){
        $companyId=$shipRow['CompanyId'];
        $shippingNumber=$shipRow['Number'];
        $invoiceNo = $shipRow['DeliveryNumber'];
        $date = $shipRow['DeliveryDate'];
        $remark = $shipRow['Remark'];
        $Operator = $shipRow['Operator'];
        include "../../model/subprogram/staffname.php";
        $companyName = $shipRow['Forshort'];
        $payType = $shipRow['PayType'];
        $id = $shipRow['Id'];
        $sign = $shipRow['Sign'];
        $checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty,SUM(DeliveryQty*Price) AS Amount  FROM $DataIn.ch1_deliverysheet WHERE Mid='$id'",$link_id));
        $Amount=$checkAmount["Amount"];
        $Amount=sprintf("%.2f",$Amount);

        $packingSql = mysql_query("Select SUM(BoxQty) AS BoxQty From $DataIn.ch2_packinglist Where Mid='$id' Group by POrderId");
        $packResult = mysql_fetch_assoc($packingSql);
        $boxQty = $packResult['BoxQty'];

        $shippinglist[] = array('companyid'=>"$companyId", 'shippingnumber'=> "$shippingNumber", 'invoiceno'=>"$invoiceNo", 'shipcompany'=>"$shipCompany", 'date'=>"$date", 'remark'=>"$remark", 'shiptype'=>"$shiptype", 'shipway'=>"$shipWay", 'operator'=>"$Operator", 'companyName'=>"$companyName", 'paytype'=>"$payType", 'id'=>"$id", 'price' =>"$Amount", 'boxes'=>"$boxQty");
    }

    echo json_encode($shippinglist);
?>