<?php
	
	include_once "../../basic/parameter.inc";
	
	$checkResult = mysql_query("SELECT L.Ac,L.Re,L.Lotsize,S.SampleSize 
                 				FROM $DataIn.qc_levels L
                 				LEFT JOIN  $DataIn.qc_lotsize S ON S.Code=L.Code     
                 				WHERE L.AQL='$AQL' AND S.Start<='$shQty' AND S.End>='$shQty'",$link_id);
               
    if ($checkRow = mysql_fetch_array($checkResult))
    {
    	$ReQty=$checkRow["Re"];
        $Lotsize=$checkRow["Lotsize"];
        $SampleSize=$Lotsize>0?$Lotsize:$checkRow["SampleSize"];      
    }
    
    $ReQty=$ReQty==""?1:$ReQty;
    $checkQty=$checkQty==0?$SampleSize:$checkQty;
	
	if (strlen($stockId)<14)
	{
    	$StockId="";//$ReStr="";  
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
   
    if ($ReQty>$Qty)
    {
	   $ReStr="允收";
    }
   	else
    {
	   $ReStr="拒收";
    }
    $bResult=mysql_query($badSql,$link_id);
    if($badRow=mysql_fetch_array($bResult))
    { 
        $i=1;$badCauseList=0;
        do
        {
	        $badQty=$badRow["badQty"]==0?"-":$badRow["badQty"];
	        $CauseId=$badRow["CauseId"];
	        $Cause=$badRow["Cause"]==""?"":$badRow["Cause"];
	        $Reason=$badRow["Reason"];
	        if($CauseId=='5656565')
	        {
            	$Cause=$Reason; 
            }
            
            $Picture=$badRow["Picture"];  
            $Bid=$badRow["Id"];
            
            $BadPicture=$badRow["BadPicture"];
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
            $badRate=$checkQty!=0?sprintf("%.1f",$badQty/$checkQty*100)."%" :"0.0%";
            $badRate=$badQty==0?"-":$badRate;
           
            if($badQty>0)
            {
                 if ($BadPicture !=0)
                 {    
                 	$badQty=$BadPicture;
                 }
                 $badCauseList=1;
            
            }
            
            /*
if($Cause != "")
            {
            	$badReasons[] = array("$Cause", "$badQty", "$badRate", "$CauseId", "$BadPicture", "$Reason", "$bFileName");
            }
*/
			$Cause = ($Cause == "")?$Reason:$Cause;
			$badReasons[] = array("$Cause", "$badQty", "$badRate", "$CauseId", "$BadPicture", "$Reason", "$bFileName");

        }
        while($badRow=mysql_fetch_array($badResult)); 
	}
?>