<?php
	
	include_once "../../basic/parameter.inc";
	$theGysId = $_POST["CompanyId"];
	if($theGysId != "all")
	{
		$companySearch = "AND M.CompanyId = '$theGysId'";
	}
	//$theGysId = "2157";
	$floor = $_POST["floor"];	
	
	if($floor == "3")
	{
		$searchRow = " And (M.Floor = '$floor' or M.Floor = 0)";
	}
	else
	{
		$searchRow = " And M.Floor = '$floor'";
	}
	
	$checkNumSql = mysql_query("SELECT M.BillNumber,M.Date
				 				FROM $DataIn.gys_shmain M 
								LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=M.Id
								WHERE 1  
								AND S.Estate = 1
								$companySearch
								$searchRow
								GROUP BY S.Mid 
								ORDER BY M.Date",$link_id);
										
	$gysList = array();
	
	while($gysRows = mysql_fetch_assoc($checkNumSql))
	{
		$billNumber = $gysRows["BillNumber"];
		$billDate = $gysRows["Date"];
		
		//$billCount = 0;
			
		$mySql="SELECT M.CompanyId,S.Id,S.Mid,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.Picture,(G.AddQty+G.FactualQty) AS cgQty,M.Date,G.POrderId,Y.OrderPO,Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark 
                FROM $DataIn.gys_shsheet S
                LEFT JOIN $DataIn.gys_shmain M ON S.Mid = M.Id
                LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId 
                LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId 
                LEFT JOIN $DataIn.stuffdata D ON D.StuffId = S.StuffId
                WHERE S.Estate = 1
                And M.BillNumber='$billNumber'
                ORDER BY S.Id";
		
         $subListResult = mysql_query($mySql);
         
         if(mysql_num_rows($subListResult) >= 1)
         {
         	$stuffIdChk = "";
			$sameStuffCount = 0;
         	$detailList = array();
           	while($subListRows = mysql_fetch_assoc($subListResult))
           	{
	           	$Id = $subListRows["Id"];
	           	$CompanyId = $subListRows["CompanyId"];
	           	$StockId = $subListRows["StockId"];
	           	$realStorckIdHolder = $subListRows["POrderId"];
	           	$StuffCname = $subListRows["StuffCname"];
	           	$cgQty = $subListRows["cgQty"];
	           	$Qty = $subListRows["Qty"];
	           	$SendSign = $subListRows["SendSign"];
	           	$StuffId = $subListRows["StuffId"];
	           	$POrderId=$subListRows["POrderId"];
	           	$LockRemark = "no";
	           	$SignString="";
	           	
	           	//????????????
	           	$CheckGSql=mysql_query("SELECT IFNULL(SUM(FactualQty+AddQty),0) AS cgQty FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId'",$link_id);
	           	$historyOrderRow = mysql_fetch_assoc($CheckGSql);
	           	$historyOrder = $historyOrderRow["cgQty"];
	           	
	           	if($stuffIdChk == "" || $stuffIdChk == $StuffId)
				{
					if($stuffIdChk == "")
					{
						$stuffIdChk = $StuffId;
					}
					$sameStuffCount += $Qty;
				}
				else if($stuffIdChk != $StuffId)
				{	
					$sameCountQty = intval($sameStuffCount);
					$sameCountQty = number_format($sameCountQty);
					$detailList[] = array("stuffIdChk"=>"$stuffIdChk", "sameCountQty"=>"$sameCountQty", "totle"=>"totle");
					$stuffIdChk = $StuffId;
					$sameStuffCount = $Qty;
				}
	           	
	           	//if ($SendSign==1) // SendSign: 0?????????1??????, 2?????? 
	           	switch ($SendSign)
	           	{
					case 1:
						$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
											LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
											WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);						   				$thQty=mysql_result($thSql,0,"thQty");
				
						//??????????????? add by zx 2011-04-27
						$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
											LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
											WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
						$bcQty=mysql_result($bcSql,0,"bcQty");	
						$cgQty=$thQty-$bcQty;
						$noQty=$cgQty;
						$SignString="(??????)";
						$StockId="????????????";
					break;
					case 2:
						$cgQty=0;
						$noQty=0;
						$SignString="(??????)";
						$StockId="????????????";
					break;
					default :
						$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty FROM $DataIn.ck1_rksheet R 
											 LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
											 WHERE R.StockId='$StockId'",$link_id);
						$rkQty=mysql_result($rkTemp,0,"Qty");	//????????????
						$noQty=$cgQty-$rkQty;				
					break;
				}   
			
				if($noQty<=0  && $SendSign!=2)
				{   //????????????????????????????????????????????????
					$LockRemark="???????????????????????????:??????????????????????????????????????????????????????";
					$czSign=0;
				}
				else 
				{
					if($noQty<$Qty && $SendSign!=2)
					{  //????????????????????????????????????,???????????????
			    		$czSign=0;
			    		$LockRemark="???????????????????????????:????????????????????????????????????????????????????????????????????????";
			    	}
			    	else 
			    	{
					//$QtyStr="<input name='QTY[$i]' type='text' id='QTY$i' value='$Qty' size='9' class='QtyRight' onfocus='toTempValue(this);this.select()' onBlur='Indepot(this,$noQty)'>";
					//???????????????????????????????????????????????????
						$QtyStr=$Qty;
					}
				}
			
				$Picture = $subListRows["Picture"];
								
				switch($Picture)
				{
					case 0:
					{
						$LockRemark = "??????????????????";
					}
					break;
					case 2:
					{
						$LockRemark = "?????????";
					}
					break;
					case 3:
					{
						$LockRemark = "??????????????????";
					}
					break;
					case 4:
					{
						$LockRemark = "??????????????????";
					}
					break;
				}

				$remarkSql=mysql_query("SELECT Remark FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
				$Remark="";
				if($remarkRow=mysql_fetch_array($remarkSql))
				{
             		$Remark=$remarkRow["Remark"];
             	}
             	
             	//???????????????????????????????????????????????????
             	$isLastBgColor = "0";
             	
             		//???????????????????????????????????????????????????
             	
             		$isLastStockSql = "SELECT G.StockId, D.StuffId
										   FROM $DataIn.cg1_stocksheet G
										   LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = G.StuffId
										   LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
										   LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
										   WHERE G.POrderId = '$POrderId'
										   AND T.mainType in (1,0)
										   AND K.tStockQty < G.OrderQty";
				
					//echo $isLastStockSql;
					$isLastStockResult = mysql_query($isLastStockSql);
					if(mysql_num_rows($isLastStockResult) == 1)
					{
						$lastStockRow = mysql_fetch_assoc($isLastStockResult);
						$lastStuffId = $lastStockRow["StuffId"];
						if($lastStuffId == $StuffId)
						{
							$isLastBgColor = "1";
						}
					}

             	
             	
             	$cgQty = intval($cgQty);
             	$cgQtyCount = number_format($cgQty);
             	
             	$noQty = intval($noQty);
             	$noQtyCount = number_format($noQty);
             	
             	$Qty = intval($Qty);
             	//$billCount += $Qty;
             	$QtyCount = number_format($Qty);
             	
             	$historyOrder = intval($historyOrder);
             	$historyOrderCount = number_format($historyOrder);
                                      
             	$detailList[]= array("stockId"=>"$StockId", "stuffName"=>"$StuffCname", "cgQtyCount"=>"$cgQtyCount", "noQtyCount"=>"$noQtyCount", "qtyCount"=>"$QtyCount", "note"=>"$Remark", "Id"=>"$Id", "lockRemark"=>"$LockRemark", "picture"=>"$Picture", "stuffId"=>"$StuffId", "isLast"=>"$isLastBgColor", "history"=>"$historyOrderCount");
			
            }
           	
           	$sameCountQty = intval($sameStuffCount);
			$sameCountQty = number_format($sameCountQty);
            $detailList[] = array("stuffIdChk"=>"$stuffIdChk", "sameCountQty"=>"$sameCountQty", "totle"=>"totle");
           	
            $gysList[] = array(array("$billNumber", "$billDate"), $detailList);
        } 
    }
    
    echo json_encode($gysList);
	
?>