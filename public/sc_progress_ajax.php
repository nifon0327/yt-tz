<?php 
/*
已更新电信---yang 20120801
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=900;
$TheColor=" bgcolor='#339900'";

//201112190101
$StuffResult = mysql_query("SELECT COUNT(*) AS StuffNumber
		                 FROM $DataIn.cg1_stocksheet S
		                 LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		                 LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
		                 WHERE S.POrderId='$POrderId' AND ST.mainType='1'",$link_id);
$StuffNumber=mysql_result($StuffResult,0,"StuffNumber");
//======================配件采购中
$KCReuslt=mysql_query("SELECT COUNT(*) AS KCNumber 
                        FROM $DataIn.cg1_stocksheet S
                        LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		                LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
                        WHERE S.POrderId='$POrderId' AND ST.mainType='1' AND S.FactualQty=0 AND S.AddQty=0
						",$link_id);
$WXReuslt=mysql_query("SELECT COUNT(*) AS YXNumber 
                        FROM $DataIn.cg1_stocksheet S
						LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
						LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		                LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
						WHERE S.POrderId='$POrderId' AND ST.mainType='1' AND M.Date IS NOT NULL",$link_id);
$KCNumber=mysql_result($KCReuslt,0,"KCNumber");//使用库存的
$YXNumber=mysql_result($WXReuslt,0,"YXNumber");//已下采购单的
$UseNumber=$KCNumber+$YXNumber; //库存+以下配件按比例绿色
$Number1=ceil($UseNumber/$StuffNumber*10);
//===================物料入库中

$RkResult=mysql_query("SELECT COUNT(*) AS Number FROM $DataIn.ck1_rksheet R
                       LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId  
					   WHERE S.POrderId='$POrderId' GROUP BY S.StockId"
					   ,$link_id);
					  
$RKNumber=mysql_num_rows($RkResult);//使用库存的
$Number2=ceil($RKNumber/$StuffNumber*10);
	  
//==================生产登记中	
//工序总数
$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
//已完成的工序数量
$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C 
			LEFT JOIN $DataIn.stufftype T ON C.TypeId=T.TypeId
			WHERE C.POrderId='$POrderId' AND T.Estate=1 ",$link_id));
			$scQty=$CheckscQty["scQty"]==""?0:$CheckscQty["scQty"];
$Number3=ceil($scQty/$gxQty*10);

//========================订单出货中
$EstateResult=mysql_query("SELECT Estate From $DataIn.yw1_ordersheet WHERE POrderId='$POrderId'",$link_id);
$Estate=mysql_result($EstateResult,0,"Estate");
if($Estate!=0){
	switch($Estate){
	   case 1: $EstateColor="&nbsp;"; break;
	   case 2: $EstateColor=" bgcolor='#339900'"; break;
	   }
	   $OverColor="&nbsp;";
    }
else{
    $EstateColor=" bgcolor='#339900'";
    $OverColor=" bgcolor='#339900'";
   }
//=======================	  
echo"<table id='$TableId' width='$subTableWidth' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>";
echo "<tr bgcolor='#CCCCCC'>
	<td width='150' height='20' align='center'>业务下单中</td>
	<td width='150' align='center'>配件采购中(".$UseNumber."/".$StuffNumber.")</td>
	<td width='150' align='center'>物料入库中(".$RKNumber."/".$StuffNumber.")</td>
	<td width='150' align='center'>生产登记中(".$scQty."/".$gxQty.")</td>
	<td width='150' align='center'>订单出货中</td>
	<td width='150' align='center'>订单完成</td>
	</tr>";	
echo "<tr>
	<td width='150' height='8' align='center'>
	<table cellspacing='0' cellpadding='0' border='0' width='100%'>
	<tr><td width='10%' class='A0000'  $TheColor>&nbsp;</td></tr>
	</table>
	</td>
	<td width='150' align='center' >
	<table cellspacing='0' cellpadding='0' border='0' width='100%'>
	<tr>
	";
	for($j=1;$j<=10;$j++){
	if($j<=$Number1)echo"<td width='10%' class='A0000'  $TheColor>&nbsp;</td>";
	else echo"<td width='10%' class='A0000'>&nbsp;</td>";
	}
	echo"</tr>
	</table>
	</td>
	<td width='150' align='center' >
	<table cellspacing='0' cellpadding='0' border='0' width='100%'>
	<tr>
	";
	for($j=1;$j<=10;$j++){
	if($j<=$Number2)echo"<td width='10%' class='A0000'  $TheColor>&nbsp;</td>";
	else echo"<td width='10%' class='A0000'>&nbsp;</td>";
	}
	echo"</tr>
	</table>
	</td>
	<td width='150' align='center' >
	<table cellspacing='0' cellpadding='0' border='0' width='100%'>
	<tr>
	";
	for($j=1;$j<=10;$j++){
	if($j<=$Number3)echo"<td width='10%' class='A0000'  $TheColor>&nbsp;</td>";
	else echo"<td width='10%' class='A0000'>&nbsp;</td>";
	}
	echo"</tr>
	</table>
	</td>
	<td width='150' align='center'>
	<table cellspacing='0' cellpadding='0' border='0' width='100%'>
	<tr><td width='10%' class='A0000'  $EstateColor>&nbsp;</td></tr>
	</table>
	</td>
	<td width='150' align='center' $OverColor>
	<table cellspacing='0' cellpadding='0' border='0' width='100%'>
	<tr><td width='10%' class='A0000'  $OverColor>&nbsp;</td></tr>
	</table>
	</td>
	</tr>";
echo "</table>";	
?>
