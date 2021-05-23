<?php
	
	include_once "../../basic/parameter.inc";
	
	$Id = $_POST["Id"];
	//$Id = "129635";
	
	$subSuppliedCheck = array();
	$upResult=mysql_query("SELECT S.Id,S.Qty,S.SendSign,D.StuffCname,D.TypeId,D.CheckSign,T.AQL,(G.AddQty+G.FactualQty) AS cgQty 
						FROM $DataIn.gys_shsheet S
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
						LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
						WHERE S.Id=$Id LIMIT 1",$link_id); 
						
	if($upRows = mysql_fetch_assoc($upResult))
	{
		$AQL=$upRows["AQL"];
		$TypeId=$upRows["TypeId"];
        $CheckSign=$upRows["CheckSign"];
        $Qty=$upRows["Qty"];
        $CheckQty = "";
        if ($CheckSign==0)
        {
            $CheckQtyStr="抽样数量";
            if ($AQL=="")
            {
               $CheckSignStr="抽检 (AQL:未设置)";   
            }
            else
            {
            	$CheckSignStr="抽检(AQL:".$AQL.")";  
               
            	$checkResult = mysql_query("SELECT L.Ac,L.Re,L.Lotsize,S.SampleSize 
                 							FROM $DataIn.qc_levels L
                 							LEFT JOIN  $DataIn.qc_lotsize S ON S.Code=L.Code     
                 							WHERE L.AQL='$AQL' 
                 							AND S.Start<='$Qty' 
                 							AND S.End>='$Qty'",$link_id);
               
               if ($checkRow = mysql_fetch_array($checkResult))
               {
               		$SampleSize=$checkRow["SampleSize"]; 
               		$Lotsize=$checkRow["Lotsize"]; 
               		$ReQty=$checkRow["Re"]==""?1:$checkRow["Re"];
               		if ($Lotsize>0) 
               		{
                   		$CheckQty=$Lotsize;
                   	}
                   	else
                   	{
                   		$CheckQty=$SampleSize;	
                   	}
               }
               else
               {  //低于最低抽样数量，全检
               		$CheckQty= $Qty;
                    $ReQty=1;
               }
            }
        }
        else
        {
            $AQL="";
            $CheckSignStr="全检";
            $CheckQtyStr="品检数量";
            $CheckQty=$Qty;
        }
        
        $subSuppliedCheck[] = "$CheckSignStr";
        $subSuppliedCheck[] = "$CheckQty";
        $subSuppliedCheck[] = "$AQL";
        $subSuppliedCheck[] = "$ReQty";
        
        $check_Result = mysql_query("SELECT Id FROM $DataIn.qc_causetype WHERE Type=$TypeId AND Estate=1 LIMIT 1",$link_id);
        if($check_row = mysql_fetch_array($check_Result))
        {
	        $cause_Result = mysql_query("SELECT Id,Cause,Picture FROM $DataIn.qc_causetype WHERE Type=$TypeId AND Estate=1",$link_id);
	    }
	    else
	    {
    		$cause_Result = mysql_query("SELECT Id,Cause,Picture FROM $DataIn.qc_causetype WHERE Type=1 AND Estate=1",$link_id); 
    	}
    	
    	$tmpCause = array();
    	while($cause_row = mysql_fetch_array($cause_Result))
    	{
			$cId=$cause_row["Id"];
			$Cause=$cause_row["Cause"];
			
			$tmpCause[] = array($cId, $Cause);
		}
        
        $subSuppliedCheck[] = $tmpCause;
        
	}	
	
	echo json_encode($subSuppliedCheck);
	//print_r($subSuppliedCheck);
	
	
?>