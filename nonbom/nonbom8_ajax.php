<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
	echo"<table id='$TableId'  cellspacing='1' border='1' align='center' >
	<tr bgcolor='#CCCCCC'>
	<td width='30' height='20'></td>
	<td width='150' align='center'>BarCode</td>
	<td width='180' align='center'>资产编号</td>
	<td width='80' align='center'>领用人</td>
	<td width='70' align='center'>领用时间</td>
	<td width='260' align='center'>领用备注</td>
	<td width='60' align='center'>状态</td>
	</tr>";
	$sListResult = mysql_query("SELECT C.BarCode,C.Id,C.GoodsNum,M.Date,M.Name AS LyMan,O.Remark,O.Estate
    FROM $DataIn.nonbom8_outfixed  O  
    LEFT JOIN $DataPublic.staffmain M ON M.Number=O.LyMan
    LEFT JOIN $DataIn.nonbom7_code  C  ON C.BarCode=O.BarCode WHERE OutId=$OutId",$link_id);
	$i=1;
	$Dir=anmaIn("download/nonbomCode/",$SinkOrder,$motherSTR);
	if($ListRows = mysql_fetch_array($sListResult)) {
		do{
             $BarCode=$ListRows["BarCode"];
             $GoodsNum=$ListRows["GoodsNum"];
             $rkName=$ListRows["rkName"];
             $Picture=$ListRows["Picture"];
             $LyDate=$ListRows["Date"];
             $LyMan=$ListRows["LyMan"];
              $Remark=$ListRows["Remark"];
              if($Picture!="") $BarCode="<span onClick='OpenOrLoad(\"$Dir\",\"$Picture\")'  style='CURSOR: pointer;color:#FF6633'>$BarCode</span>";
             $Estate=$ListRows["Estate"];
             switch($Estate){
               case "1": $EstateStr="<span class='redB'>未确认</span>";break;
               case "2": $EstateStr="<span class='yellowB'>退回仓库</span>";break;
               case "0": $EstateStr="<span class='greenB'>已确认</span>";break;
                }
			echo"<tr bgcolor='$theDefaultColor'>
			<td  align='center' height='20'>$i</td>";
			echo"<td  align='center'>$BarCode</td>";
			echo"<td  align='center'>$GoodsNum</td>";
			echo"<td  align='center'>$LyMan</td>";
			echo"<td  align='center'>$LyDate</td>";
			echo"<td >$Remark</td>";//仓库位置
			echo"<td  align='center'>$EstateStr</td>";
			echo"</tr>";
			$i++;
			}while ($ListRows = mysql_fetch_array($sListResult));	
		}

?>