<?php 

$qualityReport="&nbsp;";
if ($funFrom=="gys_sh"){
    $badResult = mysql_query("SELECT Id FROM $DataIn.qc_badrecord WHERE StockId='$StockId' AND shMid='$Mid' AND StuffId='$StuffId' LIMIT 1",$link_id);
    if($badRow = mysql_fetch_array($badResult)){
        $badId=$badRow["Id"];
        $qualityReport="<a href='../public/stuff_quality_report.php?Id=$badId&StockId=$StockId' target='_download'>View</a>";
       }
     }
else{
     if($gys_Id>0){
	     $badResult = mysql_query("SELECT Id FROM $DataIn.qc_badrecord WHERE StockId='$StockId' AND Sid='$gys_Id'",$link_id);
	    if($badRow = mysql_fetch_array($badResult)){
	        $badId=$badRow["Id"];
	        $qualityReport="<a href='../public/stuff_quality_report.php?Id=$badId&StockId=$StockId' target='_download'>View</a>";
	       }
        }  
    }
?>