<?php 

/*
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.stuffdata
$DataPublic.staffmain
$DataIn.trade_object
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
二合一已更新
电信-joseph
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//$TableId="ListTB".$RowId;
$TempArray=explode("|",$TempId);
$Name=$TempArray[0];
$predivNum=$TempArray[1];
$tableWidth=910;
$TableId=$predivNum;
$subTableWidth1=855;
$subTableWidth2=800;
/*
echo"<table id='$TableId' width='$subTableWidth' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='10' height='20'></td>
		<td width='80' align='center'>订单PO</td>
		<td width='90' align='center'>内部单号</td>
		<td width='330' align='center'>产品名称</td>				
		<td width='55' align='center'>订单数</td>
		<td width='55' align='center'>本次完成</td>
		<td width='55' align='center'>总完成进度（%）</td>
		<td width='55' align='center'>组装总时间(分)</td>
		<td width='55' align='center'>人数</td>
		<td width='55' align='center'>人力(RMB)/单品</td>
		<td width='55' align='center'>人力总计(RMB)</td>	
		";
*/
//width='$subTableWidth'
echo"<table id='$TableId' width='$subTableWidth1' frame='border' cellpadding='0' cellspacing='0'  border='0' align='right' bordercolor='#B7B7B7' bgcolor='#B7B7B7'  > <tr align='right' style='border:none;'> <td align='right' style='border:none;'>";
echo"<table id='$TableId-1' width='$subTableWidth2'  cellspacing='0' border='1' align='right' bgcolor='#B7B7B7'><tr >
		<td width='10' height='20'></td>
		<td width='350' align='center'>(小)分类</td>		
		<td width='400' align='center'>行政资料说明</td>
		<td width='60' align='center'>附件</td>
		<td width='80' align='center'>日期</td>
		<td width='85' align='center'>失效日期</td>
		<td width='60' align='center'>操作员</td>
		";

echo "</tr>";

//$Th_Col="选项|45|序号|45|日期|70|订单PO|100|内部单号|80|产品名称|300|订单数|50|本次完成|50|总完成（%）|50|组装时间(分)|50|人数|50|人力(RMB)/单品|60|人力总计(RMB)|60|备注|50|登记|60";

/*
echo "SELECT M.OrderPO,S.Estate,S.ProductId,P.cName,D.Id,D.POrderId,S.Qty,D.FQty,D.AllMins,D.Workers,D.Remark,D.Date,D.Locks,D.Operator
FROM $DataIn.sc2_Pfinish D
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=D.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
WHERE 1 And D.Date=$Date ORDER BY D.ID Desc,M.OrderPO ";
*/
//$tempstr=urldecode($Name);
//$tempstr=iconv("UTF-16","UTF-8",$Name);
//$tempstr=mb_convert_encoding("$Name",'UTF-8','unicode');
//echo "$tempstr <br>";
$Name=urldecode($Name); //解码 
//echo $Name;
$sListResult = mysql_query("SELECT D.Id,D.Caption,D.Attached,D.Date,D.EndDate,D.Locks,D.TypeId,T.Name AS Type,T.SubName,D.Operator 
FROM $DataIn.zw2_hzdoc D
LEFT JOIN $DataPublic.zw2_hzdoctype T ON T.Id=D.TypeId 
WHERE T.SubName='$Name' ORDER BY T.SubName",$link_id);

/*
echo "SELECT D.Id,D.Caption,D.Attached,D.Date,D.Locks,D.TypeId,T.Name AS Type,T.SubName,D.Operator 
FROM $DataIn.zw2_hzdoc D
LEFT JOIN $DataPublic.zw2_hzdoctype T ON T.Id=D.TypeId 
WHERE T.SubName='$Name' ORDER BY T.SubName ";
//echo "";
*/
$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		
		$Id=$StockRows["Id"];
		$TypeId=$StockRows["TypeId"];
		$Caption=$StockRows["Caption"];
		$Attached=$StockRows["Attached"];
		$d=anmaIn("download/hzdoc/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
			
		$Date=$StockRows["Date"];	
		$Today=date("Y-m-d");
		$EndDate=$StockRows["EndDate"];
		$WarningDate=date("Y-m-d",strtotime($EndDate."- 31 day")); //一个月警告
		if($WarningDate<$Today){ //一个月警告
			$EndDate="<div class='redB' title='警告'>$EndDate</div>";
		}
		/*
		else {
			if ($EndDate<=$Today){
				$EndDate="<div class='redB' title='警告'>$EndDate</div>";
			}
		}
		*/		
		$Type=$StockRows["Type"];
		$SubName=$StockRows["SubName"];
		$Locks=$StockRows["Locks"];
		$Operator=$StockRows["Operator"];
		include "../model/subprogram/staffname.php";
		
		echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
		echo"<td align='Left' style='word-break:break-all'>$SubName</td>";//
		echo"<td align='Left' style='word-break:break-all'>$Caption</td>";		
		echo"<td align='center'>$Attached</td>";
		echo"<td width='80' align='center'>$Date</td>";	
		echo"<td width='85' align='center'>$EndDate</td>";	
		echo"<td width='60' align='center'>$Operator</td>";		
		echo"</tr>";
		$i=$i+1;
		
		//echo "<td width='55' align='center'>$Date</td>";
	}while ($StockRows = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>无相关的产品.</td></tr>";
	}

//echo"</table>"."";
echo"</table>";

echo"</td><tr></table>"."";

?>