<?php   //-----------------------yang 
		$Dir="download/clientproxy/";
    	$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);
		$imgResult=mysql_query("SELECT B.Attached FROM $DataIn.yw7_clientproduct A
			LEFT JOIN $DataIn.yw7_clientproxy B  ON B.Id=A.cId
			WHERE A.ProductId='$ProductId' AND B.Estate=1 ",$link_id); 
		if($imgRow=mysql_fetch_array($imgResult)) {
		   do {	
			     $clientproxy=$imgRow["Attached"];
			     $clientproxy=anmaIn($clientproxy,$SinkOrder,$motherSTR);
			     $clientproxy="<span onClick='OpenOrLoad(\"$Dir\",\"$clientproxy\")' style='CURSOR: pointer;color:#F00;'>View</span>";	
			   }while ($imgRow=mysql_fetch_array($imgResult));   
		    }
		 else{
		 	    $clientproxy="&nbsp;";
		        }
?>