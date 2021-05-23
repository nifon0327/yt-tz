<?php

	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/public/kqClass/Kq_dailyItem.php");

		//echo $mySql;
		$eachStock = array();
		$ProductArray=array();

		$curDate=date("Y-m-d");
		$nextWeekDate=date("Y-m-d",strtotime("$curDate  +7day"));
		$nexNextWeekDate = date("Y-m-d",strtotime("$curDate  +14day"));
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek, YEARWEEK('$curDate',1) AS ThisWeek, YEARWEEK('$nexNextWeekDate',1) AS nextNextWeek",$link_id));
		$nextWeek=$dateResult["NextWeek"];
		$thisWeek = $dateResult["ThisWeek"];
		$nextNextWeek = $dateResult["nextNextWeek"];
		$SearchRows = " AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) < $nextNextWeek";
		//$SearchRows = '';
		$myOrderSql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark
,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,E.Type ,C.CompanyId, C.Forshort, YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
		Left Join $DataIn.yw3_pileadtime PL On PL.POrderId = S.POrderId
		LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId  
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
		WHERE S.scFrom>0 AND S.Estate=1 
		$SearchRows
		GROUP BY S.POrderId ORDER BY Weeks,S.ProductId,S.Id";
		//echo $myOrderSql;
		$myOrderResult = mysql_query($myOrderSql);
		while($myOrderRow = mysql_fetch_assoc($myOrderResult))
		{
			$blId=$myOrderRow["Id"];
			$OrderPO=toSpace($myOrderRow["OrderPO"]);
			$POrderId=$myOrderRow["POrderId"];
			//备料主单信息
			$cName=$myOrderRow["cName"];
			$TestStandard=$myOrderRow["TestStandard"];
			$ProductId=$myOrderRow["ProductId"];
			$Qty=$myOrderRow["Qty"];
			$Leadtime = $myOrderRow['Leadtime'];
			if($Leadtime == ""){
				continue;
			}

			// if($myOrderRow['Weeks'] == ""){
			// 	continue;
			// }

			$piDate = str_replace("*", "", $Leadtime);
			$Leadtime= substr($Leadtime, 5, 5);
			$OrderDate=$myOrderRow["OrderDate"];
			$productName = $myOrderRow["cName"];
			$companyId = $myOrderRow["CompanyId"];
			$companyShort = $myOrderRow["Forshort"];
			$weight = $myOrderRow["Weight"];
			$Unit = $myOrderRow["Unit"];
			$eCode = $myOrderRow["eCode"];
			$R_EType = $myOrderRow["Type"]==2?2:0;

			$hasLine = "";
			$line = "";
			$missionPartSql = "Select B.GroupName, C.Name From $DataIn.sc1_mission A
							   Left Join $DataIn.staffgroup B On B.Id = A.Operator
							   Left Join $DataPublic.staffmain C On B.GroupLeader = C.Number
							   Where A.POrderId = '$POrderId'
							   And B.Estate = '1' Limit 1";
			$missionPartResult = mysql_query($missionPartSql);
			while($missionRow = mysql_fetch_assoc($missionPartResult))
			{
				$line = $missionRow["GroupName"];
				$name = $missionRow["Name"];

				$hasLine = str_replace("组装", "Line ", $line);
				$line= $hasLine;
				$hasLine = $hasLine."-".$name;
			}

			//计算pi为第几周
			//echo "SELECT YEARWEEK('$piDate',1) AS Week";
			$piWeekResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$piDate',1) AS Week",$link_id));
			$piWeek = $piWeekResult["Week"];
			//是否已存在有可备料订单
			//$ProductArray[]=$ProductId;

			$isBlLockState = "no";
			if($piWeek == $nextNextWeek)
			{
				$isBlLockSql = mysql_query("Select Locks From $DataIn.yw9_blunlock Where WeekName = '$piWeek'");
				if(mysql_num_rows($isBlLockSql)==0)
				{
					$isBlLockState = "yes";
				}
				else
				{
					$isBlLockResult = mysql_fetch_assoc($isBlLockSql);
					$blLockState = $isBlLockResult["Locks"];
					if($blLockState == "1")
					{
						$isBlLockState = "yes";
					}
				}
			}

			//检查订单备料情况
			$CheckblState="
				SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IF(GL.Id>0,1,0)) AS  Locks,SUM(IFNULL(L.llEstate,0)) AS llEstate  
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
				LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
				LEFT JOIN ( 
				    SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate 
				    FROM  $DataIn.cg1_stocksheet G 
				    LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
				    WHERE  G.POrderId='$POrderId'  GROUP BY L.StockId 
				  )L ON L.StockId=G.StockId 
				WHERE G.POrderId='$POrderId' AND ST.mainType<2";

			$stockHead = mysql_query($CheckblState);
			if($mainRows = mysql_fetch_assoc($stockHead))
			{

				$mainBlQty = $mainRows["blQty"];
				$mainLlQty = $mainRows["llQty"];

				$R_K1 = $mainRows["K1"];
				$R_K2 = $mainRows["K2"];
				$R_blQty = $mainRows["blQty"];
				$R_llQty = $mainRows["llQty"];
				$R_Locks = $mainRows["Locks"];
				$R_llEstate = $mainRows["llEstate"];


				if ($R_blQty==$R_llQty)
				{
					if ($R_llEstate==0)
					{
						continue;
					}
			    }
				else
				{

			        //是否已存在有可备料订单
				    if(in_array($ProductId, $ProductArray))
				    {
				    	continue;
				    }


			        if ($R_EType==2)
			        {
			        continue;}

					if ($R_K1>=$R_K2 &&  $R_blQty!=$R_llQty && $R_Locks==0)
					{
					    $ProductArray[] = $ProductId;
					}
					else
					{
						//$ProductArray[] = $ProductId;
						continue;
					}
			    }


				//echo "$productId bl:$mainBlQty ll:$mainLlQty <br>";
				if($TestStandard == "1")
				{
					$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id Limit 1",$link_id);
					if($checkteststandardRow = mysql_fetch_array($checkteststandard))
					{
						$TestStandard = 3;
					}
				}

				$canLlState = "yes";
				if($mainBlQty != $mainLlQty)
				{
					$canLlState = "no";
				}
				else
				{
					if($hasLine == "")
					{
						continue;
					}
				}


				$boxSql = "SELECT D.Spec,A.Relation   
						   FROM $DataIn.pands A,$DataIn.stuffdata D 
						   where A.ProductId='$ProductId' 
						   AND D.TypeId = '9040' 
						   and D.StuffId=A.StuffId 
						   ORDER BY A.Id";
				$boxResult = mysql_query($boxSql);
				$boxRow = mysql_fetch_assoc($boxResult);
				$relation = explode("/", $boxRow["Relation"]);
				$boxPcs = ($relation[1] == "")?"-":$relation[1];

				$bl_cycleIpad = "";
				include"../../admin/order_date.php";
				//如果超过30天
				//$AskDay=AskDay($OrderDate);

				//$OrderDate=CountDays($OrderDate,0);
				//加急订单锁定操作，整单锁和单个配件锁都不能备料
				$Lock_Result=mysql_query("SELECT POrderId FROM $DataIn.yw2_orderexpress   WHERE POrderId='$POrderId' AND Type='2'
                                  					    UNION ALL
                                  					    SELECT POrderId FROM (SELECT LEFT(GL.StockId,12) AS POrderId,GL.Locks 
                                  					    FROM $DataIn.cg1_lockstock GL,$DataIn.cg1_stocksheet G 
                                  					    WHERE GL.Locks=0 
                                  					    AND GL.StockId=G.StockId GROUP BY POrderId) K 
                                  					    WHERE K.POrderId='$POrderId'");

				$isLockState = "no";
			    if (mysql_num_rows($Lock_Result) > 0)
				{
   	            	$isLockState = "yes";
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
            	$checkStockSql=mysql_query("SELECT G.OrderQty,G.StockId,K.tStockQty,D.StuffId,D.StuffCname,D.Picture,D.TypeId,F.Remark,M.Name,P.Forshort,U.Name AS UnitName ,T.mainType
										FROM $DataIn.cg1_stocksheet G 
										LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
										LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										LEFT JOIN $DataPublic.staffmain M ON M.Number=G.BuyerId 
										LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
										LEFT JOIN $DataIn.base_mposition F ON F.Id=D.SendFloor
										LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
										LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
										WHERE G.POrderId='$POrderId' 
										AND ((T.mainType in (0,1)) or (T.mainType = 5 and D.TypeId = 9124))
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
					$TypeId = $checkStockRow["TypeId"];
					$mainType = $checkStockRow["mainType"];

		        	$checkllQty=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
					$checkllQtyRow = mysql_fetch_assoc($checkllQty);
					$llQty = $checkllQtyRow["llQty"];
					$llQty=$llQty==""?0:$llQty;

					$OrderQty -= $llQty;

					$blInfomationSql = "SELECT A.Date, C.Name
		        						FROM $DataIn.yw9_blmain A
										LEFT JOIN $DataIn.ck5_llsheet B ON A.Id = B.Pid
										LEFT JOIN $DataPublic.staffmain C ON C.Number = A.Operator
										Where B.StockId = '$StockId'
										Order By A.Date Desc
										Limit 1";

					$blInfomationResult = mysql_query($blInfomationSql);
					$blInfomationRow = mysql_fetch_assoc($blInfomationResult);
					$blDate = substr($blInfomationRow["Date"], 0, 16);
					$blOperator = $blInfomationRow["Name"];

					if($TasksQty==1 && $Forshort=="研砼条码")
					{
			        	$Remark = "";
					}
					else if($Forshort=="研砼条码" && $TasksQty>0)
					{
			        	$Remark = "printed";
					}

					//是否已经领料
					$hadLl = "no";
					$llCheckSql = "SELECT B.Date, B.Time, C.Name
		        			   	   FROM $DataIn.ck5_llsheet A
							   	   LEFT JOIN $DataIn.ck5_llmain B ON B.id = A.Mid
							   	   LEFT JOIN $DataPublic.staffmain C ON C.Number = B.Operator
							   	   WHERE A.StockId =  '$StockId' Limit 1";
					$llCheckResult = mysql_query($llCheckSql);
					$llCheckRow = mysql_fetch_assoc($llCheckResult);
					$llOperator = $llCheckRow["Name"];
					$llDateTime = "";
					if($llOperator)
					{
			        	$llDateTime = $llCheckRow["Date"]." ".$llCheckRow["Time"];
						$llDateTime = substr($llDateTime, 0, 16);
						$hadLl = "yes";
					}

					if($canLlState == "yes" && $llQty > 0)
					{
			        	$hadLl = "no";
					}

					//引入isOccupy,isGet区分配件当前状态
					$isOccupy = "no";
					$isGet = "no";
					$totleEstate = "";
					$stuffStateSql = mysql_query("Select Estate From $DataIn.ck5_llsheet Where StockId = '$StockId'");
					if(mysql_num_rows($stuffStateSql) > 0)
					{
						$isOccupy = "yes";
						while($stuffStateResult = mysql_fetch_assoc($stuffStateSql))
						{
							$totleEstate += $stuffStateResult["Estate"];
						}
						if($totleEstate != "" && $totleEstate == 0)
						{
							$isGet = "yes";
						}
					}

					if($factoryCheck == "on"){
							$llDateTime = "";
							$blDate = "";
					}


					$listItems[] = array("stuffName" => "$StuffCname", "suppliedName" => "$Forshort", "note"=>"$Remark", "tQty"=>"$tStockQty", "orderQty" => "$OrderQty", "llQty" => "$llQty", "picture" => "$Picture",  "stockId" => "$StockId", "check" => "$checkDisabled", "stuffId" => "$StuffId", "blDate"=>"$blDate", "blOperator"=>"$blOperator", "llOperator"=>"$llOperator", "llDateTime"=>"$llDateTime", "hadLl" => "$hadLl", "TypeId"=>"$TypeId", "isOccupy"=>"$isOccupy", "isGet"=>"$isGet", "mainType"=>"$mainType");

				}

				//$bl_cycleIpad = ($bl_cycleIpad == "0")?"当天":$bl_cycleIpad."day(s)";

				$eachStock[] = array(array("OrderDate" => "$OrderDate", "OrderPO" => "$OrderPO", "productName" => "$cName", "qty" => "$Qty", "blCycle" => "$OrderDate", "testStandard" => "$TestStandard", "productId" => "$ProductId", "companyId" => "$companyId", "comapnyName" =>"$companyShort", "pOrderId" => "$POrderId", "NetWeight"=>"$weight", "boxPcs"=>"$boxPcs ".$Unit, "eCode"=>"$eCode", "canLlState"=>"$canLlState", "isLock"=>"$isBlLockState", "PIDate"=>"$piDate", "PIWeek"=>"$piWeek", "hasLine"=>"$hasLine", "line"=>"$line"), $listItems);
			}
		}


		echo json_encode($eachStock);
		//print_r($eachStock);
?>