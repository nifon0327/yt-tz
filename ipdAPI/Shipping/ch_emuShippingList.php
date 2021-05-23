<?php
	
	include_once "../../basic/parameter.inc";
	
	$targetDate = $_POST["targetDate"];
	//$targetDate = "2014-12";
	
	$dateValue=date("Y-m",strtotime($targetDate));
	$StartDate=$dateValue."-01";
	$EndDate=date("Y-m-t",strtotime($targetDate));
	
	$shippingSql="SELECT 
M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.CompanyId,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Operator,C.Forshort,C.PayType,S.InvoiceModel,B.Name
			FROM $DataIn.ch0_shipmain M
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.ch8_shipmodel S ON S.Id=M.ModelId 
			Left Join $DataPublic.staffmain B On B.Number = M.Operator
			WHERE M.Estate='1'
			and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')
			ORDER BY M.Date DESC";
	
	$shippinglist = array();
	$shippingResult = mysql_query($shippingSql);
	while($shipRow = mysql_fetch_assoc($shippingResult))
	{
		$Id = $shipRow["Id"];
		$CompanyId = $shipRow["CompanyId"];
		$Number = $shipRow["Number"];
		$Forshort = $shipRow["Forshort"];
		$InvoiceNO = $shipRow["InvoiceNO"];
		$InvoiceFile = $shipRow["InvoiceFile"];
		$Wise = $shipRow["Wise"] == ""?"":$shipRow["Wise"];
		$Date = $shipRow["Date"];
		$Locks = $shipRow["Locks"];
		$Operator = $shipRow["Name"];
		
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amounts = sprintf("%.2f",$checkAmount["Amount"]);
		
		//获取该送货单下的产品
		$shipProductSql = "SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN,P.Weight AS Weight,M.Date,E.Leadtime,P.TestStandard,P.MainWeight,P.Code,N.OrderDate AS OrderDate ,P.ProductId,N.ClientOrder, H.EndPlace, H.StartPlace,O.PackRemark as PackRemark
						   FROM $DataIn.ch0_shipsheet S 
						   LEFT JOIN $DataIn.ch0_shipmain M ON M.Id=S.Mid 
						   LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
						   LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
						   LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=O.Id 
						   LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
						   LEFT JOIN $DataIn.ch8_shipmodel H On H.Id = M.ModelId
						   WHERE S.Mid='$Id' 
						   AND S.Type='1'
						   UNION ALL
						   SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,O.Weight AS Weight,M.Date,'' AS Leadtime,'' AS TestStandard,'' AS MainWeight, '' as Code,O.Date AS OrderDate  ,'' AS ProductId,'' AS ClientOrder, H.EndPlace, H.StartPlace,''as PackRemark
						   FROM $DataIn.ch0_shipsheet S 
						   LEFT JOIN $DataIn.ch0_shipmain M ON M.Id=S.Mid 
						   LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
						   LEFT JOIN $DataIn.ch8_shipmodel H On H.Id = M.ModelId
						   WHERE S.Mid='$Id' 
						   AND S.Type='2'
						   UNION ALL
						   SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,'0' AS Weight ,M.Date,'' AS Leadtime,'' AS TestStandard,'' AS MainWeight, '' as Code,O.Date AS OrderDate,'' AS ProductId,'' AS ClientOrder, H.EndPlace, H.StartPlace, '' as PackRemark
						   FROM $DataIn.ch0_shipsheet S 
						   LEFT JOIN $DataIn.ch0_shipmain M ON M.Id=S.Mid 
						   LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
						   LEFT JOIN $DataIn.ch8_shipmodel H On H.Id = M.ModelId
						   WHERE S.Mid='$Id' 
						   AND S.Type='3'";
		
		$productList = array();
		$shipProductResult = mysql_query($shipProductSql);
		while($shipProductRow = mysql_fetch_assoc($shipProductResult))
		{
			$OrderPO = $shipProductRow["OrderPO"] == ""?"":$shipProductRow["OrderPO"];
			$POrderId = $shipProductRow["POrderId"];
			$ProductId = $shipProductRow["ProductId"];
			$lotto = '';
			$itf = '';

			$hasPrintParameterSql = "Select * From $DataIn.printparameters Where POrderId = '$POrderId' and Estate = 1 Order by Id Limit 1";
  			$hasPrintParameterResult = mysql_query($hasPrintParameterSql);
  			$hasPrintParameterRow = mysql_fetch_assoc($hasPrintParameterResult);
  			if($hasPrintParameterRow){
  				$lotto = $hasPrintParameterRow["Lotto"];
  				$itf = $hasPrintParameterRow["itf"];
  			}else{
  				$hasProductParameterSql = "Select * From $DataIn.productprintparameter Where productId = '$ProductId' and Estate = 1 Order by Id Limit 1";
  				$hasProductParameterResult = mysql_query($hasProductParameterSql);
  				$hasProductParameterRow = mysql_fetch_assoc($hasProductParameterResult);
  				if($hasProductParameterRow){
  					$lotto = $hasProductParameterRow["Lotto"];
  					$itf = $hasProductParameterRow["itf"];
  				}
  			}

  			if($lotto == ''){
        		if($CompanyId == '100024'){
            		$lotto = "ART01";
        		}
        		else if($CompanyId == '2668'){
            		$lotto = "LOP01";
        		}
        		else if($CompanyId == '1046'){
            		$lotto = "TIM01";
        		}
       			else{
            		$lotto = "ASH01";
        		}
    		}	

		    if($itf == ''){
		        $itf = "4";
		    }

			$cName = $shipProductRow["cName"];
			$eCode = $shipProductRow["eCode"];
			$TestStandard = $shipProductRow["TestStandard"];
			$ClientOrder = $shipProductRow["ClientOrder"];
			$ProductQty = $shipProductRow["Qty"];
			$Price = sprintf("%.2f", $shipProductRow["Price"]);
			$Type = $shipProductRow["Type"];
			$YandN = $shipProductRow["YandN"];
			$Amount = sprintf("%.2f",$ProductQty*$Price);	
			$sumAmount = sprintf("%.2f",$sumAmount+$Amount);
			$Weight = $shipProductRow["Weight"];
			$WG = round(($Weight*$ProductQty)/1000,2);//整单重量
			$MainWeight = ($shipProductRow["MainWeight"]==0)?"":$shipProductRow["MainWeight"];
			$EndPlace = $shipProductRow["EndPlace"];
			$StartPlace = $shipProductRow["StartPlace"];
			$Code = $shipProductRow["Code"];
			$CodeArray = explode("|", $Code);
			$Code = $CodeArray[1];

			if(count($CodeArray) == 1)
			{
				$Code = substr($CodeArray[0], 0, 13);
			}
			else
			{
				$Code = substr($CodeArray[1], 0, 13);
				$otherCode = $CodeArray[0];
			}	
			$Description = $shipProductRow["Description"];

			
			$packRemark = $shipProductRow["PackRemark"];
			
			/*获取货运方式*/
			$shipTypeResult = mysql_query("Select B.name From $DataIn.ch1_shipsplit A
						Left Join $DataPublic.ch_shiptype B On A.ShipType = B.Id
						Where A.POrderId = '$POrderId'");
			$shipTypeRow = mysql_fetch_assoc($shipTypeResult);
			$shipType = strtoupper($shipTypeRow["name"]);		
			
			
			/*
			2.每箱多少个 3.箱数 4.外箱规格 		
			*/
			$BoxResult = mysql_query("SELECT D.Spec,D.Weight,P.Relation 
									  FROM $DataIn.pands P 
									  LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId 
									  LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
									  WHERE P.ProductId='$ProductId' 
									  AND P.ProductId>0 
									  and T.TypeId='9040'",$link_id);
			$productInBox = "0";
			$boxCount = "0";				  
			if($BoxRows = mysql_fetch_assoc($BoxResult))
			{
				$Relation=explode("/", $BoxRows["Relation"]);
				$productInBox = $Relation[1];
				$BoxWeight=$BoxRows["Weight"];
				$spec = $BoxRows["Spec"];
				
				if($productInBox != 0)
				{
					if($ProductQty % $productInBox == 0)
					{
						$hasLast = "no";
						$boxCount = intval($ProductQty / $productInBox);
					}
					else
					{
						$hasLast = "yes";
						$boxCount = intval($ProductQty / $productInBox) + 1;
					}
				}
			}
			
			$netWeight = 0;
			if ($Weight>0 && $BoxWeight>0)
			{
 				$productId=$ProductId;
				include "../../model/subprogram/weightCalculate.php";
				$realWeight=number_format(($productInBox*$Weight+$extraWeight)/1000,2);
				$netWeight = number_format(($productInBox*$Weight)/1000,2);
				//echo "realWeight:" . $realWeight."<br>";
			}
			else
			{
				$realWeight=0;
			}
			
			//从packing list获取装箱
			$productInBox = "0";
			$boxCount = "0";
			$packId = "";
			if($Type == 1)
			{
				$packingSql = mysql_query("Select Id, BoxPcs, SUM(BoxQty) as BoxQty From $DataIn.ch0_packinglist Where POrderId = '$POrderId' and Mid='$Id' Group by POrderId");
				//echo "Select Id, BoxPcs, BoxQty From $DataIn.ch2_packinglist Where POrderId = '$POrderId' and Mid='$Id'"."<br>";
				
				$packingResult = mysql_fetch_assoc($packingSql);
				$productInBox = $packingResult["BoxPcs"];
				$boxCount = $packingResult["BoxQty"];
				$packId = $packingResult["Id"];
			}			
			
			$Date = date("jS M Y", strtotime($Date));
			
			//将使用的模板先下载
			$printLabel = "";
			$value = "";
			
			if($Type == "1")
			{ 
				$labelName = $CompanyId."+".str_replace("cm", "", strtolower($spec));
				
				if($CompanyId == "1089" || $CompanyId == "1066" || $CompanyId == "1064" || $CompanyId == "1024" || $CompanyId == "1081" || $CompanyId == "1080" || $CompanyId == "1077" || $CompanyId == "1073" || $CompanyId == "1092" || $CompanyId == "1013" || $CompanyId == '100057' || $CompanyId == '1093')
				{
					$labelName = $CompanyId;
				}

				$LabelResult = mysql_query("Select Parameters,Value From $DataPublic.printformatter Where ClientId = '$labelName' and Estate = '1' Order by Id Desc Limit 1");
				//if($CompanyId == )
				$LabelRow = mysql_fetch_assoc($LabelResult);
				$printLabel = $LabelRow["Parameters"];
				$value = $LabelRow["Value"];
				
				if($CompanyId == "10460000" || ($CompanyId == "1102" && strstr($printLabel, "qt"))){
					$newEcode = "$eCode-$productInBox";
					$printLabel = str_replace("*eCode", $newEcode, $printLabel);
				}else if($CompanyId == '1046'){
					$printLabel = str_replace("*eCode", '', $printLabel);
				}
				else{
					$printLabel = str_replace("*eCode", $eCode, $printLabel);
				}
				$printLabel = str_replace("*EndPlace", $EndPlace, $printLabel);
				if($CompanyId == "1046"){
					$printLabel = str_replace("*InvoiceNO", '', $printLabel);
				}else{
					$printLabel = str_replace("*InvoiceNO", $InvoiceNO, $printLabel);
				}
				$printLabel = str_replace("*WG", $netWeight, $printLabel);
				if($CompanyId == '1049')
				{
					$cgOrderPO = $OrderPO;
					if(strlen($cgOrderPO) !=6)
					{
						$cgOrderPO = str_pad($cgOrderPO, 6, "0", STR_PAD_RIGHT);

					}
					$printLabel = str_replace("*OrderPO", $cgOrderPO, $printLabel);
					$printLabel = str_replace("*Date", $Date, $printLabel);
				}
				else
				{
					$printLabel = str_replace("*OrderPO", $OrderPO, $printLabel);
					$printLabel = str_replace("*Date", date("Y-m-d"), $printLabel);
				}
				
				$printLabel = str_replace("*productInBox", $productInBox, $printLabel);
				$printLabel = str_replace("*Code", $Code, $printLabel);
				$printLabel = str_replace("*boxSize", $spec, $printLabel);
				$printLabel = str_replace("*shipType", $shipType, $printLabel);
				$printLabel = str_replace("*otherCode", $otherCode, $printLabel);
				$printLabel = str_replace("*description", $Description, $printLabel);
				$printLabel = str_replace("*lotto", $lotto, $printLabel);
				$printLabel = str_replace("*itfInit", $itf, $printLabel);
				if($CompanyId == '1049')
				{
					$productTypeCode = mysql_query("select Description,pRemark From $DataIn.productdata Where ProductId = '$ProductId'");
					$pRemarkResult = mysql_fetch_assoc($productTypeCode);
					
					$productType = 	$pRemarkResult["pRemark"];
					$printLabel = str_replace("*productType", $productType, $printLabel);
				}else if($CompanyId == '100057'){
					$productTypeCode = mysql_query("select Description,pRemark From $DataIn.productdata Where ProductId = '$ProductId'");
					$pRemarkResult = mysql_fetch_assoc($productTypeCode);
					$productType = 	$pRemarkResult["pRemark"];
					$pMark = explode('|', $productType);
					$printLabel = str_replace("*producttype", $pMark[0], $printLabel);
					$printLabel = str_replace("*DeviceType", $pMark[1], $printLabel);
					$printLabel = str_replace("*material", $pMark[2], $printLabel);
				}
				
			}
			
			//获取对应箱的重量
			$weightList = array();
			$totleWeight = 0;
			$itemCount = 0;
			$boxWeightSql = mysql_query("Select boxId, Weight from $DataIn.sc1_cjtj Where POrderId = '$POrderId'");
			while($boxWeightRows = mysql_fetch_assoc($boxWeightSql))
			{
				$boxWeight = $boxWeightRows["Weight"];
				if($boxWeight < 50)
				{
					$boxId = $boxWeightRows["boxId"];
					$weightList[$boxId] = "$boxWeight";
					$totleWeight += $boxWeight;
					$itemCount++;
				}
			}
			
			if($itemCount != 0)
			{
				$totleWeightAverage = sprintf("%.2f", $totleWeight/$itemCount);
			}
			else
			{
				$totleWeightAverage = number_format(($productInBox*$Weight)/1000,2);
			}
			
			
			$productList[] = array("PO"=>"$OrderPO", "POrderId"=>"$POrderId", "eCode"=>"$eCode", "cNmae"=>"$cName", "TestStandard"=>"$TestStandard", "Qty"=>"$ProductQty", "ProductId"=>"$ProductId", "Price"=>"$Price", "Amount"=>"$Amount", "MainWeight"=>"$MainWeight", "Weight"=>"$Weight", "TotleWeight"=>"$WG", "orderDate"=>"$orderDate", "EndPlace"=>"$EndPlace", "StartPlace"=>"$StartPlace","hasLast"=>"$hasLast", "ProductInBox"=>"$productInBox", "boxCount"=>"$boxCount", "realWeight"=>"$realWeight", "spec"=>"$spec", "ShipDate"=>"$Date", "printLabel"=>"$printLabel"."=="."$value","WeightAverage"=>"$totleWeightAverage", "packId"=>"$packId","WeightList"=>$weightList);
			
		}
		
		$shippinglist[] = array("Id"=>"$Id", "CompanyId"=>"$CompanyId", "Number"=>"$Number", "Forshort"=>"$Forshort", "InvoiceNO"=>"$InvoiceNO", "Wise"=>"$Wise", "Date"=>"$Date", "Locks"=>"$Locks", "operator"=>"$Operator", "Amounts"=> "$Amounts", "list"=>$productList);
		
	}
	
	echo json_encode($shippinglist);

?>