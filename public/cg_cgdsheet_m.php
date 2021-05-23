<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=17;
$tableMenuS=600;
$sumCols="11,12,13,14,15,16";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 配件需求单待审核列表");
$funFrom="cg_cgdsheet";
$Th_Col="选项|60|序号|30|采购|45|供应商|80|采购流水号|90|配件ID|45|配件名称|320|图档|30|历史<br>资料|35|含税价|50|默认含税价|60|单位|45|订单<br>数量|40|使用<br>库存|40|需购<br>数量|40|增购<br>数量|40|实购<br>数量|40|金额|55|更新备注|160|可用<br>库存|40";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17";
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows=" AND S.blsign = 1 AND (S.FactualQty>0 OR S.AddQty>0) AND S.Estate=1";//mainType<2为需采购KG的配件
//供应商
	$providerSql= mysql_query("SELECT 
	S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON A.TypeId=T.TypeId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	WHERE 1 $SearchRows GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='document.form1.submit();'>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;				
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and S.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,
A.StuffCname,U.Name AS UnitName,M.Name,P.Forshort,A.Gfile,A.Gstate,A.Picture,A.Gremark,C.Forshort AS Client,N.cName 
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata N ON N.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=N.CompanyId
WHERE 1 $SearchRows and  (S.FactualQty>0 OR S.AddQty>0) and S.Estate=1 ORDER BY S.BuyerId,S.CompanyId,S.StuffId DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$DefaultBgColor=$theDefaultColor;
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$theDefaultColor=$DefaultBgColor;
		$OrderSignColor=$myRow["POrderId"]==""?"bgcolor='#FFCC99'":"";
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$StockId=$myRow["StockId"];
		include "../model/subprogram/cg_cgd_jj.php";
		$StuffId=$myRow["StuffId"];
		//默认单价
		$priceRes=mysql_query("SELECT S.Price FROM $DataIn.stuffdata S WHERE S.StuffId='$StuffId'",$link_id);
		if($priceRow=mysql_fetch_array($priceRes)){
			$DefaultPrice=$priceRow["Price"]==""?"&nbsp;":$myRow["Price"];
			$DefaultPrice="<div class='greenB'>$DefaultPrice</div>";
		}
		$StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Picture=$myRow["Picture"];
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		include "../model/subprogram/stuffimg_model.php";
		include"../model/subprogram/stuff_Property.php";//配件属性
		$Price=$myRow["Price"];
		$OrderQty=$myRow["OrderQty"];
		$StockQty=$myRow["StockQty"];
		$AddQty=$myRow["AddQty"];
		$FactualQty=$myRow["FactualQty"];
		$Qty=$AddQty+$FactualQty;
		$Amount=sprintf("%.2f",$Qty*$Price);//本记录金额合计		
		$Name=$myRow["Name"];
		$Forshort=$myRow["Forshort"];		
		$AddRemark=$myRow["AddRemark"]==""?"&nbsp;":$myRow["AddRemark"];
		$Locks=$myRow["Locks"];
		$Client=$myRow["Client"];
		$cName=$myRow["cName"];
		$StockId="<div title='$Client : $cName'>$StockId</div>";
		//读取可用库存
			$checkKC=mysql_fetch_array(mysql_query("SELECT oStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId",$link_id));
			$oStockQty=$checkKC["oStockQty"];
		/*/可用库存计算
		if($StuffId!=$tempStuffId){
			$checkKC=mysql_fetch_array(mysql_query("SELECT oStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId",$link_id));
			$oStockQty=$checkKC["oStockQty"];
			$tempStuffId=$StuffId;
			//历史单价,最大值和最小值
			$checkPrice=mysql_query("SELECT MAX(Price) AS maxPrice,MIN(Price) AS minPrice FROM $DataIn.cg1_stocksheet WHERE Mid>0 AND StuffId='$StuffId' ORDER BY StuffId",$link_id);
			$maxPrice=mysql_result($checkPrice,0,"maxPrice");
			$minPrice=mysql_result($checkPrice,0,"minPrice");
			if($maxPrice==""){
				$PriceInfo="&nbsp;";
				}
			else{
				$PriceInfo="<a href='cg_historyprice.php?StuffId=$StuffId' target='_blank' title='最低历史单价: $minPrice 最高历史单价: $maxPrice'>查看</a>";
				}
			}
			*/
		$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";
		//清0
		$OrderQty=zerotospace($OrderQty);
		$StockQty=zerotospace($StockQty);
		$FactualQty=zerotospace($FactualQty);
		$AddQty=zerotospace($AddQty);
		$oStockQty=zerotospace($oStockQty);
		$Estate=$Estate==0?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		//加急订单标色
		include "../model/subprogram/cg_cgd_jj.php";
		/*加入同一单的配件  // add by zx 2011-08-04 */
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$XtableWidth=$tableWidth-160;
		$XtableWidth=0;
		//$XsubTableWidth=$subTableWidth-160;
		$StuffListTB="
			<table width='$XtableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' ><br> &nbsp;PO：$OrderPO&nbsp;<span class='redB'>业务单流水号：$POrderId </span>($Client : $cName)&nbsp;<span class='redB'>数量：$PQty </span>&nbsp;订单备注：$PackRemark <span class='redB'>出货方式：$ShipType</span> 生管备注：$sgRemark <span class='redB'>PI交期：$Leadtime</span></td>
			</tr>
			
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		$ValueArray=array(
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$StockId,1=>"align='center'"),
			array(0=>$StuffId,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile,1=>"align='center'"),
			array(0=>$OrderQtyInfo,1=>"align='center'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$DefaultPrice,1=>"align='right'"),
			array(0=>$UnitName,1=>"align='center'"),
			array(0=>$OrderQty,1=>"align='right'"),
			array(0=>$StockQty,1=>"align='right'"),
			array(0=>$FactualQty,1=>"align='right'"),
			array(0=>$AddQty,1=>"align='right'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$AddRemark,3=>"..."),
			array(0=>$oStockQty,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;echo $StockRemarkTB;	
		}while ($myRow = mysql_fetch_array($myResult));
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
