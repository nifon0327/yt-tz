<?php
	include_once "../../basic/parameter.inc";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once('../../FactoryCheck/FactoryClass/AttendanceDatetype.php');
	
	$type = $_POST["type"]; //$type = "0"; //1为全检  0为抽检 , 抽检不分退换和未退换
	//$type = "1";
	
	$theGysId = $_POST["theGysId"]; 
	//$theGysId = "2270";
	
	$backType = $_POST["back"]; //$backType = "0"; //1为未退换 0为已退换 
	//$backType = "0";
	
	$qcDate = $_POST["date"];
	//$qcDate = "2014-06";
	
	$searchRows = "";
	
	//加入供应商Id
	$searchRows .= " AND M.CompanyId='$theGysId'";
	
	//先加入品检类型
	if($type == "1")
	{
		$searchRows .= " And S.AQL=''";
		//若为全检，加入是否退换
		if($backType == "0")
		{
			$searchRows .= " And S.Estate = 0";
		}
		else
		{
			$searchRows .= " And S.Estate = 1";
		}
	}
	else
	{
		$searchRows .= " And S.AQL<>''";
	}
	
	//加入品检记录日期
	//若是全检并且是查看已退换，则要加入记录日期
	if(($type == "1" && $backType == "0") || $type == "0")
	{
		$searchRows .=" AND DATE_FORMAT(S.Date,'%Y-%m')='$qcDate'";
	}
	
	$recordList = array();
	
	$mySql="SELECT S.Id,S.StuffId,S.StockId,S.Qty,S.shQty,S.Date,S.Estate,S.Operator,D.StuffCname,D.Picture,D.CheckSign,P.CompanyId,P.Forshort,P.ProviderType,A.Name AS  OperatorName ,U.Name AS UnitName,K.tStockQty  
            From $DataIn.qc_badrecord S 
            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
            LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
            LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid 
            LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId 
            LEFT JOIN $DataPublic.staffmain A ON A.Number=S.Operator 
            LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId  
			WHERE 1 
			$searchRows 
			ORDER BY S.Date DESC,S.Estate DESC";
	//echo $mySql;
	$recordResult = mysql_query($mySql);
	while($recordRow = mysql_fetch_assoc($recordResult))
	{
		$Id = $recordRow["Id"];
		$Date = $recordRow["Date"];
		
		//$factoryCheck = "on";
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
		
        $shQty = $recordRow["shQty"];
		$Qty = $recordRow["Qty"];
        $StuffId = $recordRow["StuffId"];
        $StockId = $recordRow["StockId"];
        $StuffCname = $recordRow["StuffCname"];
		$CheckSign = $recordRow["CheckSign"];
		$Estate = $recordRow["Estate"];
        $tStockQty = $recordRow["tStockQty"];
        $Forshort = $recordRow["Forshort"];
        $ProviderType = $recordRow["ProviderType"];
        $Operator = $recordRow["Operator"];
        $OperatorName = $recordRow["OperatorName"];
        $Picture = $recordRow["Picture"];
        if ($Qty>0)
        {
	    	$badRate=sprintf("%.1f",$Qty/$shQty*100)."%";
            $Reason="";
            $cause_Result=mysql_query("SELECT T.Cause,B.CauseId,B.Reason FROM $DataIn.qc_badrecordsheet B LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId   WHERE B.Mid='$Id'",$link_id);
            
            while ( $cause_row = mysql_fetch_array($cause_Result))
            {
            	$CauseId=$cause_row["CauseId"];
                if ($CauseId=="-1")
                {
                	if ($Reason!="") $Reason.=" / ";
                    $Reason.=$cause_row["Reason"];
                }
                else
                {
                 	if ($Reason!="") $Reason.=" / ";
                        $Reason.=$cause_row["Cause"];
                }
                     
            }
        }
        else
        {
        	$badRate="-"; 
        	if($backType == "1" && $type == "1")
        	{
	        	continue;
        	}
            $Reason="";
        }
        
        if($Reason == "来自批量退回")
        {
	        $reasonSql = "Select A.Remark From $DataIn.ck6_shremark A 
	        			 Left Join $DataIn.gys_shsheet B On B.Id = A.ShId
	        			 Where B.StockId = '$StockId'";
	        $reasonResult = mysql_query($reasonSql);
	        $reasonRow = mysql_fetch_assoc($reasonResult);
	        $Reason = $reasonRow["Remark"];
	        
        }
        
        
        switch($Picture)
		{
			case 0:
			{
				$LockRemark = "无配件标准图";
			}
			break;
			case 2:
			{
				$LockRemark = "审核中";
			}
			break;
			case 3:
			{
				$LockRemark = "需更新标准图";
			}
			break;
			case 4:
			{
				$LockRemark = "审核退回修改";
			}
			break;
		}
		
		if ($Estate==1 && $Qty>0 && $CheckSign==1)
		{
        	if ($Qty>$tStockQty)
            {	
                $UpdateClick1="";
            }
            else
            {
            	//属于代购且品检不良超过5%
                if ($ProviderType==2 && ($Qty/$shQty*100)>5)
                {
	            	if ($Login_P_Number==10068 || $Login_P_Number==10868)
	                {
                        $UpdateClick1="sendback";
                    }
                    else
                    {
	                    $UpdateClick1=""; 
                    }
                }
                else
                {
                    $UpdateClick1="sendback";
                }
            }
        }
        else
        {
        	if ($CheckSign==1)
        	{
            	$UpdateClick1="ok";
            }
            else
            {
            	$UpdateClick1="-"; 
            }
        }

        $recordList[] = array("date"=>"$Date", "stuffId"=>"$StuffId", "StuffName"=>"$StuffCname", "shQty"=>"$shQty", "tStockQty"=>"$tStockQty", "qtyCount"=>"$Qty", "badRate"=>"$badRate", "operatorName"=>"$OperatorName", "note"=>"$Reason", "lockRemark"=>"$LockRemark", "picture"=>"$Picture", "Id"=>"$Id", "StockId"=>"$StockId", "estate"=>"$Estate", "checkSign"=>"$CheckSign");
        
	}
	
	echo json_encode($recordList);
?>