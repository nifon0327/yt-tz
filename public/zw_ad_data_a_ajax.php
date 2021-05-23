<?php 
//$DataPublic.zw2_hzdoc 二合一已更新
//电信-joseph
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$TempArray=explode("|",$TempId);
$Id=$TempArray[0];
$predivName=$TempArray[1];//a
$mySql="SELECT Caption,Attached FROM $DataIn.zw2_hzdoc D WHERE 1 AND TypeId='$Id' ORDER BY Id";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Caption=$myRow["Caption"];
		$Attached=$myRow["Attached"];
		$d=anmaIn("download/hzdoc/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		echo"<table cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='550'>$Caption</td>
				<td width='92' align='center'>$Attached</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>