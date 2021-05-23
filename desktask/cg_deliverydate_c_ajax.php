<?php   
//电信-zxq 2012-08-01

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//取得文件所在目录
$FromDir=get_currentDir(1);
//参数拆分
$TempArray=explode("|",$TempId);
$DeliveryDate=$TempArray[0];
$BuyerId=$TempArray[1];
$CompanyId=$TempArray[2];
$predivNum=$TempArray[3];
$tableWidth=910;
$TableId=$predivNum;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'><tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>序号</td>
		<td width='90' align='center'>采购日期</td>
		<td width='90' align='center'>交货日期</td>
		<td width='280' align='center'>配件名称</td>				
		<td width='40' align='center'>图档</td>
                <td width='60' align='center'>历史订单</td>
		<td width='60' align='center'>单价</td>
		<td width='45' align='center'>单位</td>
		<td width='60' align='center'>采购数量</td>
		<td width='60' align='center'>未收数量</td>
		<td width='90' align='center'>需求流水号</td>
		<td width='50' align='center'>采购单</td>
		<td width='60' align='center'>产品信息</td>
		</tr>";
//订单列表
$sListResult = mysql_query("SELECT M.Id,M.Date,D.StuffCname,D.Gfile,D.Gstate,D.Picture,D.Gremark,S.POrderId,S.StuffId,S.Price,U.Name AS UnitName,(S.AddQty+S.FactualQty) AS cgQty,S.StockId 
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
WHERE 1 AND S.Mid>0 AND S.rkSign>0 AND S.DeliveryDate='$DeliveryDate' AND S.BuyerId='$BuyerId' AND S.CompanyId='$CompanyId' ORDER BY S.Id",$link_id);
/*///
echo "SELECT M.Id,M.Date,D.StuffCname,D.Gfile,D.Gstate,S.POrderId,S.StuffId,S.Price,(S.AddQty+S.FactualQty) AS cgQty,S.StockId 
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
WHERE 1 AND S.Mid>0 AND S.rkSign>0 AND S.DeliveryDate='$DeliveryDate' AND S.BuyerId='$BuyerId' AND S.CompanyId='$CompanyId' ORDER BY S.Id";
*/
$i=1;
$sumQty=0;
$sumAmount=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$Mid=$StockRows["Id"];
		$Date=$StockRows["Date"];
		$POrderId=$StockRows["POrderId"];
		$StuffId=$StockRows["StuffId"];
		$StuffCname=$StockRows["StuffCname"];
		$Price=$StockRows["Price"];
		$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
		$cgQty=$StockRows["cgQty"];
		$StockId=$StockRows["StockId"];
		//include "../model/subprogram/stuffimg_model.php";
	        
		$Picture=$StockRows["Picture"];
			$Gfile=$StockRows["Gfile"];
			$Gstate=$StockRows["Gstate"];  //状态
			$Gremark=$StockRows["Gremark"];
			
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
                
                $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId' target='_blank'>查看</a>";
                
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>查看</a>";
		//收货数量计算
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
		$unQty=$cgQty-$rkQty;
		
		$OnclickStr="";
		if($Login_P_Number==$BuyerId || $Login_P_Number==10002){
			$OnclickStr="onclick='updateJq(\"$TableId\",$i,$StockId)' style='CURSOR: pointer;'";
			if($DeliveryDate=="0000-00-00"){
				$DeliveryDateShow="<span class='yellowN'>未设置</div>";
				}
			else{
				$SetDate=CountDays($DeliveryDate,5);
				if($SetDate>-1){		//离交期不大于一天，为红色
					$DeliveryDateShow="<span class='redB'>".$DeliveryDate."</span>";
					}
				else{
					if($SetDate>-5){
						$DeliveryDateShow="<span class='yellowB'>".$DeliveryDate."</span>";
						}
					else{
						$DeliveryDateShow="<span class='greenB'>".$DeliveryDate."</span>";
						}
					}
				}
			}
		else{
			$DeliveryDateShow=$DeliveryDate=="0000-00-00"?"未设置":$DeliveryDate;
			}
		//产品信息:订单交期，客户，订单PO，产品名称
		$checkSql=mysql_query("SELECT S.DeliveryDate,S.OrderPO,C.Forshort,P.cName
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		WHERE S.POrderId='$POrderId' ORDER BY POrderId DESC LIMIT 1",$link_id);
		if($checkRow=mysql_fetch_array($checkSql)){
			$OderDeliveryDate=$checkRow["DeliveryDate"]=="0000-00-00"?"未确定":$checkRow["DeliveryDate"];
			$OrderPO=$checkRow["OrderPO"];
			$Forshort=$checkRow["Forshort"];
			$cName=$checkRow["cName"];
			$TempInfo="订单交期:".$OderDeliveryDate."&#13;客户简称:".$Forshort."&#13;订单PO号:".$OrderPO."&#13;产品名称:".$cName;
			$ProductInfo="<img src='../images/remark.gif' alt='$TempInfo' width='18' height='18'>";
			}
		else{
			$ProductInfo="&nbsp;";
			}
		if($unQty!=0)
		{
			echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";
			echo"<td  align='center'>$Date</td>";
			echo"<td  align='center' $OnclickStr>$DeliveryDateShow</td>";
			echo"<td>$StuffCname</td>";
			echo"<td align='center'>$Gfile</td>";
                        echo"<td align='center'>$OrderQtyInfo</td>";
			echo"<td align='center'>$Price</td>";
			echo"<td align='center'>$UnitName</td>";
			echo"<td align='center'>$cgQty</td>";//采购数量
			echo"<td align='center'><div class='redN'>$unQty</div></td>";//未收货数量
			echo"<td align='center'>$StockId</td>";
			echo"<td align='center'>$PurchaseIDStr</td>";
			echo"<td align='center'>$ProductInfo</td>";
			echo"</tr>";
		
		$i++;
		}
 		}while ($StockRows = mysql_fetch_array($sListResult));
	}
else{
	echo"<tr><td height='30'>没有出货明细资料,请检查.</td></tr>";
	}
echo"</table>";
?>
