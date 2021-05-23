<?php 
//ewen 2013-03-18 OK
$SearchSTR=0;
if ($CompanyId==""){return;}
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|60|序号|40|下单日期|100|采购单号|60|采购|60|备注|300|配件ID|40|配件名称|200|申购数量|60|货币|40|单价|60|金额|60|流水号|50";
$ColsNumber=100;
$tableMenuS=500;
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量,13
echo "<input name='CompanyId' type='hidden' id='CompanyId' value='$CompanyId' >";
$SearchRows=" AND B.CompanyId='$CompanyId'";
echo"<select name='Pagination' id='Pagination' onchange='RefreshPage(\"nonbom11_s1\")'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
/*
$mySql="SELECT A.Mid,B.PurchaseID,B.CompanyId,B.BuyerId,B.Remark,B.Date,B.Operator,
C.Forshort,D.Name AS Buyer
FROM $DataIn.nonbom6_cgsheet A
LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.Mid 
LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
LEFT JOIN $DataPublic.staffmain D ON D.Number=B.BuyerId
LEFT JOIN $DataIn.nonbom12_cwsheet E ON E.cgId=A.Id
WHERE 1 $SearchRows AND A.Mid>0 AND E.cgId IS NULL GROUP BY A.Mid ORDER BY B.PurchaseID DESC";
*/
$mySql="SELECT A.Mid,B.PurchaseID,B.CompanyId,B.BuyerId,B.Remark,B.Date,B.Operator,
C.Forshort,D.Name AS Buyer
FROM $DataIn.nonbom6_cgsheet A
LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.Mid 
LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
LEFT JOIN $DataPublic.staffmain D ON D.Number=B.BuyerId
WHERE 1 $SearchRows AND A.Mid>0  GROUP BY A.Mid ORDER BY B.PurchaseID DESC";

$Keys=31;
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($myResult)){
	do{
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$PurchaseID=$mainRows["PurchaseID"];
		$Buyer=$mainRows["Buyer"];
        $checkSql1=mysql_fetch_array(mysql_query("SELECT SUM(A.Price*A.Qty) AS Amount
		FROM $DataIn.nonbom6_cgsheet A  WHERE Mid='$Mid'",$link_id));
       $SumAmount=$checkSql1["Amount"];

		$checkidValue=$Mid."^^".$PurchaseID."^^".$SumAmount;

		$Remark=$mainRows["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$mainRows[Remark]' width='16' height='16'>";
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		echo"<tr bgcolor='$theDefaultColor'
			onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
			onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
			onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
		echo"<td class='A0111' width='$Field[1]' align='center'>$Choose</td>";
		echo"<td class='A0101' width='$Field[3]' align='center'>$j</td>";//$OrderSignColor为订单状态标记色
		echo"<td  class='A0101' width='$Field[5]'>$Date</td>";
		echo"<td  class='A0101' width='$Field[7]'>$PurchaseID</td>";
		echo"<td  class='A0101' width='$Field[9]'>$Buyer</td>";
		echo"<td  class='A0101' width='$Field[11]'>$Remark</td>";
		echo"<td  class='A0101'>";
		//明细记录
		$checkSql=mysql_query("SELECT A.Id,A.GoodsId,A.Price,A.Qty,(A.Price*A.Qty) AS Amount,B.GoodsName,C.Symbol
		FROM $DataIn.nonbom6_cgsheet A
		LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
		LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency
		WHERE Mid='$Mid'",$link_id);
		if($checkRow = mysql_fetch_array($checkSql)){
			echo"<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
			do{
				echo"<tr><td  class='A0001' width='$Field[13]' align='right'>$checkRow[GoodsId]</td>";
				echo"<td  class='A0001' width='$Field[15]'>$checkRow[GoodsName]</td>";
				echo"<td  class='A0001' width='$Field[17]' align='right'>$checkRow[Qty]</td>";
				echo"<td  class='A0001' width='$Field[19]' align='center'>$checkRow[Symbol]</td>";
				echo"<td  class='A0001' width='$Field[21]' align='right'>".sprintf("%.2f",$checkRow["Price"])."</td>";
				echo"<td  class='A0001' width='$Field[23]' align='right'>".sprintf("%.2f",$checkRow["Amount"])."</td>";
				echo"<td width='$Field[25]' align='center'>$checkRow[Id]</td></tr>";
				}while($checkRow = mysql_fetch_array($checkSql));
			echo"</table>";
			}
		echo"</td></tr></table>";
		$i++;$j++;
		}while ($mainRows = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>