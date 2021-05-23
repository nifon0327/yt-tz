<?php
  $DateTemp = date("Y");
  $Date=date("Y-m-d");
  $Operator = $Login_P_Number;
  echo $mStockId;
  if($mStockId>0){
	  
		$GetcgStockRow = mysql_fetch_array(mysql_query("SELECT CompanyId,BuyerId FROM $DataIn.cg1_stocksheet WHERE StockId IN ($mStockId) LIMIT 1",$link_id));
		$cgCompanyId=$GetcgStockRow["CompanyId"];
		$cgBuyerId=$GetcgStockRow["BuyerId"];

		//自动单号计算
		$Bill_TempRow=mysql_fetch_array(mysql_query("SELECT MAX(PurchaseID) AS maxID FROM $DataIn.cg1_stockmain WHERE PurchaseID LIKE '$DateTemp%'",$link_id)); 
		$cgPurchaseID =$Bill_TempRow["maxID"];
		
		if ($cgPurchaseID ){
			$cgPurchaseID =$cgPurchaseID+1;}
		else{
			$cgPurchaseID =$DateTemp."0001";
		}
        
        
        
		 //保存主采购单资料
		$CheckMidRow  = mysql_fetch_array(mysql_query("SELECT Id FROM  $DataIn.cg1_stockmain WHERE CompanyId ='$CompanyId' AND Date = '$Date'",$link_id));
		$thisCgMid = $CheckMidRow["Id"];
		if($thisCgMid == ""){
			  $inStockMainSql="INSERT INTO $DataIn.cg1_stockmain 
			  (Id,CompanyId,BuyerId,PurchaseID,DeliveryDate,Remark,Date,Operator)
			  VALUES (NULL,'$cgCompanyId','$cgBuyerId','$cgPurchaseID','0000-00-00','系统生成','$Date','$Operator')";
			  $inStockMainAction = mysql_query($inStockMainSql);
			  $thisCgMid=mysql_insert_id();
		}
		

		if($thisCgMid>0){ 
		
			$updateStocksheetSql = "UPDATE $DataIn.cg1_stocksheet SET Mid='$thisCgMid',Locks=0 WHERE StockId IN ($mStockId)  AND Mid='0' AND (AddQty+FactualQty)>0 ";
			$updateStockSheetResult = mysql_query($updateStocksheetSql);
			if($updateStockSheetResult){
		         //按设置的交货周期更新交货日期
		        $CheckSql  = "SELECT G.Id,S.jhDays,T.jhDays AS TypeJhDays  
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
				WHERE G.StockId IN ($mStockId)";
				$CheckjhDayResult=mysql_query($CheckSql,$link_id);
			    while($CheckjhDayRow = mysql_fetch_array($CheckjhDayResult)){
			           $stockSheetId=$CheckjhDayRow["Id"];
				       $jhDays=$CheckjhDayRow["jhDays"]==0?$CheckjhDayRow["TypeJhDays"]:$CheckjhDayRow["jhDays"];
				       $DeliveryDate=date("Y-m-d",strtotime("$Date  +$jhDays  day"));
		               
				       $weekRow = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$DeliveryDate',1) AS DeliveryWeek",$link_id));
				       $DeliveryWeek = $weekRow["DeliveryWeek"];
				       
				       $DeliveryDateSql = "UPDATE $DataIn.cg1_stocksheet SET DeliveryDate=' $DeliveryDate',DeliveryWeek ='$DeliveryWeek' WHERE Id='$stockSheetId'";
		               $DeliveryDateResult = mysql_query($DeliveryDateSql);                
		         }
			 }
		}
	  
  }

?>