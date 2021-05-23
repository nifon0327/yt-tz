<?php
    include_once "../../basic/parameter.inc";
    $includeList = scandir('shipClass');
    foreach ($includeList as $key => $value) {
        $extendName = substr(strrchr($value,'.'),1);
        if(strtolower($extendName) === 'php'){
            include_once 'shipClass/'.$value;
        }
    }

    $shipId = $_POST['shipId'];
    $companyId = $_POST['companyId'];
    $InvoiceNO = $_POST['invoiceNo'];
    $module = $_POST['module'];

    // $companyId = '1004';
    // $InvoiceNO = 'Cellular2652';
    // $shipId = '11669';
    // $module = 'ship';

    $sheetBase = 'ch1_shipsheet';
    $mainBase = 'ch1_shipmain';
    $packBase = 'ch2_packinglist';

    if($module === 'emu'){
        $sheetBase = 'ch0_shipsheet';
        $mainBase = 'ch0_shipmain';
        $packBase = 'ch0_packinglist';
    }

    $shipItem = array();
    $subShippingItemSql = "SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Price,S.Type,S.YandN,P.Weight AS Weight,M.Date,E.Leadtime,P.TestStandard,P.MainWeight,P.Code,N.OrderDate AS OrderDate ,P.ProductId,N.ClientOrder, H.EndPlace, H.StartPlace,O.PackRemark as PackRemark,P.Description,P.Code,IFNULL(Y.printQty, 0) as printQty
                          FROM $DataIn.$packBase L
                          LEFT JOIN $DataIn.$sheetBase S ON S.POrderId = L.POrderId
                          LEFT JOIN $DataIn.$mainBase M ON M.Id=S.Mid
                          LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId
                          LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
                          LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=O.Id
                          LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId
                          LEFT JOIN $DataIn.ch8_shipmodel H On H.Id = M.ModelId
                          LEFT JOIN $DataIn.printsync Y ON Y.ShipId = L.Mid AND Y.POrderId = L.POrderId
                          WHERE L.Mid='$shipId'
                          AND S.Type <> 2
                          AND L.BoxRow > 0
                          GROUP BY S.POrderId
                          ORDER BY L.Id";
    //echo $subShippingItemSql;
    $boxQueue = 1;
    $subShipItemResult = mysql_query($subShippingItemSql);
    while($subShipItem = mysql_fetch_assoc($subShipItemResult)){
        $productId = $subShipItem['ProductId'];
        $POrderId = $subShipItem['POrderId'];
        $productName = $subShipItem['eCode'];
        $endPlace = $subShipItem['EndPlace'];
        $date = $subShipItem['Date'];
        $Weight = $subShipItem['Weight'];
        $OrderPO = $subShipItem['OrderPO'];
        $cName = $subShipItem['cName'];
        $printQty = $subShipItem['printQty'];

        //根据Bom表读取外箱标签、内箱标签
        $outboxCode = '';
        $inboxCode = '';
        $codeResult = mysql_query("SELECT D.StuffId, D.StuffCname
                                  FROM $DataIn.cg1_stocksheet P 
                                  INNER JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId  
                                  WHERE P.POrderId = '$POrderId'
                                  AND D.TypeId in (9124, 9033)",$link_id);

        switch(mysql_num_rows($codeResult)){
            case 1: //只有一个的默认为外箱条码
                $codeRow = mysql_fetch_assoc($codeResult);
                $stuffCname = explode('-', $codeRow['StuffCname']);
                $outboxCode = $stuffCname[count($stuffCname)-1];
                $codetitle = $stuffCname[count($stuffCname)-2].'-'.$stuffCname[count($stuffCname)-1];
            break;
            default :
                while($codeRow = mysql_fetch_assoc($codeResult)){
                    $stuffCname = explode('-', $codeRow['StuffCname']);
                    if(strpos($codeRow['StuffCname'], '(外箱)')){
                        $codetitle = $stuffCname[count($stuffCname)-2].'-'.$stuffCname[count($stuffCname)-1];;
                        $outboxCode = $stuffCname[count($stuffCname)-1];
                    }else if(strpos($codeRow['StuffCname'], '(内箱)')){
                        $inboxCode = $codeRow['StuffCname'];
                    }
                }
            break;
        }

        $packingSql = mysql_query("Select Id, MAX(BoxPcs) as BoxPcs, MIN(BoxPcs) as MinBoxPcs, BoxSpec, Max(WG) as WG, Min(WG) as minWG, SUM(BoxQty) as BoxQty, SUM(FullQty) as Qty From $DataIn.$packBase Where POrderId = '$POrderId' and Mid='$shipId' AND BoxRow > 0");
        $packResult = mysql_fetch_assoc($packingSql);

        $qty = $packResult['Qty'];
        $minBoxPcs = $packResult['MinBoxPcs'];
        $BoxPcs = $packResult['BoxPcs'];
        $BoxQty = $packResult['BoxQty']; //总箱数
        $size = str_replace("cm", "", strtolower($packResult['BoxSpec']));

        if(intval($BoxPcs) < intval($minBoxPcs)){
            $tmpPCS = $minBoxPcs;
            $minBoxPcs = $BoxPcs;
            $BoxPcs = $tmpPCS;
        }

        $WG = round($Weight*$BoxPcs/1000,2);//整单重量
        $minWG = round($Weight*$minBoxPcs/1000, 2);

        //取出生产时每箱的重量
        $weightArray = array();
        $avgWeight = 0;
        $boxWeightSql = "SELECT boxId, Weight FROM $DataIn.sc1_cjtj WHERE POrderId = $POrderId AND Weight > 0 AND Weight <100
                         UNION 
                         SELECT 'avg' as boxId, AVG(Weight) as Weight FROM $DataIn.sc1_cjtj WHERE POrderId = $POrderId AND Weight > 0 AND Weight <100";
        $boxWeightResult = mysql_query($boxWeightSql);
        while($boxWeightRow = mysql_fetch_assoc($boxWeightResult)){
            $boxId = $boxWeightRow['boxId'];
            $weight = $boxWeightRow['Weight'];
            if($boxId === 'avg'){
                $avgWeight = $weight;
            }else{
                $weightArray[$boxId] = number_format("$weight",2,'.','');
            }
        }
        $avgWeight = number_format($avgWeight,2,'.','');

        $thisBoxQueue = $boxQueue+$BoxQty-1;
        $startPosition = $boxQueue - 1;
        $tempItem = array('name' => $productName,
                          'productId' => $productId,
                          'POrderId' => $POrderId,
                          'boxPcs' => $BoxPcs,
                          'codeName' => "$codetitle",
                          'code' => "$outboxCode",
                          'queue' => $boxQueue.'-'.$thisBoxQueue,
                          'startPosition' => $startPosition,
                          'boxQty' => "$BoxQty",
                          'qty' => "$qty",
                          'endPlace' => "$endPlace",
                          'date' => "$date",
                          'invoiceNo' => "$invoiceNo",
                          'minBoxPcs' => "$minBoxPcs",
                          'boxPcs' => "$BoxPcs",
                          'avgWeight' => "$avgWeight",
                          'weights' => $weightArray,
                          'netWeight' => number_format("$WG",2,'.','')."",
                          'minWeight' => number_format("$minWG", 2, '.', '')."",
                          'PO' => "$OrderPO",
                          'cName' => "$cName",
                          'boxSize' => $size." CM",
                          'printCount' => "$printQty");

        $labelType = LabelFactory::CreateLabel($companyId, $size, $DataPublic);
        $labelClass = $labelType['class'];
        if($labelClass !== ''){
            $companyLabel = new $labelClass;
            if(method_exists($companyLabel, 'addCharacter')){
                $characters = $companyLabel->addCharacter($tempItem, $DataIn, $DataPublic);
                $tempItem = array_merge_recursive($tempItem, $characters);
            }
        }

        $shipItem[] = $tempItem;
        $boxQueue+=$BoxQty;
    }

    $getLabelFramesql = "SELECT Parameters FROM $DataIn.printformatter WHERE ClientId = $companyId and Estate =2 Limit 1";
    //echo $getLabelFramesql;
    $getLabelFrameResult = mysql_query($getLabelFramesql);
    $getLabelRow = mysql_fetch_assoc($getLabelFrameResult);
    if($getLabelRow){
        $labelFrame = $getLabelRow['Parameters'];
    }else{
        $labelFrame = "NoLabel|DefaultLabel";
    }
    

    echo json_encode(array('label'=>$labelFrame, 'items' =>$shipItem));

?>