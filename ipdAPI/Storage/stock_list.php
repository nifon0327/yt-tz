<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$companyId = $_POST["CompanyId"];
	
	$mySql="SELECT A.POrderId, A.OrderPO,A.Qty,A.cName,A.TestStandard,A.ProductId,A.K1,A.K2,A.Leadtime,A.OrderDate  FROM (
					SELECT S.POrderId, S.OrderPO,S.Qty,P.cName,P.TestStandard,P.ProductId,SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2,PI.Leadtime,M.OrderDate,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty
					FROM  (SELECT Id,OrderNumber,POrderId,ProductId,OrderPO,Qty FROM $DataIn.yw1_ordersheet WHERE  Estate>0)S 
					LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
					LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
					LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
					LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
					LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
					LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
					LEFT JOIN ( 
						SELECT L.StockId,SUM(L.Qty) AS Qty 
						FROM $DataIn.yw1_ordersheet S 
						LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId 
						LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
						WHERE 1 AND S.Estate>0 AND L.StockId IS NOT NULL  GROUP BY L.StockId
						) L ON L.StockId=G.StockId 
					WHERE 1 
					AND M.CompanyId = '$companyId' 
					AND T.mainType<2 GROUP BY S.POrderId) A 
				WHERE K1>=K2 
				AND A.blQty!=A.llQty 
				ORDER BY  A.OrderDate";
		
		$eachStock = array();
		$stockHead = mysql_query($mySql);
		while($mainRows = mysql_fetch_assoc($stockHead))
		{
			
			//备料主单信息
			$blId=$mainRows["Id"];
			$POrderId=$mainRows["POrderId"];
			$cName=$mainRows["cName"];
			$TestStandard=$mainRows["TestStandard"];
			$ProductId=$mainRows["ProductId"];
			//include "../../admin/Productimage/getOnlyPOrderImage.php";
			$OrderPO=$mainRows["OrderPO"];
			$Qty=$mainRows["Qty"];
			$Leadtime=$mainRows["Leadtime"];
			$OrderDate=$mainRows["OrderDate"];
			include"../../admin/order_date.php";
			//如果超过30天
			$AskDay=AskDay($OrderDate);
			
			$OrderDate=CountDays($OrderDate,0);
			//加急订单锁定操作，整单锁和单个配件锁都不能备料
			$Lock_Result=mysql_fetch_array(mysql_query("SELECT POrderId FROM $DataIn.yw2_orderexpress   WHERE POrderId='$POrderId' AND Type='2'
                                  					    UNION ALL
                                  					    SELECT POrderId FROM (SELECT LEFT(GL.StockId,12) AS POrderId,GL.Locks 
                                  					    FROM $DataIn.cg1_lockstock GL,$DataIn.cg1_stocksheet G 
                                  					    WHERE GL.Locks=0 
                                  					    AND GL.StockId=G.StockId GROUP BY POrderId) K 
                                  					    WHERE K.POrderId='$POrderId'",$link_id));
            $newPOrderId=$Lock_Result["POrderId"];
            $Locks=$newPOrderId==""?1:0;
            $LockRemarks=$Locks==1?"":"订单已锁";
                       
            if ($LockRemarks!="" || $Locks==0) 
            {
	            continue;
            }           
                      
            //////           
            $checkTasksQty=mysql_query("SELECT Qty AS TasksQty FROM $DataIn.sc3_printtasks WHERE POrderId='$POrderId' AND (CodeType=1 OR CodeType=2 OR CodeType=4)",$link_id);
            if (mysql_num_rows($checkTasksQty)>0)
            {
				$TasksQty=mysql_result($checkTasksQty,0,"TasksQty");
			}
			else
			{
				$TasksQty=0;
			}

            //订单产品对应的配件信息
            $checkStockSql=mysql_query("SELECT G.OrderQty,G.StockId,K.tStockQty,D.StuffId,D.StuffCname,D.Picture,F.Remark,M.Name,P.Forshort,U.Name AS UnitName 
										FROM $DataIn.cg1_stocksheet G 
										LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
										LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										LEFT JOIN $DataPublic.staffmain M ON M.Number=G.BuyerId 
										LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
										LEFT JOIN $DataIn.base_mposition F ON F.Id=D.SendFloor
										LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
										LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
										WHERE G.POrderId='$POrderId' 
										AND T.mainType<2 
										ORDER BY D.SendFloor",$link_id);
			
			$listItems = array();
			while($checkStockRow=mysql_fetch_array($checkStockSql))
			{
				$llCount=0;
				$Name=$checkStockRow["Name"];
				$Forshort=$checkStockRow["Forshort"];
				$StockId=$checkStockRow["StockId"];
				$StuffId=$checkStockRow["StuffId"];
				$StuffCname=$checkStockRow["StuffCname"];
				$UnitName=$checkStockRow["UnitName"];
				$Picture=$checkStockRow["Picture"];
				$tStockQty=$checkStockRow["tStockQty"];
				$OrderQty=$checkStockRow["OrderQty"];
				$Remark=$checkStockRow["Remark"];
				
				$checkllQty=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
				$checkllQtyRow = mysql_fetch_assoc($checkllQty);
				$llQty = $checkllQtyRow["llQty"];
				
				$llQty=$llQty==""?0:$llQty;
		        $llQtyTemp=$OrderQty-$llQty;
		        $checkDisabled="";
		        
		        if ($llQtyTemp<=0)
		        {//是否已领料
					//$llCount+=1;//已领料数据
					$checkDisabled="disabled";
				}
				else
				{
					if($tStockQty<=0)
					{//判断在库量是否可进行领料
						$checkDisabled="disabled";
					}
					else
					{
						
					}
				}
                if($Locks==0)
                {//锁定订单和没标准图订单不能备料 
					$checkDisabled="disabled";
                }

		        

				$listItems[] = array("$StuffCname", "$Forshort", "$Name", "$Remark", "$tStockQty", "$OrderQty", "$llQty", "$Picture",  "$StockId", "$checkDisabled", "$StuffId");
				
			}
			
			$bl_cycleIpad = ($bl_cycleIpad == "0")?"当天":$bl_cycleIpad."day(s)";
			
			$eachStock[] = array(array("$OrderDate", "$OrderPO", "$cName", "$Qty", "$bl_cycleIpad", "$TestStandard", "$ProductId"), $listItems);
	}
	
	echo json_encode($eachStock);
	
?>