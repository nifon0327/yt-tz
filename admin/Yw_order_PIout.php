<?php   
//电信-EWEN
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
$From=$From==""?"PIout":$From;
//需处理参数
$tableMenuS=800;
$funFrom="yw_order";
$helpFile=1;//有帮助文件
$nowWebPage=$funFrom."_PIout";
$unColorCol=0;//不着色列
$Th_Col="操作|55|序号|30|PO|80|中文名|210|&nbsp;|30|Product Code|150|已出<br>(下单次数)|70|退货|50|成品重<br>(g)|50|Unit|40|Price|55|利润|80|Qty|50|Amount|70|订单利润|60|订单备注|180|生管备注|110|采购备注|110|待出备注|110|Air/Sea|60|采购|45|备料|45|组装|45|待出|45|交期|70|操作员|55|期限|40|背卡<br>条码|30|PE袋<br>条码|30|白盒<br>坑盒|30|外箱<br>标签|30|报价规则|450";	
	$ColsNumber=30;
	$myTask=1;
	$sumCols="12,13,14";

//更新
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//步骤4：需处理-条件选项
//$ThisMonth=date("Y-m");
 $Temptoday=date("Y-m-d");
$SearchRows.=" AND PI.Leadtime<='$Temptoday'  AND left(PI.Leadtime,3)='201'";	
if($From!="slist"){
	$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort,SUM(S.Qty*S.Price*R.Rate) AS Amount
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
	LEFT JOIN $DataPublic.currencydata R ON R.Id=C.Currency
    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
	WHERE S.Estate>0 $ClientStr $SearchRows  GROUP BY M.CompanyId order by Amount DESC,M.CompanyId",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];$theForshort=$ClientRow["Forshort"];
			//$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows.="and M.CompanyId='$theCompanyId'";$DefaultClient=$theForshort;
				}
			else{
				echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		echo"</select>&nbsp;";
		}
	//分类
	$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName,C.Color,SUM(S.Qty*S.Price*R.Rate) AS Amount 
	FROM $DataIn.yw1_ordermain M
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
    LEFT JOIN $DataIn.trade_object CD ON M.CompanyId=CD.CompanyId
	LEFT JOIN $DataPublic.currencydata R ON R.Id=CD.Currency
    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
	WHERE S.Estate>0 $SearchRows   GROUP BY P.TypeId ORDER BY Amount DESC,T.TypeId",$link_id);

	if ($TypeRow = mysql_fetch_array($TypeResult)){
		echo"<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{
			$theTypeId=$TypeRow["TypeId"];
			$TypeName=$TypeRow["TypeName"];
			$Color=$TypeRow["Color"]==""?"#FFFFFF":$TypeRow["Color"];
			if($TypeId==$theTypeId){
				echo"<option value='$theTypeId' style= 'color: $Color;font-weight: bold' selected>$TypeName</option>";
				$SearchRows.=" AND P.TypeId='$theTypeId'";
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
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.MainWeight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime,S.dcRemark
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
WHERE 1 and S.Estate>0 $SearchRows  ORDER BY PI.Leadtime ASC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$thisTOrmbOUTsum=0;
	do{
	  	//初始化计算的参数
		$OrderCgRemark="";
		$OrderRemark="";
		
		
		$m=1;$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		//加密参数
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
        $OrderDate=$myRow["OrderDate"];
        include "order_date.php";
		$PI=$myRow["PI"];
		if($PI!=""){
			$f1=anmaIn($PI.".pdf",$SinkOrder,$motherSTR);
			$d1=anmaIn("download/pipdf/",$SinkOrder,$motherSTR);		
			$PI="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>查看</a>";
			$PIoId=$POrderId . "|" .  $Id;//弹出DIV传值用
			}
		else{
			$PI="&nbsp;";
			$PIoId="N";
			}
		$ClientOrder=$myRow["ClientOrder"];
		if($ClientOrder!=""){//原单在序号列显示
			$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
			$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);		
			$ClientOrder="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$i</a>";
			}
		else{
			$ClientOrder=$i;
			}
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		/////////
			
		//订单总数
		$checkAllQty= mysql_query("
								  SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders FROM( 
									SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.eCode LIKE (SELECT eCode FROM $DataIn.productdata WHERE ProductId='$ProductId') GROUP BY OrderPO
									)A",$link_id);

		$AllQtySum=toSpace(mysql_result($checkAllQty,0,"AllQty"));
		$Orders=mysql_result($checkAllQty,0,"Orders");
		
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
			$ShipQtySum="<span class='yellowB'>".$ShipQtySum."</span>";
		
		////////
		$Weight=$myRow["Weight"];$MainWeight=$myRow["MainWeight"]==0?"&nbsp;":$myRow["MainWeight"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=sprintf("%.3f",$myRow["Price"]);
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$Leadtime=$myRow["Leadtime"]==""?"&nbsp;":$myRow["Leadtime"];
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
        $cgRemark=$myRow["cgRemark"]==""?"&nbsp;":$myRow["cgRemark"];
		$bjRemark=$myRow["bjRemark"]==""?"&nbsp;":$myRow["bjRemark"];
        $dcRemark=$myRow["dcRemark"]==""?"&nbsp;":$myRow["dcRemark"];
		$ShipType=$myRow["ShipType"];
		 //出货方式
	   if (strlen(trim($ShipType))>0){
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;'/>";
	    }

		//读取操作员姓名
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/$AskDay'";
		
		$OrderDate=CountDays($OrderDate,0);
		//$POrderId=$myRow["POrderId"];
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];
		
		$thisSaleAmount=sprintf("%.2f",$Qty*$Price);//本订单卖出金额
		$sumSaleAmount=sprintf("%.2f",$sumSaleAmount+$thisSaleAmount);
		$sumQty=$sumQty+$Qty;
		//交货期
		//include "../model/subprogram/product_chjq.php";
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
		$CostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost,SUM(A.OrderQty*A.Price*C.Rate) AS oTheCost2,C.Symbol,B.ProviderType
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1  AND S.POrderId='$POrderId' GROUP BY C.Id ORDER BY A.Id DESC",$link_id);
		if($CostRow= mysql_fetch_array($CostResult)){
			do{
				$cbAmount=sprintf("%.3f",$CostRow["oTheCost"]);
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
		$profitRMB2PC=$Price==0?0:sprintf("%.0f",($profitRMB2*100)/($Price*$Rate));
		
		//净利分类
		if($profitRMB2PC>15){
			$ViewSign=$ProfitType==4?1:0;
			$profitRMB2="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='greenB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
			}
		else{
			if($profitRMB2PC>7){
				$ViewSign=$ProfitType==3?1:0;
				$profitRMB2="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
				}
			else{
				if($profitRMB2<0){
					$ViewSign=$ProfitType==1?1:0;
					$profitRMB2="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='purpleB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
					}
				else{
					$ViewSign=$ProfitType==2?1:0;
					$profitRMB2="<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
					}
				}
			}
		
			//订单状态色：有未下采购单，则为白色
			$checkColor=mysql_query("SELECT G.Id,G.StockId FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId'",$link_id);
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
				}		       
		       
		     //采购单锁定
		    $OrderCgRemark="";$cgRemarked=0;
			$TmpCgRemark="";
			 $CheckStockSql=mysql_query("SELECT * FROM $DataIn.cg1_lockstock K WHERE K.StockId LIKE '$POrderId%' AND K.Locks=0 AND exists (SELECT StockId FROM $DataIn.cg1_stocksheet WHERE StockId=K.StockId AND POrderId='$POrderId')",$link_id);
			 while($CheckStockRow=mysql_fetch_array($CheckStockSql)) 
				{      
				     if ($CheckStockRow["Remark"]!=""){
				       $OrderCgRemark.=$OrderCgRemark==""?"原因:".$CheckStockRow["Remark"]:"," .$CheckStockRow["Remark"];
					   $TmpCgRemark.=$OrderCgRemark==""?"".$CheckStockRow["Remark"]:"," .$CheckStockRow["Remark"];
				       }
				       $cgRemarked=1;
					   $OrderSignColor="bgColor='#0099FF'";	 //break; //找到一个跳出当前循环  
				}
		       if ($cgRemarked==1 && $OrderCgRemark==""){
			          $OrderCgRemark="未填写原因";
					  $TmpCgRemark=$OrderCgRemark;
		       }

             $ColbgColor="";$UrgentColor="";
			//加急订单
			$checkExpress=mysql_query("SELECT Type,Remark FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
			if($checkExpressRow = mysql_fetch_array($checkExpress)){
				do{
					$Type=$checkExpressRow["Type"];
					$UPRemark=$checkExpressRow["Remark"];
					switch($Type){
						case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
						case 2:$ColbgColor="bgcolor='#FF0000'"; $OrderRemark="未确定产品 ".$UPRemark ;
						       //$LockRemark=$OrderRemark="未确定产品 ".$UPRemark ; 
								break;		//未确定产品
						}
					}while ($checkExpressRow = mysql_fetch_array($checkExpress));
				}
			   	
		
                        //拆分订单
                $checkSplit=mysql_query("SELECT SPOrderId FROM $DataIn.yw1_ordersplit WHERE OPOrderId='$POrderId' LIMIT 1",$link_id);
			if($splitRow = mysql_fetch_array($checkSplit)){
		            $SPOrderId=$splitRow["SPOrderId"]; 
                            $Qty="<a href='yw_order_split.php?Sid=$SPOrderId' target='_blank'><div style='color:#000000;Font-weight:bold;'>$Qty</div></a>";
                        }


				//动态读取 $thisTOrmbINo
			$TempStrtitle=$TmpCgRemark.$OrderRemark.$OrderRemark;
			 $TempStrtitle=$TempStrtitle==""?"显示或隐藏配件采购明细资料":$TempStrtitle;
			/*$showPurchaseorder="<img onClick='ShowOrHide1(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='$TempStrtitle' width='13' height='13' style='CURSOR: pointer'>";*/
			//0:内容	1：对齐方式		2:单元格属性		3：截取
			//检查权限:订单备注、出货方式的权限
			$Weight=zerotospace($Weight);
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
				$GrossProfit=sprintf("%.0f",$GrossProfit);
				$GrossProfitSUM=$GrossProfitSUM+$GrossProfit;
				if ($GrossProfit<0){				
					$GrossProfit=-1*$GrossProfit;
					$GrossProfit="<div class='redB'>-$GrossProfit</div>";
					}
				//array(0=>$MainWeight,1=>"align='right'"),	
				$ValueArray=array(
					array(0=>$OrderPO),
					array(0=>$TestStandard,3=>"line"),
					array(0=>$CaseReport,1=>"align='center'"),
					array(0=>$eCode,	3=>"..."),
					array(0=>$ShipQtySum,		1=>"align='right'",2=>$TempInfo),
				   array(0=>$ReturnedQty,		1=>"align='center'",2=>$TempInfo2),
					array(0=>$Weight,1=>"align='right'"),
					array(0=>$Unit,				1=>"align='center'"),
					array(0=>$Price, 			1=>"align='right'"),
					array(0=>$profitRMB2,		1=>"align='right'"),
					array(0=>$Qty,				1=>"align='right'"),
					array(0=>$thisSaleAmount,	1=>"align='right'"),
					array(0=>$GrossProfit,		1=>"align='right'"),
					array(0=>$PackRemark),
					array(0=>$sgRemark, 		1=>"style='color:#F00;'"),
                   array(0=>$cgRemark, 		1=>"align='center'"),
                   array(0=>$dcRemark, 		1=>"align='center'"),
					array(0=>$ShipType,			1=>"align='center'"),
                    array(0=>$wl_cycle,			1=>"align='center'"),
                     array(0=>$bl_cycle,			1=>"align='center'"),
                     array(0=>$sc_cycle,			1=>"align='center'"),
                     array(0=>$sctj_date,			1=>"align='center'"),
					 array(0=>$Leadtime,			1=>"align='center'"),
					array(0=>$Operator,			1=>"align='center'"),
					array(0=>$OrderDate,		    1=>"align='center' $BackImg"),
					array(0=>$CodeFile,			1=>"align='center'"),
					array(0=>$LableFile,		    1=>"align='center'"),
					array(0=>$WhiteFile,		    1=>"align='center'"),
					array(0=>$BoxFile,			    1=>"align='center'"),
					array(0=>$bjRemark,			3=>"...")
					);
			$checkidValue=$Id;
			include "subprogram/read_model_6_yw.php";
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' >
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>";
			  $OrderDate=$myRow["OrderDate"];
              include "yw_order_PIout_ajax.php";
           echo"</div><br></td></tr></table>";
		
		}while ($myRow = mysql_fetch_array($myResult));			
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
include "../model/subprogram/ColorInfo.php";
echo '</div>';
//$myResult1 = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle($SubCompany.$DefaultClient."PI逾期列表");
include "../model/subprogram/read_model_menu.php";
?>
<script>
/*function ShowOrHide1(e,f,Order_Rows,POrderId,RowId,FromT){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(POrderId!=""){			
			var url="../admin/yw_order_PIout_ajax.php?POrderId="+POrderId+"&RowId="+RowId+"&FromT="+FromT; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					}
				}
			ajax.send(null); 
			}
	   }
}*/
</script>
