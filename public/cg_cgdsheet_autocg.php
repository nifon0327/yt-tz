<?php
 include "../basic/parameter.inc";
 
 

 
 $tempCG_mStockId = array();
 $CheckAutoCgResult =mysql_query("SELECT SUM(S.OrderQty) AS OrderQty,SC.mStockId,
 G.BuyerId,G.CompanyId,SC.sPOrderId
 FROM $DataIn.yw1_scsheet   SC  
 LEFT JOIN $DataIn.yw1_stocksheet  S ON SC.sPOrderId = S.sPOrderId
 LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId = SC.mStockId
 WHERE  SC.ActionId=105 AND SC.WorkShopId = 0  AND G.Mid = 0  GROUP BY SC.sPOrderId ",$link_id);
 while($CheckAutoCgRow=mysql_fetch_array($CheckAutoCgResult)){
	  $OrderQty = $CheckAutoCgRow["OrderQty"];
      $BuyerId = $CheckAutoCgRow["BuyerId"];
      $mStockId= $CheckAutoCgRow["mStockId"];
      $CompanyId= $CheckAutoCgRow["CompanyId"];
      $sPOrderId= $CheckAutoCgRow["sPOrderId"];
      
      $checkllRow=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE sPOrderId = '$sPOrderId'",$link_id));
      
	  $llQty = $checkllRow["llQty"];
       
      if($llQty==$OrderQty){
        $tempCG_mStockId[]    = $mStockId;
        
       }
 }
$CompanyId =  "100291";
$BuyerId =  "10777";

$CG_mStockId        = implode(",", $tempCG_mStockId);

$DateTemp=date("Y");
$Date = date("Y-m-d");

$BillRow=mysql_fetch_array(mysql_query("SELECT MAX(PurchaseID) AS maxID 
FROM $DataIn.cg1_stockmain WHERE PurchaseID LIKE '$DateTemp%'",$link_id)); 
$PurchaseID =$BillRow["maxID"];
if ($PurchaseID ){
	$PurchaseID =$PurchaseID+1;}
else{
	$PurchaseID =$DateTemp."0001";
}
 


//保存主采购单资料
$CheckMidRow  = mysql_fetch_array(mysql_query("SELECT Id FROM  $DataIn.cg1_stockmain WHERE CompanyId ='$CompanyId' AND Date = '$Date'",$link_id));
$Mid = $CheckMidRow["Id"];
if($Mid == ""){
	  $inRecode="INSERT INTO $DataIn.cg1_stockmain 
	  (Id,CompanyId,BuyerId,PurchaseID,DeliveryDate,Remark,Date,Operator)
	  VALUES (NULL,'$CompanyId','$BuyerId','$PurchaseID','0000-00-00','系统生成','$Date','$BuyerId')";
	  
	  $inAction = mysql_query($inRecode);
}


if($Mid>0){ 
	$Sql = "UPDATE $DataIn.cg1_stocksheet SET Mid='$Mid',Locks=0  WHERE StockId IN ($CG_mStockId) AND Mid='0' AND (AddQty+FactualQty)>0 ";
	$Result = mysql_query($Sql);	
	if($Result){
		$Log.="需求单明细 ($CG_mStockId) 加入主采购单 $Mid 成功!<br>";
	 }
}


 
?>