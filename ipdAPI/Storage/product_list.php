<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$typeId = $_POST["TypeId"];
	//$typeId = "7100";
	$companyId = $_POST["CompanyId"];
	//$companyId = "1004";
	$productTypeId = $_POST["ProductTypeId"];
	//$productTypeId = "8049";
	$Login_P_Number = $_POST["Operator"];
	
	$productList = array();
	$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.pRemark,P.TypeId
			FROM $DataIn.yw1_ordermain M
			LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
			LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
			WHERE 1  
			AND A.TypeId='$typeId' 
			AND S.scFrom>0 
			AND S.Estate>0 
			AND M.CompanyId='$companyId'
			AND P.TypeId='$productTypeId'
			ORDER BY M.OrderDate";
	//echo $mySql;
	$productResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($productResult))
	{
		$OrderPO = $myRow["OrderPO"];
		
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandardIpad=$myRow["TestStandard"];
		include "../../admin/Productimage/getProductImage.php";
		
		$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id",$link_id);
		if($checkteststandardRow = mysql_fetch_array($checkteststandard))
		{
			$TestStandardIpad = "3";
		}
		$scdays = "";
		$ShipType=$myRow["ShipType"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$sgRemark=$myRow["sgRemark"]==""?"":$myRow["sgRemark"];
		$OrderDate=$myRow["OrderDate"];
        include "../../admin/order_date.php";
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../../images/cj$AskDay'";	
		$OrderDate=CountDays($OrderDate,0);
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];
			
		$sumQty=$sumQty+$Qty;
		
		//订单状态色：有未下采购单，则为白色
		$checkColor=mysql_query("SELECT G.Id FROM $DataIn.cg1_stocksheet G 
								 LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
								 LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
								 WHERE 1 
								 AND T.mainType<2 
								 AND G.Mid='0' 
								 and (G.FactualQty>'0' OR G.AddQty>'0' ) 
								 and G.PorderId='$POrderId' LIMIT 1",$link_id);
								 
		if($checkColorRow = mysql_fetch_array($checkColor))
		{
			//$OrderSignColor="bgColor='#69B7FF'";//有未下需求单
			$czSign=0;//不能操作
		}
		else
		{//已全部下单	
			//$OrderSignColor="bgColor='#FFCC00'";	//设默认绿色
			//生产数量与工序数量不等时，黄色
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//工序总数
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
														FROM $DataIn.cg1_stocksheet G
														LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
														LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
														WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			//WHERE G.POrderId='$POrderId' AND A.TypeId<8000",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C 
													   LEFT JOIN $DataIn.stufftype T ON C.TypeId=T.TypeId
													   WHERE C.POrderId='$POrderId' 
													   AND T.Estate=1 ",$link_id));
			$scQty=$CheckscQty["scQty"];
	
			if($gxQty==$scQty)
			{//生产完毕
				//$OrderSignColor="bgColor='#339900'";
				$czSign=0;//不能操作
			}
				////////////////////////////////////////////////////////////////
		}
		
		// 有料才可以登记产量
        $BlSign=0;
        $CheckblResult=mysql_query("SELECT SUM(G.OrderQty) AS blQty ,IFNULL(SUM(K.Qty),0) AS llQty
									FROM $DataIn.cg1_stocksheet G
									LEFT JOIN $DataIn.ck5_llsheet K ON K.StockId=G.StockId
									LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
									LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
									WHERE G.POrderId='$POrderId' 
									AND T.mainType<2  
									GROUP BY G.StuffId",$link_id);
        if($CheckblRow=mysql_fetch_array($CheckblResult))
        {
        	do
        	{
			      $blQty=$CheckblRow["blQty"];
                  $llQty=$CheckblRow["llQty"];
                  if($llQty==0)
                  {
	                  $BlSign++;
                  }
                  
            }while($CheckblRow=mysql_fetch_array($CheckblResult));
        }
        $ColbgColor="";
		//加急订单
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress))
		{
			do
			{
				$Type=$checkExpressRow["Type"];
				switch($Type)
				{
					case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
					case 2:$ColbgColor="bgcolor='#FF0000'";$czSign=0;break;		//未确定产品
					case 7:$theDefaultColor="#FFA6D2";break;		//加急
				}
			}while ($checkExpressRow = mysql_fetch_array($checkExpress));
		}
		
		//此工序总数
		$CheckStuffQty=mysql_fetch_array(mysql_query("SELECT ifnull(SUM(G.OrderQty),0) AS sQty 
													  FROM $DataIn.cg1_stocksheet G
													  LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
													  WHERE G.POrderId='$POrderId' 
													  AND A.TypeId='$TypeId'",$link_id));
		$SumGXQty=$CheckStuffQty["sQty"];
		//已完成的工序数量
		$CheckCfQty=mysql_fetch_array(mysql_query("SELECT ifnull(SUM(C.Qty),0) AS cfQty 
												   FROM $DataIn.sc1_cjtj C 
												   WHERE C.POrderId='$POrderId' 
												   AND C.TypeId='$TypeId'",$link_id));
		$OverPQty=$CheckCfQty["cfQty"];
		
		//已生产数字显示方式
		switch($OverPQty)
		{
			case 0:
			{
				$OverPQty="0";
			}
			break;
			default://生产数量非0
			{
				if($SumGXQty==$OverPQty)
				{//生产完成
					//$OverPQty="<div class='greenB'>$OverPQty</div>";
					$czSign=0;//不能操作
				}
				else
				{
					if($SumGXQty>$OverPQty)
					{//未完成
						//$OverPQty="<div class='yellowB'>$OverPQty</div>";
					}
					else
					{//多完成
						//$OverPQty="<div class='redB'>$OverPQty</div>";
					}
				}
			}
			break;
		}
		
		$registerPermission= "no";
		if($czSign==1)
		{//可以操作
			if($SubAction==31  && ($Login_TypeId==$TypeId || ($Login_TypeId==7100 && $TypeId==7040)  ||  ($Login_TypeId==7030 && $TypeId==7100) ||  ($Login_TypeId==7020 && $TypeId==7090) || $Login_P_Number==10023 || $Login_P_Number==10871 || $Login_P_Number==11203 || $Login_P_Number==10369  || $Login_P_Number==10200 ||  $Login_P_Number==10868 ||($Login_P_Number==10782 && $TypeId=7050) ||  ($Login_P_Number==11319 && $TypeId=7090)))
			{//有权限:需要是该类别下的小组成员，方有权登记
		    	if($BlSign==0) 
                {//有料才可以登记产量
                    $registerPermission = "yes";
                   }
            }
        }
        
       /*
 if($Estate!=1)
        {//生产完毕
			$UpdateIMG="";
			$UpdateClick="bgcolor='#339900'";
		}
*/
		
		$scdays = ($iPhone_scdays == "")?"": $iPhone_scdays."days";
		$productList[] = array("OrderPO"=>"$OrderPO", "CName"=>"$cName", "ECode"=>"$eCode", "Ship"=>"$ShipType", "Qty"=>"$Qty", "Note"=>"$sgRemark", "scCycle"=>"$scdays", "OrederDate"=>"$OrderDate", "OverQty"=>"$OverPQty", "TestStandar"=>"$TestStandardIpad", "ProductId"=>"$ProductId", "CheckDisable"=>"", "POrderId"=>"$POrderId", "registerPermission"=>"$registerPermission");
        
	}
	
	echo json_encode($productList);
	
?>