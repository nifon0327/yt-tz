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
	<td width='30' height='20' align='center'>序号</td>
	<td width='150' align='center'>BarCode</td>
	<td width='180' align='center'>资产编号</td>
	<td width='40' align='center'>图片</td>
	<td width='180' align='center'>报废原因</td>
	<td width='90' align='center'>报废时间</td>
	<td width='60' align='center'>操作</td>
	</tr>";
	$sListResult = mysql_query("SELECT B.Id, B.Picture,B.Estate,B.Date,B.Remark,B.Operator,C.BarCode,C.GoodsNum
    FROM  $DataIn.nonbom10_bffixed  B 
   LEFT JOIN $DataIn.nonbom7_code  C  ON C.BarCode=B.BarCode
  WHERE B.BfId=$BfId AND B.GoodsId=$GoodsId ",$link_id);
	$i=1;
	$Dir=anmaIn("download/nonbombf/",$SinkOrder,$motherSTR);
	if($ListRows = mysql_fetch_array($sListResult)) {
		do{
             $BarCode=$ListRows["BarCode"];
             $GoodsNum=$ListRows["GoodsNum"];
             $Date=$ListRows["Date"];
             $Operator=$ListRows["Operator"];
		     include "../model/subprogram/staffname.php";
             $Remark=$ListRows["Remark"];
             $Picture=$ListRows["Picture"];
            $PictureStr="";
            if($Picture!="") {
			      $Picture=anmaIn($Picture,$SinkOrder,$motherSTR);
                 $PictureStr="<span onClick='OpenOrLoad(\"$Dir\",\"$Picture\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
              }
			echo"<tr bgcolor='$theDefaultColor'>
			<td  align='center' height='20'>$i</td>";
			echo"<td  align='center'>$BarCode</td>";
			echo"<td  align='center'>$GoodsNum</td>";
			echo"<td align='center'>$PictureStr</td>";
			echo"<td  align='center'>$Remark</td>";
			echo"<td  align='center'>$Date</td>";
			echo"<td align='center'>$Operator</td>";
			echo"</tr>";
			$i++;
			}while ($ListRows = mysql_fetch_array($sListResult));	
		}

?>