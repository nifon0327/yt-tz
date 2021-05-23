<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$stuffType = $_POST["stuffType"];
	//$stuffType = "组装";
	
	if($stuffType == "组装")
	{
		$typeIdResult = mysql_query("Select Parameter From $DataPublic.sc4_funmodule Where ModuleName = '$stuffType' Limit 1");
		$stuffTypeRow = mysql_fetch_assoc($typeIdResult);
		$stuffType = $stuffTypeRow["Parameter"];
		
		$mySql="SELECT A.*,C.Forshort, C.PickNumber,P.cName,P.TestStandard, P.Weight, P.eCode,PI.Leadtime FROM (
						SELECT 
						M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId, S.sgRemark, S.PackRemark,S.Qty,S.Price,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,
						SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2   
						FROM $DataIn.yw1_ordermain M
						LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						LEFT JOIN (
									 SELECT L.StockId,SUM(L.Qty) AS Qty 
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
									 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
									 WHERE  S.scFrom>0 AND S.Estate=1 and L.Estate=0 GROUP BY L.StockId
								 ) L ON L.StockId=G.StockId
						WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  GROUP BY S.POrderId 
						) A 
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId  
			            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=A.Id 
                        LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
					WHERE A.K1>=A.K2 AND A.blQty=A.llQty ORDER BY PI.Leadtime";
		
	}
	else
	{
		$stuffType = "other";
		
		$mySql="SELECT B.*,C.Forshort, C.PickNumber ,P.cName,P.TestStandard,PI.Leadtime,P.Weight, PI.Leadtime, P.eCode, U.Name as Unit FROM (
               SELECT A.*,IFNULL(L.Qty,0) AS scedQty,sum(IF(D.TypeId<>7100,G.OrderQty,0)) AS scQty,SUM(IF(D.TypeId<>7100,1,0)) AS gSign FROM (
						SELECT 
						M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,
						SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2 ,S.sgRemark, S.PackRemark,S.Estate,S.Locks
						FROM $DataIn.yw1_ordermain M
						LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						LEFT JOIN (
									 SELECT L.StockId,SUM(L.Qty) AS Qty 
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
									 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
									 WHERE  S.scFrom>0 AND S.Estate=1 and L.Estate=0  GROUP BY L.StockId
								 ) L ON L.StockId=G.StockId
						WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  GROUP BY S.POrderId 
						) A 
					   LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=A.POrderId 
			           LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId  
			           LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
                       LEFT JOIN (
									 SELECT S.POrderId,SUM(L.Qty) AS Qty 
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.sc1_cjtj L ON S.POrderId=L.POrderId
									 WHERE  S.scFrom>0 AND S.Estate=1 and L.TypeId<>7100 GROUP BY S.POrderId 
						 ) L ON A.POrderId=L.POrderId 
			           WHERE A.K1>=A.K2 AND A.blQty=A.llQty  AND ST.mainType=3  and ST.TypeId<>7100 GROUP BY A.POrderId 
			         )B 
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId  
			            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=B.Id 
                        LEFT JOIN $DataIn.productdata P ON P.ProductId=B.ProductId
                        LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
				WHERE B.scQty>B.scedQty AND B.gSign>0 ORDER BY OrderDate"; 
	}
				
	$products = array();		
	$registrListResult = mysql_query($mySql);
	while($registrRow = mysql_fetch_assoc($registrListResult))
	{
		$lockState = "";
		$POrderId = $registrRow["POrderId"];
		$qty = $registrRow["Qty"];
		$productId = $registrRow["ProductId"];
		$companyId = $registrRow["CompanyId"];
		$companyShort = $registrRow["Forshort"];
		$productName = $registrRow["cName"];
		$productEcode = $registrRow["eCode"];
		$orderPO = $registrRow["OrderPO"];
		$orderDate = $registrRow["OrderDate"];
		$POrderId = $registrRow["POrderId"];
		$productId = $registrRow["ProductId"];
		$qty = $registrRow["Qty"];
		$note = $registrRow["sgRemark"];
		$weight = $registrRow["Weight"];
		$TestStandardIpad = $registrRow["TestStandard"];
		$Unit = $registrRow["Unit"];
		$pickNumber = $registrRow["PickNumber"];
		$packRemark = $registrRow["PackRemark"];
		$piDate = str_replace("*", "", $registrRow["Leadtime"]);
		$piDate = date("Y-m-d", strtotime($piDate));
		
		$missionQeury = mysql_query("Select Operator From $DataIn.sc1_mission Where POrderId = '$POrderId'");
		$missionResult = mysql_fetch_assoc($missionQeury);
		$mission = $missionResult["Operator"];
		

		//处理标准图
		if($TestStandardIpad == "1")
		{
			$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id Limit 1",$link_id);
			if($checkteststandardRow = mysql_fetch_array($checkteststandard))
			{
				$TestStandardIpad = 3;
			}
		}

		$checkTypeSql = "SELECT B.TypeId, A.OrderQty, C.TypeName
						 FROM $DataIn.cg1_stocksheet A
						 LEFT JOIN $DataIn.stuffdata B ON B.stuffId = A.stuffId
						 Left Join $DataIn.stufftype C ON C.TypeId = B.TypeId
						 WHERE A.POrderId =  '$POrderId'
						 And C.mainType = 3
						 Order By B.TypeId Desc";
		
		//echo $checkTypeSql."<br>";		 
		//处理类型分类		
		$procsses = array();		 
		$checkTypeResult = mysql_query($checkTypeSql);
		if(mysql_num_rows($checkTypeResult) == 1 && $stuffType == "7100")
		{
			//对应组装项目
			$checkTypeRow = mysql_fetch_assoc($checkTypeResult);
			$processesTypeId = $checkTypeRow["TypeId"];
			$processesOrderQty = $checkTypeRow["OrderQty"];
			$thisProcessesName = $checkTypeRow["TypeName"];
			$thisProcessesQty = scTotle($POrderId, $processesTypeId, $DataIn);
			
			$procsses[] = array("typeName"=>"$thisProcessesName", "typeId"=>"$processesTypeId", "OrderQty" => "$processesOrderQty", "qty" => "$thisProcessesQty");
		}
		else
		{
			//对应加工项目
			while($checkTypeRow = mysql_fetch_assoc($checkTypeResult))
			{
			
				$processesTypeId = $checkTypeRow["TypeId"];
				$processesOrderQty = $checkTypeRow["OrderQty"];
				$thisProcessesName = $checkTypeRow["TypeName"];
				$thisProcessesQty = scTotle($POrderId, $processesTypeId, $DataIn);
				
				if($stuffType != "7100" && $processesTypeId == "7100")
				{
					
					continue;
				}
								
				if($stuffType == "7100" && $processesTypeId != "7100")
				{
					if($processesOrderQty == $thisProcessesQty)
					{
						continue;
					}
					else if($processesOrderQty > $thisProcessesQty)
					{
						$lockState = "processing";
					}
				}				
				
				$procsses[] = array("typeName"=>"$thisProcessesName", "typeId"=>"$processesTypeId", "OrderQty" => "$processesOrderQty", "qty" => "$thisProcessesQty");
				
			}
			
		}
								
		$FromWebPage="LBL";
	    include "../../admin/order_datetime.php";
	    $BlDate=$lbl_Date;
	    if($BlDate == "")
	    {
		    $BlDate = "0000-00-00 00:00:00";
	    }
	    
	    $workHours=$lbl_Hours==""?0:$lbl_Hours;
	    $BlDate=substr($BlDate, 0, 16);
	    
	    
	    $isLock = false;
	    if($stuffType == "7100")
	    {
	    	$checkcgLockSql=mysql_query("SELECT GL.Locks FROM $DataIn.cg1_stocksheet G
										 LEFT JOIN $DataIn.cg1_lockstock GL  ON G.StockId=GL.StockId 
										 WHERE  
										 G.POrderId='$POrderId' AND GL.Locks=0",$link_id);
			if(mysql_num_rows($checkcgLockSql) > 0)
			{
				$isLock = true;
			}
		}
		else
		{
			$checkcgLockSql=mysql_query("SELECT GL.Locks, B.TypeId
										 FROM $DataIn.cg1_stocksheet G
										 LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId = GL.StockId
										 LEFT JOIN $DataIn.stuffdata A ON A.StuffId = G.StuffId
										 LEFT JOIN $DataIn.stufftype B ON B.TypeId = A.TypeId
										 WHERE G.POrderId =  '$POrderId'
										 AND GL.Locks =0",$link_id);
			
			while($lockResutl = mysql_fetch_assoc($checkcgLockSql))
			{
				$lock = $lockResutl["TypeId"];
				if($lock = "7100")
				{
					$lockState = "lock";
					$isLock = true;
				}
				else
				{
					$isLock = true;
				}
			}
			
		}
	    //锁定
	    $checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=2 LIMIT 1",$link_id);
	    if($isLock || mysql_num_rows($checkExpress) > 0)
	    {
			$lockState = "lock";	
			//continue;	    
		}
		else if($lockState != "processing")
		{
		   	$lockState = "no";
	    }
	    
	    include("../../model/subprogram/weightCalculate.php");
	    		
		if($extraWeight == "error")
		{
			$extraWeight = $erorType;
		}
		
		//读取喷码格式(临时)
		$printFormatterSql = "Select Parameters,Value,qrFormat From $DataPublic.printformatter Where ClientId = 0 limit 1";
		$printResult = mysql_query($printFormatterSql);
		$printRow = mysql_fetch_assoc($printResult);
		$paramters = $printRow["Parameters"];
		$printValue = $printRow["Value"];
		$qrFormat = $printRow["qrFormat"];
		
		
		
		$overQty = scTotle($POrderId, "7100", $DataIn);
		$products[] = array("OrderPO"=>"$orderPO", "CName"=>"$productName", "ECode"=>"$productEcode", "Qty"=>"$qty", "Note"=>"$note", "OrederDate"=>"$orderDate", "TestStandar"=>"$TestStandardIpad", "ProductId"=>"$productId", "POrderId"=>"$POrderId", "companyId"=>"$companyId", "companyName"=>"$companyShort", "ProductType"=>"$productType", "BLDate"=>"$BlDate", "LockState" => "$lockState", "NetWeight"=>"$weight", "boxPcs"=>"$boxPcs"." PCS", "printFormatter" => "$paramters", "printValue" => "$printValue", "qrFormat"=> "$qrFormat", "pickerNumber"=>"$pickNumber", "Processes" => $procsses, "extraWeight"=>"$extraWeight", "packRemark" => "$packRemark", "OverQty" => "$overQty", "PI" => "$piDate", "mission"=>"$mission");
	
	}		
	
	echo json_encode($products);
	
?>

<?php
	
	function scTotle($POrderId, $TypeId, $DataIn)
	{
		//$scTotleSql = "Select Sum(Qty) As Totle From $DataIn.sc1_cjtj Where POrderId = '$POrderId' And TypeId = '$TypeId' Limit 1";
		$scTotleSql = "Select Sum(Qty) As Totle From $DataIn.sc1_cjtj Where POrderId = '$POrderId' And TypeId = '$TypeId' and Estate = '1'";
		//echo $scTotleSql."<br>/";
		$scTotleResult = mysql_query($scTotleSql);
		$scTotleRow = mysql_fetch_assoc($scTotleResult);
		
		$totleQty = ($scTotleRow["Totle"] != "")?$scTotleRow["Totle"]:"0";
		
		return $totleQty;
		
	}
	
?>