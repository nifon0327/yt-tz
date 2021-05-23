<?php 
//电信
//代码共享-EWEN 2012-10-29
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$SearchRows=$BID==""?"":" AND A.BranchId='$BID'";
$SearchRows.=$BGID==""?"":" AND B.BloodGroup='$BGID'";
$checkResult = mysql_query("SELECT A.Name,B.BloodGroup
												FROM $DataPublic.staffmain A 
												LEFT JOIN $DataPublic.staffsheet B ON B.Number=A.Number 
												WHERE 1 $SearchRows AND A.Estate=1 AND (A.cSign='$Login_cSign' OR A.cSign='0') ORDER BY A.BranchId,A.JobId,A.Name",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	echo"<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
	$i=1;
	do{
		$Name=$checkRow["Name"];
		$BloodGroup=$checkRow["BloodGroup"];
		$BG1=$BG2=$BG3=$BG4=$BG0="&nbsp;";
		$BloodGroup=$checkRow["BloodGroup"];
		$TempNum="BG".strval($BloodGroup); 
		$$TempNum="<span class='GreenB'>√</span>";	
		echo "<tr align='center'>
		<td height='25' width='49' class='A0101'>$i</td>
		<td width='60' class='A0101'>$Name</td>
		<td width='60' class='A0101'>$BG1</td>
    	<td width='60' class='A0101'>$BG2</td>
    	<td width='60' class='A0101'>$BG3</td>
    	<td width='60' class='A0101'>$BG4</td>
    	<td width='60' class='A0100'>$BG0</td>
		</tr>";
		$i++;
		}while($checkRow = mysql_fetch_array($checkResult));
	echo"</table>";
	}
?>