<?php 
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$myRow = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.forwardcharge WHERE CompanyId='$CompanyId' AND Type='$ShipType' LIMIT 1",$link_id));
$CFSCharge = $myRow["CFSCharge"]==""?0.00:$myRow["CFSCharge"];
$THCCharge = $myRow["THCCharge"]==""?0.00:$myRow["THCCharge"];
$WJCharge  = $myRow["WJCharge"]==""?0.00:$myRow["WJCharge"];
$SXCharge  = $myRow["SXCharge"]==""?0.00:$myRow["SXCharge"];
$ENSCharge = $myRow["ENSCharge"]==""?0.00:$myRow["ENSCharge"];
$BXCharge  = $myRow["BXCharge"]==""?0.00:$myRow["BXCharge"];
$GQCharge  = $myRow["GQCharge"]==""?0.00:$myRow["GQCharge"];
$DFCharge  = $myRow["DFCharge"]==""?0.00:$myRow["DFCharge"];
$TDCharge  = $myRow["TDCharge"]==""?0.00:$myRow["TDCharge"];
echo $CFSCharge."|".$THCCharge."|".$WJCharge."|".$SXCharge."|".$ENSCharge."|".$BXCharge."|".$GQCharge."|".$DFCharge."|".$TDCharge;
?>