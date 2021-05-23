<?php
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$Id = $_POST["Id"];
	//$Id = "83661";
	
	$badRecordList = array();
	$upSql=mysql_query("SELECT B.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.TypeId, D.CheckSign,(G.AddQty+G.FactualQty) AS cgQty, T.AQL
                		FROM $DataIn.qc_badrecord B 
                		LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=B.shMid AND B.StockId=S.StockId AND B.StuffId=S.StuffId 
                		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=B.StockId
                		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.StuffId 
                		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
                		WHERE B.Id=$Id LIMIT 1",$link_id); 
    
    if($upData = mysql_fetch_array($upSql))
    {
		$StuffId=$upData["StuffId"];
		$StockId=$upData["StockId"];
		$Qty=$upData["Qty"];
		$AQL = $upData["AQL"];
		$cgQty=$upData["cgQty"];
		$StuffCname=$upData["StuffCname"];
		$CheckSign = $upData["CheckSign"];
        $TypeId=$upData["TypeId"];
        
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
        
        $SendSign=$upData["SendSign"];
        switch ($SendSign)
        {
           case 1:
               $StockId="本次补货";
               break;
           case 2:
               $StockId="本次备品";
               break;
        }
    }
    
    $badRecordList[] = "$CheckSignStr";
    $badRecordList[] = "$CheckQty";
    $badRecordList[] = "$AQL";
    $badRecordList[] = "$ReQty";
    
    $badReason = array();
    $check_Result = mysql_query("SELECT Id FROM $DataIn.qc_causetype WHERE Type=$TypeId AND Estate=1 LIMIT 1",$link_id);
    if($check_row = mysql_fetch_array($check_Result))
    {
    	$cause_Result = mysql_query("SELECT Id,Cause,Picture FROM $DataIn.qc_causetype WHERE Type=$TypeId AND Estate=1",$link_id);
    }
    else
    {
    	$cause_Result = mysql_query("SELECT Id,Cause,Picture FROM $DataIn.qc_causetype WHERE Type=1 AND Estate=1",$link_id); 
    }
    
    while ( $cause_row = mysql_fetch_array($cause_Result))
    {
		$cId=$cause_row["Id"];
		$Cause=$cause_row["Cause"];

        $Bid=0;
        $sheet_Result=mysql_query("SELECT B.Id,B.Qty,B.Picture  FROM $DataIn.qc_badrecordsheet B 
                            	   WHERE B.Mid='$Id' AND B.CauseId='$cId'",$link_id);
        if($sheet_row = mysql_fetch_array($sheet_Result))
        {
        	$CauseQty=$sheet_row["Qty"];
            $sumQty+=$CauseQty;
              
            $Bid=$sheet_row["Id"];
            $Picture=$sheet_row["Picture"];
            if ($Picture==1)
            {
            	$PictureName="Q".$Bid.".jpg";		 
                $IsDisplayed="none";
            }
            else
            {
            	$checkPicSql=mysql_query("SELECT F.Picture  FROM $DataIn.qc_badrecordfile F  WHERE F.Mid='$Bid' ",$link_id);
            	if($checkPicRow=mysql_fetch_array($checkPicSql))
            	{ 
                       $PictureName="";
                       $delPicture="";
                       $IsDisplayed="";  
                }
                else
                {
                	$BadPicture="未上传";
                	$PictureName="";
                	$delPicture="";
                	$IsDisplayed="";
                 }
             }
         }
        else
        {
        	$PictureName="";
            $CauseQty=0;
            $BadPicture="";
            $delPicture="";
            $IsDisplayed="none";
        }
        
        $badReason[] = array("$cId", "$Cause", "$CauseQty", "$PictureName", "$Bid");
        
    }
    $sheet_Result=mysql_query("SELECT B.Id,B.Qty,B.Reason,B.Picture FROM $DataIn.qc_badrecordsheet B 
                            WHERE B.Mid='$Id' AND B.CauseId='-1'",$link_id);
    if($sheet_row = mysql_fetch_array($sheet_Result))
    {
    	$otherbadQty=$sheet_row["Qty"];
        $otherCause=$sheet_row["Reason"];
        $sumQty+=$otherbadQty;
              
        $Bid=$sheet_row["Id"];
        $Picture=$sheet_row["Picture"];
        if($Picture==1)
        {
        	$PictureName="Q".$Bid.".jpg";			
	    }
	    else
	    {
        	$checkPicSql=mysql_query("SELECT F.Picture  FROM $DataIn.qc_badrecordfile F  WHERE F.Mid='$Bid' ",$link_id);
        	if($checkPicRow=mysql_fetch_array($checkPicSql))
            { 
                $PictureName="";
                $IsDisplayed="";  
                $delPicture="";  
            }
            else
            {
            	$BadPicture="未上传";
                $PictureName="";
                $IsDisplayed="";
                $delPicture="";  
            }
        }
       
    }
    else
    {
    	$IsDisplayed="none";
        $otherbadQty=0;
        $PictureName="";
        $otherCause="";
        $delPicture="";  
    }
     
    $badRecordList[] = $badReason;
    $badRecordList[] = array("$otherbadQty", "$Bid", "$otherCause", "$PictureName");
    $badRecordList[] = "$sumQty";
    
    echo json_encode($badRecordList);
    	
?>