<?php
$Th_Col="选项|30|配件|40|ID|30|客户|80|期限|60|采购流水号|80|采购单号|80|半成品名|280|采购备注|180|订单数量|60|生产数量|60|已确认入库|60|未确认入库|60|入库确认|60";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
//步骤4：需处理-条件选项
if (strlen($tempStuffCname)>0){
	$SearchRows.=" AND D.StuffCname LIKE '%$tempStuffCname%' ";
	$searchList1="<input class='ButtonH_25' type='button'  id='cancelQuery' value='取消' onclick='ResetPage(1,1)'/>";
    }
else{
	$searchList1="<input type='text' name='tempStuffCname' id='tempStuffCname' value='' width='20'/> &nbsp;<input class='ButtonH_25' type='button'  id='okQuery' value='查询' onclick='ResetPage(1,1)'/>";
 }
 
 	echo"<table id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr >
	<td colspan='".($Cols-6)." height='40px' class=''>$WorkShopList &nbsp;&nbsp; $dataList &nbsp;&nbsp; <input class='ButtonH_25' type='button'  id='batchPassbutton' value='入库确认' onclick='batchPassRkdata(2)'/></td><td colspan='6' align='right' class=''>$searchList1<input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$j=2;$i=1;


$mySql="SELECT O.Forshort,S.POrderId,SUM(S.Qty) AS Qty,S.StockId,(G.FactualQty + G.AddQty) AS OrderQty,
D.StuffId,D.StuffCname,D.Picture,
G.DeliveryDate,G.DeliveryWeek,
SM.PurchaseID,M.mStockId
FROM $DataIn.sc1_cjtj  S 
LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
LEFT JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
LEFT JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
LEFT  JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
INNER JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
WHERE 1  $SearchRows  GROUP BY S.StockId ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{		
		$POrderId=$myRow["POrderId"];
		$Forshort=$myRow['Forshort'];
		$StuffId=$myRow["StuffId"];
		$Picture=$myRow["Picture"];
		$StuffCname=$myRow["StuffCname"];
        $PurchaseID=$myRow["PurchaseID"];
        $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
    	include "../model/subprogram/stuffimg_model.php";
        include"../model/subprogram/stuff_Property.php";//配件属性
		$Qty=$myRow["Qty"];
		$OrderQty =$myRow["OrderQty"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
        $DeliveryDate=$myRow["DeliveryDate"];
        $DeliveryWeek=$myRow["DeliveryWeek"];
	    include "../model/subprogram/deliveryweek_toweek.php";
		$sumQty=$sumQty+$Qty;
		$mStockId=$myRow["mStockId"];
		$StockId =$myRow["StockId"];
		
		
	    $CheckscQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS scQty 
		FROM $DataIn.sc1_cjtj C 
		WHERE  C.POrderId = '$POrderId' AND C.StockId = $StockId 
		AND C.Estate = 0 AND DATE_FORMAT(C.Date,'%Y-%m-%d') = '$chooseDate'",$link_id));
		$scQty0=$CheckscQty["scQty"]; //已确认数
   
		$CheckscQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS scQty 
		FROM $DataIn.sc1_cjtj C 
		WHERE  C.POrderId = '$POrderId' AND C.StockId = $StockId 
		AND C.Estate = 1 AND DATE_FORMAT(C.Date,'%Y-%m-%d') = '$chooseDate'",$link_id));
		$scQty1=$CheckscQty["scQty"]; //未确认数
		
	    $UpdateIMG = "";
	    $UpdateClick ="";
	    $CheckData="<input type='checkbox' disabled />";
		if($SubAction==31  ){
              $UpdateIMG="<img src='../images/register.png' width='30' height='30'";
              $UpdateClick="onclick=\"passRkdata(this,$POrderId,$StockId,2)\" ";
              $CheckData="<input type='checkbox' id='checkId$i' name='checkId$i' value='$POrderId|$StockId' />";
              
        }
        if($scQty1==0 && $scQty0>0){
	        $UpdateIMG = "<span class ='blueB'>已确认</span>";
	        $UpdateClick ="";
	        $CheckData="<input type='checkbox' disabled />";
        }
		//动态读取配件资料
		$showPurchaseorder="[ + ]";
		$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
	
		echo"<tr><td class='A0111' align='center'>$CheckData</td><td class='A0101' align='center' id='theCel$i' height='25' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId,\"$mStockId\",\"showSemi\");' >$showPurchaseorder</td>";
		echo"<td class='A0101' align='center' $OrderSignColor>$i</td>";
		echo"<td class='A0101' align='center' >$Forshort</td>";
		echo"<td class='A0101' align='center' >$DeliveryWeek</td>";
		echo"<td class='A0101' align='center' >$StockId</td>";
		echo"<td class='A0101' align='center' >$PurchaseID</td>";
		echo"<td class='A0101'>$StuffCname</td>";
		echo"<td class='A0101' align='center'>$Remark</td>";
		echo"<td class='A0101' align='right'>$OrderQty</td>";
		echo"<td class='A0101' align='right'>$Qty</td>";
		if($scQty0>0){
			$scQty0 = "<span class ='blueB'>$scQty0</span>";
			}
		else if ($scQty0 == 0 )$scQty0 = "";
		echo"<td class='A0101' align='right'>$scQty0</td>";
		if($scQty1>0){
			$scQty1 = "<span class ='yellowB'>$scQty1</span>";
			}
		else if ($scQty1 == 0 )$scQty1 = "&nbsp;";
		echo"<td class='A0101' align='right'>$scQty1</td>";
		echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
		echo"</tr>";
		echo $ListRow;
		$j=$j+2;$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'>
	         <div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
?>