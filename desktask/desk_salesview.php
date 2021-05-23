<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:500px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; } 
.imgContainer img {     display:block; } 
-->
</style>
<?php   
//电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;				
$tableMenuS=500;
$funFrom="clientorder";
$nowWebPage=$funFrom."_read";
//121权限(毛利)
$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId=$Login_P_Number LIMIT 1",$link_id);
if($TRow = mysql_fetch_array($TResult)){
	$Th_Col="操作|55|序号|30|PO号|80|中文名|235|Product Code|150|单位|35|售价|55|产品毛利|55|数量|50|金额|65|订单毛利|60|订单备注|110|产品备注|110|出货方式|90|操作|55|期限|40|PI|30|内部单号|80";//出货时间|80|
	$ColsNumber=16;
	$myTask=1;
	$sumCols=9;
	}
else{
	$Th_Col="操作|55|序号|30|PO号|80|中文名|235|Product Code|150|单位|35|售价|55|数量|50|金额|65|订单备注|110|产品备注|110|出货方式|90|操作|55|期限|40|PI|30|内部单号|80";//出货时间|80|
	$ColsNumber=14;
	$myTask=0;
	$sumCols=8;
	}
//更新
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="0,1,2,3,20,21,22,6,7,8,9,10,37,38";
//步骤3：
if($ClientId!=""){
	$ClientSTR="and M.CompanyId='$ClientId'";
	}
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows =" AND V.SalesId='$Login_P_Number'";	
	$ClientResult= mysql_query("SELECT 
			M.CompanyId,C.Forshort FROM $DataIn.yw1_ordermain M
 			LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
 			LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
			LEFT JOIN $DataIn.yw6_salesview V ON V.CompanyId=C.CompanyId 
			WHERE S.Estate>0 $ClientSTR $SearchRows group by M.CompanyId order by M.CompanyId ,M.OrderDate desc",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		do{
			$ClientValue=$ClientRow["CompanyId"];
			$Forshort=$ClientRow["Forshort"];
			if($CompanyId==""){
				$CompanyId=$ClientValue;
				}
			if($CompanyId==$ClientValue){
				echo"<option value='$ClientValue' selected>$Forshort</option>";
				$SearchRows="and M.CompanyId='$ClientValue'";
				$DefaultClient=$Forshort;
				}
			else{
				echo"<option value='$ClientValue'>$Forshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		echo"</select>&nbsp;";
		}
	}
echo"$CencalSstr";
if($myTask==1){
	for ($i=8;$i<=10;$i++){
		$temp1=2*$i;
		$Field1=$Field[$temp1];
		if($i==$sumCol){
			$sumColOption.="<option value='$i' selected>$Field1</option>";
			}
		else{
			$sumColOption.="<option value='$i'>$Field1</option>";
			}			
		}
	}
else{
	for ($i=7;$i<=8;$i++){
		$temp1=2*$i;
		$Field1=$Field[$temp1];
		if($i==$sumCol){
			$sumColOption.="<option value='$i' selected>$Field1</option>";
			}
		else{
			$sumColOption.="<option value='$i'>$Field1</option>";
			}			
		}
	}
//计算列
echo"<select name='sumCol' id='sumCol' onchange='document.form1.submit()'>$sumColOption</select>(计算列)";

//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:480px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
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
List_Title($Th_Col,"1",0);
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.Operator,
S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,
P.cName,P.eCode,P.TestStandard,P.pRemark,U.Name AS Unit,PI.PI
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
WHERE 1 and S.Estate>0 $SearchRows ORDER BY M.CompanyId ,M.OrderDate DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$Dir=anmaIn("teststandard",$SinkOrder,$motherSTR);
if($myRow = mysql_fetch_array($myResult)){
	do{
	  	//初始化计算的参数
		$m=1;
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
				//加密参数
		$PI=$myRow["PI"];
		$f1=anmaIn($PI.".pdf",$SinkOrder,$motherSTR);
		$d1=anmaIn("invoice",$SinkOrder,$motherSTR);		
		$PI=$PI==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",\"\",\"ch\")' style='CURSOR: pointer;color:#FF6633'>查看</span>";
		
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);			
		$TestStandard=$myRow["TestStandard"];
		if($TestStandard==1){
			$TestStandard="T".$ProductId.".jpg";
			$TestStandard=anmaIn($TestStandard,$SinkOrder,$motherSTR);
			$cName="<span onClick='OpenOrLoad(\"$Dir\",\"$TestStandard\")' style='CURSOR: pointer;color:#FF6633' alt='点击查阅产品规格图!'>$cName</span>";
			}
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$PackRemark=$myRow["PackRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
		$ShipType=$myRow["ShipType"];
		//读取操作员姓名
		$Operator=$myRow["Operator"];
		$staffResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Operator' LIMIT 1",$link_id);
		if($staffRow = mysql_fetch_array($staffResult)){
			$Operator=$staffRow["Name"];
			}
		$OrderDate=$myRow["OrderDate"];
		$OrderDate=CountDays($OrderDate,0);
		$POrderId=$myRow["POrderId"];
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];
		
		$thisSaleAmount=sprintf("%.2f",$Qty*$Price);//本订单卖出金额
		$sumSaleAmount=sprintf("%.2f",$sumSaleAmount+$thisSaleAmount);
		$sumQty=$sumQty+$Qty;
		/*毛利计算*////////////
		$CompanyId=$myRow["CompanyId"];
		$currency_Temp = mysql_query("SELECT A.Rate,A.Symbol FROM $DataPublic.currencydata A LEFT JOIN $DataIn.trade_object B ON A.Id=B.Currency WHERE  B.CompanyId=$CompanyId ORDER BY B.CompanyId LIMIT 1",$link_id);
		if($RowTemp = mysql_fetch_array($currency_Temp)){
			$Rate=$RowTemp["Rate"];//汇率
			$Symbol=$RowTemp["Symbol"];//货币符号
			}
		$thisTOrmbOUT=sprintf("%.4f",$thisSaleAmount*$Rate);//转成人民币的卖出金额
		//产品RMB$saleRMB_P=sprintf("%.4f",$Price*$Rate);
		
		//配件成本计算
		//配件成本(RMB)
		$Cost_Temp=mysql_query("SELECT 
		SUM((A.FactualQty+A.AddQty)*A.Price*C.Rate) AS oTheCost,
		SUM(A.OrderQty*A.Price*C.Rate) AS pTheCost 
					FROM $DataIn.cg1_stocksheet A	
					LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
					LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
					WHERE A.POrderId='$POrderId' ORDER BY A.Id DESC",$link_id);
		
		$thisTOrmbINo=sprintf("%.4f",mysql_result($Cost_Temp,0,"oTheCost"));
		$GrossProfit=$thisTOrmbOUT-$thisTOrmbINo;
		$thisTOrmbINp=sprintf("%.4f",mysql_result($Cost_Temp,0,"pTheCost"));
		$GrossProfitp=$thisTOrmbOUT-$thisTOrmbINp;
		//$GrossProfitSUM=$GrossProfitSUM+$GrossProfit;//毛利总额
		//单品毛利
		$profitRMB=sprintf("%.4f",$GrossProfitp/$Qty);		
		$profitRMB=$profitRMB<=0.3?"<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB</sapn></a>":$profitRMB=$profitRMB<=0.7?"<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB</sapn></a>":"<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='greenB'>$profitRMB</sapn></a>";

		////////////////////////////
		/*订单状态色
		if($Estate<3){
			//检查是否已经全部下单:检查非库单的Mid是否为0,如果没有，则已全部下单
			$checkColor=mysql_query("SELECT Id FROM $DataIn.cg1_stocksheet WHERE Mid='0' and (FactualQty>'0' OR AddQty>'0' ) and PorderId='$POrderId' LIMIT 1",$link_id);
			if(!$checkColorRow = mysql_fetch_array($checkColor)){
				$OrderSignColor="bgColor='#FFCC00'";
				}
			}
		else{
			$OrderSignColor="bgColor='#339900'";
			}
			*/
		////////////////////////////
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
			
		//加急订单
		$checkExpress=mysql_query("SELECT Id FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id LIMIT 1",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			$theDefaultColor="#FFA6D2";
			}
		//动态读取
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//0:内容	1：对齐方式		2:单元格属性		3：截取
		
		if($myTask==0){
			$ValueArray=array(
			0=>array(0=>$OrderPO),
			1=>array(0=>$cName),
			2=>array(0=>$eCode,
					 3=>"..."),
			3=>array(0=>$Unit,
					 1=>"align='center'"),
			4=>array(0=>$Price,
					 1=>"align='right'"),
			5=>array(0=>$Qty,
					 1=>"align='right'"),
			6=>array(0=>$thisSaleAmount,
					 1=>"align='right'"),
			7=>array(0=>$PackRemark,
					 2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,10,$POrderId,2)' style='CURSOR: pointer'",
					 3=>"..."),
			8=>array(0=>$pRemark,
					 3=>"..."),
			9=>array(0=>$ShipType,
					 1=>"align='center'",
					 2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,11,$POrderId,3)' style='CURSOR: pointer'",
					 3=>"..."),
			10=>array(0=>$Operator,
					 1=>"align='center'"),
			11=>array(0=>$OrderDate,
					 1=>"align='center'"),
			12=>array(0=>$PI,
					  1=>"align='center'"),
			13=>array(0=>$POrderId,
					  1=>"align='center'"),
				);
				/*		10=>array(0=>$DeliveryDate,
					 1=>"align='center'",
					 2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,12,$POrderId,4)' style='CURSOR: pointer'",
					 3=>"..."),*/

			}
		else{
			$GrossProfit=sprintf("%.0f",$GrossProfit);
			$GrossProfitSUM=$GrossProfitSUM+$GrossProfit;
			if ($GrossProfit<0){				
				$GrossProfit=-1*$GrossProfit;
				$GrossProfit="<div class='redB'>-$GrossProfit</div>";
				}
			$ValueArray=array(
				0=>array(0=>$OrderPO),
				1=>array(0=>$cName),
				2=>array(0=>$eCode,
						 3=>"..."),
				3=>array(0=>$Unit,
						 1=>"align='center'"),
				4=>array(0=>$Price,
						 1=>"align='right'"),
				5=>array(0=>$profitRMB,
						 1=>"align='right'"),
				6=>array(0=>$Qty,
						 1=>"align='right'"),
				7=>array(0=>$thisSaleAmount,
						 1=>"align='right'"),
				8=>array(0=>$GrossProfit,
						 1=>"align='right'"),
				9=>array(0=>$PackRemark,
						 2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,12,$POrderId,2)' style='CURSOR: pointer'",
						 3=>"..."),
				10=>array(0=>$pRemark,
					 	3=>"..."),
				11=>array(0=>$ShipType,
						 1=>"align='center'",
						 2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,13,$POrderId,3)' style='CURSOR: pointer'",
						 3=>"..."),
				12=>array(0=>$Operator,
						 1=>"align='center'"),
				13=>array(0=>$OrderDate,
						 1=>"align='center'"),
				14=>array(0=>$PI,
					  1=>"align='center'"),
				15=>array(0=>$POrderId,
						  1=>"align='center'"),
				);
				/*
								12=>array(0=>$DeliveryDate,
						 1=>"align='center'",
						 2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,14,$POrderId,4)' style='CURSOR: pointer'",
						 3=>"..."),

				*/
			}
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
	if($myTask==1){
		$pml="<td class='A0101' width='55' align='right'>&nbsp;</td>";
		$oml="<td class='A0101' width='60' align='right'>$GrossProfitSUM</td>";
		}
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='$thePointerColor'><tr>";
	echo"<td class='A0111' width='640' height='20'>合计</td>";
	echo $pml;
	echo"<td class='A0101' width='50' align='right'>$sumQty</td>";
	echo"<td class='A0101' width='65' align='right'>$sumSaleAmount</td>";
	echo $oml;
	echo"<td class='A0101'>&nbsp;</td>";
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
ChangeWtitle($DefaultClient."客户未出明细列表");
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function updateJq(TableId,RowId,runningNum,toObj){//表格序号;表格数据行号，流水号，更新源
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//采购单交货期
				InfoSTR="更新采购流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的采购单交货期:<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'>";
				break;
			case 2:	//订单备注
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的<br>订单备注<input name='PackRemark' type='text' id='PackRemark' size='50' class='INPUT0100'><br>";
				break;
			case 3://出货方式
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的<br>出货方式<input name='ShipType' type='text' id='ShipType' size='50' class='INPUT0100'><br>";
				break;
			case 4://出货时间
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的出货时间<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>";
				break;
			}
		if(toObj>1){
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
			}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		theDiv.filters.revealTrans.apply();//防止错误
		theDiv.filters.revealTrans.play(); //播放
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	theDiv.filters.revealTrans.apply();
	theDiv.style.visibility = "hidden";
	theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
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
				eval("ListTB"+tempTableId).rows[tempRowId].cells[16].innerHTML="<span onclick='updateJq("+tempTableId+","+tempRowId+","+temprunningNum+",1)' style='CURSOR: pointer;color:#FF9966'>"+tempDeliveryDate+"</sapn>";
				CloseDiv();
				}
			break;
		case "2":		//订单说明 PackRemark
			var tempPackRemark0=document.form1.PackRemark.value;
			var tempPackRemark1=encodeURIComponent(tempPackRemark0);
			myurl="clientorder_updated.php?POrderId="+temprunningNum+"&tempPackRemark="+tempPackRemark1+"&ActionId=PackRemark";
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
			myurl="clientorder_updated.php?POrderId="+temprunningNum+"&tempShipType="+tempShipType1+"&ActionId=ShipType";
			retCode=openUrl(myurl);
			if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempShipType0+"</NOBR></DIV>";
				CloseDiv();
				}
			break;
		case "4":		//出货时间
			var tempDeliveryDate=document.form1.DeliveryDate.value;
			myurl="clientorder_updated.php?POrderId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=DeliveryDate";
			retCode=openUrl(myurl);
			if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempDeliveryDate+"</NOBR></DIV>";
				CloseDiv();
				}
			break;
		}
	}
//-->
</script>
