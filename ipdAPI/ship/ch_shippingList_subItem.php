<?php
    include_once "../../basic/parameter.inc";
    include "../../model/subprogram/weightExtra.php";

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

    // $companyId = '100185';
    // $InvoiceNO = 'INNOV8 060';
    // $shipId = '1443';
    $subItems = array();

    $subShippingItemSql = "SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Price,S.Type,S.YandN,P.Weight AS Weight,M.Date,E.Leadtime,P.TestStandard,P.MainWeight,P.Code,N.OrderDate AS OrderDate ,P.ProductId,N.ClientOrder, H.EndPlace, H.StartPlace,O.PackRemark as PackRemark,P.Description,M.Ship,P.Code,IFNULL(Y.printQty, 0) as printQty,L.WG
                          FROM $DataIn.ch2_packinglist L
                          LEFT JOIN $DataIn.ch1_shipsheet S ON S.POrderId = L.POrderId
                          LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=L.Mid
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
    //echo $subShippingItemSql.'<br>';
    $boxQueue = 1;
    $totleBox = 0;
    $subShippingItemResult = mysql_query($subShippingItemSql);
    while($subShippingItem = mysql_fetch_assoc($subShippingItemResult)){

        $subId = $subShippingItem['Id'];
        $POrderId = $subShippingItem['POrderId'];
        $OrderPO = str_replace(' ', '',$subShippingItem['OrderPO']);
        $cName = $subShippingItem["cName"];
        $eCode = $subShippingItem['eCode'];
        $Price = $subShippingItem['Price'];
        $Type = $subShippingItem['Type'];
        $Weight = $subShippingItem['Weight'];
        $ProductId = $subShippingItem['ProductId'];
        $EndPlace = $subShippingItem['EndPlace'];
        $Date = $subShippingItem['Date'];
        $labelCode = $subShippingItem['Code'];
        $originallabelCode = $labelCode;
        $Description = $subShippingItem['Description'];
        $tempDescrip = json_encode($Description);
        $tempDescrip = str_replace('\ufffc', '', $tempDescrip);
        $Description = json_decode($tempDescrip);
        $printQty = $subShippingItem['printQty'];
        $chWG = $subShippingItem['WG'];
        $shipType = $subShippingItem['Ship'];
        //根据Bom表读取外箱标签、内箱标签
        $outboxCode = '';
        $inboxCode = '';
        $tradeCode = '';
        $codeResult = mysql_query("SELECT D.StuffId, D.StuffCname
                                  FROM $DataIn.cg1_stocksheet P 
                                  INNER JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId  
                                  INNER JOIN $DataIn.stufftype T On T.TypeId = D.TypeId
                                  WHERE P.POrderId = '$POrderId'
                                  AND D.TypeId in (9124, 9033)",$link_id);

        // echo "SELECT D.StuffId, D.StuffCname
        //                           FROM $DataIn.cg1_stocksheet P
        //                           INNER JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId
        //                           INNER JOIN $DataIn.stufftype T On T.TypeId = D.TypeId
        //                           WHERE P.POrderId = '$POrderId'
        //                           AND D.TypeId in (9124, 9033)<br>";
        switch(mysql_num_rows($codeResult)){
            case 1: //只有一个的默认为外箱条码
                $codeRow = mysql_fetch_assoc($codeResult);
                $stuffCname = explode('-', $codeRow['StuffCname']);
                $outboxCode = $stuffCname[count($stuffCname)-1];
            break;
            default :
                while($codeRow = mysql_fetch_assoc($codeResult)){
                    if(strpos($codeRow['StuffCname'], '(ITF)')){
                        continue;
                    }
                    //echo $codeRow['StuffCname'].'<br>';
                    $stuffCname = explode('-', $codeRow['StuffCname']);
                    if(strpos($codeRow['StuffCname'], '外箱')){
                        $outboxCode = $stuffCname[count($stuffCname)-1];
                    }else if(strpos($codeRow['StuffCname'], '内箱')){
                        $inboxCode = $stuffCname[count($stuffCname)-1];
                    }else if(strpos($codeRow['StuffCname'], '批次标') !== false){
                       $tradeCode = $stuffCname[count($stuffCname)-1];
                    }
                }
            break;
        }

        $outboxCode = preg_replace("/[^\s\d]/", "", $outboxCode);
        $outboxCode = str_replace("\t", "", $outboxCode);
        $inboxCode = preg_replace("/[^\s\d]/", "", $inboxCode);
        //echo $POrderId.' '.$outboxCode.'<br>';
        if($outboxCode == ''){
            $labelCode = explode('|', $labelCode);
            if(count($labelCode) == 1){
                $outboxCode = $labelCode[0];
            }else{
                for($i=0; $i<count($labelCode); $i++){
                    if((strlen($labelCode[$i])==13 || strlen($labelCode[$i])==14) && intval($labelCode[$i]) > 0){
                        $outboxCode = $labelCode[$i];
                        break;
                    }
                }
            }
        }

        //尺寸和装箱
        $packingSql = mysql_query("Select Id, MAX(BoxPcs) as BoxPcs, MIN(BoxPcs) as MinBoxPcs, BoxSpec, Max(WG) as WG, Min(WG) as minWG, SUM(BoxQty) as BoxQty, SUM(FullQty) as Qty From $DataIn.ch2_packinglist Where POrderId = '$POrderId' and Mid='$shipId' AND BoxRow > 0");

        $packResult = mysql_fetch_assoc($packingSql);
        //$WG = $packResult['WG'];
        $size = str_replace("cm", "", strtolower($packResult['BoxSpec']));
        $BoxPcs = $packResult['BoxPcs']; //装箱数
        $minBoxPcs = $packResult['MinBoxPcs'];
        $Qty = $packResult['Qty'];

        if(intval($BoxPcs) < intval($minBoxPcs)){
            $tmpPCS = $minBoxPcs;
            $minBoxPcs = $BoxPcs;
            $BoxPcs = $tmpPCS;
        }

        $BoxQty = $packResult['BoxQty']; //总箱数
        $totleBox += $BoxQty;
        $WG = round($Weight*$BoxPcs/1000,2);//整单重量
        $minWG = round($Weight*$minBoxPcs/1000, 2);
        $minWeight = round(($Weight*$minBoxPcs+extraWeigth($ProductId))/1000, 2);

        $hasLast = $Qty%$BoxPcs;
        //先替换默认的项目
        //研砼HK有转发规则
        $targetCompanyId = $companyId;
        $specalAdd = "";
        $targetStr = 'iP6S液硅胶壳';
        if($companyId == '100426'){
            $ToOutNameResult = mysql_query("SELECT C.CompanyId
                    FROM $DataIn.yw7_clientOutData O
                    LEFT JOIN  $DataIn.yw1_ordersheet S  ON O.POrderId=S.POrderId
                    LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
                    LEFT JOIN $DataIn.trade_object C ON C.Forshort=D.ToOutName
                    WHERE  O.POrderId=$POrderId",$link_id);

            $toOutNameRow = mysql_fetch_assoc($ToOutNameResult);
            $targetCompanyId = $toOutNameRow['CompanyId'];
        }else if($companyId == '1094' && strpos($cName,  $targetStr) !== false){
           $specalAdd = '+qr';
        }

        $labelInfomation = array('eCode' => $eCode,
                                 'EndPlace' => $EndPlace,
                                 'Date' => $Date,
                                 'InvoiceNO' => $InvoiceNO,
                                 'OrderPO' => $OrderPO,
                                 'Code' => $outboxCode,
                                 'boxSize' => $size.' cm',
                                 'inboxCode' => $inboxCode,
                                 'POrderId' => $POrderId,
                                 'ProductId' => $ProductId,
                                 'description' => "$Description",
                                 'shipId' => $shipId,
                                 'printType'=>'ship',
                                 'shipTYpe' => "$shipType",
                                 'systemCode'=>$originallabelCode,
                                 'companyIdStr' => $targetCompanyId,
                                 'tradeCode' => $tradeCode);

        //再根据相应的特殊要求替换



        $labelType = LabelFactory::CreateLabel($targetCompanyId, $size.$specalAdd, $DataPublic);
        $labelFormatter = $labelType['formatter'];

        foreach($labelInfomation as $key=>$value){
            if(strpos($labelFormatter, '*'.$key)){
                $labelFormatter = str_replace('*'.$key, $value , $labelFormatter);
            }
        }
        $labelClass = $labelType['class'];
        if($labelClass !== ''){
          $companyLabel = new $labelClass;
          $label = $companyLabel->getLabel($labelFormatter, $labelInfomation, $DataIn, $DataPublic);
        }else{
          $label = $labelFormatter;
        }

        //获取成品库位
        $baseArray = array();
        $baseContent = "";
        $baseSsql = "SELECT B.Region,B.Location From yw1_orderrk A
                    Left Join ck_location B On A.LocationId = B.Id
                    Where A.POrderId = $POrderId
                    AND A.LocationId != 0";
        $baseResult = mysql_query($baseSsql);
        while($baseRow = mysql_fetch_assoc($baseResult)){
          $Region = $baseRow['Region'];
          $location = $baseRow['Location'];
          $baseName = $Region.$location;
          $baseArray[] = $baseName;
        }
        $baseContent = implode("|", $baseArray);


        //取出生产时每箱的重量
        $weightArray = array();
        $avgWeight = 0;
        // $boxWeightSql = "SELECT boxId, Weight FROM $DataIn.sc1_cjtj WHERE POrderId = $POrderId AND Weight > 0 AND Weight <100
        //                  UNION
        //                  SELECT 'avg' as boxId, AVG(Weight) as Weight FROM $DataIn.sc1_cjtj WHERE POrderId = $POrderId AND Weight > 0 AND Weight <100";
        $boxWeightSql = "SELECT A.boxId, A.Weight FROM $DataIn.sc1_cjtj A
                         LEFT JOIN $DataIn.yw1_ordersheet B On B.POrderId = A.POrderId
                         Left Join $DataIn.yw1_ordermain C On C.OrderNumber = B.OrderNumber
                         WHERE B.ProductId = $ProductId
                         AND A.Weight > 0 
                         AND A.Weight <100
                         AND Left(C.OrderDate, 7) >= date_format(date_sub(curdate(), interval 2 month), '%Y-%m')
                         AND B.Estate=0
                         UNION 
                         SELECT 'avg' as boxId, AVG(A.Weight) as Weight FROM $DataIn.sc1_cjtj A
                         LEFT JOIN $DataIn.yw1_ordersheet B On B.POrderId = A.POrderId
                         Left Join $DataIn.yw1_ordermain C On C.OrderNumber = B.OrderNumber
                         WHERE B.ProductId = $ProductId
                         AND A.Weight > 0 
                         AND A.Weight <100
                         AND Left(C.OrderDate, 7) >= date_format(date_sub(curdate(), interval 2 month), '%Y-%m')
                         AND A.Weight != (SELECT Min(A.Weight) FROM $DataIn.sc1_cjtj A LEFT JOIN $DataIn.yw1_ordersheet B On B.POrderId = A.POrderId WHERE B.ProductId = $ProductId AND A.Weight > 0 AND A.Weight <100)
                         AND B.Estate=0";
        //echo  $boxWeightSql.'<br>';
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
        if($avgWeight == '' or $avgWeight == 0){
          $avgWeight = $chWG;
        }
        $thisBoxQueue = $boxQueue+$BoxQty-1;
        $startPosition = $boxQueue - 1;
        $subItems[] = array('name' => "$cName-$OrderPO",
                            'productId' => "$ProductId",
                            'boxPcs' => "$BoxPcs", //装箱数
                            'boxQty' => "$BoxQty",
                            'netWeight' => number_format("$WG",2,'.','')."",
                            'minWeight' => number_format("$minWG", 2, '.', '')."",
                            'qty' => "$Qty",
                            'POrderId' => $POrderId,
                            'label' => "$label",
                            'avgWeight' => "$avgWeight",
                            'minGW' =>"$minWeight",
                            'weightes' => $weightArray,
                            'code' => "$eCode",
                            'hasLast' => "$hasLast",
                            'startPosition' => "$startPosition",
                            'queue' => $boxQueue.'-'.$thisBoxQueue,
                            'printQty' => "$printQty",
                            'baseContent' => "$baseContent");
        $boxQueue+=$BoxQty;
    }

     foreach($subItems as $key=>$value){
        $newLabel = str_replace('*totleBox', $totleBox, $value['label']);
        $subItems[$key]['label'] = $newLabel;
     }


    echo json_encode($subItems);
?>