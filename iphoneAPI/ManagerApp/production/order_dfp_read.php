<?php
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/public/kqClass/Kq_dailyItem.php");
	include_once("$path/public/kqClass/Kq_otHourSet.php");
	$authority = array("10259","10200","10341","11965","10868",
	
						"10001","11606","11093","10369","10002","11010","11454","10782","11204","10043","10125","10224","10763");
	
	
	
	$chenLZ = (in_array($LoginNumber,$authority))?true: false;
	//$stuffType = $_POST["stuffType"];
	$curDate=date("Y-m-d");
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 $curWeek=$dateResult["NextWeek"];
	$typeIdResult = mysql_query("Select Parameter From $DataPublic.sc4_funmodule Where ModuleName = '$stuffType' Limit 1");
	$stuffTypeRow = mysql_fetch_assoc($typeIdResult);
	$stuffType = $stuffTypeRow["Parameter"];
		$OverTotalQty = $totalQty = $rowCount=0;
		$dfpList=array();
		
		$testCondi = "A.blQty=A.llQty";
		$testLm = "";
		$condi2= " S.Estate=1";
		/*
	$mySql="SELECT A.*,C.Forshort, C.PickNumber,P.cName,P.TestStandard,P.ProductId, P.Weight, P.eCode,PI.Leadtime,PIL.LeadTime as aLeadTime FROM
			(
				SELECT M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId, S.sgRemark, S.PackRemark,S.Qty,S.Price,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty, SUM(L.Estate) as llEstate,SUM(IF(G.OrderQty>0,1,0)) as count, S.ShipType
						FROM $DataIn.yw1_ordersheet S
						INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
						INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
						INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						INNER JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						LEFT JOIN (
									 SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(L.Estate) as Estate
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
									 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
									 WHERE  S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
								 ) L ON L.StockId=G.StockId
						WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  GROUP BY S.POrderId 
						) A 
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId  
			            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=A.Id 
			            LEFT Join $DataIn.yw3_pileadtime PIL On PIL.POrderId = A.POrderId
                        LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
					WHERE A.blQty=A.llQty ORDER BY C.Forshort , PI.Leadtime, A.Qty Desc ";
	*/
	$mySql="SELECT M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.sgRemark,S.PackRemark,S.Qty,S.Price,S.ShipType,C.Forshort, C.PickNumber,P.cName,P.TestStandard,P.ProductId, P.Weight, P.eCode,PI.Leadtime,PIL.LeadTime as aLeadTime,A.count,A.llEstate 
FROM(
                 SELECT S0.POrderId,S0.count,S0.llEstate,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty  
										   FROM (      
											           SELECT  S.POrderId,S.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(L.Estate) as llEstate,SUM(IF(G.OrderQty>0,1,0)) as count   
														FROM $DataIn.yw1_ordermain M
														LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
														LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
														LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
														LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
														LEFT JOIN  $DataIn.stuffmaintype SM ON SM.Id=ST.mainType  
														LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId  
													LEFT JOIN  	$DataIn.stuffproperty T on T.StuffId=G.StuffId AND T.Property='8'
				                        WHERE 1    AND S.scFrom>0 AND S.Estate=1 AND G.Level=1  AND SM.blSign=1 AND T.StuffId IS NULL   
											GROUP BY G.StockId 
										 )S0 
						GROUP BY S0.POrderId 
               )A 
                        LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
				        LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
			            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
                        LEFT JOIN $DataIn.yw3_pileadtime PIL On PIL.POrderId = S.POrderId 
                        LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
					WHERE A.blQty=A.llQty ORDER BY C.Forshort , PI.Leadtime, S.Qty Desc ";
					
	$products = array();		
	$registrListResult = mysql_query($mySql);
	while($registrRow = mysql_fetch_assoc($registrListResult))
	{
		$lockState = "";
		$POrderId = $registrRow["POrderId"];
		$llItemCountResult = mysql_query("Select Count(*) as count From $DataIn.ck5_llsheet Where POrderId = '$POrderId'");
		$llItemCountRow = mysql_fetch_assoc($llItemCountResult);
		$llItemCount = $llItemCountRow["count"];
		
		//
		//判断是否占用完毕
		$canUsed = "";
		$llEstate = $registrRow["llEstate"];
		$count = $registrRow["count"];
		//	if ($LoginNumber != "11965") {
		if($llEstate == "" || $llItemCount<$count){
			continue;
		}
		else if($llEstate == 0){
			$canUsed= "ready";
			//continue;
		}
		else if($llEstate > 0 && $llItemCount >= $count){
			$canUsed = "yes";	
		//continue;
		}
		else{
			continue;
		}
		//	}
		//$qty = $registrRow["Qty"];
		$productId = $registrRow["ProductId"];
		$companyId = $registrRow["CompanyId"];
		$companyShort = $registrRow["Forshort"];
		$productName = $registrRow["cName"];
		$productEcode = $registrRow["eCode"];
		$orderPO = $registrRow["OrderPO"];
		$orderDate = $registrRow["OrderDate"];
		$odDays=(strtotime($curDate)-strtotime($orderDate))/3600/24;
		$POrderId = $registrRow["POrderId"];
		
		$qty = $registrRow["Qty"];
		$note = $registrRow["sgRemark"];
		$weight = $registrRow["Weight"];
		$TestStandardIpad = $registrRow["TestStandard"];
		$Unit = $registrRow["Unit"];
		$pickNumber = $registrRow["PickNumber"];
		$packRemark = $registrRow["PackRemark"];
		
		
		 $AppFilePath="http://www.middlecloud.com/download/teststandard/T" .$productId.".jpg";
					    $Weight=(float)$weight;
                    $WeightSTR="";
                   
                      include "../../model/subprogram/weightCalculate.php";
                      if ($Weight>0){
	                       $extraWeight=$extraWeight == "error"?"":$extraWeight+($Weight*$boxPcs); 
	                       $WeightSTR=$Weight>0?"$productEcode|$Weight|$boxPcs|$extraWeight":"";
                      }
		$piLeadTimeHolder = ($registrRow["Leadtime"] == "")?$registrRow["aLeadTime"]:$registrRow["Leadtime"];
		$piDate = str_replace("*", "", $piLeadTimeHolder);
		$piDate = date("Y-m-d", strtotime($piDate));
		$shipType = $registrRow["ShipType"];
		$piWeekResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$piDate',1) AS Week",$link_id));
		$piWeek = $piWeekResult["Week"];
		$line = "";
		$mission = "";
		$missionQeury = mysql_query("Select B.GroupName From $DataIn.sc1_mission A
							   		INNER Join $DataIn.staffgroup B On B.Id = A.Operator
							   		
							   		Where A.POrderId = '$POrderId' And B.Estate = '1' Limit 1");
		
		if($missionResult = mysql_fetch_assoc($missionQeury)){
			//$mission = $missionResult["Name"];
			$mission = $missionResult["GroupName"];
			//$line = str_replace("组装", "Line ", $line);
		
		}
		//已有分配拉线 不在待分配里面
		if ($mission != "") {
			continue;
		}
		//处理标准图
		if($TestStandardIpad == "1"){
			$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id Limit 1",$link_id);
			if($checkteststandardRow = mysql_fetch_array($checkteststandard)){
				$TestStandardIpad = 3;
			}
		}

		$checkTypeSql = "SELECT B.TypeId, A.OrderQty, C.TypeName
						 FROM $DataIn.cg1_stocksheet A
						 INNER JOIN $DataIn.stuffdata B ON B.stuffId = A.stuffId
						 INNER Join $DataIn.stufftype C ON C.TypeId = B.TypeId
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
	    
	   /*
	    $workHours=$lbl_Hours==""?0:$lbl_Hours;
	    $BlDate=substr($BlDate, 0, 16);
	    
	    //$factoryCheck = "off";
	    if($factoryCheck == "on"){
			$BlDate = '';
		}
	    
	    $isLock = false;
	    if($stuffType == "7100"){
	    	$checkcgLockSql=mysql_query("SELECT GL.Locks FROM $DataIn.cg1_stocksheet G
										 LEFT JOIN $DataIn.cg1_lockstock GL  ON G.StockId=GL.StockId 
										 WHERE  
										 G.POrderId='$POrderId' AND GL.Locks=0",$link_id);
			if(mysql_num_rows($checkcgLockSql) > 0){
				$isLock = true;
			}
		}
		else{
			$checkcgLockSql=mysql_query("SELECT GL.Locks, B.TypeId
										 FROM $DataIn.cg1_stocksheet G
										 LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId = GL.StockId
										 LEFT JOIN $DataIn.stuffdata A ON A.StuffId = G.StuffId
										 LEFT JOIN $DataIn.stufftype B ON B.TypeId = A.TypeId
										 WHERE G.POrderId =  '$POrderId'
										 AND GL.Locks =0",$link_id);
			
			while($lockResutl = mysql_fetch_assoc($checkcgLockSql)){
				$lock = $lockResutl["TypeId"];
				if($lock = "7100"){
					$lockState = "lock";
					$isLock = true;
				}
				else{
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
		
	   */
	    
	    
		//读取喷码格式
		/*
		$printFormatterSql = "Select Parameters,Value,qrFormat From $DataPublic.printformatter Where ClientId = 0 limit 1";
		$printResult = mysql_query($printFormatterSql);
		$printRow = mysql_fetch_assoc($printResult);
		$paramters = $printRow["Parameters"];
		$printValue = $printRow["Value"];
		$qrFormat = $printRow["qrFormat"];
		
			
		
		 $tempArray=array(
                      "Id"=>"$POrderId",
                      "RowSet"=>array("bgColor"=>"$rowColor"),
                       "weeks"=>array("Text"=>"$Weeks","bg"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$ScLine"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                      "Col1"=> array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
					  "Col2"=> array("Text"=>$myRow["Forshort"],"Color"=>"#358FC1"),"Col3"=> array("Text"=>"$OrderPO"),
                      "Col4"=>array("Text"=>"$QtySTR","bgColor"=>"$FactualQty_Color"),
                      "Col5"=>array("Text"=>"$Date","Color"=>"#358FC1"),
                      "Remark"=>array("Text"=>"$Remark"),"icon4"=>"scdj_11",
                        "rTopTitle"=>array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
                       
                   );
				   $POrderId = $POrderId;
				
				   include "order_detail_items.php";
				   
                   $newData[]=array("Tag"=>"data","data"=>$tempArray,
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_gray","value"=>"1","Args"=>"$CompanyId|$ProductId"),"List"=>$products);
		*/
		 $BLDateTime = GetDateTimeOutString($BlDate,'');
		 $bgColor="";
		 if ($piWeek < $curWeek) {
			$OverTotalQty += $qty; 
			$bgColor= "#FF0000";
		} 
		$totalQty += $qty;
		
		$rowColor = $onHome == 1 ? "#F6F6F6":"";
		 $piWeek=substr($piWeek,4,2);
		    $TestStandard=$TestStandardIpad;
                    $TestStandard=$TestStandardIpad;
                    include "order/order_TestStandard.php";
					$swpDict = array();
					$dfpid = "nodata";
					if (  $chenLZ) {
						if ("#FFA500" == $TestStandardColor) {
							$swpDict=array("Right"=>"358FC1-分配");
							if ($LoginNumber == 11965 || $LoginNumber==10341) {
								$swpDict = array("Right"=>"FF0000-取消占用,358FC1-分配");
							}
							$dfpid = "dfp";
						} else {
						
							//$swpDict=array("Right"=>"FF0000-取消占用");,FF0000-取消占用
						}
						
					} 
										
					if ($hasOper==true && "#FFA500" != $TestStandardColor) {
						//配件领齐
						$checkLQEstate = mysql_query("select lqEstate from $DataIn.ck_dfp_pjlq where POrderId = '$POrderId'");
						$lqEstate = 0;
						if ($checkLQEstateRow = mysql_fetch_assoc($checkLQEstate)) {
							$lqEstate = (int)$checkLQEstateRow["lqEstate"];
						}
						if ($lqEstate==0) {
						
							$swpDict=array("Right"=>"358FC1-配件领齐");
							$dfpid = "dfpNew";
								$rowColor =  "#F6F6F6";
						}
						} 
		 $tempArray=array(
                      "Id"=>"$POrderId",
                      "RowSet"=>array("bgColor"=>"$rowColor"),
                       "weeks"=>array("Text"=>"$piWeek","bg"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$ScLine"),
                      "Title"=>array("Text"=>"$productName","Color"=>"#383e41"),
                      "Col1"=> array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
					  "Col2"=> array("Text"=>"$companyShort","Color"=>"#358FC1"),"Col3"=> array("Text"=>"$orderPO"),
                      "Col4"=>array("Text"=>"$qty"),"icon4"=>"scdj_11",
                      "Col5"=>array("Text"=>"$BLDateTime","Color"=>"#858888"),
                      
                       
                   );
				   $onEdit = 1;
				   $distriStar = true;
				 
				     //include "order_detail_items.php";
				   	$jsonArray[]=array("Tag"=>"data","data"=>$tempArray,"load"=>"",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_blue","value"=>"1"),"List"=>array(),"Swap"=>$swpDict,"CellID"=>"$dfpid","Args"=>"$POrderId","sbID"=>"qxzy",
				   "Tap"=>"0",
				   "TapImg"=>array("File"=>"$AppFilePath","Args"=>"$WeightSTR"),
				    "Picture"=>$TestStandard > 0 ? "1" : "",
				   "productImg"=>"http://www.ashcloud.com/$TestStandardIcon");
				   
				   $rowCount++;
		//$overQty = scTotle($POrderId, "7100", $DataIn);
		//$products[] = array("OrderPO"=>"$orderPO", "CName"=>"$productName", "ECode"=>"$productEcode", "Qty"=>"$qty", "Note"=>"$note", "OrederDate"=>"$orderDate", "TestStandar"=>"$TestStandardIpad", "ProductId"=>"$productId", "POrderId"=>"$POrderId", "companyId"=>"$companyId", "companyName"=>"$companyShort", "ProductType"=>"$productType", "BLDate"=>"$BlDate", "LockState" => "$lockState", "NetWeight"=>"$weight", "boxPcs"=>"$boxPcs"." PCS", "printFormatter" => "$paramters", "printValue" => "$printValue", "qrFormat"=> "$qrFormat", "pickerNumber"=>"$pickNumber", "Processes" => $procsses, "extraWeight"=>"$extraWeight", "packRemark" => "$packRemark", "OverQty" => "$overQty", "PI" => "$piDate", "mission"=>"$mission", "canUsed"=>"$canUsed", "PIWeek"=>"$piWeek", "ShipType"=>"$shipType", "line"=>"$line");
	
	}		
	$OverTotalQty =$OverTotalQty <= 0 ?"": number_format($OverTotalQty);
	$totalQty = number_format($totalQty);
	 //$totalQty=$rowCount>0?"$totalQty($rowCount)":$totalQty;
	 $dfpList = $jsonArray;
	$tempArray = array("Title"=>array("Text"=>"总计","FontSize"=>"14","Bold"=>"1"),"Col2"=>array("Text"=>"$OverTotalQty","Color"=>"#FF0000","FontSize"=>"14","Frame"=>"115,10,70,15"),"Col3"=>array("Text"=>$rowCount>0?"$totalQty($rowCount)":"$totalQty","FontSize"=>"14"));
		$tempArray2[] = array("Tag"=>"total","CellID"=>"total","data"=>$tempArray);
	//echo json_encode($products);
	array_splice($jsonArray,0,0,$tempArray2);
	$groupsSQL = "Select B.GroupName,B.Id as GPid, C.Name From $DataIn.staffgroup B 
				   Left Join $DataPublic.staffmain C On B.GroupLeader = C.Number
 				   Where B.Estate=1 and  B.TypeId = '7100' ";
	$groupsRs = mysql_query($groupsSQL);
	$listGroup = array();
	while ($groupsRow = mysql_fetch_array($groupsRs)) {
		$GroupName = $groupsRow["GroupName"];
		$GroupLeader = $groupsRow["GPid"];
		//$GroupName = substr($GroupName,1);
		$GroupName = str_replace("组装", "", $GroupName);
		//$GroupName = mb_substr($GroupName,2,1,'utf-8');
		$Name = $groupsRow["Name"];
		$listGroup[] = array($GroupName,$Name,$GroupLeader);
		
	}
	
  $jsonArray=array("cellList"=>$jsonArray,"groupList"=>$listGroup); 
?>

<?php
	
	function scTotle($POrderId, $TypeId, $DataIn)
	{
		//$scTotleSql = "Select Sum(Qty) As Totle From $DataIn.sc1_cjtj Where POrderId = '$POrderId' And TypeId = '$TypeId' and Estate = '1'";
		$scTotleSql = "SELECT SUM(S.Qty) AS Qty 
               FROM $DataIn.sc1_cjtj S 
               LEFT JOIN $DataIn.yw1_scsheet C ON C.sPOrderId=S.sPOrderId 
               WHERE S.POrderId = '$POrderId' and S.Estate = '1' AND C.ActionId=101
               ";
		//echo $scTotleSql."<br>/";
		$scTotleResult = mysql_query($scTotleSql);
		$scTotleRow = mysql_fetch_assoc($scTotleResult);
		
		$totleQty = ($scTotleRow["Totle"] != "")?$scTotleRow["Totle"]:"0";
		
		return $totleQty;
		
	}
	
?>