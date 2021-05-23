<?php
	
include "../../basic/downloadFileIP.php";
	$stuffOutArray = array();
	$curDate=date("Y-m-d");
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS ThisWeek",$link_id));
		
		$thisWeek = $dateResult["ThisWeek"];
	$sendOutStuffSql = "Select A.StuffId, C.ProductId, C.POrderId, D.StuffCName, S.CompanyId, P.Forshort, B.OrderQty, C.OrderPO, B.StockId, D.Picture, M.OrderDate,PS.TestStandard,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,
	YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks,PQ.Forshort as pqForshort,B.StuffId as  bStuffId
						From $DataIn.yw1_ordersheet C
						INNER Join $DataIn.cg1_stuffunite A On C.ProductId = A.ProductId
						Left Join $DataIn.yw1_ordermain M ON M.OrderNumber=C.OrderNumber
						LEFT JOIN $DataIn.productdata PS ON PS.ProductId=C.ProductId
						Left Join $DataIn.cg1_stocksheet B On B.POrderId = C.POrderId
						Left Join $DataIn.stuffdata D On D.StuffId = A.StuffId
						Left Join $DataIn.stuffType E On E.TypeId = D.TypeId
						LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=C.Id
		                Left Join $DataIn.yw3_pileadtime PL On PL.POrderId = C.POrderId
						Left Join $DataIn.bps S On S.StuffId = A.StuffId
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
						LEFT JOIN $DataIn.trade_object PQ ON PQ.CompanyId=M.CompanyId
						Where C.scFrom != '0'
						And C.Estate != '0'
						And E.mainType < 2
						And E.mainType < 2  AND E.TypeId<>9033 AND E.TypeId<>9066   
						Group by C.POrderId Order by S.CompanyId ";
	//echo $sendOutStuffSql;
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
		$bStuffId =  $sendOutStuffRow["bStuffId"];
		$mainStuffName = $sendOutStuffRow["StuffCName"];
		$companyId = $sendOutStuffRow["CompanyId"];
		
		$mainQty = $sendOutStuffRow["OrderQty"];
		$orderPO = $sendOutStuffRow["OrderPO"];
		$picture = $sendOutStuffRow["Picture"];
		$OrderDate = $sendOutStuffRow["OrderDate"];
		$Weeks = $sendOutStuffRow["Weeks"];
		$TestStandard=$sendOutStuffRow["TestStandard"];
         include "order/order_TestStandard.php";
		  $BlDate = GetDateTimeOutString($OrderDate,'');
				 
			$canCheck = 1;
			if ($bStuffId!="") {
				
				$checkNew = mysql_query("select tStockQty from $DataIn.ck9_stocksheet where StuffId=$bStuffId");
				if ($checkNewRow = mysql_fetch_array($checkNew)) {
					if ($checkNewRow["tStockQty"]>0) {
						$canCheck = 0;
						echo("cancheck ??? $canCheck \n")
					}
				}
				
				
			}
			
		$qtyCheckSql = "SELECT B.OrderQty, IFNULL( SUM( L.Qty ) , 0 ) AS llQty, K.tStockQty
						FROM $DataIn.cg1_stuffunite A
						LEFT JOIN $DataIn.cg1_stocksheet B ON B.StuffId = A.uStuffId 
						LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = B.StockId
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = A.uStuffId
						WHERE A.POrderId = '$POrderId' AND A.ProductId =  '$ProductId' and  A.StuffId =  '$mainStuffId'";
		$qtyCheckResult = mysql_query($qtyCheckSql);
		$qtyCheckRow =mysql_fetch_assoc($qtyCheckResult);
		$totleLLQty = $qtyCheckRow["llQty"];
		$totleOrderQty = $qtyCheckRow["OrderQty"];
		$totleTStockQty = $qtyCheckRow["tStockQty"];
		
		//$orderQtySql = mysql_query("Select Sum(OrderQty) as")
//orderqtysql = mys	
		//echo "ll:$totleLLQty  order:$totleOrderQty   tStock:$totleTStockQty   $POrderId  $ProductId<br>";
		
		if($totleLLQty == $totleOrderQty ||($totleLLQty != $totleOrderQty && $totleOrderQty > $totleTStockQty) )
/*

if ($totleTStockQty <= 0)
*/
		{
			if ($canCheck==1)
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
		//echo $getUnionStuffSql."<br>";
		$unionStuffResult = mysql_query($getUnionStuffSql);
		while($unionStuffRow = mysql_fetch_assoc($unionStuffResult))
		{
			$uStuffId = $unionStuffRow["uStuffId"];
			$uStuffName = $unionStuffRow["StuffCname"];
			$uPicture = $unionStuffRow["Picture"];
            //$StockId = $unionStuffRow["StockId"];
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

/*
	$llStockQtySql = mysql_fetch_assoc(mysql_query("Select Sum(Qty) as llQty From $DataIn.ck5_llsheet Where StuffId = '$uStuffId' and StockId = '$uStockId' and Estate=0"));
			$llQty = $llStockQtySql["llQty"] == ""?"0":$llStockQtySql["llQty"];

*/
		
			
	$StuffProp="";
   if ($uStuffId==114133 || $uStuffId==127622 || $uStuffId==129301 || $uStuffId==126088 ){
        $StuffProp="gysc1";
   } else//$StuffProp="gys$Property";
			$StuffProp="gys".$unionStuffRow["Property"];
$ImagePath=$picture>0?"$donwloadFileIP/download/stufffile/".$uStuffId. "_s.jpg":"";
		$tempdata = array("Title"=>array("Text"=>"$uStuffName","Color"=>"$StuffColor"),"Col2"=>array("Text"=>"$orderQty"),"Col3"=>array("Text"=>"$tStockQty"),"Picture"=>"$ImagePath","Prop"=>"$StuffProp","star"=>"1");
       		//$products[] = array("Tag"=>"aessery","$Date", "$StockId", "$StuffCname", "$OrderQty", "$rkQty", "$tStockQty", "$llQty", "$scQty", "$Buyer", "$Position", "$StuffId", "$Picture");
		
			$unionStuffArray[] = array("Tag"=>"acessery","data"=>$tempdata,"Args"=>"$uStockId|$uStuffId" ,"CellID"=>"subb",
			"onTap"=>array("Value"=>"$picture","File"=>"$ImagePath"),"onEdit"=>"$onEdit","Swap"=>array("Right"=>"0099FF-备料"));
            //是否可备料
        //   $unionStuffArray[] = array("uStuffName"=>"$uStuffName", "uStuffId"=>"$uStuffId", "orderQty"=>"$orderQty", "blQty"=>"$llQty", "tStockQty"=>"$tStockQty", "uPicture"=>"$uPicture", "StockId"=>"$uStockId");			
		}	 
		
		if(count($unionStuffArray)  == 0 ){
			continue;
		}

		//$stuffOutArray[] = array("mainStuffName"=>"$mainStuffName", "mainStuffId"=>"$mainStuffId", "ProductId"=>"$ProductId", "POrderId"=>"$POrderId", "CompanyId"=>"$companyId", "CompanyName"=>"$cmpanyName", "Qty"=>"$mainQty", "OrderPO"=>"$orderPO", "Picture"=>"$picture","unionStuffs"=>$unionStuffArray);
		$bgColor = $thisWeek > $Weeks ? "#FF0000":"";
		$OverTotalQty +=  $thisWeek > $Weeks ? $mainQty:0;
				$Weeks = $Weeks >0 ? substr($Weeks,4,2) : "";
		$tempArray=array(
                      "Id"=>"$POrderId",
                      //"RowSet"=>array("bgColor"=>"$rowColor"),
                       "weeks"=>array("Text"=>"$Weeks","bg"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$ScLine"),
                      "Title"=>array("Text"=>"$mainStuffName","Color"=>"$TestStandardColor"),
                      //"Col1"=> array("Text"=>"$odDays"."d","Color"=>"#0000FF"),
					    "Col2"=> array("Text"=>"$compoName","Color"=>"#0099FF"),"Col3"=> array("Text"=>"$orderPO"),
                      "Col4"=>array("Text"=>"$mainQty","bgColor"=>"$FactualQty_Color"),
                      "Col5"=>array("Text"=>"$BlDate","Color"=>"#0099FF"),"icon4"=>"scdj_11"
                      //"Remark"=>array("Text"=>"$Remark"),"icon4"=>"scdj_11",
                        //"rTopTitle"=>array("Text"=>"$odDays"."d","Color"=>"#0000FF"),
                       
                   );
				   
			$rowCount ++;
			
			$totalQty += $mainQty;
			$cmpanyName = $sendOutStuffRow["Forshort"];
		if ($lastComId == $companyId) {
			//$stuffOutArray[$sectionCount]["List"][]=array();
			
				   if (1==1) {
					     $stuffOutArray[$sectionCount-1]["List"][]=array("Tag"=>"data","data"=>$tempArray,"CellID"=>"data1",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_gray","value"=>"0","Args"=>"$companyId|$ProductId"),"List"=>"[]");//"Swap"=>array("Right"=>"FF0000-备料")
				   $stuffOutArray[$sectionCount-1]["List"]=array_merge($stuffOutArray[$sectionCount-1]["List"],$unionStuffArray);
				   $stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"]+=$mainQty;
				   $aaa = $stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"];
				   $stuffOutArray[$sectionCount-1]["data"]["Col3"]["Text"]="$aaa";
				   }
				 
				   
		} else {
		if (1==1) {
			$tempSection = array("Title"=>array("Text"=>"$cmpanyName","Color"=>"#0099FF","Frame"=>"16,10,100,15"),
							      "Col3"=>array("Text"=>"$mainQty")
									);
			$stuffOutArray[]=array("List"=>array(),
					               "Tag"=>"total","CellID"=>"Total0",
								    "data"=>$tempSection,
									"onTap"=>array("value"=>"1","hidden"=>"1","CellID"=>"sec"));
									//$stuffOutArray[$sectionCount]["Col3"]["Text"]+=$mainQty;
									 $stuffOutArray[$sectionCount]["List"][]=array("Tag"=>"data","data"=>$tempArray,"CellID"=>"data1",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_gray","value"=>"0","Args"=>"$companyId|$ProductId"),"List"=>"[]");//"Swap"=>array("Right"=>"FF0000-备料")
				   
				  $stuffOutArray[$sectionCount]["List"]= array_merge($stuffOutArray[$sectionCount]["List"],$unionStuffArray);
			$sectionCount++;
			}
		}
		
		$lastComId = $companyId;

	}
/*
	$ir = 0;
	foreach ($stuffOutArray as $aee) {
		if ($aee["Tag"] == "") {
			$stuffOutArray[$ir]["Tag"] = "data";
			$stuffOutArray[$ir]["CellID"] = "nodata";
		}
		$ir ++;
	}
*/
	$tempArray2= array();
		 $totalQty = number_format($totalQty);
 $OverTotalQty = $OverTotalQty >0 ? number_format($OverTotalQty):"";
		  $totalQty=$rowCount>0?"$totalQty($rowCount)":$totalQty;
        $tempArray = array("Title"=>array("Text"=>"总计","FontSize"=>"14","Bold"=>"1"),"Col2"=>array("Text"=>"$OverTotalQty","Color"=>"#FF0000","FontSize"=>"14","Frame"=>"115,10,70,15"),"Col3"=>array("Text"=>"$totalQty","FontSize"=>"14"));
		$tempArray2[] = array("Tag"=>"total","CellID"=>"total","data"=>$tempArray);
		array_splice($stuffOutArray,0,0,$tempArray2);
		if ($totalQty<=0) {
			$stuffOutArray = array();
		}
$jsonArray=array("cellList"=>$stuffOutArray);
	
?>