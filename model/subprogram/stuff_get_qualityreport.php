<?php 
//$DataIn.qc_badrecord电信---yang 20120801
//通过采购单流水号获得品检报告
$qualityReport="&nbsp;";
if ($funFrom=="gys_sh"){
    $badResult = mysql_query("SELECT Id FROM $DataIn.qc_badrecord WHERE StockId='$StockId' AND shMid='$Mid' AND StuffId='$StuffId' LIMIT 1",$link_id);
	    if($badRow = mysql_fetch_array($badResult)){
	        $badId=$badRow["Id"];
	        $qualityReport="<a href='../model/subprogram/stuff_quality_report.php?Id=$badId&StockId=$StockId' target='_blank'>View</a>";
	       }
}
else{
    $badResult = mysql_query("SELECT Id FROM $DataIn.qc_badrecord WHERE StockId='$StockId'  LIMIT 1",$link_id);
		    if($badRow = mysql_fetch_array($badResult)){
		        $badId=$badRow["Id"];
		        $qualityReport="<a href='../model/subprogram/stuff_quality_report.php?Id=$badId&StockId=$StockId' target='_blank'>View</a>";
		       }
       }
?>