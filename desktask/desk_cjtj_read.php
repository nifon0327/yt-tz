<?php   
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$tableMenuS=600;
$funFrom="desk_cjtj";
$nowWebPage=$funFrom."_read";
$OrderType=$OrderType==""?1:$OrderType;
if($Tid==6){
	$Th_Col="&nbsp;|60|序号|30|客户|80|PO号|100|中文名|300|Product Code|200|订单数量|60|生产状态|60|已完成|80|未完成|80";//出货时间|80|
	$sumCol=6;
	}
else{
	$Th_Col="&nbsp;|60|序号|30|客户|80|PO号|100|中文名|300|订单数量|60|生产状态|60|已完成|80|未完成|80";//出货时间|80|
	}
$ColsNumber=6;
$sumCol=$sumCol==""?5:$sumCol;
//更新
//需计算的车缝相关产品
$sumCols="".$sumCol;
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	$DataSTR="";
	//统计分类
	echo"<select name='Tid' id='Tid' onchange='document.form1.submit()'>";
		$typeResult = mysql_query("SELECT Id,Remark  FROM $DataIn.sc1_counttype WHERE Estate=1 ORDER BY Id",$link_id);
		if ($typeRow = mysql_fetch_array($typeResult)){
			do{
				$typeValue=$typeRow["Id"];
				$Remark=$typeRow["Remark"];
				$Tid=$Tid==""?$typeValue:$Tid;
				if($Tid==$typeValue){
					echo"<option value='$typeValue' selected>$Remark</option>";
					$SearchRows=" AND T.Id='$typeValue'";
					}
				else{
					echo"<option value='$typeValue'>$Remark</option>";
					}
				}while($typeRow = mysql_fetch_array($typeResult));
			}
	echo"</select>&nbsp;";
	echo"<select name='OrderType' id='OrderType' onchange='ResetPage(this.name)'>";
	if($OrderType==1){
		echo"<option value='1' selected>未出</option>
		<option value='0'>已出</option></select>&nbsp;";
		$SearchRows.=" AND S.Estate>0";
		}
	else{
		echo"<option value='1'>未出</option>
		<option value='0' selected>已出</option></select>&nbsp;";
		$SearchRows.=" AND S.Estate=0 AND CM.Date>='2009-04-01'";
		$DataSTR="LEFT JOIN $DataIn.ch1_shipsheet CS ON CS.POrderId=S.POrderId
			LEFT JOIN  $DataIn.ch1_shipmain CM ON CM.Id=CS.Mid";
		}
	$ClientResult= mysql_query("SELECT P.CompanyId,C.Forshort 
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId 
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	LEFT JOIN $DataIn.sc1_counttype T ON T.TypeId=D.TypeId
	$DataSTR
	WHERE 1 $SearchRows 
	GROUP BY P.CompanyId order by P.CompanyId",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];$theForshort=$ClientRow["Forshort"];
			//$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";$SearchRows.=" AND M.CompanyId='$theCompanyId'";$DefaultClient=$theForshort;
				}
			else{
				echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		echo"</select>&nbsp;";
		}
	}
include "../model/subprogram/read_model_5.php";
$sumQty=0;$unQtySum=0;$cfQtySum=0;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT
S.Id,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.DeliveryDate,S.ShipType,S.scFrom,S.Estate,S.Locks,P.CompanyId,P.cName,P.eCode,P.TestStandard,P.pRemark,C.Forshort
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.sc1_counttype T ON T.TypeId=D.TypeId
$DataSTR
WHERE 1 $SearchRows ORDER BY P.CompanyId ,S.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
	  	//初始化计算的参数
		$m=1;
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$Id=$myRow["Id"];
		//$TypeId=$myRow["TypeId"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		$Forshort=$myRow["Forshort"];
		if($TestStandard==1){
			include "subprogram/teststandard_y.php";
			}
		else{
			if($TestStandard==2){
				$TestStandard="<div class='blueB' title='标准图审核中'>$cName</div>";
				}
			else{
				$TestStandard=$cName;
				}
			}
		$Qty=$myRow["Qty"];
		$POrderId=$myRow["POrderId"];
		$scFrom=$myRow["scFrom"];
		$Locks=1;$Keys=5;
		switch($scFrom){
			case 0:
			$scSign="<div class='greenB'>生产完成</div>";
			break;
			case 2:
			$scSign="<div class='yellowB'>生产中</div>";
			break;
			default:
			$scSign="&nbsp;";$Locks=0;
			break;
			}
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		
		$sumQty=$sumQty+$Qty;
		//订单状态色
		$checkColor=mysql_query("SELECT Id FROM $DataIn.cg1_stocksheet WHERE Mid='0' and (FactualQty>'0' OR AddQty>'0' ) and PorderId='$POrderId' LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
			}
		else{//已全部下单，看领料数量		
			$OrderSignColor="bgColor='#339900'";	//设默认绿色
			//领料数量不等时，黄色
			$checkLL=mysql_fetch_array(mysql_query("SELECT SUM(L.Qty) AS LQty FROM $DataIn.ck5_llsheet L WHERE L.StockId LIKE '$POrderId%'",$link_id));
			$LQty=$checkLL["LQty"];
			$checkCK=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS GQty FROM $DataIn.cg1_stocksheet G WHERE G.POrderId='$POrderId'",$link_id));
			$GQty=$checkCK["GQty"];	
			if($GQty!=$LQty){
				$OrderSignColor="bgColor='#FFCC00'";
				}
			}
		$ColbgColor="";
		//加急订单
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			do{
				$Type=$checkExpressRow["Type"];
				switch($Type){
					case 1:$ColbgColor="bgcolor='#0066FF'";break;
					case 7:$theDefaultColor="#FFA6D2";break;
					}
				}while ($checkExpressRow = mysql_fetch_array($checkExpress));
			}
		//动态读取
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"$Tid\");' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//读取生产列表，读取此订单的生产记录
		$CheckCfQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS cfQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId' AND C.Tid='$Tid'",$link_id));
		$cfQty=$CheckCfQty["cfQty"]==""?0:$CheckCfQty["cfQty"];$cfQtySum+=$cfQty;
		
		$unQty=$Qty-$cfQty;$unQtySum+=$unQty;
		//着色设置
		$BGcolor="class='yellowB'";
		if($cfQty==0){//红色
			$BGcolor="class='redB'";
			}
		if($unQty==0){//绿色
			$BGcolor="class='greenB'";
			$Locks=0;
			}
		$cfQty=zerotospace($cfQty);
		$unQty=zerotospace($unQty);
		if($Tid==6){
			$ValueArray=array(
				array(0=>$Forshort),
				array(0=>$OrderPO),
				array(0=>$TestStandard),
				array(0=>$eCode),
				array(0=>$Qty,		1=>"align='right'"),
				array(0=>$scSign,	1=>"align='center'"),
				array(0=>"<div $BGcolor>$cfQty</div>",	1=>"align='right'"),
				array(0=>"<div $BGcolor>$unQty</div>", 	1=>"align='right'")
				);
			}
		else{
			$ValueArray=array(
				array(0=>$Forshort),
				array(0=>$OrderPO),
				array(0=>$TestStandard),
				array(0=>$Qty,		1=>"align='right'"),
				array(0=>$scSign,	1=>"align='center'"),
				array(0=>"<div $BGcolor>$cfQty</div>",	1=>"align='right'"),
				array(0=>"<div $BGcolor>$unQty</div>", 	1=>"align='right'")
				);
			}
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='$thePointerColor'><tr>";
	echo"<td class='A0111' height='20'>合计</td>";
	echo"<td class='A0101' width='60' align='right'>$sumQty</td>";
	echo"<td class='A0101' width='60' align='right'>&nbsp;</td>";
	echo"<td class='A0101' width='80' align='right'>$cfQtySum</td>";
	echo"<td class='A0101' width='80' align='right'>$unQtySum</td>";
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle($SubCompany."车间生产统计类未出明细列表");
$ActioToS="";//11
include "../model/subprogram/read_model_menu.php";
?>