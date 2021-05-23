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
//参数拆分
//参数拆分
$TempArray=explode("|",$TempId);
$scFrom=$TempArray[0];//生产标记
$CompanyId=$TempArray[1]; //客户
$predivNum=$TempArray[2];//上级标记
$tableWidth=910;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'><tr bgcolor='#99FF99'>
		<td width='30' align='center'>序号</td>
		<td width='90' align='center'>PO</td>	
		<td width='90' align='center'>订单流水号</td>
		<td width='280' align='center'>中文名</td>
		<td width='200' align='center'>Product Code</td>				
		<td width='40' align='center'>Unit</td>
		<td width='50' align='center'>Price</td>
		<td width='50' align='center'>Qty</td>
		<td width='50' align='center'>Amount</td>
		<td width='100' align='center'>How to Ship</td>
		<td width='50' align='center'>操作员</td>
		<td width='50' align='center'>期限</td>
		</tr>";
$mySql="SELECT 
M.OrderDate,M.Operator,
S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,
P.cName,P.eCode,P.TestStandard,P.pRemark,U.Name AS Unit
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
WHERE 1 AND S.Estate>0 AND S.scFrom='$scFrom' AND M.CompanyId=$CompanyId ORDER BY M.OrderDate DESC,M.Id DESC";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1010;
$subTableWidth=990;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$OrderPO=$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];
		$Operator=$myRow["Operator"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount=$Qty*$Price;
		$ShipType=$myRow["ShipType"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
		$Unit=$myRow["Unit"];
		/*if($TestStandard==1){
			include "subprogram/teststandard_y.php";
			}
		else{
			if($TestStandard==2){
				$TestStandard="<div class='blueB' title='标准图审核中'>$cName</div>";
				}
			else{
				$TestStandard=$cName;
				}
			}*/
		include "../model/subprogram/staffname.php";
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/$AskDay'";
		$OrderDate=CountDays($OrderDate,0);
		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";
		echo"<td  align='center'>$OrderPO</td>";
		echo"<td  align='center'>$POrderId</td>";
		echo"<td>$TestStandard</td>";
		echo"<td>$eCode</td>";
		echo"<td align='center'>$Unit</td>";
		echo"<td align='center'>$Price</td>";//未收货数量
		echo"<td align='center'>$Qty</td>";//采购数量
		echo"<td align='center'>$Amount</td>";
		echo"<td align='center'>$ShipType</td>";
		echo"<td align='center'>$Operator</td>";
		echo"<td align='center'>$OrderDate</td>";
		echo"</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td height='30'>没有出货明细资料,请检查.</td></tr>";
	}
echo"</table>";
?>