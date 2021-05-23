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
$TableId="ListTB".$RowId;
$subTableWidth=300;
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
/*
echo"<table id='$TableId'   cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='10'>&nbsp;</td>
		<td width='10' height='20'></td>
		<td width='150' align='center'>(小)分类</td>		
		";

echo "</tr> ";
*/
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

$DirArray = explode('/', $_SERVER['PHP_SELF']);
$DirArray = array_reverse($DirArray);
$FromDir=$DirArray['1'];

//echo $Name;
$sListResult = mysql_query("SELECT Distinct D.TypeId, T.SubName 
FROM $DataIn.zw2_hzdoc D
LEFT JOIN $DataPublic.zw2_hzdoctype T ON T.Id=D.TypeId 
WHERE T.Name='$Name' ORDER BY T.SubName",$link_id);


/*
echo "SELECT D.Mid,D.StuffId,D.ReQty,D.Remark,D.Date,D.Estate,D.Locks,D.Operator FROM $DataIn.ck10_tfsheet D
WHERE 1 And D.Mid=$Mid ORDER BY D.Date Desc ";
//echo "";
*/
$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		/*
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
		$Type=$StockRows["Type"];
		*/
		$SubName=$StockRows["SubName"];
		$TypeId=$StockRows["TypeId"];
		/*
		$Locks=$StockRows["Locks"];
		$Operator=$StockRows["Operator"];
		include "../model/subprogram/staffname.php";
		*/
		/*
		$URL="zw_hzdoc_ajax_a.php";
		$theParam="SubName=".urlencode($SubName);
		
		$showPurchaseorder="<img onClick='PubblicShowOrHide(SubTable$i,showgif$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\");' name='showgif$i' src='../images/showtable.gif' 
		alt='显示或隐藏关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		//echo "PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\")";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='SubTable$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			

			
		echo"<table><tr >
		<td width='55'></td>
		<td>$showPurchaseorder</td>
		<td  width='45'  align='right' height='20'>$i</td>";//
		echo"<td width='300' align='Left'>$SubName</td>";//
		echo"</tr> </table>";
		echo $StuffListTB;
		*/
		$predivNum="$TypeId";
		$DivNum=$predivNum."c";
		//echo "HideTable_$DivNum$i <br>";
		$TempId=urlencode($SubName)."|$DivNum";  //"$DeliveryDate|$BuyerId|$CompanyId|$DivNum";//交期|采购|供应商
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"zw_hzdoc_ajax_a\",\"$FromDir\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr >
			    <td width='55'></td>
				 <td bgcolor='#99FF99' width='300'>&nbsp;$showPurchaseorder $SubName</td>
			</tr></table>";
		echo"<table width='$tableWidth'   cellspacing='1' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#FFFFFF'>
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div><br>
				</td>
			</tr></table>
			";		
		
		$i=$i+1;
		
		//echo "<td width='55' align='center'>$Date</td>";
	}while ($StockRows = mysql_fetch_array($sListResult));
}
else{
	echo"<table><tr><td height='30' colspan='6'>无相关的产品.</td></tr></table>";
	}

//echo"</table>"."";

?>