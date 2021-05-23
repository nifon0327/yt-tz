<?php 
/*
已更新电信---yang 20120801
*/
include "../model/subprogram/s1_model_1.php";
$Th_Col="&nbsp;|40|序号|30|客户|80|PO号|100|中文名|300|工序总数|80|完成数量|80|剩余数量|80";//出货时间|80|
$ColsNumber=8;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$cjtjSTR=" AND T.Id='$Jid'";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$Bid=$Bid==""?1:$Bid;
$Parameter.=",Bid,$Bid,Jid,$Jid";
echo"<select name='Bid' id='Bid' onchange='ResetPage(this.name)'>";
if($Bid==1){
	echo"<option value='1' selected>未出</option>
	<option value='0'>已出</option></select>&nbsp;";
	$sSearch=" AND S.Estate>0";
	}
else{
	echo"<option value='1'>未出</option>
	<option value='0' selected>已出</option></select>&nbsp;";
	$sSearch=" AND S.Estate=0 AND CM.Date>='2009-04-01'";
	$DataSTR="LEFT JOIN $DataIn.ch1_shipsheet CS ON CS.POrderId=S.POrderId
		LEFT JOIN  $DataIn.ch1_shipmain CM ON CM.Id=CS.Mid";
	}
echo"<input name='Jid' type='hidden' id='Jid' value='$Jid'><select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT P.CompanyId,
S.Id,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.pRemark,C.Forshort
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.sc1_counttype T ON T.TypeId=D.TypeId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
$DataSTR
WHERE 1 $cjtjSTR $sSearch ORDER BY P.CompanyId,S.Id DESC";
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
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
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
		$Forshort=$myRow["Forshort"];
		$Qty=$myRow["Qty"];
		$POrderId=$myRow["POrderId"];
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=1;
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
		//0:内容	1：对齐方式		2:单元格属性		3：截取
		//已完成工序数
		$CheckCfQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS cfQty 
		FROM $DataIn.sc1_cjtj C 
		LEFT JOIN $DataIn.sc1_counttype T ON T.Id=C.Tid
		WHERE C.POrderId='$POrderId' AND T.Id=$Jid",$link_id));
		$OverQty=$CheckCfQty["cfQty"]==""?0:$CheckCfQty["cfQty"];$cfQtySum+=$cfQty;
		//未完成订单数
		$UnQty=$Qty-$OverQty;
		
		//$unQty=$stuffQty-$cfQty;$unQtySum+=$unQty;
		$Locks=1;
		//着色设置
		$BGcolor="class='yellowB'";
		if($OverQty==0){//红色
			$BGcolor="class='redB'";
			}
		if($UnQty==0){//绿色
			$BGcolor="class='greenB'";
			$Locks=0;
			$LockRemark="工序已完成";
			}
		$cfQty=zerotospace($cfQty);
		$unQty=zerotospace($unQty);
		$checkidValue=$POrderId."^^".$OrderPO."^^".$cName."^^".$Qty."^^".$OverQty."^^".$UnQty;
		$ValueArray=array(
			array(0=>$Forshort),
			array(0=>$OrderPO),
			array(0=>$TestStandard),
			array(0=>"<div $BGcolor>$Qty</div>",	1=>"align='right'"),
			array(0=>"<div $BGcolor>$OverQty</div>",	1=>"align='right'"),
			array(0=>"<div $BGcolor>$UnQty</div>",		1=>"align='right'")
			);
		include "../model/subprogram/read_model_6.php";
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
?>