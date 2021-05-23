<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1350;

//来自于生产登记页面
echo"<table id='$TableId'  cellspacing='1' border='1' align='center'>
	<tr bgcolor='#CCCCCC'>
	<td width='10' height='20'></td>
	<td width='90' align='center'>待购流水号</td>
	<td width='330' align='center'>配件名称</td>
	<td width='65' align='center'>订单数量</td>
	<td width='65' align='center'>生产数量</td>
	</tr>";
$ordercolor=3;
$sListResult = mysql_query("SELECT 
	S.StockId,S.POrderId,S.OrderQty,A.StuffCname,A.TypeId
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
	WHERE S.POrderId='$POrderId' AND T.mainType=3 ORDER BY S.StockId",$link_id);

	$i=1;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	if($StockRows = mysql_fetch_array($sListResult)) {
		do{
/////////////////////////////
			//颜色	0绿色	1白色	2黄色	3绿色
			//初始化
			$scQty="-";
			$OnclickStr="";
			$StockId=$StockRows["StockId"];
			$StuffCname=$StockRows["StuffCname"];
			$OrderQty=$StockRows["OrderQty"];
			//生产数量
			$scSql=mysql_query("SELECT ifnull(SUM(S.Qty),0) AS scQty FROM $DataIn.sc1_cjtj S
				                WHERE 1 AND S.StockId='$StockId' ",$link_id); 
			$scQty=mysql_result($scSql,0,"scQty");												
			$TempColor=$OrderQty==$scQty?3:2;
			//采单颜色标记
			switch($TempColor){
				case 1://白色
					$Sbgcolor="#FFFFFF";
					$ordercolor=1;
					break;
				case 2://黄色
					$Sbgcolor="#FFCC00";
					$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
					break;
				case 3://绿色
					$Sbgcolor="#339900";
					$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
					break;
					}
			echo"<tr bgcolor='$theDefaultColor'>
			<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//配件状态 
			echo"<td align='center'>$StockId</td>";//配件采购流水号
			echo"<td $ChangeStuff>$StuffCname</td>";//配件名称
			echo"<td align='right'>$OrderQty</td>";//订单需求数量
			echo"<td><div align='right' style='color:#339900;font-weight: bold;'>$scQty</div></td>";//生产数量
			echo"</tr>";
			$i++;
/////////////////////////////////////
			}while ($StockRows = mysql_fetch_array($sListResult));	
		}
?>