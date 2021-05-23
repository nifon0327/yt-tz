<?php   	
//独立已更新**********电信---yang 20120801
include "../basic/chksession.php";
include "../basic/parameter.inc";
$checkJob=mysql_query("SELECT J.Id,J.Name FROM $DataPublic.jobdata J WHERE J.Estate=1 AND J.Id>10",$link_id);
$i=1;//生产部门人员变化图例
echo"<table  border='0' cellspacing='0'><tr>
    <td colspan='2' align='center'>&nbsp;</td>
  </tr>";
if($checkRow=mysql_fetch_array($checkJob)){
	do{
		$JobId=$checkRow["Id"];
		//$Name=$checkRow["Name"];
		$Bc=$i%2;
		echo"<tr><td><img src='charttopng_staff_ajax.php?JobId=$JobId&Bc=$Bc'></td><td><img src='charttopng_staff_ajax1.php?JobId=$JobId&Bc=$Bc'></td></tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkJob));
	}
echo"</table>";
?>
