<?php   
/*电信---yang 20120801
$DataPublic.adminitype
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
echo "<SCRIPT src='../model/publicfun.js' type=text/javascript></script>";
$sumCols="7,10";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=11;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 退货产品记录");
$funFrom="product_returned";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|40|退货日期|80|产品ID|60|产品名称|250|Product Code|150|已出数量<br>(下单次数)|100|退货数量|60|最后出货<br>日期|80|单价|60|退货金额|80|最后下单<br>日期|80|操作|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
$flag=0;
$ActioToS="1,2,4,5,6,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	//客户
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT R.CompanyId,C.Forshort FROM $DataIn.product_returned R 
	LEFT JOIN $DataIn.trade_object C ON R.CompanyId=C.CompanyId
	WHERE 1 AND C.Estate=1 GROUP BY R.CompanyId",$link_id);
	if($myrow = mysql_fetch_array($result)){
		do{
			$theCompanyId=$myrow["CompanyId"];
			$theForshort=$myrow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows.=" AND R.CompanyId=".$theCompanyId;
				}
			 else{
			 	echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		}
	echo"</select>&nbsp;";
	echo"<select name='ProductType' id='ProductType' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT P.TypeId,T.TypeName,T.Letter,C.Color 
	FROM $DataIn.product_returned R 
	LEFT JOIN $DataIn.productdata P ON P.eCode=R.eCode
	LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
	WHERE T.Estate=1 $SearchRows GROUP BY P.TypeId ORDER BY T.mainType DESC,T.Letter",$link_id);
	echo "<option value='' selected>全部分类</option>";
	while ($myrow = mysql_fetch_array($result)){
			$TypeId=$myrow["TypeId"];
			$Color=$myrow["Color"]==""?"#FFFFFF":$myrow["Color"];
			if ($ProductType==$TypeId){
				echo "<option value='$TypeId' style= 'color: $Color;font-weight: bold' selected>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			else{
				echo "<option value='$TypeId' style= 'color: $Color;font-weight: bold'>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			} 
    echo"</select>&nbsp;";
	
	$TypeIdSTR=$ProductType==""?"":" AND P.TypeId=".$ProductType;
	$SearchRows.=$TypeIdSTR;
	
	
	$TempProfitSTR="LastMStr".strval($ReQtyType); 
	$$TempProfitSTR="selected";
	echo"<select name='ReQtyType' id='LastM' onchange='ResetPage(this.name)'>";
		echo"<option value='' $LastMStr>全部退货</option>
		<option value='1' style= 'color:#FF00CC;'  $LastMStr1>超过1‰不良品</option>
		<option value='2' style= 'color:#FF0000;'  $LastMStr2>18个月内未下单</option>
	</select>&nbsp;";
	/*
	switch($ReQtyType){
		case 1://<6
			$ShipMonthStr=" AND (E.Months<6 OR E.Months IS NULL)";
		break;
		case 2://6<=  <12
			$ShipMonthStr=" AND E.Months>5 AND E.Months<12 AND E.Months IS NOT NULL";
		break;
		case 3://>=12
			$ShipMonthStr=" AND E.Months>11 AND E.Months IS NOT NULL";
		break;
		default://全部
		$ShipMonthStr="";
		break;
		}
	*/
	
	
	}
else{
	$flag=1;
}
//$otherAction="<span onClick='putXML()' $onClickCSS>导入EXCEL文件</span>&nbsp;";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT R.Id,R.eCode,R.ReturnMonth,R.Qty,R.Price,(R.Qty*R.Price) AS Amount,R.Locks,P.cName,P.ProductId,R.Operator
FROM $DataIn.product_returned R 
LEFT JOIN $DataIn.productdata P ON P.ProductId=R.ProductId
WHERE 1 $SearchRows ORDER BY R.ReturnMonth DESC,R.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$ReturnMonth=$myRow["ReturnMonth"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$myRow["Amount"]);
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		
		//add by zx 2012-09-27 Begin
		include "../model/subprogram/product_chjq.php";
		//订单总数
		$checkAllQty= mysql_query("
								  SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders FROM( 
									SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.eCode LIKE (SELECT eCode FROM $DataIn.productdata WHERE ProductId='$ProductId') GROUP BY OrderPO
									)A
								  ",$link_id);
		$AllQtySum=toSpace(mysql_result($checkAllQty,0,"AllQty"));
		$Orders=mysql_result($checkAllQty,0,"Orders");
		//已出货数量
		$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
		$ShipQtySum=toSpace(mysql_result($checkShipQty,0,"ShipQty"));
		
		//最后出货日期
		$checkLastdate= mysql_query("SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m-%d') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,S.ProductId            
                FROM $DataIn.ch1_shipmain M 
	            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
                WHERE 1  AND  S.ProductId='$ProductId'  GROUP BY S.ProductId ORDER BY M.Date DESC	" ,$link_id);
		
		
		$LastMonth=mysql_result($checkLastdate,0,"LastMonth");
		$Months=mysql_result($checkLastdate,0,"Months");
		//echo "$LastShipMonth : $Months";
		if($Months!=NULL){
			if($Months<6){//6个月内绿色
				$LastShipMonth="<div class='greenB'>".$LastMonth."</div>";
				}
			else{
				if($Months<12){//6－12个月：橙色
					$LastShipMonth="<div class='yellowB'>".$LastMonth."</div>";
					}
				else{//红色
					$LastShipMonth="<div class='redB'>".$LastMonth."</div>";
					}
				}
			
			}
		else{//没有出过货
			$LastShipMonth="&nbsp;";
			}
			
		//最后下单日期
		$checkLastdate= mysql_query("SELECT DATE_FORMAT(MAX(M.OrderDate),'%Y-%m-%d') AS LastOrderDate,TIMESTAMPDIFF(MONTH,MAX(M.OrderDate),now()) AS OrderMonths,S.ProductId            
				FROM $DataIn.yw1_ordermain M 
				LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
				WHERE 1  AND  S.ProductId='$ProductId'  GROUP BY S.ProductId ORDER BY M.OrderDate DESC	" ,$link_id);
		

		$LastOrderDate=mysql_result($checkLastdate,0,"LastOrderDate");
		$OrderMonths=mysql_result($checkLastdate,0,"OrderMonths");	
		if($OrderMonths!=NULL){
			if($OrderMonths>=18){//6个月内绿色
				$LastOrderDate="<div class='redB'>".$LastOrderDate."</div>";
				}
		}
		
		//百分比
		$TempInfo="style='CURSOR: pointer;' onclick='ViewChart($ProductId,1)'";
		$TempPC=$AllQtySum==0?0:($ShipQtySum/$AllQtySum)*100;
		$TempPC=$TempPC>=1?(round($TempPC)."%"):(sprintf("%.2f",$TempPC)."%");
		if($AllQtySum>0){
			$TempInfo.="title='订单总数:$AllQtySum,已出数量占:$TempPC'";
			}
         //退货数量
		$checkReturnedQty= mysql_query("SELECT SUM(Qty) AS ReturnedQty FROM $DataIn.product_returned WHERE ProductId='$ProductId'",$link_id);
		$ReturnedQty=toSpace(mysql_result($checkReturnedQty,0,"ReturnedQty"));
		
		
		if($ReturnedQty>0 && $ShipQtySum>0){
			//退货百分比
			$ReturnedPercent=sprintf("%.1f",(($ReturnedQty/$ShipQtySum)*1000));
			if($ReturnedPercent>=5){
				//$ReturnedQty="<span class=\"redB\">".$ReturnedQty."</span>";
				$Qty="<span class=\"redB\">".$Qty."</span>";
				}
			else{
					if($ReturnedPercent>=2){
						//$ReturnedQty="<span class=\"yellowB\">".$ReturnedQty."</span>";
						$Qty="<span class=\"yellowB\">".$Qty."</span>";
						}
					else{
						//$ReturnedQty="<span class=\"greenB\">".$ReturnedQty."</span>";
						$Qty="<span class=\"greenB\">".$Qty."</span>";
						}
					}
			$ReturnedP=
			$TempInfo2="style='CURSOR: pointer;' onclick='ViewChart($ProductId,2)' title=\" 总退货量：$ReturnedQty  退货率：$ReturnedPercent ‰\"";
			}
		else{
			$ReturnedQty="&nbsp;";
			$TempInfo2="";
			}
			//$ShipQtySum="<span class='yellowB'>".$ShipQtySum."</span>";
		//高清图片检查		
		$checkImgSQL=mysql_query("SELECT Picture FROM $DataIn.productimg WHERE ProductId=$ProductId",$link_id);
		if($checkImgRow=mysql_fetch_array($checkImgSQL)){
			$Picture=$checkImgRow["Picture"];
			$f=anmaIn($Picture,$SinkOrder,$motherSTR);
				$ProductId="<a href='openorload.php?d=$d&f=$f&Type=product'>$ProductId</a>";
			}	
		$ShipQtySum="<span class='yellowB'>".$ShipQtySum."</span>";
		$GfileStr=$GfileStr==""?"&nbsp;":$GfileStr;
		$TableId="ListTable$i";
		
		//出货数量和下单次数
		if($Orders>0){
			if($Orders<2){
				$ShipQtySum=$ShipQtySum."<span class=\"redB\">($Orders)</span>";
				}
			else{
				if($Orders>4){
					$ShipQtySum=$ShipQtySum."<span class=\"greenB\">($Orders)</span>";
					}
				else{
					$ShipQtySum=$ShipQtySum."<span class=\"yellowB\">($Orders)</span>";	
					}
				}
			}
        $FromDir='public';
		$URL="productdata_chart.php";
        $theParam="Pid=$ProductId&Type=2";
		//echo "$theParam";
		//获取当前文件所在目录
		$showPurchaseorder="<img onClick='P_ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"$FromDir\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏子分类情况.' width='13' height='13' style='CURSOR: pointer'>";
		//echo "PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\")";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		$ShowStr="";	
		switch($ReQtyType){
			case 1://超过1‰不良品
			    if($ReturnedPercent<1){
					$ShowStr="未超过1‰不良品";
				}
			break;
			case 2://18个月内末下单
			

				//echo "$LastOrderDate: $OrderMonths";
				if($OrderMonths!=NULL){
					if($OrderMonths<18){//
						$ShowStr="18个月内有下单";
					}
				}
				
			break;

			default://全部
				$ShowStr="";
			break;
			}			
		//add by zx 2012-09-27 End

		if  ($ShowStr=="") {
		
				
				$ValueArray=array(
					array(0=>$ReturnMonth,
							 1=>"align='center'"),
					array(0=>$ProductId,
							 1=>"align='center'"),
					array(0=>$cName),
					array(0=>$eCode),
					array(0=>$ShipQtySum,		1=>"align='center'",2=>$TempInfo),
					array(0=>$Qty,
							 1=>"align='right'",2=>$TempInfo2),
					array(0=>$LastShipMonth,			1=>"align='center'"),
					array(0=>$Price,
							 1=>"align='center'"),
					array(0=>$Amount,					
							 1=>"align='right'"),
					array(0=>$LastOrderDate,			1=>"align='center'"),
					array(0=>$Operator,					
							 1=>"align='center'")
					);
				$checkidValue=$Id;
				
				include "../model/subprogram/read_model_6.php";
				echo $StuffListTB;	
				}
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
if($flag==1){
	$Sum=0;
	$sumSql="SELECT R.Id,R.eCode,R.ReturnMonth,R.Qty,R.Price,(R.Qty*R.Price) AS Amount,R.Locks,P.cName,P.ProductId,R.Operator
	FROM $DataIn.product_returned R 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=R.ProductId WHERE 1 $SearchRows ORDER BY R.ReturnMonth,R.Id";

	$sumResult = mysql_query($sumSql,$link_id);
	if($sumRow = mysql_fetch_array($sumResult)){
		do{
			$Sum+=$sumRow["Amount"];
			$Count=$sumRow["Count"];
		}while($sumRow = mysql_fetch_array($sumResult));
	}
	$SumToTal= mysql_num_rows($sumResult);
	echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";		
	echo "<tr>";
	echo "<td class='A0111' width=220 align='center' height='30'>数量：</td>";
	echo "<td class='A0101' width=250 align='center' height='30'>$SumToTal</td>";	
	echo "<td class='A0101' align='center' height='30'>总计：</td>";
	echo "<td class='A0101' width=200 align='center' height='30'>$Sum</td>";
	echo "</tr></table>";
}
//步骤7：
echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
<tr>
<td class='A0010' width=\"100\" align=\"right\">下单次数:</td>
<td width=\"100\" align=\"center\"><span class=\"greenB\">(绿色)></span></td>
<td width=\"100\" align=\"center\">4</td>
<td width=\"100\" align=\"center\"><span class=\"yellowB\">>=(橙色)></span></td>
<td width=\"100\" align=\"center\">1</td>
<td width=\"100\" align=\"center\"><span class=\"redB\">=(红色)</span></td>
<td width=\"100\">&nbsp;</td>
<td width=\"100\"><span class=\"redB\">&nbsp;</span></td>
<td class=\"A0001\">&nbsp;</td>
</tr>
<tr>
<td class='A0010'  align=\"right\">退货率:</td>
<td align=\"center\"><span class=\"greenB\">(绿色)<</span></td>
<td align=\"center\">2‰</td>
<td align=\"center\"><span class=\"yellowB\"><=(橙色)<</span></td>
<td align=\"center\">5‰</td>
<td align=\"center\"><span class=\"redB\"><=(红色)</span></td>
<td>&nbsp;</td>
<td><span class=\"redB\">&nbsp;</span></td>
<td class=\"A0001\">&nbsp;</td>
</tr>

<td class='A0010'  align=\"right\">18个月未下单:</td>
<td align=\"center\"><span class=\"redB\">(红色)<</span></td>
<td align=\"center\">&nbsp;</td>
<td align=\"center\">&nbsp;</td>
<td align=\"center\">&nbsp;</td>
<td align=\"center\">&nbsp;</td>
<td>&nbsp;</td>
<td><&nbsp;</td>
<td class=\"A0001\">&nbsp;</td>
</tr>
</table>";
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript">
 function putXML(){
     var r=Math.random(); 
     var BackData=window.showModalDialog("product_returned_inxml.php","BackData","dialogHeight:500px;dialogWidth:780px;center=yes;help=0;scroll=yes"); 
 }
function ViewChart(Pid,OpenType){
	document.form1.action="../public/productdata_chart.php?Pid="+Pid+"&Type="+OpenType;
	document.form1.target="_blank";
	document.form1.submit();		
	document.form1.target="_self";
	document.form1.action="";
	} 
</script>
