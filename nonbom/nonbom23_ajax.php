<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//echo $BarCodeS;
$TableId="ListTB".$RowId;
	echo"<table id='$TableId'  cellspacing='1' border='1' align='center' >
	<tr bgcolor='#CCCCCC'>
	<td width='30' height='20' align='center'>序号</td>
	<td width='150' align='center'>BarCode</td>
	<td width='180' align='center'>资产编号</td>
	<td width='260' align='center'>转出备注</td>
	<td width='80' align='center'>接收人</td>
	<td width='70' align='center'>接收时间</td>
	</tr>";
	$sListResult = mysql_query("SELECT C.BarCode,C.GoodsNum,M.Name,B.Date,B.Remark
    FROM   $DataIn.nonbom8_turnfixed  F
    LEFT JOIN  $DataIn.nonbom7_code  C ON F.BarCode=C.BarCode
    LEFT JOIN $DataIn.nonbom8_turn  B ON B.Id=F.TurnId
    LEFT JOIN $DataPublic.staffmain M ON M.Number=B.InNumber
    WHERE B.Id=$TurnId",$link_id);
	$i=1;
	$Dir=anmaIn("download/nonbomCode/",$SinkOrder,$motherSTR);
	if($ListRows = mysql_fetch_array($sListResult)) {
		do{
             $BarCode=$ListRows["BarCode"];
             $GoodsNum=$ListRows["GoodsNum"];
             $Picture=$ListRows["Picture"];
             $BackDate=$ListRows["Date"];
             $Name=$ListRows["Name"];
              $Remark=$ListRows["Remark"];
              if($Picture!="") $BarCode="<span onClick='OpenOrLoad(\"$Dir\",\"$Picture\")'  style='CURSOR: pointer;color:#FF6633'>$BarCode</span>";
			echo"<tr bgcolor='$theDefaultColor'>
			<td  align='center' height='20'>$i</td>";
			echo"<td  align='center'>$BarCode</td>";
			echo"<td  align='center'>$GoodsNum</td>";
			echo"<td >$Remark</td>";
			echo"<td  align='center'>$Name</td>";
			echo"<td  align='center'>$BackDate</td>";
			echo"</tr>";
			$i++;
			}while ($ListRows = mysql_fetch_array($sListResult));	
		}

?>