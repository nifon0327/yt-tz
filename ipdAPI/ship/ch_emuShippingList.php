<?php

    include_once "../../basic/parameter.inc";
    $month = $_POST['month'];
    //$month = '2014-12';
    $firstDate = $month.'-01';
    $lastDate = date('Y-m-t', strtotime($month));

    //shipping list main title data
    $shippinglist = array();
    $shippinglistSql="SELECT 
M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.CompanyId,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Operator,C.Forshort,C.PayType,S.InvoiceModel,B.Name
            FROM $DataIn.ch0_shipmain M
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
            LEFT JOIN $DataIn.ch8_shipmodel S ON S.Id=M.ModelId 
            Left Join $DataPublic.staffmain B On B.Number = M.Operator
            WHERE M.Estate='1'
            and ((M.Date>'$firstDate' and M.Date<'$lastDate') OR M.Date='$firstDate' OR M.Date='$lastDate')
            ORDER BY M.Date DESC";

    //echo $shippinglistSql.'<br>';
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