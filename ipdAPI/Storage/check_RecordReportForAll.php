<?php
	
	if (strlen($stockId)<14)
	{
         // $StockId="";
         $badSql="SELECT B.Id,B.Qty AS badQty,IF(B.CauseId='-1',5656565,B.CauseId) AS CauseId,B.Reason,B.Picture AS BadPicture,T.Cause,T.Picture 
                  FROM $DataIn.qc_badrecordsheet B 
                  LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                  WHERE B.Mid='$Mid' order by CauseId";
    }
    else
    {
        $badSql="SELECT B.Id,SUM(B.Qty) AS badQty,IF(B.CauseId='-1',5656565,B.CauseId) AS CauseId,B.Reason,B.Picture AS BadPicture,T.Cause,T.Picture
                 FROM $DataIn.qc_badrecord S 
                 LEFT JOIN $DataIn.qc_badrecordsheet B ON B.Mid=S.Id  
                 LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                 WHERE S.StockId='$stockId' AND S.Date='$Date' GROUP BY B.CauseId order by CauseId"; 
    }
    //echo $badSql;
    $bResult = mysql_query($badSql); 
	while($badRow = mysql_fetch_assoc($bResult))
	{
		$badQty=$badRow["badQty"]==0?"-":$badRow["badQty"];
		$CauseId=$badRow["CauseId"];
        $Cause=$badRow["Cause"];
        $BadPicture=$badRow["BadPicture"];
        $Reason=$badRow["Reason"];
        if ($CauseId=='5656565')
        {
            $Cause=$Reason; 
        }
        
        $Bid=$badRow["Id"];
        if($BadPicture==1)
        {
	    	$bFileName="Q".$Bid.".jpg";
        }
        else
        {
        	$checkPicSql=mysql_query("SELECT F.Picture  FROM $DataIn.qc_badrecordfile F  WHERE F.Mid='$Bid' ",$link_id);
            if($checkPicRow=mysql_fetch_array($checkPicSql))
            { 
            	$bFileName= $checkPicRow["Picture"];
            	$BadPicture = 1;
            }  
        }
        
        $badRate=($shQty!=0)?sprintf("%.1f",$badQty/$shQty*100)."%":"0.0%";
        
        
        $badReasons[] = array("$Cause", "$badQty", "$badRate", "$CauseId", "$BadPicture","$Reason", "$bFileName");
	}
	
?>