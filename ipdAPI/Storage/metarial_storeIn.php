<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once('../../FactoryCheck/FactoryClass/AttendanceDatetype.php');
	
	$companyId = $_POST["companyId"];
	//$companyId = "2064";
	
	$month = $_POST["month"];
	//$month = "2014-03";
	//$factoryCheck = "yes";
	$rkBills = array();
	//处理出目标月份
	$StartDate=$month."-01";
	$EndDate=date("Y-m-t",strtotime($month));
	$billItems = array();
	$rkBillSql = "Select A.BillNumber, A.Locks, A.Operator, A.Date, A.Id
				 From $DataIn.ck1_rkmain A
				 Where A.CompanyId = '$companyId'
				 And ((A.Date>'$StartDate' and A.Date<'$EndDate') OR A.Date='$StartDate' OR A.Date='$EndDate')
				 Order by A.Date Desc ";
	//echo $rkBillSql;
	$rkBillResult = mysql_query($rkBillSql);
	while($rkBillRow = mysql_fetch_assoc($rkBillResult))
	{
		$billNumber = $rkBillRow["BillNumber"];
		$billLock = $rkBillRow["Locks"];
		$billOperator = $rkBillRow["Operator"];
		$billDate = $rkBillRow["Date"];
		
		
		//$factoryCheck = "off";
		if($factoryCheck != "on"){
            /************加入过滤***************/
            $Number = $billOperator;
            $sheet = new WorkScheduleSheet($Number, substr($billDate, 0, 10), $attendanceTime['start'], $attendanceTime['end']);
            $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
            $datetype = $datetypeModle->getDatetype($Number, substr($billDate, 0, 10), $sheet);
            if($datetype['morning'] != 'G' && $datetype['afternoon'] != 'G'){
                continue;
            }
            $Date = substr($billDate, 0, 10);
		}
		
		
				
		$billId = $rkBillRow["Id"];
		
		$billItemSql = "SELECT S.Id,S.Mid,S.gys_Id,S.StockId,S.StuffId,S.Qty,S.Locks,
						D.StuffCname,D.TypeId,D.Picture,G.POrderId,G.FactualQty+G.AddQty AS cgQty, G.FactualQty, G.AddQty, G.BuyerId,MP.Remark AS Position,U.Name AS UnitName,
						Y.OrderPO,Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime ,CP.cName,CP.TestStandard,K.tStockQty  
						FROM $DataIn.ck1_rksheet S
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
						LEFT JOIN $DataIn.stufftype T ON T.Id=D.TypeId
						LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
						LEFT JOIN $DataIn.base_mposition MP ON MP.Id=D.SendFloor  
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
						LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
						LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
						LEFT JOIN $DataIn.productdata CP ON CP.ProductId=Y.ProductId
						LEFT JOIN $DataIn.ck9_stocksheet K ON S.StuffId=K.StuffId
						Where S.Mid = '$billId'
						Order by S.StuffId";
		//echo $billItemSql;
		$billItemResult = mysql_query($billItemSql);
		while($billItemRow = mysql_fetch_assoc($billItemResult))
		{
			$StuffId = $billItemRow["StuffId"];	
			if($StuffId != "")
			{
				$checkidValue=$billItemRow["Id"];
				$StuffCname=$billItemRow["StuffCname"];
				$Qty=$billItemRow["Qty"];
				$cgQty=$billItemRow["cgQty"];
				$StockId=$billItemRow["StockId"];
				$Locks=$billItemRow["Locks"];
				$Picture=$billItemRow["Picture"];
				$TypeId=$billItemRow["TypeId"];
                $UnitName=$billItemRow["UnitName"];
                $POrderId=$billItemRow["POrderId"];
				$Position=$billItemRow["Position"]==""?"未设置":$mainRows["Position"];
				
				$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
				$rkRow = mysql_fetch_assoc($rkTemp);
				$rkQty=$rkRow["Qty"]==""?0:$rkRow["Qty"];
				
				$ProductId=$billItemRow["ProductId"];
                $OrderPO=$billItemRow["OrderPO"];
                $PQty=$billItemRow["PQty"];
		        $PackRemark=$billItemRow["PackRemark"];
		        $sgRemark=$billItemRow["sgRemark"];
		        $ShipType=$billItemRow["ShipType"];
		        $Leadtime=$billItemRow["Leadtime"];
		        
		        $FactualQty=$billItemRow["FactualQty"];
				$AddQty=$billItemRow["AddQty"];
				$tStockQty=$billItemRow["tStockQty"];
		        
		        $buyerId = $billItemRow["BuyerId"];
		        $getBuyerNameSql = "Select Name from $DataPublic.staffmain Where Number = '$buyerId'";
		        $getBuyerResult = mysql_query($getBuyerNameSql);
		        $buyerRow = mysql_fetch_array($getBuyerResult);
		        $buyerName = $buyerRow["Name"];		        
		        
		        $billItems[] = array("stuffId"=>"$StuffId", "stuffName"=>"$StuffCname", "cgQty"=>"$cgQty", "rkQty"=>"$Qty", "StockId"=>"$StockId","Picture"=>"$Picture", "Buyer"=>"$buyerName", "Position"=>"$Position", "Id"=>"$checkidValue", "BillNumber"=>"$billNumber", "Lock"=>"$billLock", "BillDate"=>"$billDate", "BillOperator"=>"$billOperator", "BillId"=>"$billId", "FactualQty"=>"$FactualQty", "AddQty"=>"$AddQty", "tStockQty"=>"$tStockQty", "typeId"=>"$TypeId");
				
			}
		}
	}
	
	echo json_encode($billItems);
	
?>