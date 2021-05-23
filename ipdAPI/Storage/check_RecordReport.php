<?php
	
	include_once "../../basic/parameter.inc";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/public/kqClass/Kq_dailyItem.php");
	
	$id = $_POST["id"];
	//$id = "248210";
	$stockId = $_POST["stockId"];
	//$stockId = "20140605050702";
	//$factoryCheck = "yes";
	$searchRow = (strlen($stockId) < 14)?"S.Id='$id' ":"S.StockId='$stockId' ";
	
	$mySql="SELECT S.Id,S.Date,S.StuffId,S.StockId,SUM(S.shQty) AS shQty,SUM(S.checkQty) AS checkQty,SUM(S.Qty) AS Qty,S.AQL,S.shMid,D.StuffCname,D.Picture,D.CheckSign,A.Name AS  Operator 
        	FROM $DataIn.qc_badrecord S 
        	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
        	LEFT JOIN $DataPublic.staffmain A ON A.Number=S.Operator 
        	WHERE $searchRow
        	GROUP BY S.Date 
        	ORDER BY S.Date DESC";
	
	$badReport = array();
	$badResult = mysql_query($mySql);
	while($report = mysql_fetch_assoc($badResult))
	{
		$Date = $report["Date"];
		
		//$factoryCheck = "no";
		if($factoryCheck == "on"){
            $staffNumberSql = mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE JobId = 39 AND GroupId = 604 Limit 1");
            $staffNumberResult = mysql_fetch_assoc($staffNumberSql);
            /************加入过滤***************/
            $Number = $staffNumberResult['Number'];
            $sheet = new WorkScheduleSheet($Number, substr($Date, 0, 10), $attendanceTime['start'], $attendanceTime['end']);
            $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
            $datetype = $datetypeModle->getDatetype($Number, substr($Date, 0, 10), $sheet);
            if($datetype['morning'] != 'G' && $datetype['afternoon'] != 'G'){
              continue;
            }
            $Date = substr($Date, 0, 10);
        }
		
		$pStuffId = $report["StuffId"];
		$pStockId = $report["StockId"];
		if(strlen($pStockId)<14)
		{
    		switch ($pStockId)
    		{
	    		case -1:
	    		{
            		$pStockId="补货单";
            	}
            	break;
            	case -2:
            	{
            		$pStockId="备品单";
            	}
            	break;
            }
        }
        
        $Mid = $report["Id"];
       	$shQty = $report["shQty"];
       	$checkQty = $report["checkQty"];
       	$qty = $report["Qty"];
       	$stuffName = $report["StuffCname"];
       	 //$picture = $report["Picture"];
       	$checkSign = $report["CheckSign"];
       	
       	$shMid = $report["shMid"];
       	$companyResultSql = "Select A.Company From $DataIn.companyinfo A
       						 Left Join $DataIn.gys_shmain B On B.CompanyId = A.CompanyId
       						 Where B.Id = '$shMid' Limit 1";
       	$companyResult = mysql_query($companyResultSql);
       	$companyRow = mysql_fetch_assoc($companyResult);
       	$company = $companyRow["Company"];
       	
       	$name = $report["Operator"];
       	$AQL=$report["AQL"];
       	
       	$goodRate=sprintf("%.2f",($shQty-$qty)/$shQty*100);
       	
       	$badReasons = array();
       	if ($checkSign == "1" || $AQL == "")
       	{
	       	include "check_RecordReportForAll.php";
       	}
       	else if($checkSign == "0")
       	{
	       	include "check_RecordReportForSome.php";
       	}       	
     
       	$badReport[] = array(array("$stuffName", "$pStockId", "$pStuffId", "$Date", "$company", "$name", "$shQty", "$checkQty", "$AQL", "$goodRate", "$ReStr"), $badReasons);
    }	
    
	echo json_encode($badReport);
	//print_r($badReport);	
?>