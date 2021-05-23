<?php 
session_start();
$Operator=$Login_P_Number;
$DateTemp=date("Y");
$Ids=$CG_mStockId;
$GetCidResult = $myPDO->query("SELECT CompanyId,BuyerId FROM $DataIn.cg1_stocksheet 
WHERE StockId IN ($Ids) LIMIT 1");
$GetCidRow = $GetCidResult->fetch(PDO::FETCH_ASSOC);

$CompanyId=$GetCidRow["CompanyId"];
$Number=$GetCidRow["BuyerId"];
$GetCidResult = null;
$GetCidRow = null;

//自动单号计算
$Bill_Temp=$myPDO->query("SELECT MAX(PurchaseID) AS maxID FROM $DataIn.cg1_stockmain WHERE PurchaseID LIKE '$DateTemp%'"); 
$BillRow = $Bill_Temp->fetch(PDO::FETCH_ASSOC);
$PurchaseID =$BillRow["maxID"];

if ($PurchaseID ){
	$PurchaseID =$PurchaseID+1;}
else{
	$PurchaseID =$DateTemp."0001";
}
$Bill_Temp = null;
$BillRow = null;


//保存主采购单资料
$CheckMidResult  = $myPDO->query("SELECT Id FROM  $DataIn.cg1_stockmain WHERE CompanyId ='$CompanyId' AND Date = '$Date'");
$CheckMidRow = $CheckMidResult->fetch(PDO::FETCH_ASSOC);
$Mid = $CheckMidRow["Id"];
if($Mid == ""){
	  $inRecode="INSERT INTO $DataIn.cg1_stockmain 
	  (Id,CompanyId,BuyerId,PurchaseID,DeliveryDate,Remark,Date,Operator)
	  VALUES (NULL,'$CompanyId','$Number','$PurchaseID','0000-00-00','系统生成','$Date','$Operator')";
	  $inAction = $myPDO->exec($inRecode);
	  $Mid=$myPDO->lastInsertId();
}

$CheckMidResult = null;
$CheckMidRow = "";

if($Mid>0){ 
	$Sql = "UPDATE $DataIn.cg1_stocksheet SET Mid='$Mid',Locks=0  WHERE StockId IN ($Ids) AND Mid='0' AND (AddQty+FactualQty)>0 ";
	$Result = $myPDO->exec($Sql);	
	if($Result){
		$Log.="需求单明细 ($Ids) 加入主采购单 $Mid 成功!<br>";
	 }
}

?>
