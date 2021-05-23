<?php 
//ewen 2013-03-05 OK
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
include "../model/subprogram/read_model_3.php";
//过滤条件
if($From!="slist"){
	$SearchRows=" AND B.Estate='3'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' selected>未结付货款</option><option value='0' >已结付货款</option></select>&nbsp;";	
	//供应商
	$providerSql = mysql_query("SELECT B.CompanyId,C.Forshort FROM $DataIn.nonbom12_cwsheet B LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId WHERE 1 $SearchRows GROUP BY B.CompanyId ORDER BY C.Forshort",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{
			$Forshort=$providerRow["Forshort"];
			$thisCompanyId=$providerRow["CompanyId"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" AND B.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
		}
	}
else{
	$SearchRows.=" AND B.Estate='3'";
	}
//结付的银行
include "../model/selectbank1.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

echo"$CencalSstr";

//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.Id,B.cgId,B.GoodsId,B.Qty,B.Price,(B.Qty*B.Price) AS Amount,B.Month,B.Estate,B.Locks,B.Date,
H.TypeName,
C.Forshort,C.CompanyId,
D.GoodsName,D.BarCode,D.Unit,D.Attached,
E.wStockQty,E.oStockQty,E.mStockQty,F.Operator,G.PurchaseID,G.Id AS cgMid,I.Symbol
FROM $DataIn.nonbom12_cwsheet B
LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=B.GoodsId
LEFT JOIN $DataPublic.nonbom2_subtype H  ON H.Id=D.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock E ON E.GoodsId=B.GoodsId
LEFT JOIN $DataIn.nonbom6_cgsheet F ON F.Id=B.cgId
LEFT JOIN $DataIn.nonbom6_cgmain G ON G.Id=F.Mid
LEFT JOIN $DataPublic.currencydata I ON I.Id=C.Currency
WHERE 1 $SearchRows ORDER BY B.Date DESC,B.cgId,B.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$cgId=$myRow["cgId"];
		$GoodsId=$myRow["GoodsId"];
		$GoodsName=$myRow["GoodsName"];
		$BarCode=$myRow["BarCode"];
		$TypeName=$myRow["TypeName"]==""?"&nbsp;":$myRow["TypeName"];
		$Estate= "<div class='yellowB'>未结付</div>";
		$Date=$myRow["Date"];
		$Month=$myRow["Month"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
		$Unit=$myRow["Unit"];
		$Symbol=$myRow["Symbol"];
		$Price=sprintf("%.3f",$myRow["Price"]);
		$Qty=del0($myRow["Qty"]);
		$Amount=sprintf("%.3f",$Price*$Qty);
		$Forshort=$myRow["Forshort"];
		$Locks=$myRow["Locks"];
		//检查收货数量
		//入库数量
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.nonbom7_insheet WHERE GoodsId='$GoodsId' AND cgId='$cgId'",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:del0($rkQty);
		$wsBgColor="";
		$wsQty=$Qty-$rkQty;
		if($wsQty!=0){
			if($rkQty==0){
				$rkQty="&nbsp;";
				}
			else{
				$rkQty="<a href='nonbom7_list.php?cgId=$cgId' target='_blank'><span class='redB'>$rkQty</span></a>";
				}
			$LockRemark="还有欠数,不能结付!";
			}
		else{
			$rkQty="<a href='nonbom7_list.php?cgId=$cgId' target='_blank'><span class='greenB'>$rkQty</span></a>";
			}
		$cgMid=$myRow["cgMid"];
		$PurchaseID=$myRow["PurchaseID"];
		$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$cgMidSTR' target='_blank'>$PurchaseID</a>";
		$CompanyIdTemp=$myRow["CompanyId"];
		//加密
		$CompanyIdTemp=anmaIn($CompanyIdTemp,$SinkOrder,$motherSTR);		
		$Forshort="<a href='nonbom3_view.php?d=$CompanyIdTemp' target='_blank'>$Forshort</a>";
		//历史单价
		$Price="<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
		//配件分析
		$GoodsId="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
		$ValueArray=array(
			array(0=>$Forshort),
			array(0=>$PurchaseID,1=>"align='center'"),
			array(0=>$TypeName),
			array(0=>$GoodsId,1=>"align='center'"),
			array(0=>$GoodsName),
            array(0=>$BarCode,1=>"align='center'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$rkQty,1=>"align='right'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='right'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Month,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
if($CompanyId!=""){
$DjTable="";
$checkDj=mysql_query("SELECT B.PayDate,A.Id,A.PurchaseID,A.Amount,A.Remark,A.Date,C.Name AS Operator 
	FROM $DataIn.nonbom11_djsheet A
	LEFT JOIN $DataIn.nonbom11_djmain B ON B.Id=A.Mid
	LEFT JOIN $DataPublic.staffmain C ON C.Number=A.Operator
	 WHERE A.CompanyId='$CompanyId' AND A.Did='0'",$link_id);
if($checkRow = mysql_fetch_array($checkDj)){
	$DjTable="<br><table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#CCCCCC'><tr><td class='A1111' height='30'>未抵货款订金列表</td></tr></table>
		<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
		<tr bgcolor='#CCCCCC'>
		<td width='40' height='25' class='A0111' align='center'>选项</td><td width='40' class='A0101' align='center'>序号</td>
		<td width='100' class='A0101' align='center'>预付日期</td>
		<td width='100' class='A0101' align='center'>预付采购单</td>
		<td class='A0101' align='center'>预付说明</td>
		<td width='60' class='A0101' align='center'>预付金额</td>
		<td width='75' class='A0101' align='center'>请款人</td>
		</tr>";
	$d=1;
	do{
		$djPayDate=$checkRow["PayDate"];
		$djId=$checkRow["Id"];
		$PurchaseID=$checkRow["PurchaseID"];
		$djAmount=$checkRow["Amount"]<0?"<div class='redB'>$checkRow[Amount]</div>":$checkRow["Amount"];
		$djRemark=$checkRow["Remark"];
		$djDate=$checkRow["Date"];
		$djOperator=$checkRow["Operator"];
		$DjTable.="<tr>
			<td align='center' class='A0111' height='20'><input name='checkdj[]' type='checkbox' id='checkdj$d' value='$djId'></td>
			<td align='center' class='A0101'>$d</td>
			<td align='center' class='A0101'>$djPayDate</td>
			<td class='A0101' align='center'>$PurchaseID</td>
			<td class='A0101'>$djRemark</td>
			<td align='right' class='A0101'>$djAmount</td>
			<td align='center' class='A0101'>$djOperator</td>
			</tr>";
		$d++;
		}while ($checkRow = mysql_fetch_array($checkDj));
	$DjTable.="</table>";
	}
echo $DjTable;
}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";

?>
<script>
function zhtj(obj){
	switch(obj){
		case "chooseMonth":
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
		break;
		}
	document.form1.action="";
	document.form1.submit();
	}
</script>