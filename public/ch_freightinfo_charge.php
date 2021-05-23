<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=980;

$dataArray=explode("|",$args);
$CompanyId=$CompanyId==""?$dataArray[0]:$CompanyId;

echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' style='margin-left:60px;'><tr bgcolor='#CCCCCC'>
		<td width='30' height='20'>序号</td>
		<td width='70' align='center'>类型</td>
		<td width='70' align='center'>CFS费</td>
		<td width='70' align='center'>THC费</td>	
		<td width='70' align='center'>文件费</td>
		<td width='70' align='center'>手续费</td>
		<td width='70' align='center'>ENS费</td>
		<td width='70' align='center'>保险费</td>
		<td width='70' align='center'>过桥费</td>
		<td width='70' align='center'>电放费</td>
		<td width='70' align='center'>提单费</td>
		</tr>";

$mySql = "SELECT * FROM $DataIn.forwardcharge WHERE CompanyId='$CompanyId'";
$myResult = mysql_query($mySql,$link_id);
$i=1;
if ($myRow = mysql_fetch_array($myResult)) {
	do{
	    $Type=$myRow["Type"]==1?"<span class='redB'>空运</span>":"<span class='blueB'>海运</span>";
        $CFSCharge=$myRow["CFSCharge"];
		$THCCharge=$myRow["THCCharge"];
		$WJCharge=$myRow["WJCharge"];
		$SXCharge=$myRow["SXCharge"];
		$ENSCharge=$myRow["ENSCharge"];
		$BXCharge=$myRow["BXCharge"];
		$GQCharge=$myRow["GQCharge"];
		$DFCharge=$myRow["DFCharge"];
		$TDCharge=$myRow["TDCharge"];
		
    	echo"<tr bgcolor='$theDefaultColor'><td  align='right' height='20'>$i</td>";
		echo"<td  align='center' >$Type</td>";	
		echo"<td  align='center'>$CFSCharge</td>";
		echo"<td  align='center'>$THCCharge</td>";		
		echo"<td  align='center'>$WJCharge</td>";
		echo"<td  align='center'>$SXCharge</td>";
		echo"<td  align='center'>$ENSCharge</td>";
		echo"<td  align='center'>$BXCharge</td>";
		echo"<td  align='center'>$GQCharge</td>";
		echo"<td  align='center'>$DFCharge</td>";
		echo"<td  align='center'>$TDCharge</td>";
		echo"</tr>";
		$i=$i+1;
		
	}while ($myRow = mysql_fetch_array($myResult));
}


echo"</table>"."";

?>