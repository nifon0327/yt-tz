<?php

     include_once "../../basic/parameter.inc";
     $month = $_POST['month'];
     //$month = '2015-05';
     $firstDate = $month.'-01';
     $lastDate = date('Y-m-t', strtotime($month));

     //shipping list main title data
     $shippinglist = array();

     $shippinglistSql="SELECT M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.ShipType,M.Ship,M.Operator,T.Type as incomeType,C.Forshort,C.PayType 
            FROM $DataIn.ch1_shipmain M
            LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
            INNER JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
            WHERE M.Estate='0' 
            AND (M.Date BETWEEN '$firstDate' AND '$lastDate')
            AND M.Shiptype = ''
            ORDER BY M.Date DESC";
    //echo $shippinglistSql;
    $shippingResult = mysql_query($shippinglistSql);
    while($shipRow = mysql_fetch_assoc($shippingResult)){
        $companyId=$shipRow['CompanyId'];
        $shippingNumber=$shipRow['Number'];
        $invoiceNo = $shipRow['InvoiceNO'];
        $shipCompany = $shipRow['Wise'];
        $date = $shipRow['Date'];
        $remark = $shipRow['Remark'];
        $shiptype = $shipRow['ShipType'];
        $shipWay = $shipRow['Ship'];
        $Operator = $shipRow['Operator'];
        include "../../model/subprogram/staffname.php";
        $companyName = $shipRow['Forshort'];
        $payType = $shipRow['PayType'];
        $id = $shipRow['Id'];
        $sign = $shipRow['Sign'];

        $checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$id'",$link_id));
        $Amount=$checkAmount["Amount"]*$sign;
        $Amount=sprintf("%.2f",$Amount);

        $packingSql = mysql_query("Select SUM(BoxQty) AS BoxQty From $DataIn.ch2_packinglist Where Mid='$id' Group by POrderId");
        $packResult = mysql_fetch_assoc($packingSql);
        $boxQty = $packResult['BoxQty'];

        $shippinglist[] = array('companyid'=>"$companyId", 'shippingnumber'=> "$shippingNumber", 'invoiceno'=>"$invoiceNo", 'shipcompany'=>"$shipCompany", 'date'=>"$date", 'remark'=>"$remark", 'shiptype'=>"$shiptype", 'shipway'=>"$shipWay", 'operator'=>"$Operator", 'companyName'=>"$companyName", 'paytype'=>"$payType", 'id'=>"$id", 'price' =>"$Amount", 'boxes'=>"$boxQty");
    }

    echo json_encode($shippinglist);
?>