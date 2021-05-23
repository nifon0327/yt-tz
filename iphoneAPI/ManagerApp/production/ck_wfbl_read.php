<?php
	
	include "../../basic/downloadFileIP.php";
	$stuffOutArray = array();
	$curDate=date("Y-m-d");
		
	$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS ThisWeek",$link_id));
		
	$thisWeek = $dateResult["ThisWeek"];
	$sendOutStuffSql = "Select A.StuffId, C.ProductId, C.POrderId, D.StuffCName, S.CompanyId, P.Forshort, B.OrderQty, Y.OrderPO, B.StockId, D.Picture, M.OrderDate,PS.TestStandard,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,
	YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks,PQ.Forshort as pqForshort
						From $DataIn.yw1_ordersheet C
						INNER Join $DataIn.yw1_ordermain M ON M.OrderNumber=C.OrderNumber
						INNER JOIN $DataIn.productdata PS ON PS.ProductId=C.ProductId
						INNER Join $DataIn.cg1_stocksheet B On B.POrderId = C.POrderId
						INNER Join $DataIn.pands_unite A On C.ProductId = A.ProductId
						INNER Join $DataIn.stuffdata D On D.StuffId = A.StuffId
						INNER Join $DataIn.stuffType E On E.TypeId = D.TypeId
						LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=C.Id
		                Left Join $DataIn.yw3_pileadtime PL On PL.POrderId = C.POrderId
						Left Join $DataIn.stuffproperty F On F.StuffId = A.uStuffId
						Left Join $DataIn.bps S On S.StuffId = A.StuffId
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
						LEFT JOIN $DataIn.trade_object PQ ON PQ.CompanyId=M.CompanyId
						Left Join $DataIn.yw1_ordersheet Y On Y.POrderId = C.POrderId 
						Where C.scFrom != '0'
						And C.Estate != '0'
						And E.mainType < 2  AND E.TypeId<>9033 AND E.TypeId<>9066   
						Group by C.POrderId Order by S.CompanyId,Weeks ";
	//echo $sendOutStuffSql;//						And F.property is NULL
	$sendOutStuffResult = mysql_query($sendOutStuffSql);
	$lastComId = "-111";
	$sectionCount = 0;
	$sectionQty = 0;	$rowCount = $OverTotalQty = $totalQty = 0;
	while($sendOutStuffRow = mysql_fetch_assoc($sendOutStuffResult))
	{
		$compoName = $sendOutStuffRow["pqForshort"];
		$mainStuffId = $sendOutStuffRow["StuffId"];
		$ProductId = $sendOutStuffRow["ProductId"];
		$POrderId = $sendOutStuffRow["POrderId"];
		$mainStuffName = $sendOutStuffRow["StuffCName"];
		$companyId = $sendOutStuffRow["CompanyId"];
		
		$mainQty = $sendOutStuffRow["OrderQty"];
		$orderPO = $sendOutStuffRow["OrderPO"];
		$Picture = $sendOutStuffRow["Picture"];
		$OrderDate = $sendOutStuffRow["OrderDate"];
		$Weeks = $sendOutStuffRow["Weeks"];
		$TestStandard=$sendOutStuffRow["TestStandard"];
                    include "order/order_TestStandard.php";
		  $BlDate = GetDateTimeOutString($OrderDate,'');
				 

		$qtyCheckSql = "SELECT SUM( B.OrderQty ) AS OrderQty, IFNULL( SUM( L.Qty ) , 0 ) AS llQty, SUM( K.tStockQty ) AS tStockQty
						FROM $DataIn.pands_unite A
						LEFT JOIN $DataIn.cg1_stocksheet B ON B.StuffId = A.uStuffId 
						LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = B.StockId
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = A.uStuffId
						WHERE A.ProductId =  '$ProductId' and B.POrderId = '$POrderId' AND A.StuffId =  '$mainStuffId'";
		$qtyCheckResult = mysql_query($qtyCheckSql);
		$qtyCheckRow =mysql_fetch_assoc($qtyCheckResult);
		$totleLLQty = $qtyCheckRow["llQty"];
		$totleOrderQty = $qtyCheckRow["OrderQty"];
		$totleTStockQty = $qtyCheckRow["tStockQty"];
		
		if($totleLLQty == $totleOrderQty ||($totleLLQty != $totleOrderQty && $totleOrderQty > $totleTStockQty) ) {
			continue;
		}
		
		//获取关联的配件
		$unionStuffArray = array();
		$getUnionStuffSql = "SELECT U.uStuffId, S.StuffCname, T.mainType, S.Picture,P.Property
							 FROM $DataIn.pands_unite U
							 LEFT JOIN $DataIn.stuffdata S ON S.StuffId = U.uStuffId
							 LEFT JOIN $DataIn.stufftype T ON T.TypeId = S.TypeId
							 LEFT JOIN  $DataIn.stuffproperty P ON P.StuffId=U.uStuffId
							 WHERE U.ProductId = '$ProductId'
							 AND U.StuffId = '$mainStuffId'
							 GROUP BY U.uStuffId
							 ORDER BY T.mainType";

		$unionStuffResult = mysql_query($getUnionStuffSql);
		while($unionStuffRow = mysql_fetch_assoc($unionStuffResult))
		{
			
			$uStuffId = $unionStuffRow["uStuffId"];
			$uStuffName = $unionStuffRow["StuffCname"];
			$uPicture = $unionStuffRow["Picture"];

			 $StuffColor="#000000";
			 switch ($uPicture){
			           case  1: $StuffColor="#FFA500";break;
			           case  2: $StuffColor="#FF00FF";break;
			           case  4: $StuffColor="#FFD800";break;
			           case  7: $StuffColor="#0033FF";break;
			 }
            //获取数量
            $orderQtySql = mysql_fetch_assoc(mysql_query("Select OrderQty, StockId From $DataIn.cg1_stocksheet Where StuffId='$uStuffId' and POrderId = '$POrderId'"));
            $orderQty = $orderQtySql["OrderQty"];

            if($orderQty == ""){
            	continue;
            }
            $uStockId = $orderQtySql["StockId"];

            $tStockQtySql = mysql_fetch_assoc(mysql_query("Select tStockQty From $DataIn.ck9_stocksheet Where StuffId = '$uStuffId'"));
			$tStockQty=$tStockQtySql["tStockQty"];
	
	$StuffProp="";
   if ($uStuffId==114133 || $uStuffId==127622 || $uStuffId==129301 || $uStuffId==126088 ){
        $StuffProp="gysc1";
   } else
		$StuffProp="gys".$unionStuffRow["Property"];
			
	$ImagePath=$uPicture>0?"$donwloadFileIP/download/stufffile/".$uStuffId. "_s.jpg":"";
	$tempdata = array("Title"=>array("Text"=>"$uStuffName","Color"=>"$StuffColor"),"Col2"=>array("Text"=>"$orderQty"),"Col3"=>array("Text"=>"$tStockQty"),"Picture"=>"$ImagePath","Prop"=>"$StuffProp","star"=>"1");
		
	$unionStuffArray[] = array("Tag"=>"acessery","data"=>$tempdata,"Args"=>"$uStockId|$uStuffId" ,"CellID"=>"subb",
			"onTap"=>array("Value"=>"$uPicture","File"=>"$ImagePath"),"onEdit"=>"$onEdit","Swap"=>array("Right"=>"358FC1-备料"));
       			
		}	 
		$TMPcount = count($unionStuffArray) ;
		if($TMPcount  == 0 ){
			continue;
		}
		
       $unionStuffArray[$TMPcount-1]["isLast"] = 1;
		$bgColor = $thisWeek > $Weeks ? "#FF0000":"";
		$OverTotalQty +=  $thisWeek > $Weeks ? $mainQty:0;
				$Weeks = $Weeks >0 ? substr($Weeks,4,2) : "";
	    
	
	    include "submodel/stuffname_color.php";
		$tempArray=array(
                      "Id"=>"$POrderId",
                      //"RowSet"=>array("bgColor"=>"$rowColor"),
                       "weeks"=>array("Text"=>"$Weeks","bg"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$ScLine"),
                      "Title"=>array("Text"=>"$mainStuffName","Color"=>"$StuffColor"),
                      //"Col1"=> array("Text"=>"$odDays"."d","Color"=>"#0000FF"),
					    "Col2"=> array("Text"=>"$compoName","Color"=>"#358FC1"),"Col3"=> array("Text"=>"$orderPO"),
                      "Col4"=>array("Text"=>"$mainQty","bgColor"=>"$FactualQty_Color"),
                      "Col5"=>array("Text"=>"$BlDate","Color"=>"#858888"),"icon4"=>"scdj_11" 
                   );
				   
			$rowCount ++;
			
			$totalQty += $mainQty;
			$cmpanyName = $sendOutStuffRow["Forshort"];
		if ($lastComId == $companyId) {
			
				   $stuffOutArray[$sectionCount-1]["List"][]=array("Tag"=>"data","data"=>$tempArray,"CellID"=>"data1",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_gray","value"=>"0","Args"=>"$companyId|$ProductId"),"List"=>"[]");//"Swap"=>array("Right"=>"FF0000-备料")
				   $stuffOutArray[$sectionCount-1]["List"]=array_merge($stuffOutArray[$sectionCount-1]["List"],$unionStuffArray);
				   $stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"]+=$mainQty;
				   $aaa = $stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"];
				   $stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"]="$aaa";
				   $stuffOutArray[$sectionCount-1]["N"]++;
		} else {
		
			$tempSection = array("Title"=>array("Text"=>"$cmpanyName","Color"=>"#358FC1","Frame"=>"16,10,100,15","FontSize"=>"13.5"),
							      "Col3"=>array("Text"=>"$mainQty")
									);
			$stuffOutArray[]=array("List"=>array(),
					               "Tag"=>"total","CellID"=>"Total0","N"=>1,
								    "data"=>$tempSection,
									"onTap"=>array("value"=>"1","hidden"=>"1","CellID"=>"sec"));
									 $stuffOutArray[$sectionCount]["List"][]=array("Tag"=>"data","data"=>$tempArray,"CellID"=>"data1",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_gray","value"=>"0","Args"=>"$companyId|$ProductId"),"List"=>"[]");//"Swap"=>array("Right"=>"FF0000-备料")
				   
				  $stuffOutArray[$sectionCount]["List"]= array_merge($stuffOutArray[$sectionCount]["List"],$unionStuffArray);
				  if ($sectionCount > 0) {
					   $stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"]=number_format($stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"])."(".$stuffOutArray[$sectionCount-1]["N"].")";
				  }
			$sectionCount++;
		}
		
		$lastComId = $companyId;

	}
	$tempArray2= array();
	$stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"]=number_format($stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"])."(".$stuffOutArray[$sectionCount-1]["N"].")";
 $totalQty = number_format($totalQty);
 $OverTotalQty = $OverTotalQty >0 ? number_format($OverTotalQty):"";
 $totalQty=$rowCount>0?"$totalQty($rowCount)":$totalQty;
 $tempArray = array("Title"=>array("Text"=>"总计","FontSize"=>"14","Bold"=>"1"),"Col2"=>array("Text"=>"$OverTotalQty","Color"=>"#FF0000","FontSize"=>"14","Frame"=>"115,10,70,15"),"Col3"=>array("Text"=>"$totalQty","FontSize"=>"14"));
 $tempArray2[] = array("Tag"=>"total","CellID"=>"total","data"=>$tempArray);
 array_splice($stuffOutArray,0,0,$tempArray2);
			
 $jsonArray=array("cellList"=>$stuffOutArray);
	
?>