
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
/*电信---yang 20120801
$DataIn.yw1_ordermain
$DataIn.yw1_ordersheet
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.cg1_stocksheet
$DataIn.trade_object
$DataIn.ck5_llsheet
$DataIn.yw2_orderexpress
分开已更新
*/
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
include "../model/subprogram/business_authority.php";//看客户权限
$From=$From==""?"read":$From;
$ColsNumber=18;
$tableMenuS=800;
$funFrom="yw_order";
$helpFile=1;//有帮助文件
$Pagination=$Pagination==""?1:$Pagination;
$nowWebPage=$funFrom."_read";
$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId=$Login_P_Number LIMIT 1",$link_id);
if($TRow = mysql_fetch_array($TResult)){
	$unColorCol=23;//不着色列
	$Th_Col="操作|55|序号|30|PO|80|原单|40|中文名|235|Product Code|150|已出<br>数量|50|退货<br>数量|50|单重|40|检讨|40|Unit|35|Price|55|Profit|55|理论净利|80|Qty|50|Amount|70|订单毛利|60|订单备注|110|生管备注|110|How to Ship|80|出货日期|70|PI交期|70|交期<br>差值|50|交货<br>均期|40|操作员|55|期限|40|PI|30|背卡<br>条码|30|PE袋<br>条码|30|白盒<br>坑盒|30|外箱<br>标签|30|报价规则|180";//出货时间|80||物料交期|80|产品备注|110
	$ColsNumber=28;
	$myTask=1;
	$sumCols="14,15,16";
	}
else{
	$unColorCol=20;//不着色列
	$Th_Col="操作|55|序号|30|PO|80|原单|40|中文名|235|Product Code|150|已出<br>数量|50|退货<br>数量|50|单重|40|检讨|40|Unit|35|Price|55|Qty|50|Amount|70|订单备注|110|生管备注|110|How to Ship|80|出货日期|70|PI交期|70|交期<br>差值|50|交货<br>均期|40|操作员|55|期限|40|PI|30|背卡<br>条码|30|PE袋<br>条码|30|白盒<br>坑盒|30|外箱<br>标签|30|报价规则|180";//出货时间|80|物料交期|80|产品备注|110|
	$ColsNumber=25;
	$myTask=0;
	$sumCols="12,13";
	}
//更新
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
	WHERE S.Estate=0 $ClientStr GROUP BY M.CompanyId order by Amount DESC,M.CompanyId",$link_id);

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
	WHERE S.Estate=0 $SearchRows GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId",$link_id);
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
	}
else{
	//查询的条件
	echo "<input name='SearchRows' type='hidden' id='SearchRows' value='$SearchRows'>";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
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
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
LEFT JOIN $DataIn.yw2_orderteststandard I ON I.POrderId=S.POrderId 
WHERE 1 and S.Estate=0 $SearchRows ORDER BY M.CompanyId,M.OrderDate DESC,M.Id DESC";
//echo "$mySql";
}
else {
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
WHERE 1 and S.Estate=0 $SearchRows ORDER BY M.CompanyId,M.OrderDate DESC,M.Id DESC";
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
			//$PI="<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
			$PI="<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>查看</a>";
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
			//$ClientOrder="<span onClick='OpenOrLoad(\"$d2\",\"$f2\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
			$ClientOrder="<a href=\"../admin/openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>查看</a>";
			}
		else{
			$ClientOrder="&nbsp;";
			}
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
				/////////
		
		//订单总数
		$checkAllQty= mysql_query("SELECT SUM(Qty) AS AllQty FROM $DataIn.yw1_ordersheet WHERE ProductId='$ProductId'",$link_id);
		$AllQtySum=toSpace(mysql_result($checkAllQty,0,"AllQty"));
		//已出货数量
		$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
		$ShipQtySum=toSpace(mysql_result($checkShipQty,0,"ShipQty"));
		//$ShipQtySum
		//百分比
		$TempInfo="style='CURSOR: pointer;' onclick='ViewChart($ProductId,1)'";
		$TempPC=($ShipQtySum/$AllQtySum)*100;
		$TempPC=$TempPC>=1?(round($TempPC)."%"):(sprintf("%.2f",$TempPC)."%");
		if($AllQtySum>0){
			$TempInfo.="title='订单总数:$AllQtySum,已出数量占:$TempPC'";
			}
		$GfileStr=$GfileStr==""?"&nbsp;":$GfileStr;
		$Weight=zerotospace($Weight);
		//退货数量
		$checkReturnedQty= mysql_query("SELECT SUM(Qty) AS ReturnedQty FROM $DataIn.product_returned WHERE eCode='$eCode'",$link_id);
		$ReturnedQty=toSpace(mysql_result($checkReturnedQty,0,"ReturnedQty"));
		if($ReturnedQty>0 && $ShipQtySum>0){
			//退货百分比
			$ReturnedPercent=sprintf("%.1f",(($ReturnedQty/$ShipQtySum)*1000));
			if($ReturnedPercent>=5){
				$ReturnedQty="<span class=\"redB\">".$ReturnedQty."</span>";
				}
			else{
					if($ReturnedPercent>=2){
						$ReturnedQty="<span class=\"yellowB\">".$ReturnedQty."</span>";
						}
					else{
						$ReturnedQty="<span class=\"greenB\">".$ReturnedQty."</span>";
						}
					}
			$ReturnedP=
			$TempInfo2="style='CURSOR: pointer;' onclick='ViewChart($ProductId,2)' title=\"退货率：$ReturnedPercent ‰\"";
			
			}
		else{
			$ReturnedQty="&nbsp;";
			$TempInfo2="";
			}
			$ShipQtySum="<div class='yellowB'>".$ShipQtySum."</div>";
		
		////////

		$Weight=$myRow["Weight"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";

		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=sprintf("%.3f",$myRow["Price"]);
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$Leadtime=$myRow["Leadtime"];
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
		/*毛利计算*////////////
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
		$CostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost,SUM(A.OrderQty*A.Price*C.Rate) AS oTheCost2,C.Symbol,B.ProviderType
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
					$$AmountTemp=sprintf("%.0f",$TempoTheCost);//毛利成本
					$AmountTemp2="llcbAmount".strval($TempSymbol);
				$$AmountTemp2=sprintf("%.0f",$TempoTheCost2);//理论成本
				}while ($CostRow= mysql_fetch_array($CostResult));
			}
		$GrossProfitp=sprintf("%.3f",$thisTOrmbOUT-$cbAmountUSD-$cbAmountRMB);
		//单品毛利
		$profitRMB=sprintf("%.3f",$GrossProfitp/$Qty);
		
		//理论净利
		$profitRMB2=sprintf("%.3f",($thisTOrmbOUT-$llcbAmountUSD-$llcbAmountRMB-$llcbAmountRMB*$HzRate)/$Qty);
		
		
		$GrossProfit=$GrossProfitp;
		$profitRMB2PC=sprintf("%.0f",($profitRMB2*100)/($Price*$Rate));
		
		if($profitRMB2PC>15){
			$profitRMB2="<a href='../public/pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='greenB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
			}
		else{
			if($profitRMB2PC>7){
				$profitRMB2="<a href='../public/pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
				}
			else{
				if($profitRMB2<0){
					$profitRMB2="<a href='../public/pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='purpleB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
					}
				else{
					$profitRMB2="<a href='../public/pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
					}
				}
			}
		//订单状态色：有未下采购单，则为白色
		$checkColor=mysql_query("SELECT G.Id,G.StockId 
		FROM $DataIn.cg1_stocksheet G 
		LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId'",$link_id);
	if($checkColorRow = mysql_fetch_array($checkColor)){
		
			    $OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
		      do{   
			     $StockId=$checkColorRow["StockId"];
		         $CheckStockSql=mysql_query("SELECT * FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1",$link_id);
				// echo "SELECT * FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1";
				 if($CheckStockRow=mysql_fetch_array($CheckStockSql)) 
				    {      
				           $OrderSignColor="bgColor='#0099FF'";	  
						    //echo $OrderSignColor;
				    }
		        }while($checkColorRow = mysql_fetch_array($checkColor));
		
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
			}
		
                //出货日期 
                $checkDate=mysql_fetch_array(mysql_query("SELECT M.Date FROM $DataIn.ch1_shipsheet S 
                LEFT JOIN  $DataIn.ch1_shipmain M ON M.Id=S.Mid WHERE  S.PorderId='$POrderId' LIMIT 1",$link_id));  
                $Date=$checkDate["Date"];
                
                if ($Leadtime!="" && strtotime($Leadtime)>0){
                   $diffday=(strtotime($Date)-strtotime($Leadtime))/3600/24;
                   if ($diffday<=0){
                       if ($diffday<-5) {
                           $diffday="<div class='greenB'>⬆  " . abs($diffday) . "天</div>";
                          }
                      else{
                           $diffday="<span class='greenB'>⬆  </span>" . abs($diffday) . "天";
                         }
                    }
                    else{
                        if ($diffday>5) { 
                               $diffday="<div class='redB'>⬇  " . abs($diffday) . "天</div>";
                           }
                           else {
                               $diffday="<span class='redB'>⬇  </span>" . abs($diffday) . "天";
                           }
                     }
                }          
                else{
                   $diffday="&nbsp;"; 
                   $Leadtime=$Leadtime==""?"&nbsp;":$Leadtime;
                }
                
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//0:内容	1：对齐方式		2:单元格属性		3：截取
		//检查权限:订单备注、出货方式的权限
		$Weight=zerotospace($Weight);
		if($myTask==0){
			$ValueArray=array(
				array(0=>$OrderPO,          2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,2,$POrderId,25)' style='CURSOR: pointer'",3=>"..."),
				array(0=>$ClientOrder,		1=>"align='center'"),
				array(0=>$TestStandard),
				array(0=>$eCode,			3=>"..."),
				array(0=>$ShipQtySum,		1=>"align='center'",2=>$TempInfo),
				array(0=>$ReturnedQty,		1=>"align='center'",2=>$TempInfo2),
				array(0=>$Weight,1=>"align='right'"),
				array(0=>$CaseReport,1=>"align='center'"),
				array(0=>$Unit,				1=>"align='center'"),
				array(0=>$Price, 			1=>"align='right'"),
				array(0=>$Qty, 				1=>"align='right'"),
				array(0=>$thisSaleAmount,	1=>"align='right'"),
				array(0=>$PackRemark,		3=>"..."),
				array(0=>$sgRemark, 		3=>"..."),
				array(0=>$ShipType, 		1=>"align='center'",3=>"..."),
                array(0=>$Date,			1=>"align='center'"),
				array(0=>$Leadtime,		1=>"align='center'",3=>"..."),
                array(0=>$diffday,		1=>"align='center'"),
				array(0=>$JqAvg,			1=>"align='center'"),
				array(0=>$Operator,			1=>"align='center'"),
				array(0=>$OrderDate,		1=>"align='center'"),
				array(0=>$PI, 				1=>"align='center'"),
				array(0=>$CodeFile,			1=>"align='center'"),
				array(0=>$LableFile,		1=>"align='center'"),
				array(0=>$WhiteFile,		1=>"align='center'"),
				array(0=>$BoxFile,			1=>"align='center'"),
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
				array(0=>$OrderPO,			3=>"..."),
				array(0=>$ClientOrder,		1=>"align='center'"),
				array(0=>$TestStandard),
				array(0=>$eCode,			3=>"..."),
				array(0=>$ShipQtySum,		1=>"align='center'",2=>$TempInfo),
				array(0=>$ReturnedQty,		1=>"align='center'",2=>$TempInfo2),
				array(0=>$Weight,           1=>"align='right'"),
				array(0=>$CaseReport,       1=>"align='center'"),
				array(0=>$Unit,				1=>"align='center'"),
				array(0=>$Price, 			1=>"align='right'"),
				array(0=>$profitRMB,		1=>"align='right'"),
				array(0=>$profitRMB2,		1=>"align='right'"),
				array(0=>$Qty,				1=>"align='right'"),
				array(0=>$thisSaleAmount,	1=>"align='right'"),
				array(0=>$GrossProfit,		1=>"align='right'"),
				array(0=>$PackRemark,		3=>"..."),
				array(0=>$sgRemark, 		3=>"..."),
				array(0=>$ShipType,			1=>"align='center'",3=>"..."),
				array(0=>$Date,			1=>"align='center'"),
				array(0=>$Leadtime,		1=>"align='center'",3=>"..."),
                                array(0=>$diffday,		1=>"align='center'"),
				array(0=>$JqAvg,			1=>"align='center'"),
				array(0=>$Operator,			1=>"align='center'"),
				array(0=>$OrderDate,		1=>"align='center'"),
				array(0=>$PI,				1=>"align='center'"),
				array(0=>$CodeFile,			1=>"align='center'"),
				array(0=>$LableFile,		1=>"align='center'"),
				array(0=>$WhiteFile,		1=>"align='center'"),
				array(0=>$BoxFile,			1=>"align='center'"),
				array(0=>$bjRemark,			3=>"...")
				);
			}
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6_yw.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
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
				array(0=>"&nbsp;"	),array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$sumQty,1=>"align='right'"),
				array(0=>$sumSaleAmount,	1=>"align='right'"),
				array(0=>"&nbsp;&nbsp;"   ),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
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
				array(0=>"&nbsp;"	),array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$sumQty,1=>"align='right'"),
				array(0=>$sumSaleAmount,	1=>"align='right'"),
				array(0=>$GrossProfitSUM,		1=>"align='right'"),
				array(0=>"毛利率：".$ClientPC."%",		1=>"align='left'"),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
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
				
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";			
		}
		
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
include "../model/subprogram/ColorInfo.php";
echo '</div>';
$myResult1 = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult1);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle($SubCompany.$DefaultClient."客户已出明细列表");
include "../model/subprogram/read_model_menu.php";
?>
<script>
function ViewChart(Pid,OpenType){
	document.form1.action="../public/productdata_chart.php?Pid="+Pid+"&Type="+OpenType;
	document.form1.target="_blank";
	document.form1.submit();		
	document.form1.target="_self";
	document.form1.action="";
	}
</script>