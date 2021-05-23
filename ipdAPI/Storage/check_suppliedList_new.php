<?php
	
	include_once "../../basic/parameter.inc";
	
	$CheckSign = $_POST["CheckSign"];
	//$CheckSign = "1";
	
	$gysList = array();
	
	$checkNum = mysql_query("SELECT M.BillNumber,M.Date,P.Forshort,P.Letter,P.CompanyId
				 				FROM $DataIn.gys_shmain M 
								LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=M.Id
								LEFT JOIN $DataIn.trade_object P ON P.CompanyId = M.CompanyId
								WHERE 1  
								AND S.Estate = 2
								GROUP BY S.Mid 
								ORDER BY M.Date",$link_id);

	if(mysql_num_rows($checkNum) > 0)
	{
		while($gysRows = mysql_fetch_assoc($checkNum))
		{
			$billNumber = $gysRows["BillNumber"];
			$billDate = $gysRows["Date"];
			$companyForshot = $gysRows["Forshort"];
			$companyLetter = $gysRows["Letter"];
			$theGysId = $gysRows["CompanyId"];
			$billCount = 0;
			
			$checkBillSql = "SELECT S.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.Picture,T.AQL,(G.AddQty+G.FactualQty) AS cgQty,M.Date,D.TypeId,Y.ProductId,B.shDate
							 FROM $DataIn.gys_shsheet S
							 LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
							 LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
							 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
							 LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
							 Left Join $DataIn.yw1_ordersheet Y On Y.POrderId = G.POrderId
							 Left Join $DataIn.gys_shdate B On B.Sid = S.Id
							 WHERE 1 
							 And S.Estate=2 
							 And M.CompanyId='$theGysId' 
							 And M.BillNumber='$billNumber'   
							 And D.CheckSign = '$CheckSign'
							 ORDER BY S.Id";
			
			$billResult = mysql_query($checkBillSql);
			if(mysql_num_rows($billResult)>0)
			{
				$tmpLine = array();
				while($billRows = mysql_fetch_assoc($billResult))
				{
					$Id = $billRows["Id"];					//记录ID
					//$Date = $billRows["Date"];				//送货单生成日期
					$StockId = $billRows["StockId"];			//配件需求流水号
					$StuffId = $billRows["StuffId"];			//配件ID
					$TypeId = $billRows["TypeId"];    //配件类型
					$StuffCname = $billRows["StuffCname"];	//配件名称
					//$CheckSign = $billRows["CheckSign"];   //品检要求：0－抽检，1－全检
					$cgQty = $billRows["cgQty"];				//采购总数
					$Qty = $billRows["Qty"];					//供应商送货数量
					$Picture = $billRows["Picture"];			//配件图片
					$AQL = $billRows["AQL"];
					$SendSign = $billRows["SendSign"];
					$SignString="";
					$ProductId = $billRows["ProductId"];
					$shDate = $billRows["shDate"];
					//获取是否有出错案例
					$hasError = "no";
					$errorCaseSql = "Select * From $DataIn.casetoproduct Where ProductId = '$ProductId'";
					$errorResult = mysql_query($errorCaseSql);
					if(mysql_num_rows($errorResult) > 0)
					{
						$hasError = "yes";
					}
								
					$lockMark = "no";
			
					//if ($SendSign==1) // SendSign: 0送货，1补货, 2备品 
					switch ($SendSign)
					{
						case 1:
						{
							$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  
												FROM $DataIn.ck2_thmain M  					   
												LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
												WHERE M.CompanyId = '$CompanyId' 
												AND S.StuffId = '$StuffId' ",$link_id);
							$thQty=mysql_result($thSql,0,"thQty");
				
							//补货的数量 add by zx 2011-04-27
							$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  
												FROM $DataIn.ck3_bcmain M 
												LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
												WHERE M.CompanyId = '$CompanyId' 
												AND S.StuffId = '$StuffId' ",$link_id);
							$bcQty=mysql_result($bcSql,0,"bcQty");	
							$cgQty=$thQty-$bcQty;
							$noQty=$cgQty;
							$SignString="(补货)";
							$StockId="本次补货";
						}
						break;
						case 2:
						{
							$cgQty=0;
							$noQty=0;
							$SignString="(备品)";
							$StockId="本次备品";
						}
						break;
						default :
						{
							$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty 
				    						 	 FROM $DataIn.ck1_rksheet R 
				    						 	 LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
				    						 	 WHERE R.StockId='$StockId'",$link_id);
				    		$rkQty=mysql_result($rkTemp,0,"Qty");	//收货总数
				    		$noQty=$cgQty-$rkQty;	
				    	}			
				    	break;
				    }	
			
				    if($noQty <= 0 && $SendSign !=2)
				    {
						$lockMark = "已全部入库";
					}		
					else if($noQty < $Qty && $SendSign != 2)
					{
						$lockMark = "当前送货量大于未送货量";
					}
					
					$Picture = $billRows["Picture"];
					switch($Picture)
					{
						case 0:
						{
							$LockRemark = "无配件标准图";
						}
						break;
						case 2:
						{
							$LockRemark = "审核中";
						}
						break;
						case 3:
						{
							$LockRemark = "需更新标准图";
						}
						break;
						case 4:
						{
							$LockRemark = "审核退回修改";
						}
						break;
					}

			
					$Remark = "";
					$remarkSql=mysql_query("SELECT Remark FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
					if($remarkRow=mysql_fetch_array($remarkSql))
					{
            			$Remark=$remarkRow["Remark"];
            		}
            	
            		$cgQty = intval($cgQty);
            		$cgQtyCount = number_format($cgQty);
             	
            		$noQty = intval($noQty);
            		$noQtyCount = number_format($noQty);
             	
            		$Qty = intval($Qty);
            		$billCount += $Qty;
            		$QtyCount = number_format($Qty);
            
            		$tmpLine[] = array("$StockId", "$StuffCname($shDate)", "$cgQtyCount", "$noQtyCount", "$QtyCount", "$Remark", "$AQL", "$Id", "$StuffId", "$ProductId", "$hasError", "$lockMark", "$Picture");
			
            	}
			
            	$gysList[] = array(array("$billNumber", "$billDate", "$companyForshot", "$companyLetter", "$theGysId", "$billCount"), $tmpLine);
			}		
		}
	}
	
	echo json_encode($gysList);
	
?>