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
$tableMenuS=900;
$funFrom="yw_order";
$helpFile=1;//有帮助文件
$nowWebPage=$funFrom."_kbl";
$unColorCol=0;//不着色列
$Th_Col="操作|55|序号|30|PO|80|中文名|210|&nbsp;|30|Product Code|150|已出数量<br>(下单次数)|70|退货<br>数量|50|单品重<br>(g)|40|成品重<br>(g)|50|Unit|40|Price|55|利润|80|Qty|50|Amount|70|订单利润|60|订单备注|110|生管备注|110|采购备注|110|Air/Sea|60|采购|45|备料|45|组装|45|待出|45|交期|70|操作员|55|期限|40|背卡<br>条码|30|PE袋<br>条码|30|白盒<br>坑盒|30|外箱<br>标签|30|报价规则|450";
$ColsNumber=30;
$sumCols="13,14,15";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//步骤4：需处理-条件选项
 $Temptoday=date("Y-m-d");
if($From!="slist"){
	$SearchRows ="";	

	$ClientResult= mysql_query("SELECT * FROM ( 
   SELECT M.CompanyId,C.Forshort,SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2 ,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(E.Type,0) AS Type 
   FROM (SELECT OrderNumber,ProductId,POrderId FROM $DataIn.yw1_ordersheet WHERE scFrom>0 AND Estate=1) S 
   LEFT JOIN $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber 
   LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
   LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
   LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
   LEFT JOIN ( 
             SELECT L.StockId,SUM(L.Qty) AS Qty 
             FROM $DataIn.yw1_ordersheet S 
             LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId 
             LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
             WHERE 1 AND S.scFrom>0 AND S.Estate=1  GROUP BY L.StockId
           ) L ON L.StockId=G.StockId 
   LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
   LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
   LEFT JOIN $DataIn.yw2_orderexpress E ON (E.POrderId=S.POrderId AND E.Type=2)  
   WHERE  ST.mainType<2  GROUP BY S.POrderId 
) A WHERE A.K1>=A.K2 AND A.blQty!=A.llQty AND A.Type<>2  Group by A.CompanyId",$link_id);

/*
  $ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort FROM $DataIn.ck_bldatetime B 
			                  LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=B.POrderId 
			                  LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			                  LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
			                  WHERE B.Estate=1 AND S.Estate=1 AND S.scFrom>0 Group by M.CompanyId",$link_id);
*/			                
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
        echo"<option value='' selected>全部</option>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];
            $theForshort=$ClientRow["Forshort"];
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows="and M.CompanyId='$theCompanyId'";$DefaultClient=$theForshort;
				}
			else{
				echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		echo"</select>&nbsp;";
		}
	//分类
	/*
$TypeResult= mysql_query("SELECT * FROM ( 
   SELECT T.TypeId,T.TypeName,SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2 ,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(E.Type,0) AS Type 
   FROM $DataIn.yw1_ordermain M 
   LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
   LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
   LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
   LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
   LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
   LEFT JOIN ( 
           SELECT L.StockId,SUM(L.Qty) AS Qty 
           FROM $DataIn.yw1_ordersheet S 
           LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId 
           LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
           WHERE 1 AND S.scFrom>0 AND S.Estate=1  GROUP BY L.StockId
           ) L ON L.StockId=G.StockId 
   LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
   LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
    LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
   WHERE 1 and S.scFrom>0 AND S.Estate=1 AND ST.mainType<2 $SearchRows GROUP BY S.POrderId 
) A WHERE A.K1>=A.K2 AND A.blQty!=A.llQty AND A.Type<>2 GROUP BY A.TypeId ",$link_id);
*/
$TypeResult= mysql_query("SELECT T.TypeId,T.TypeName FROM $DataIn.ck_bldatetime B 
			                   LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=B.POrderId 
			                   LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
			                   LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			                  WHERE B.Estate=1 AND S.Estate=1 AND S.scFrom>0 Group by T.TypeId",$link_id);
			                  
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
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name='shuaxin' type='button' id='shuaxin'  value='手动刷新' onclick='myrefresh()'>"."<span class='redB'>(请手动刷新下,因为可备料订单随时会变!)</span>";
//步骤5：
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/CurrencyList.php";
$sumQty=0;
$sumSaleAmount=0;
$sumTOrmb=0;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
/*
$mySql="SELECT * FROM (
SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark
,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime,
SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,
SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2,
SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(E.Type,0) AS Type 
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2  
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
LEFT JOIN (
             SELECT L.StockId,SUM(L.Qty) AS Qty FROM $DataIn.yw1_ordersheet S 
             LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
             LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
             WHERE 1  AND S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
         ) L ON L.StockId=G.StockId
WHERE 1 and S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  $SearchRows  GROUP BY S.POrderId ) A 
WHERE A.K1>=A.K2  AND A.blQty!=A.llQty AND A.Type<>2  ORDER BY A.OrderDate ASC,A.Id DESC";
*/
$curDate=date("Y-m-d");
$nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
$nextWeek=$dateResult["NextWeek"];
$SearchRows.=" AND  YEARWEEK(PI.Leadtime,1)<='$nextWeek' ";
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark
,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime,E.Type  
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId 
WHERE 1 and S.scFrom>0 AND S.Estate=1  $SearchRows  GROUP BY S.POrderId ORDER BY M.OrderDate,S.ProductId,S.Id";
//LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2  
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$thisTOrmbOUTsum=0;$ProductArray=array();
	do{
		$m=1;
        $AskDay="";
		$Id=$myRow["Id"];
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"];
		
		 $R_EType=$myRow["Type"]==2?2:0;
		
        //检查订单备料情况
		$CheckblState=mysql_query("
				SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IF(GL.Id>0,1,0)) AS  Locks,SUM(IFNULL(L.llEstate,0)) AS llEstate  
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
				LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
				LEFT JOIN ( 
				    SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate 
				    FROM  $DataIn.cg1_stocksheet G 
				    LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
				    WHERE  G.POrderId='$POrderId'  GROUP BY L.StockId 
				  )L ON L.StockId=G.StockId 
				WHERE G.POrderId='$POrderId' AND ST.mainType<2 ",$link_id);
					//SELECT * FROM () A WHERE A.K1>=A.K2 AND A.blQty!=A.llQty  AND A.Locks=0
		        //if (mysql_num_rows($CheckblState)<=0)	 continue;
		 if($blStateRow = mysql_fetch_array($CheckblState)){
		      $R_K1=$blStateRow["K1"];
		      $R_K2=$blStateRow["K2"];
			  $R_blQty=$blStateRow["blQty"];
			  $R_llQty=$blStateRow["llQty"];
			  $R_Locks=$blStateRow["Locks"];
			  $R_llEstate=$blStateRow["llEstate"];
			  $R_llEstate=0;
			  
			  if ($R_blQty==$R_llQty){
				       if ($R_llEstate==0) continue;//&& $R_EType==0
			  }
			 else{
			           //是否已存在有可备料订单
			        
			          if (in_array($ProductId, $ProductArray)) continue;
			          if ($R_EType==2) continue;
					  if ($R_K1>=$R_K2 &&  $R_blQty!=$R_llQty && $R_Locks==0){
						    $ProductArray[]=$ProductId;   
					  }
					  else{
						    $ProductArray[]=$ProductId; continue;
					  }
			  }
		 }     
		
        $OrderDate=$myRow["OrderDate"];
        include "order_date.php";
       // if($blSign==1){ continue;}
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
		
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);		
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
		include "../model/subprogram/PI_Leadtime.php";
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
        $cgRemark=$myRow["cgRemark"]==""?"&nbsp;":$myRow["cgRemark"];
		$bjRemark=$myRow["bjRemark"]==""?"&nbsp;":$myRow["bjRemark"];
		$ShipType=$myRow["ShipType"];
		/*
        if($ShipType=="sea"||$ShipType=="Sea"||$ShipType=="SEA")$ShipPicture="<img src='../images/boat.png' title='船运' width='18' height='16'/>";
        else $ShipPicture="";
       // $ShipType.=$ShipPicture;
       */  
        //出货方式
	   if (strlen(trim($ShipType))>0){
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;'/>";
	    }
	    else{
		   $ShipType="&nbsp;";
	    }

		//读取操作员姓名
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/$AskDay'";		
		$OrderDate=CountDays($OrderDate,0);
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];	
		$thisSaleAmount=sprintf("%.2f",$Qty*$Price);//本订单卖出金额
		$sumSaleAmount=sprintf("%.2f",$sumSaleAmount+$thisSaleAmount);
		$sumQty=$sumQty+$Qty;
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
		
		//利润
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
		if($ViewSign==1 || $ProfitType==""){
			//订单状态色：有未下采购单，则为白色
			$checkColor=mysql_query("SELECT G.Id,G.StockId FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId'",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			
					$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
				  do{   
					 $StockId=$checkColorRow["StockId"];
					 $CheckStockSql=mysql_query("SELECT * FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1",$link_id);
					 if($CheckStockRow=mysql_fetch_array($CheckStockSql)) 
						{      
							   $OrderSignColor="bgColor='#0099FF'";	 break; //找到一个跳出当前循环  
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
            
			       
             $ColbgColor="";$UrgentColor="";
			//加急订单
			$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
			if($checkExpressRow = mysql_fetch_array($checkExpress)){
				do{
					$Type=$checkExpressRow["Type"];
					switch($Type){
						case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
						case 2:$ColbgColor="bgcolor='#FF00'";break;		//未确定产品
						//case 7:$UrgentColor="bgcolor='#C11186'";break;		//加急A
						//case 8:$UrgentColor="bgcolor='#8db4e2'";break;		//加急B
						//case 9:$UrgentColor="bgcolor='#ffff00'";break;		//加急C	
						}
					}while ($checkExpressRow = mysql_fetch_array($checkExpress));
				}
			
                        //拆分订单
                $checkSplit=mysql_query("SELECT SPOrderId FROM $DataIn.yw1_ordersplit WHERE OPOrderId='$POrderId' LIMIT 1",$link_id);
			if($splitRow = mysql_fetch_array($checkSplit)){
		            $SPOrderId=$splitRow["SPOrderId"]; 
                            $Qty="<a href='yw_order_split.php?Sid=$SPOrderId' target='_blank'><div style='color:#000000;Font-weight:bold;'>$Qty</div></a>";
                        }
                 //PI交期订单范围加色
              /*  if($myRow["Leadtime"]!=""){
                            $PItime= ceil((strtotime($Temptoday)-strtotime($Leadtime))/3600/24);
                            if($PItime>=0)$theDefaultColor="#FFA6D2";
                            else {
	                                 if($PItime>=-7 && $PItime<0)$theDefaultColor="#D3E9D3";
                                     }
				    }*/

				//动态读取 $thisTOrmbINo
			$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			//0:内容	1：对齐方式		2:单元格属性		3：截取//检查权限:订单备注、出货方式的权限
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
				$ValueArray=array(
					 array(0=>$OrderPO,	     3=>"..."),
					 array(0=>$TestStandard,3=>"line"),
					 array(0=>$CaseReport,1=>"align='center'"),
					 array(0=>$eCode,	3=>"..."),
					 array(0=>$ShipQtySum,		1=>"align='right'",2=>$TempInfo),
				     array(0=>$ReturnedQty,		1=>"align='center'",2=>$TempInfo2),
					 array(0=>$MainWeight,     1=>"align='right'"),
					 array(0=>$Weight,             1=>"align='right'"),
					 array(0=>$Unit,				    1=>"align='center'"),
					 array(0=>$Price, 			        1=>"align='right'"),
					 array(0=>$profitRMB2,		1=>"align='right'"),
					 array(0=>$Qty,				    1=>"align='right'"),
					 array(0=>$thisSaleAmount,	1=>"align='right'"),
					 array(0=>$GrossProfit,		1=>"align='right'"),
					 array(0=>$PackRemark,		3=>"..."),
					 array(0=>$sgRemark, 		     3=>"..."),
                     array(0=>$cgRemark, 		    1=>"align='center'",3=>"..."),
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
			echo $StuffListTB;
		   }//净利分类显示结束
        }while ($myRow = mysql_fetch_array($myResult));
			$m=1;
		   // $ClientPC=sprintf("%.0f",$GrossProfitSUM*100/$thisTOrmbOUTsum);
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
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$sumQty,1=>"align='right'"),
				array(0=>$sumSaleAmount,	1=>"align='right'"),
				array(0=>$GrossProfitSUM,		1=>"align='right'"),
				array(0=>"利润率：".$ClientPC."%",		1=>"align='left'"),
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
				array(0=>"&nbsp;"	)
				);		
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";			
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
ChangeWtitle($SubCompany.$DefaultClient."可备料订单列表");
$ActioToS="1"; 
include "../model/subprogram/read_model_menu.php";
?>
<script>
function myrefresh(){
    window.location.reload(); 
}
//setTimeout('myrefresh()',1000000);
</script>