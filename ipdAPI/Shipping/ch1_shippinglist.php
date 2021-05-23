<?php
	
	include_once "../../basic/parameter.inc";
	
	$dateValue = "2014-09";
	
	$mySql="SELECT M.Id, M.DeliveryNumber,M.Remark,M.DeliveryDate,M.Operator ,C.Forshort,M.CompanyId
        	FROM $DataIn.ch1_deliverymain M
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
			WHERE DATE_FORMAT(M.DeliveryDate,'%Y-%m')='$dateValue' AND C.CompanyId = 1056";
	//echo $mySql;
	$myResult = mysql_query($mySql);
	
	$shippinglist = array();
	while($myRow = mysql_fetch_assoc($myResult))
	{
		$Id=$myRow["Id"];
		$DeliveryNumber=$myRow["DeliveryNumber"];
		$Forshort=$myRow["Forshort"]; 
		$DeliveryDate=$myRow["DeliveryDate"];
		$Operator=$myRow["Operator"];
		$CompanyId = $myRow["CompanyId"];
		
		$sListResult = mysql_query("SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.DeliveryQty,S.Price,S.Type,
									P.TestStandard,P.ProductId,P.Code,O.Qty,P.Weight
									FROM $DataIn.ch1_deliverysheet S 
									LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
									LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
									WHERE S.Mid='$Id' AND S.Type='1'
									UNION ALL
									SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.Description 
									AS eCode,S.DeliveryQty,S.Price,S.Type,'' AS TestStandard,'' as ProductId,'' as Code,'' as Qty, '' as Weight
									FROM $DataIn.ch1_deliverysheet S 
									LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
									WHERE S.Mid='$Id' AND S.Type='2'
									UNION ALL
									SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.DeliveryQty,S.Price,
									S.Type,'' AS TestStandard,'' as ProductId, '' as Code,'' as Qty, '' as Weight
									FROM $DataIn.ch1_deliverysheet S 
									LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
									WHERE S.Mid='$Id' AND S.Type='3'",$link_id);
		
		$productList = array();
		while($StockRows = mysql_fetch_array($sListResult))
		{
			$Id=$StockRows["Id"];
			$DeliveryQty=$StockRows["DeliveryQty"];
			$Price=$StockRows["Price"];
			$DeliveryAmount=$DeliveryQty*$Price;
			$DeliveryAmount=sprintf("%.2f",$DeliveryAmount)==0?"&nbsp;":sprintf("%.2f",$DeliveryAmount);
			$Price=sprintf("%.2f",$Price)==0?"&nbsp;":sprintf("%.2f",$Price);
		
			$OrderPO=$StockRows["OrderPO"]==""?"":$StockRows["OrderPO"];
			$POrderId=$StockRows["POrderId"];
			$cName=$StockRows["cName"];
			$eCode=$StockRows["eCode"];
			$labelCode = $StockRows["Code"];
			$TestStandard=$StockRows["TestStandard"];
			$ProductId = $StockRows["ProductId"];
			$Code = $StockRows["Code"];
			$ProductQty = $StockRows["Qty"];

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
						
			if($CompanyId === "1091")
			{
				$BoxResult = mysql_query("SELECT D.Spec,D.Weight,P.Relation 
									  FROM $DataIn.pands P 
									  LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId 
									  LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
									  WHERE P.ProductId='$ProductId' 
									  AND P.ProductId>0 
									  and T.TypeId='9040'",$link_id);
							  
				if($BoxRows = mysql_fetch_assoc($BoxResult))
				{
					$Relation=explode("/", $BoxRows["Relation"]);
					$productInBox = $Relation[1];
					$BoxWeight=$BoxRows["Weight"];
					$spec = $BoxRows["Spec"];
				}
				
				$Weight = $StockRows["Weight"];
				$WG = round(($Weight*$productInBox)/1000,2)+1;

				
				$labelName = $CompanyId."+".str_replace("cm", "", strtolower($spec));
				$LabelResult = mysql_query("Select Parameters,Value From $DataPublic.printformatter Where ClientId = '$labelName' and Estate = '1'");
				
				$LabelRow = mysql_fetch_assoc($LabelResult);
				$printLabel = $LabelRow["Parameters"];
				$value = $LabelRow["Value"];
				
				$Code = explode("|", $Code);
				if(count($Code) == 3)
				{
					$printLabel = str_replace("*name", $Code[0], $printLabel);
					$printLabel = str_replace("*eCode", $Code[1], $printLabel);
					$printLabel = str_replace("*code", $Code[2], $printLabel);
				
					$titleArray = explode(" FOR", strtoupper($Code[0]));
					$subTitle = $titleArray[1];
					$orderInfomations = explode(" ", $titleArray[0]);
					
					//print_r($orderInfomations);
					
					$bigTitle = "";
					$color = "";
					//print_r($orderInfomations);
					for($i=0; $i<count($orderInfomations); $i++)
					{
						if($i == count($orderInfomations)-1)
						{
							$color = $orderInfomations[$i];
						}
						else
						{
							$bigTitle = $bigTitle." ".$orderInfomations[$i];
						}
					}
				
					$printLabel = str_replace("*bigTitle", $bigTitle, $printLabel);
					$printLabel = str_replace("*subTitle", "for ".$subTitle, $printLabel);
					$printLabel = str_replace("*color", $color, $printLabel);
					$printLabel = str_replace("*wg", $WG, $printLabel);
				
					$orderNumer = explode("-", $DeliveryNumber);
					$printLabel = str_replace("*orderNumber", $orderNumer[1], $printLabel);
				}
			}
			else{
				$labelName = $CompanyId."+".str_replace("cm", "", strtolower($spec));
				
				if($CompanyId == "1056"){
					$labelName = $CompanyId;
				}

				$LabelResult = mysql_query("Select Parameters,Value From $DataPublic.printformatter Where ClientId = '$labelName' and Estate = '1' Order by Id Desc Limit 1");
							
				$LabelRow = mysql_fetch_assoc($LabelResult);
				$printLabel = $LabelRow["Parameters"];
				$value = $LabelRow["Value"];
				
				if(($CompanyId == "1046" && !strstr($printLabel, "qt3")) || ($CompanyId == "1102" && strstr($printLabel, "qt")))
				{
					$newEcode = "$eCode-$productInBox";
					$printLabel = str_replace("*eCode", $newEcode, $printLabel);
				}
				else
				{
					$printLabel = str_replace("*eCode", $eCode, $printLabel);
				}

				if($CompanyId == "1077")
				{
					$InvoiceNO = "ISY-0399";
				}

				$printLabel = str_replace("*EndPlace", $EndPlace, $printLabel);
				if(CompanyId == "1077")
				{
					$InvoiceNO = "ISY-0399";
				}
				$printLabel = str_replace("*InvoiceNO", $InvoiceNO, $printLabel);
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
					$printLabel = str_replace("*Date", date("Y-m-d", strtotime($Date)), $printLabel);
				}

				$printLabel = str_replace("*productInBox", $productInBox, $printLabel);
				$printLabel = str_replace("*Code", $Code, $printLabel);
				$printLabel = str_replace("*boxSize", $spec." CM", $printLabel);

				if($CompanyId == "1066" && $Wise != "")
				{
					$shipType =(strstr(strtolower($Wise), "sea"))?"SEA":"";
				}
				$printLabel = str_replace("*shipType", $shipType, $printLabel);
				$printLabel = str_replace("*otherCode", $otherCode, $printLabel);
				$printLabel = str_replace("*description", $Description, $printLabel);
				$printLabel = str_replace("*lotto", $lotto, $printLabel);
				$printLabel = str_replace("*itfInit", $itf, $printLabel);
				
				//$tifCode = "4".substr($Code, 0, strlen($Code)-1);
				//$printLabel = str_replace("*tifCode", $tifCode, $printLabel);
				
				if($CompanyId == '1049')
				{
					$productTypeCode = mysql_query("select Description,pRemark From $DataIn.productdata Where ProductId = '$ProductId'");
					$pRemarkResult = mysql_fetch_assoc($productTypeCode);
					
					$productType = 	$pRemarkResult["pRemark"];
					$printLabel = str_replace("*productType", $productType, $printLabel);
				}
				
			}

			
			$productList[] = array("PO"=>"$OrderPO", "POrderId"=>"$POrderId", "eCode"=>"$eCode", "cNmae"=>"$cName", "TestStandard"=>"$TestStandard", "Qty"=>"$DeliveryQty", "ProductId"=>"$ProductId", "Price"=>"$Price", "Amount"=>"$DeliveryAmount",  "TotleWeight"=>"$WG", "realWeight"=>"$realWeight", "spec"=>"$spec", "printLabel"=>"$printLabel"."=="."$value","WeightAverage"=>"$totleWeightAverage", "boxCount"=>"0", "boxCount"=>"$boxCount");
			
		}
		
		$shippinglist[] = array("Id"=>"$Id", "CompanyId"=>"$CompanyId", "Number"=>"$DeliveryNumber", "Forshort"=>"$Forshort", "Date"=>"$DeliveryDate", "operator"=>"$Operator", "list"=>$productList);
		
	}
	
	echo json_encode($shippinglist);
	
?>