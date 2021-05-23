<?php 
	
	include "../../basic/parameter.inc";
	
	$waitShip = array();
	$waitShipSql = "SELECT M.OrderNumber, M.CompanyId, M.OrderDate,  '1' AS 
TYPE , S.Id, S.OrderPO, S.POrderId, S.ProductId, S.Qty, S.Price, S.PackRemark, P.cName, P.eCode, P.TestStandard, S.ShipType, S.dcRemark, M.COmpanyId, C.Forshort
					FROM $DataIn.yw1_ordersheet S
					LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
					LEFT JOIN $DataIn.productdata P ON P.ProductId = S.ProductId
					LEFT JOIN $DataIn.trade_object C ON M.COmpanyId = C.CompanyId
					WHERE S.Estate =  '2'
					AND S.scFrom =0
					UNION ALL 
					SELECT  '' AS OrderNumber, S.CompanyId, S.Date AS OrderDate,  '2' AS 
TYPE , S.Id,  '' AS OrderPO, S.SampId AS POrderId,  '' AS ProductId, S.Qty, S.Price,  '' AS PackRemark, S.CompanyId, S.SampName AS cName, S.Description AS eCode,  '' AS TestStandard,  '' AS ShipType,  '' AS dcRemark, C.Forshort
					FROM $DataIn.ch5_sampsheet S
					LEFT JOIN $DataIn.trade_object C ON S.COmpanyId = C.CompanyId
					WHERE S.Estate =  '1'";
			
	$waitShipResult = mysql_query($waitShipSql);
	while($myRow = mysql_fetch_assoc($waitShipResult))
	{
		$Id=$myRow["Id"];
		$OrderPO=$myRow["OrderPO"]==""?"":$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];
		$POrderId=$myRow["POrderId"];
		$companyId = $myRow["CompanyId"];
		$companyName = $myRow["Forshort"];
		$testStandard = $myRow["TestStandard"];
		
		$ProductId=$myRow["ProductId"]==""?"":$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$Price=number_format($myRow["Price"], 2);	
		$Amount=sprintf("%.2f",$Qty*$Price);
		$PackRemark=$myRow["PackRemark"];
		$PackRemark=$PackRemark==""?"":$PackRemark;
        $dcRemark=$myRow["dcRemark"]==""?"":$myRow["dcRemark"];
		$cName=$myRow["cName"]; 
		$eCode=$myRow["eCode"]; 
		$Type=$myRow["TYPE"];
        $ShipType=$myRow["ShipType"];
        
        $OrderPO=$Type==2?"随货项目":$OrderPO;
		$Locks=1;
		if($Type==1 && $POrderId!="201208290103")
		{//如果是订单：检查生产数量与需求数量是否一致，如果不一致，不允许选择
			//工序总数
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C 
			LEFT JOIN $DataIn.stufftype T ON C.TypeId=T.TypeId
			WHERE C.POrderId='$POrderId' AND T.Estate=1",$link_id));
			$scQty=$CheckscQty["scQty"];
			if($gxQty!=$scQty)
			{//生产完毕
				$LockRemark="生产登记异常！";
				$Locks=0;//不能操作
			}
			//检查领料记录 备料总数与领料总数比较
			$CheckblQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType<2",$link_id));
			$blQty=$CheckblQty["blQty"];
			$CheckllQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS llQty 
				FROM $DataIn.cg1_stocksheet G 										
				LEFT JOIN  $DataIn.ck5_llsheet K ON K.StockId = G.StockId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				WHERE G.POrderId='$POrderId' AND K.Estate=0",$link_id));
			
			$llQty=$CheckllQty["llQty"];
			//echo "$blQty!=$llQty";
			if($blQty!=$llQty)
			{//领料完毕
				$LockRemark.="领料异常！";
				$Locks=0;//不能操作
			}
		}
		
		$waitShip[] = array("companyId"=>"$companyId", "orderDate"=>"$OrderDate", "type"=>"$Type", "Id"=>"$Id", "orderPO"=>"$OrderPO", "POrderId"=>"$POrderId", "ProductId"=>"$ProductId", "Qty"=>"$Qty", "PackRemark"=>"$PackRemark", "cName"=>"$cName", "eCode"=>"$eCode", "TestStandard"=>"$testStandard", "ShipType"=>"$ShipType", "CompanyName"=>"$companyName", "LockRemark"=>"$LockRemark", "Lock"=>"$Locks", "Price"=>"$Price");
        
	}
	
	echo json_encode($waitShip);

?>