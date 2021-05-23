<?php
	
	include "../../basic/parameter.inc";
	
	$POrderId = $_POST["POrderId"];
	//$POrderId = "201211020402";
	
	$products = array();
	
	$checkProduct=mysql_query("SELECT Y.ProductId,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,C.Forshort AS Client,P.cName,P.TestStandard    
        					   FROM $DataIn.yw1_ordersheet Y
        					   LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        					   LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        					   LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
        					   WHERE Y.POrderId='$POrderId'",$link_id);
    
    								
    while($checkProductRows = mysql_fetch_assoc($checkProduct))
    {    	
	    $ProductId = $checkProductRows["ProductId"];
	    $OrderPO = $checkProductRows["OrderPO"];
	    $PQty = $checkProductRows["PQty"];
	    $Client = $checkProductRows["Client"];
	    $cName = $checkProductRows["cName"];
	    $TestStandard = $checkProductRows["TestStandard"];
	    $PackRemark = $checkProductRows["PackRemark"] == ""?"--":$checkProductRows["PackRemark"];
	    $sgRemark = $checkProductRows["sgRemark"] == ""?"--":$checkProductRows["sgRemark"];
	    $ShipType = $checkProductRows["ShipType"] == ""?"--":$checkProductRows["ShipType"];
	    $Leadtime = $checkProductRows["Leadtime"] == "0000-00-00"?"--":$checkProductRows["Leadtime"];
	    
	    $lineTwoText = "订单PO:$OrderPO      业务流水号:$POrderId      数量:$PQty";
	    $products[] = array("$Client:$cName", $lineTwoText, "$ProductId", "$TestStandard");
	    
	    $sListResult = mysql_query("SELECT 
	S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,
	M.Date,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.TypeId,B.Name,C.Forshort,C.Currency,MP.Remark AS Position,ST.mainType,MT.TypeColor ,K.tStockQty 
									FROM $DataIn.cg1_stocksheet S
									LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
									LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
									LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
									LEFT JOIN $DataIn.base_mposition MP ON MP.Id=ST.Position 
									LEFT JOIN $DataPublic.staffmain B ON B.Number=S.BuyerId
									LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
									LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
									WHERE S.POrderId='$POrderId' ORDER BY S.StockId",$link_id);
	    
	    while($StockRows = mysql_fetch_assoc($sListResult))
	    {
	    	$scQty="-";
	    
	    	$Mid=$StockRows["Mid"];
	    	$thisId=$StockRows["Id"];
	    	$StockId=$StockRows["StockId"];
	    	$StuffCname=$StockRows["StuffCname"];
	    	$Position=$StockRows["Position"]==""?"未设置":$StockRows["Position"];
	    	$Price=$StockRows["Price"];
	    	$Forshort=$StockRows["Forshort"];
	    	$Buyer=$StockRows["Name"];
	    	$BuyerId=$StockRows["BuyerId"];
	    	$OrderQty=$StockRows["OrderQty"];
	    	$StockQty=$StockRows["StockQty"];
	    	$FactualQty=$StockRows["FactualQty"];
	    	$AddQty=$StockRows["AddQty"];
	    	$Date=$StockRows["Date"];
	    	$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
	    	$DeliveryDate=$StockRows["DeliveryDate"];		
	    	$StuffId=$StockRows["StuffId"];
	    	$Picture=$StockRows["Picture"];
	    	$TypeId=$StockRows["TypeId"];
	    	$mainType=$StockRows["mainType"];
	    	$TypeColor=$StockRows["TypeColor"];
	    	$Currency=$StockRows["Currency"];
	    	$tStockQty=$StockRows["tStockQty"];
        
	    	if($FactualQty==0 && $AddQty==0)
	    	{
				$TempColor=3;			//绿色
				$Date="使用库存";
				$FactualQty="-";$AddQty="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";//$Buyer="-";$Forshort="-";
			}
			else
			{
				if($Date=="")
				{//未下采购单
					if($mainType==1)
					{
						$TempColor=1;		//白色
						$Date="未下采购单";
					}
					else
					{		//统计项目:8000以下黄色，	8000-9000绿色
						if($mainType==3)
						{
							//生产数量
							$scSql=mysql_query("SELECT ifnull(SUM(S.Qty),0) AS scQty
											FROM $DataIn.sc1_cjtj S
											LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
											LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId 
											WHERE 1 AND G.StockId='$StockId' AND D.TypeId=S.TypeId",$link_id); 
							$scQty=mysql_result($scSql,0,"scQty");				
							$TempColor=$OrderQty==$scQty?3:2;
							$Date="生产项目";
						}
						else
						{
							$Date="统计项目";
							$TempColor=3;		//绿色
						}
						//$Date="统计项目";
						$Position="-";
					}	
					$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";
				}
				else
				{//已下采购单
					$TempColor=3;		//绿色
					$ReceiveDate=$StockRows["ReceiveDate"];
					//收货情况				
					$rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
					$rkQty=mysql_result($rkTemp,0,"Qty");
					$Mantissa=$FactualQty+$AddQty-$rkQty;
                                        
					if ($Mantissa>0)
					{
                		$rkQty=$rkQty; 
                	}
                	else
                	{
                		$rkQty=$rkQty; 
                	}
						
						//可更新交期,如果当前浏览者的ID与采购的ID一致，则可以更新交期
					//if($BuyerId==){}
					if($DeliveryDate=="0000-00-00"){$DeliveryDate="-";}
				}
			}
		
			//备领料情况
			$llQty=0;$llBgColor="";$llEstate="";
			if($mainType==1) 
			{	
				$checkllQty=mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
				$llQty=mysql_result($checkllQty,0,"llQty");
				$llQty=$llQty==""?0:$llQty;
				if($llQty>$OrderQty)
				{//领料总数大于订单数,提示出错
					$llBgColor="";
				}
				else
				{
					if($llQty==$OrderQty)
					{//刚好全领，绿色
						$llBgColor="";
					}
					else
					{				//未领完，黄色
						$llBgColor="";
					}
				}
				$llEstate=mysql_result($checkllQty,0,"llEstate");
			}
	    
			//库存数量    
			if($mainType==1) 
			{    
        		if ($tStockQty>=$OrderQty-$llQty)
        		{
            		$tStockQty=$tStockQty;
            	}
            	else
            	{
            		$tStockQty=$tStockQty;
            	}
            }
            else
            {
       			$tStockQty="-"; 
       		}  
       
       		$llQty=$llQty==0?"-":$llQty;
       
       		//加工工序
       		$CheckProcessSql=mysql_query("SELECT A.Id FROM $DataIn.process_bom A  WHERE A.ProductId='$ProductId' AND A.StuffId='$StuffId' LIMIT 1",$link_id);
       		if($CheckProcessRow=mysql_fetch_array($CheckProcessSql))
       		{}
       
       		$products[] = array("$Date", "$StockId", "$StuffCname", "$OrderQty", "$rkQty", "$tStockQty", "$llQty", "$scQty", "$Buyer", "$Position", "$StuffId", "$Picture");
	   }
	    
    }
    
    echo json_encode($products);
	
?>