<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$companyId = $_POST["companyId"];
	$stuffs = array();
	
	$mySql="SELECT M.PurchaseID,M.Date,M.Id,S.StuffId,S.StockId,S.AddQty,S.FactualQty,P.Forshort,B.Name,D.StuffCname,M.BuyerId,D.Picture
			FROM $DataIn.cg1_stockmain M
			LEFT JOIN $DataIn.cg1_stocksheet S ON M.Id=S.Mid
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
			LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId= D.StuffId AND OP.Property=2
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.staffmain B ON M.BuyerId=B.Number
			WHERE 1
			AND S.rkSign>0 
			AND ( S.Mid>0   OR   (S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0))) 
			ORDER BY M.BuyerId,M.Id";
	
	$myResult = mysql_query($mySql);
	while($mainRows = mysql_fetch_assoc($myResult))
	{
		$Mid=$mainRows["Id"];
		$Date=$mainRows["Date"];
		$PurchaseID=$mainRows["PurchaseID"];
		$Forshort=$mainRows["Forshort"];
		$Buyer=$mainRows["Name"];
		$BuyerId = $mainRows["BuyerId"];
		$picture = $mainRows["Picture"];
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!="")
		{			
			$StuffCname=$mainRows["StuffCname"];
			$StockId=$mainRows["StockId"];
			$FactualQty=$mainRows["FactualQty"];
			$AddQty=$mainRows["AddQty"];			
			$CountQty=$FactualQty+$AddQty;
			
			//收货数量计算
			$ReQty_Temp=mysql_query("SELECT SUM(Qty) AS a1 FROM $DataIn.ck1_rksheet WHERE StockId='$StockId'",$link_id);
			$ReQty=mysql_fetch_assoc($ReQty_Temp);
			$ReQty = $ReQty["a1"];
			$Unreceive=$CountQty-$ReQty;
		}
		
		if($Unreceive > 0)
		{
			$stuffs[] = array("Id"=>"$Mid", "Date"=>"$Date", "PurchaseID"=>"$PurchaseID", "Buyer"=>"$Buyer", "BuyerId"=>"$BuyerId", "StuffId"=>"$StuffId", "StuffName"=>"$StuffCname", "StockId"=>"$StockId", "FactualQty"=>"$FactualQty", "AddQty"=>"$AddQty", "CountQty"=>"$CountQty", "UnReceive"=>"$Unreceive", "Picture"=>"$picture");
		}
	}
	
	echo json_encode($stuffs);
	
?>