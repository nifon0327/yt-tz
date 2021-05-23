<?php
	
	include_once "../../basic/parameter.inc";
	$theGysId = $_POST["CompanyId"];
	//$theGysId = "2270";
	
	$checkNumSql = mysql_query("SELECT M.BillNumber,M.Date FROM $DataIn.gys_shmain M 
	LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=M.Id
	WHERE 1 
	And M.CompanyId = '$theGysId' 
	AND S.Estate = 1
	GROUP BY S.Mid 
	ORDER BY M.BillNumber 
	DESC",$link_id);
		
	$gysList = array();
	while($gysRows = mysql_fetch_assoc($checkNumSql))
	{
		$billNumber = $gysRows["BillNumber"];
		$billDate = $gysRows["Date"];
		
		$mySql="SELECT M.CompanyId,S.Id,S.Mid,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,(G.AddQty+G.FactualQty) AS cgQty,M.Date,
                G.POrderId,Y.OrderPO,Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark 
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		WHERE M.CompanyId = '$theGysId' 
		And S.Estate = 1
		And M.BillNumber='$billNumber'
		ORDER BY S.Id";
		
		$subListResult = mysql_query($mySql);
		
		$detailList = array();
		while($subListRows = mysql_fetch_assoc($subListResult))
		{
			$Id = $subListRows["Id"];
			$CompanyId = $subListRows["CompanyId"];
			$StockId = $subListRows["StockId"];
			$StuffCname = $subListRows["StuffCname"];
			$cgQty = $subListRows["cgQty"];
			$Qty = $subListRows["Qty"];
			$SendSign = $subListRows["SendSign"];
			$SignString="";
			//if ($SendSign==1) // SendSign: 0送货，1补货, 2备品 
			switch ($SendSign)
			{
				case 1:
					$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
											   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
					$thQty=mysql_result($thSql,0,"thQty");
				
					//补货的数量 add by zx 2011-04-27
					$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
											   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
					$bcQty=mysql_result($bcSql,0,"bcQty");	
					$cgQty=$thQty-$bcQty;
					$noQty=$cgQty;
					$SignString="(补货)";
					$StockId="本次补货";
				break;
				case 2:
					$cgQty=0;
					$noQty=0;
					$SignString="(备品)";
					$StockId="本次备品";
				break;
				default :
					$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty FROM $DataIn.ck1_rksheet R 
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
					WHERE R.StockId='$StockId'",$link_id);
					$rkQty=mysql_result($rkTemp,0,"Qty");	//收货总数
					$noQty=$cgQty-$rkQty;				
				break;
			 }			
			 
			 $remarkSql=mysql_query("SELECT Remark FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
			 
			 $Remark="";
             if($remarkRow=mysql_fetch_array($remarkSql))
             {
             	$Remark=$remarkRow["Remark"];
             }
                                      
             $detailList[]= array("$StockId", "$StuffCname", "$cgQty", "$noQty", "$Qty", "$Remark");
			
		}
		
		$gysList[] = array($billNumber, $billDate, $detailList);
		
	}
	
	echo json_encode($gysList);
	
?>