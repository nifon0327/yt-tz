<?php 
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo"<table id='$TableId' cellspacing='1' border='1' align='right'><tr bgcolor='#CCCCCC'>
		<td width='10' height='20'></td>
		<td width='80' align='center'>序号</td>
		<td width='340' align='center'>配件分析</td>
		<td width='100' align='center'>报价分析(USD)</td>				
		</tr>";

//参数拆分
$myResult = mysql_query("SELECT S.Id,S.Name,M.Sprice,M.Simg
FROM $DataIn.ywbj_pands M 
LEFT JOIN $DataIn.ywbj_stuffdata S ON S.Id=M.Sid 
WHERE M.Pid='$TempId' ORDER BY M.Id",$link_id);
$i=1;
$TableWidth=680;
if($myRow = mysql_fetch_array($myResult)){
	$SumAmount=0;
	do{
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Sprice=$myRow["Sprice"];
		$Simg=$myRow["Simg"];
		$SumAmount+=$Sprice;
		$Dir=anmaIn("download/ywbjimg/",$SinkOrder,$motherSTR);
		if($Simg==1){
			$Simg="S".$Id.".jpg";
			$Simg=anmaIn($Simg,$SinkOrder,$motherSTR);
			$Name="<span onClick='OpenOrLoad(\"$Dir\",\"$Simg\")' style='CURSOR: pointer;color:#FF6633'>$Name</span>";
			}
		echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>&nbsp;</td>";
		echo"<td align='center'>$i</td>";
		echo"<td>$Name</td>";
		echo"<td align='right'>$Sprice</td>";
		echo"</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
	$SumAmount=sprintf("%.4f",$SumAmount);
	echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>&nbsp;</td><td align='center'>合计</td>
		<td>&nbsp;</td>
		<td align='right'>$SumAmount</td>
		</tr>";
	}
echo"</table>";
?>