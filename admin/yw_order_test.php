<?php   
//电信-EWEN
?>
<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0;} 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php   
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";

echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/Totalsharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_yw.js' type=text/javascript></script>
<script src='../model/lookup.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
include "../model/subprogram/sys_parameters.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=18;

$tableMenuS=800;
$funFrom="yw_order";
$helpFile=1;//有帮助文件
$nowWebPage=$funFrom."_read";
$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId=$Login_P_Number LIMIT 1",$link_id);
if($TRow = mysql_fetch_array($TResult)){
	$unColorCol=21;//不着色列
	$Th_Col="操作|55|序号|30|PO|80|原单|40|中文名|235|检讨|40|Product Code|150|Unit|35|Price|55|Profit|55|理论净利|80|Qty|50|Amount|70|订单利润|60|订单备注|110|生管备注|110|How to Ship|80|PI交期|70|交货<br>均期|40|操作员|55|期限|40|PI|30|报价规则|180";//出货时间|80||物料交期|80|产品备注|110
	$ColsNumber=24;
	$myTask=1;
	$sumCols="11,12,13";
	}
else{
	$unColorCol=18;//不着色列
	$Th_Col="操作|55|序号|30|PO|80|原单|40|中文名|235|检讨|40|Product Code|150|Unit|35|Price|55|Qty|50|Amount|70|订单备注|110|生管备注|110|How to Ship|80|PI交期|70|交货<br>均期|40|操作员|55|期限|40|PI|30|报价规则|180";//出货时间|80|物料交期|80|产品备注|110|
	$ColsNumber=21;
	$myTask=0;
	$sumCols="9,10";
	}
//更新
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort,SUM(S.Qty*S.Price*R.Rate) AS Amount
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
	LEFT JOIN $DataPublic.currencydata R ON R.Id=C.Currency
	WHERE S.Estate>0 GROUP BY M.CompanyId order by Amount DESC,M.CompanyId",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];$theForshort=$ClientRow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";$SearchRows="and M.CompanyId='$theCompanyId'";$DefaultClient=$theForshort;
				}
			else{
				echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		echo"</select>&nbsp;";
		}
	//分类
	$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName,C.Color 
	FROM $DataIn.yw1_ordermain M
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
	WHERE S.Estate>0 $SearchRows GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId",$link_id);
	if ($TypeRow = mysql_fetch_array($TypeResult)){
		echo"<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{
			$theTypeId=$TypeRow["TypeId"];
			$TypeName=$TypeRow["TypeName"];
			$Color=$TypeRow["Color"]==""?"#FFFFFF":$TypeRow["Color"];
			if($TypeId==$theTypeId){
				echo"<option value='$theTypeId' style= 'color: $Color;font-weight: bold' selected>$TypeName</option>";$SearchRows.=" AND P.TypeId='$theTypeId'";
				}
			else{
				echo"<option value='$theTypeId' style= 'color: $Color;font-weight: bold'>$TypeName</option>";
				}
			}while($TypeRow = mysql_fetch_array($TypeResult));
		echo"</select>&nbsp;";
		}
	//状态/标记选择
	//$SignType=$SignType==""?0:$SignType;
	$TempEstateSTR="SignTypeStr".strval($SignType); 
	$$TempEstateSTR="selected";
	echo"<select name='SignType' id='SignType' onchange='ResetPage(this.name)'>";
		echo"<option value='' $SignTypeStr0>全部订单</option>
		<option value='2' $SignTypeStr2>未确定订单</option>
		<option value='3' $SignTypeStr3>可生产订单</option>
		<option value='7' $SignTypeStr7>加急订单</option>
		<option value='11' $SignTypeStr11>需修改订单</option>
		";
	//过滤
		switch($SignType){
		  case 0:
		    break;
		  case 3:
		    $SearchRows.=" AND S.Estate=2"; 
			break;
		  case 11:
		    $SearchRows.=" AND I.Type='9'"; 
			break;
		  default:
		    $SearchRows.=" AND T.Type='$SignType'";
		}
	}
else{
	//查询的条件
	echo "<input name='SearchRows' type='hidden' id='SearchRows' value='$SearchRows'>";
	}
echo"$CencalSstr";

//增加快带查询Search按钮
$searchtable="productdata|P|cName|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
include "../model/subprogram/QuickSearch.php";

//步骤5：
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/CurrencyList.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理FILTER: revealTrans(transition=7,duration=0.5) blendTrans(duration=0.5);
$sumQty=0;
$sumSaleAmount=0;
$sumTOrmb=0;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
if ($SignType=="11"){//只显示需修改标准图的订单$DataIn.yw2_orderteststandar Type='9' 
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
LEFT JOIN $DataIn.yw2_orderteststandard I ON I.POrderId=S.POrderId 
WHERE 1 and S.Estate>0 $SearchRows ORDER BY M.CompanyId,M.OrderDate DESC,M.Id DESC";
//echo "$mySql";
}
else {
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
WHERE 1 and S.Estate>0 $SearchRows ORDER BY M.CompanyId,M.OrderDate DESC,M.Id DESC";
}
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$thisTOrmbOUTsum=0;
	do{

	  	//初始化计算的参数
		$m=1;$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		//加密参数
		//加密参数
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$PI=$myRow["PI"];
		if($PI!=""){
			$f1=anmaIn($PI.".pdf",$SinkOrder,$motherSTR);
			$d1=anmaIn("download/pipdf/",$SinkOrder,$motherSTR);		
			$PI="<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
			$PIoId=$POrderId . "|" .  $Id;//弹出DIV传值用
			}
		else{
			$PI="&nbsp;";
			$PIoId="N";
			}
		$ClientOrder=$myRow["ClientOrder"];
		if($ClientOrder!=""){
			$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
			$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);		
			$ClientOrder="<span onClick='OpenOrLoad(\"$d2\",\"$f2\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
			}
		else{
			$ClientOrder="&nbsp;";
			}
		
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		include "../model/subprogram/product_teststandard.php";
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=sprintf("%.3f",$myRow["Price"]);
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$Leadtime=$myRow["Leadtime"]==""?"&nbsp;":$myRow["Leadtime"];
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
		$bjRemark=$myRow["bjRemark"]==""?"&nbsp;":$myRow["bjRemark"];
		$ShipType=$myRow["ShipType"];
		//读取操作员姓名
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$OrderDate=$myRow["OrderDate"];
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/$AskDay'";
		
		$OrderDate=CountDays($OrderDate,0);
		//$POrderId=$myRow["POrderId"];
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];
		
		$thisSaleAmount=sprintf("%.3f",$Qty*$Price);//本订单卖出金额
		$sumSaleAmount=sprintf("%.3f",$sumSaleAmount+$thisSaleAmount);
		$sumQty=$sumQty+$Qty;
		//交货期
		include "../model/subprogram/product_chjq.php";
		/*利润计算*////////////
		$CompanyId=$myRow["CompanyId"];
		if($CompanyId==1044){
			$psValue=0.95;
			}
		else{
			$psValue=1;
			}
		$currency_Temp = mysql_query("SELECT A.Rate,A.Symbol FROM $DataPublic.currencydata A LEFT JOIN $DataIn.trade_object B ON A.Id=B.Currency WHERE  B.CompanyId=$CompanyId ORDER BY B.CompanyId LIMIT 1",$link_id);
		if($RowTemp = mysql_fetch_array($currency_Temp)){
			$Rate=$RowTemp["Rate"];//汇率
			$Symbol=$RowTemp["Symbol"];//货币符号
			}
		$thisTOrmbOUT=sprintf("%.3f",$thisSaleAmount*$Rate);//转成人民币的卖出金额******************************
		$thisTOrmbOUTsum+=$thisTOrmbOUT;
		//产品RMB$saleRMB_P=sprintf("%.4f",$Price*$Rate);
		
		//配件成本计算:只计算需要采购的部分
		$cbAmountUSD=0;$cbAmountRMB=0;$llcbAmountUSD=0;$llcbAmountRMB=0;//初始化
		$CostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost,SUM(A.OrderQty*A.Price*C.Rate) AS oTheCost2,C.Symbol
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1  AND S.POrderId='$POrderId' GROUP BY C.Id ORDER BY A.Id DESC",$link_id);
		if($CostRow= mysql_fetch_array($CostResult)){
			do{
				$cbAmount=sprintf("%.3f",$CostRow["oTheCost"]);
				//$cbAmount2=sprintf("%.2f",$CostRow["oTheCost2"]);
				$TempSymbol=$CostRow["Symbol"];
				$TempoTheCost=$CostRow["oTheCost"];
				$TempoTheCost2=$CostRow["oTheCost2"];
					$AmountTemp="cbAmount".strval($TempSymbol);
					$$AmountTemp=sprintf("%.0f",$TempoTheCost);//利润成本
					$AmountTemp2="llcbAmount".strval($TempSymbol);
				$$AmountTemp2=sprintf("%.0f",$TempoTheCost2);//理论成本
				}while ($CostRow= mysql_fetch_array($CostResult));
			}
		$GrossProfitp=sprintf("%.3f",$thisTOrmbOUT-$cbAmountUSD-$cbAmountRMB);
		//单品利润
		$profitRMB=sprintf("%.3f",$GrossProfitp/$Qty);
		
		//理论净利
		$profitRMB2=sprintf("%.3f",($thisTOrmbOUT-$llcbAmountUSD-$llcbAmountRMB-$llcbAmountRMB*$HzRate)/$Qty);
		
		
		$GrossProfit=$GrossProfitp;
		$profitRMB2PC=sprintf("%.0f",($profitRMB2*100)/($Price*$Rate));
		
		if($profitRMB2PC>15){
			$profitRMB2="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='greenB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
			}
		else{
			if($profitRMB2PC>7){
				$profitRMB2="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
				}
			else{
				$profitRMB2="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
				}
			}
		//订单状态色：有未下采购单，则为白色
		$checkColor=mysql_query("SELECT G.Id FROM $DataIn.cg1_stocksheet G 
		LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId' LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
			}
		else{//已全部下单	
			$OrderSignColor="bgColor='#339900'";	//设默认绿色
			//生产数量与工序数量不等时，黄色
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//工序总数
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
			FROM $DataIn.cg1_stocksheet G
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId'",$link_id));
			$scQty=$CheckscQty["scQty"];

			if($gxQty!=$scQty){
				$OrderSignColor="bgColor='#FFCC00'";
				}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			}
		$ColbgColor="";
		//加急订单
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			do{
				$Type=$checkExpressRow["Type"];
				switch($Type){
					case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
					case 2:$ColbgColor="bgcolor='#FF00'";break;		//未确定产品
					case 7:$theDefaultColor="#FFA6D2";break;		//加急					
					}
				}while ($checkExpressRow = mysql_fetch_array($checkExpress));
			}
			
        //获取最后需采购的配件交货日期
		//动态读取 $thisTOrmbINo
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//0:内容	1：对齐方式		2:单元格属性		3：截取
		//检查权限:订单备注、出货方式的权限
		if($myTask==0){
			$ValueArray=array(
				array(0=>$OrderPO,          2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,2,$POrderId,25)' style='CURSOR: pointer'",3=>"..."),
				array(0=>$ClientOrder,		1=>"align='center'"),
				array(0=>$TestStandard),
				array(0=>$CaseReport,1=>"align='center'"),
				array(0=>$eCode,			3=>"..."),
				array(0=>$Unit,				1=>"align='center'"),
				array(0=>$Price, 			1=>"align='right'"),
				array(0=>$Qty, 				1=>"align='right'"),
				array(0=>$thisSaleAmount,	1=>"align='right'"),
				array(0=>$PackRemark,		2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,11,$POrderId,2)' style='CURSOR: pointer'",3=>"..."),
				array(0=>$sgRemark, 		1=>"style='color:#F00;'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,12,$POrderId,6)' style='CURSOR: pointer'",	3=>"..."),
				array(0=>$ShipType, 		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,13,$POrderId,3)' style='CURSOR: pointer'", 3=>"..."),
				array(0=>$Leadtime,			2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,14,\"$PIoId\",4)'",3=>"..."),
				array(0=>$JqAvg,			1=>"align='center'"),
				array(0=>$Operator,			1=>"align='center'"),
				array(0=>$OrderDate,		1=>"align='center' $BackImg"),
				array(0=>$PI, 				1=>"align='center'"),
				array(0=>$bjRemark,			3=>"..."),
				);
			}
		else{
			$GrossProfit=sprintf("%.0f",$GrossProfit);
			$GrossProfitSUM=$GrossProfitSUM+$GrossProfit;
			if ($GrossProfit<0){				
				$GrossProfit=-1*$GrossProfit;
				$GrossProfit="<div class='redB'>-$GrossProfit</div>";
				}
			$ValueArray=array(
				array(0=>$OrderPO,			2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,2,$POrderId,25)' style='CURSOR: pointer'",3=>"..."),
				array(0=>$ClientOrder,		1=>"align='center'"),
				array(0=>$TestStandard),
				array(0=>$CaseReport,		1=>"align='center'"),
				array(0=>$eCode,			3=>"..."),
				array(0=>$Unit,				1=>"align='center'"),
				array(0=>$Price, 			1=>"align='right'"),
				array(0=>$profitRMB,		1=>"align='right'"),
				array(0=>$profitRMB2,		1=>"align='right'"),
				array(0=>$Qty,				1=>"align='right'"),
				array(0=>$thisSaleAmount,	1=>"align='right'"),
				array(0=>$GrossProfit,		1=>"align='right'"),
				array(0=>$PackRemark,		2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,14,$POrderId,2)' style='CURSOR: pointer'", 3=>"..."),
				array(0=>$sgRemark, 		1=>"style='color:#F00;'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,15,$POrderId,6)' style='CURSOR: pointer'", 	3=>"..."),
				array(0=>$ShipType,			1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,16,$POrderId,3)' style='CURSOR: pointer'",3=>"..."),
				array(0=>$Leadtime,			2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,17,\"$PIoId\",4)'",3=>"..."),
				array(0=>$JqAvg,			1=>"align='center'"),
				array(0=>$Operator,			1=>"align='center'"),
				array(0=>$OrderDate,		1=>"align='center' $BackImg"),
				array(0=>$PI,				1=>"align='center'"),
				array(0=>$bjRemark,			3=>"...")
				);
			}
		$checkidValue=$Id;
		include "subprogram/read_model_6_yw.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
		//array(0=>"&nbsp;&nbsp;"   ), 如果想有框线，又无值，则用此方法，可以跟前面形在框
		if($myTask==0){
			$m=1;
		    $ClientPC=sprintf("%.0f",$GrossProfitSUM*100/$thisTOrmbOUTsum);
			$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$sumQty,1=>"align='right'"),
				array(0=>$sumSaleAmount,	1=>"align='right'"),
				array(0=>"&nbsp;&nbsp;"   ),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
			//	array(0=>"&nbsp;"	),
			//	array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
                array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				
				);
				
			//$checkidValue=$Id;
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";			
		}
		else
		{
			$m=1;
		    $ClientPC=sprintf("%.0f",$GrossProfitSUM*100/$thisTOrmbOUTsum);
			$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$sumQty,1=>"align='right'"),
				array(0=>$sumSaleAmount,	1=>"align='right'"),
				array(0=>$GrossProfitSUM,		1=>"align='right'"),
				array(0=>"利润率：".$ClientPC."%",		1=>"align='left'"),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
			//	array(0=>"&nbsp;"	),
			//	array(0=>"&nbsp;"	),
                array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				
				);
				
			//$checkidValue=$Id;
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";			
		}
		
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
$myResult1 = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult1);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle($SubCompany.$DefaultClient."客户未出明细列表");
if($From!="slist"){
	$ActioToS="1,2,3,88,21,45,22,23,7,8,11,38,39,60,65,74";
	}
else{//查询页面可用功能
	$ActioToS="1,2,3,88,21,22,23,7,8,11,39,60";
	}
include "../model/subprogram/read_model_menu.php";
?>
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script language="JavaScript" type="text/JavaScript">

function updateJq(TableId,RowId,runningNum,toObj){//行即表格序号;列，流水号，更新源
	showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js

	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(toObj==25){theDiv.style.left=event.clientX + document.body.scrollLeft+'px';}
	else{
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	}
	//theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//采购单交货期
				InfoSTR="<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='14' class='TM0000' readonly>的采购单交货期:<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
				break;
			case 2:	//订单备注
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='TM0000' readonly>的订单备注<input name='PackRemark' type='text' id='PackRemark' size='50' class='INPUT0100'><br>";
				break;
			case 3://出货方式
				InfoSTR="<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='TM0000' readonly>订单出货方式<input name='ShipType' type='text' id='ShipType' size='50' class='INPUT0100'><br>";
				break;
			case 4://PI交期
			  if (runningNum=="N"){
					InfoSTR="无法更新！原因是该订单还未生成PI信息。</br><div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='关闭' onclick='CloseDiv()'></div>";
					toObj=0;
					break;
				}
			    var runNum=runningNum.split("|");
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runNum' value='"+runNum[0]+"' size='12' class='TM0000' readonly><input name='PIoId' type='hidden' id='PIoId' value='"+runNum[1]+"'>的PI交货日期<input name=PIDate' type='text' id='PIDate' size='10' maxlength='10' class='INPUT0100' onfocus='WdatePicker()' readonly>";
				break;
			case 6://生管备注
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的生管备注<input name='sgRemark' type='text' id='sgRemark' size='50' class='INPUT0100'>";
				break;
			case 25:	//订单备注
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly> 的PO:<input name='OrderPO' type='text' id='OrderPO' size='20' class='INPUT0100'><br>";
				break;					
			}
		if(toObj>1){
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
			}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9; 
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	//theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
	}

function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;
	switch(ObjId){
		case "1":		//更新采购单交货期:
			var tempDeliveryDate=document.form1.DeliveryDate.value;
			myurl="purchaseorder_updated.php?StockId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=jq";
			retCode=openUrl(myurl);
			if (retCode!=-2){
				//更新成功,隐藏DIV，并且重新写该单元格的内容	或 重新动态更新需求单列表
				if(tempDeliveryDate==""){
					tempDeliveryDate="未确定";
					}
			var ColorDate=Number(DateDiff(tempDeliveryDate));
			if(ColorDate<2){
				eval("ListTB"+tempTableId).rows[tempRowId].cells[15].innerHTML="<div class='redB'>"+tempDeliveryDate+"</div>";
				}
			else{
				if(ColorDate<5){
					eval("ListTB"+tempTableId).rows[tempRowId].cells[15].innerHTML="<div class='yellowB'>"+tempDeliveryDate+"</div>";
					}
				else{
					eval("ListTB"+tempTableId).rows[tempRowId].cells[15].innerHTML="<div class='greenB'>"+tempDeliveryDate+"</div>";
					}
				}
				CloseDiv();
				}
			break;
		case "2":		//订单说明 PackRemark
			var tempPackRemark0=document.form1.PackRemark.value;
			var tempPackRemark1=encodeURIComponent(tempPackRemark0);
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&tempPackRemark="+tempPackRemark1+"&ActionId=PackRemark";
			var ajax=InitAjax(); 
	　		ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
	　			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempPackRemark0+"</NOBR></DIV>";
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;
		case "3":		//出货方式
			var tempShipType0=document.form1.ShipType.value;
			var tempShipType1=encodeURIComponent(tempShipType0);
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&tempShipType="+tempShipType1+"&ActionId=ShipType";
			/* 只能在IE上 要改成在AJAX上
			retCode=openUrl(myurl);
			if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempShipType0+"</NOBR></DIV>";
				CloseDiv();
				}
			*/
			var ajax=InitAjax(); 
			ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempShipType0+"</NOBR></DIV>";
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;
		case "4":		//PI交期
			var tempPIDate=document.form1.PIDate.value;
			var tempPIoId=document.form1.PIoId.value;
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&PIoId="+tempPIoId+"&PIDate="+tempPIDate+"&ActionId=PIDate";
			var ajax=InitAjax(); 
	　		ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
	　			if(ajax.readyState==4){// && ajax.status ==200
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempPIDate+"</NOBR></DIV>";
				CloseDiv();
				}
			}
		    ajax.send(null); 
			break;
		case "6"://更新生管备注
			var tempsgRemark=document.form1.sgRemark.value;
			var tempsgRemark1=encodeURIComponent(tempsgRemark);//传输中文
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&sgRemark="+tempsgRemark1+"&ActionId=sgRemark";
			var ajax=InitAjax(); 
	　		ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
	　			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempsgRemark+"</NOBR></DIV>";
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;		
		case "25":		//更新PO
			var OrderPO=document.form1.OrderPO.value;
			var tempOrderPO=encodeURIComponent(OrderPO);
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&tempOrderPO="+tempOrderPO+"&ActionId=OrderPO";
			var ajax=InitAjax(); 
	　		ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
	　			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' ><NOBR>"+OrderPO+"</NOBR></DIV>";
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;				
		}
	}
	
function ChangeStuff(tempTableId,tempRowId,TypeId,toObj,StockId){
	var num=Math.random();  
	BackStockId=window.showModalDialog("yw_order_choosestuff.php?r="+num+"&TypeId="+TypeId+"&SearchNum=1","BackStockId","dialogHeight =800px;dialogWidth=500px;center=yes;scroll=yes");
	if (BackStockId){
  		var FieldArray=BackStockId.split("^^");//配件名称，配件单价
		var tempStuffId=FieldArray[0];
		var tempStuffCname=FieldArray[1];
		var tempPrice=FieldArray[2];
		//更新配件
		myurl="yw_order_updated.php?StockId="+StockId+"&StuffId="+tempStuffId+"&Price="+tempPrice+"&ActionId=ChangeStuff";
			retCode=openUrl(myurl);
			if (retCode!=-2){
				eval("ListTB"+tempTableId).rows[tempRowId].cells[3].innerHTML="<div class='greenB'>"+FieldArray[1]+"</div>";
				eval("ListTB"+tempTableId).rows[tempRowId].cells[4].innerHTML=FieldArray[2];
				}
		}
	}


</script>
<script language="JavaScript" type="text/JavaScript">

//只供yw_order_Ajax add by zx 2011-01-09
function updateLock(TableId,RowId,runningNum){//行即表格序号;列，流水号，更新源
	showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;//表格名称
		InfoSTR="<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='14' class='TM0000' readonly>&nbsp;<select name='myLock' id='myLock' size='1'> <option value='1'>解锁</option>  <option value='0'>锁定</option> </select> &nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdateLock("+RowId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}
/*
function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	theDiv.filters.revealTrans.apply();
	theDiv.style.visibility = "hidden";
	theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	}
*/
function aiaxUpdateLock(RowId){
	var tempTableId=document.form1.ActionTableId.value;
	var temprunningNum=document.form1.runningNum.value;
	var tempmyLock=document.form1.myLock.value;
	myurl="../admin/yw_order_ajax_updated.php?StockId="+temprunningNum+"&myLock="+tempmyLock+"&Action=Lock";
	//alert (myurl);
	
	/*
	retCode=openUrl(myurl);
	if (retCode!=-2){
		//更新成功,隐藏DIV，并且重新写该单元格的内容	或 重新动态更新需求单列表
		if(tempmyLock=="1"){
			eval(tempTableId).rows[RowId].cells[0].innerHTML="<div title='采购未锁定'> <img src='../images/unlock.png' width='15' height='15'> </div>";
			}
		else{
			eval(tempTableId).rows[RowId].cells[0].innerHTML="<div style='background-color:#FF0000' title='采购已锁定' > <img src='../images/lock.png' width='15' height='15'></div>";
			}
	CloseDiv();
	}*/
	var ajax=InitAjax(); 
	ajax.open("GET",myurl,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
		if(tempmyLock=="1"){
			eval(tempTableId).rows[RowId].cells[0].innerHTML="<div title='采购未锁定'> <img src='../images/unlock.png' width='15' height='15'> </div>";
			}
		else{
			eval(tempTableId).rows[RowId].cells[0].innerHTML="<div style='background-color:#FF0000' title='采购已锁定' > <img src='../images/lock.png' width='15' height='15'></div>";
			}
	CloseDiv();
			}
		}
	ajax.send(null); 	
}
</script>