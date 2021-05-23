<?php
	
	include "../../basic/parameter.inc";
	
	$mySql="SELECT M.Id, M.DeliveryNumber,M.Remark,M.DeliveryDate,M.Operator ,F.Forshort ,M.Estate
        FROM $DataIn.ch1_deliverymain M
        LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=M.ForwaderId 
        WHERE M.Estate = '2'";
      
	$loadingBillResult = mysql_query($mySql);
	while($loadingBillRow = mysql_fetch_assoc($loadingBillResult))
	{
		$Id=$loadingBillRow["Id"];
		$DeliveryNumber=$loadingBillRow["DeliveryNumber"];
		$Forshort=$loadingBillRow["Forshort"]; 
		$Remark=$loadingBillRow["Remark"];
		$DeliveryDate=$loadingBillRow["DeliveryDate"];
		$Operator=$loadingBillRow["Operator"];
		
		$sListSql = "SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.DeliveryQty,S.Price,S.Type,
					 P.TestStandard,P.ProductId, M.CompanyId, P.Code
					 FROM $DataIn.ch1_deliverysheet S 
					 LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
					 LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
					 Left Join $DataIn.ch1_deliverymain M On M.Id = S.MId
					 WHERE S.Mid='$Id' AND S.Type='1'
					 UNION ALL
					 SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.Description 
					 AS eCode,S.DeliveryQty,S.Price,S.Type,'' AS TestStandard, '' as ProductId, '' as CompanyId, '' as Code
					 FROM $DataIn.ch1_deliverysheet S 
					 LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
					 WHERE S.Mid='$Id' AND S.Type='2'
					 UNION ALL
					 SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.DeliveryQty,S.Price,
					 S.Type,'' AS TestStandard,'' as ProductId, '' as CompanyId, '' as Code
					 FROM $DataIn.ch1_deliverysheet S 
					 LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
					 WHERE S.Mid='$Id' AND S.Type='3'";
		//echo $sListSql."<br>";
		$sListReslut = mysql_query($sListSql);
		while($StockRows = mysql_fetch_array($sListReslut))
		{
			$Id=$StockRows["Id"];
			$DeliveryQty=$StockRows["DeliveryQty"];
			$Price=$StockRows["Price"];
			$DeliveryAmount=$DeliveryQty*$Price;
			$DeliveryAmount=sprintf("%.2f",$DeliveryAmount)==0?"&nbsp;":sprintf("%.2f",$DeliveryAmount);
			$Price=sprintf("%.2f",$Price)==0?"":sprintf("%.2f",$Price);
		
			$OrderPO=$StockRows["OrderPO"]==""?"":$StockRows["OrderPO"];
			$POrderId=$StockRows["POrderId"];
			$cName=$StockRows["cName"];
			$eCode=$StockRows["eCode"];
			$TestStandard=$StockRows["TestStandard"];
			$ProductId = $StockRows["ProductId"];
			$Type = $StockRows["Type"];
			$CompanyId = $StockRows["CompanyId"];
			$outBoxCode = $StockRows["Code"];
			//处理外箱
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
				$packingSql = mysql_query("Select Id, BoxPcs, SUM(BoxQty) as BoxQty From $DataIn.ch2_packinglist Where POrderId = '$POrderId' and Mid='$Id' Group by POrderId");
				//echo "Select Id, BoxPcs, BoxQty From $DataIn.ch2_packinglist Where POrderId = '$POrderId' and Mid='$Id'"."<br>";
				
				$packingResult = mysql_fetch_assoc($packingSql);
				$productInBox = $packingResult["BoxPcs"];
				$boxCount = $packingResult["BoxQty"];
				$packId = $packingResult["Id"];
			}			
			
			//将使用的模板先下载
			$printLabel = "";
			$value = "";
			
			$outBoxCode = split("|", $outBoxCode);
			if($Type == "1" && count($outBoxCode) == 3)
			{
				$labelName = $CompanyId."+".str_replace("cm", "", strtolower($spec));
				$LabelResult = mysql_query("Select Parameters,Value From $DataPublic.printformatter Where ClientId = '$labelName'");
				
				if($CompanyId == '1091')
				{
					$outBoxCode = split("|", $outBoxCode);
					
					$eCode = $outBoxCode[0];
					$Code = $outBoxCode[1];
				}
				else
				{}
				
			}
			
			
			
			
		}
		

	}
	
?>