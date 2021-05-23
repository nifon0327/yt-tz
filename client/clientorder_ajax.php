<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
echo"<table id='$TableId' width='1100' cellspacing='1' border='1' align='center'>
	<tr bgcolor='#CCCCCC'>
		<td width='10'></td>
		<td width='70' align='center'>采购日期</td>
		<td width='300' align='center'>配件名称</td>		
		<td width='55' align='center'>QC图</td>
		<td width='55' align='center'>品检报告</td>
		<td width='55' align='center'>历史订单</td>	
		<td width='55' align='center'>订单数量</td>
		<td width='55' align='center'>使用库存</td>
		<td width='55' align='center'>需购数量</td>
		<td width='55' align='center'>增购数量</td>		
		<td width='55' align='center'>实物库存</td>		
		<td width='55' align='center'>收货数量</td>
		<td width='55' align='center'>欠数数量</td>
		<td width='55' align='center'>备料数量</td>
		<td width='80' align='center'>交货期</td>
	</tr>";
//需求单列表<td width='55' align='center'>采购</td>		<td width='95' align='center'>供应商</td>
$ordercolor=3;
$sListResult = mysql_query("SELECT 
	S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,
	M.Date,A.StuffCname,A.Picture,A.TypeId,B.Name,C.Forshort,ST.mainType,MT.TypeColor,K.tStockQty,S.DeliveryWeek
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
	LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
    LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId
	LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType
	LEFT JOIN $DataIn.staffmain B ON B.Number=S.BuyerId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
	WHERE S.POrderId='$POrderId' AND ST.TypeId!=9104  AND MT.Id <2 AND S.Level=1 ORDER BY S.StockId",$link_id);
$i=1;
$Dir=anmaIn("stufffile",$SinkOrder,$motherSTR);
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		//颜色	0绿色	1白色	2黄色	3绿色
		//初始化
		$rkQty=0;		$thQty=0;		$bcQty=0;		$llQty=0;
		$thisId=$StockRows["Id"];
		$StockId=$StockRows["StockId"];
		$StuffCname=$StockRows["StuffCname"];
		$Price=$StockRows["Price"];
		$Forshort=$StockRows["Forshort"];
		$Buyer=$StockRows["Name"];
		$BuyerId=$StockRows["BuyerId"];
		$OrderQty=$StockRows["OrderQty"];
		$StockQty=$StockRows["StockQty"];
		$FactualQty=$StockRows["FactualQty"];
		$AddQty=$StockRows["AddQty"];
		$Date=$StockRows["Date"];
		$DeliveryDate=$StockRows["DeliveryDate"];		
		$StuffId=$StockRows["StuffId"];
		$Picture=$StockRows["Picture"];
		$TypeId=$StockRows["TypeId"];
		$mainType=$StockRows["mainType"];
		$TypeColor=$StockRows["TypeColor"];
        $tStockQty=$StockRows["tStockQty"];
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";
       include "../model/subprogram/stuffimg_qcfile.php";        		    //配件QC检验标准图        
		//需求单情况 如果有足够库存
		if($FactualQty==0 && $AddQty==0){
			$TempColor=2;			//黄色
			$Date="使用库存";
			$FactualQty="-";$AddQty="-";$Buyer="-";$Forshort="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";
			$DeliveryDate="-";$DeliveryWeek="-";
			}
		else{
			if($Date==""){//未下采购单
					if($mainType==1){
						$TempColor=1;		//白色
						$Date="未下采购单";
						}
					else{		//统计项目:mainType=3黄色，2绿色
						if($mainType==3){
							//生产数量
							$scSql=mysql_query("SELECT ifnull(SUM(S.Qty),0) AS scQty
								FROM $DataIn.sc1_cjtj S
								LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
								LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId 
								WHERE 1 AND G.StockId='$StockId' AND D.TypeId=S.TypeId",$link_id); 
								$scQty=mysql_result($scSql,0,"scQty");												
							$TempColor=$OrderQty==$scQty?3:2;
							
							}
						else{
							$TempColor=3;		//绿色
							}
						$Date="统计项目";
						$Position="-";
						}	
					$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";$DeliveryWeek ="-";
					}
				else{//已下采购单
					$TempColor=3;		//绿色
					$ReceiveDate=$StockRows["ReceiveDate"];
					//收货情况				
					$rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
					$rkQty=mysql_result($rkTemp,0,"Qty");
					$Mantissa=$FactualQty+$AddQty-$rkQty;
						
					if($DeliveryDate=="0000-00-00"){
					     $DeliveryDate="-"; $DeliveryWeek="-"; 
					 }else{
						$DeliveryWeek = $StockRows["DeliveryWeek"];
	                    include "../model/subprogram/deliveryweek_toweek.php";
					  }
					}
				}
			//采单颜色标记
		switch($TempColor){
			case 1://白色
				$Sbgcolor="#FFFFFF";
				$ordercolor=1;
				break;
			case 2://黄色
				$Sbgcolor="#FFCC00";
				$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
				break;
			case 3://绿色
				$Sbgcolor="#339900";
				$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
				break;
				}
		$OrderQtyInfo="<a href='../public/cg_historyorder_client.php?StuffId=$StuffId&Id=$thisId' target='_blank'>view</a>";//给客人看，但不能看价格
		//配件分类颜色
		 $checkllQty=mysql_fetch_array(mysql_query("SELECT SUM(L.Qty) AS llQty,
			 sum(case  when L.Estate=1 then L.Estate  else 0 end) as llEstate  
			 FROM $DataIn.ck5_llsheet L 
			 LEFT JOIN $DataIn.yw1_scsheet S ON S.sPOrderId = L.sPOrderId 
			 WHERE L.StockId='$StockId' AND (S.Level = 1 OR L.sPOrderId =0)",$link_id)); //成品是没有工单的
		     $llQty=$checkllQty["llQty"];
		if($llQty>=$OrderQty){
			$llQty = "<span class='greenB'>$llQty</span>";
		}
		
		$theDefaultColor=$TypeColor;
		echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right'>$i</td>";//配件状态 
		echo"<td  align='center'>$Date</td>";
		echo"<td>$StuffCname</td>";//配件名称
		echo"<td align='center'>$QCImage</td>";//QC 图
		echo"<td align='center'>$qualityReport</td>";//品检报告
		echo"<td align='center'>$OrderQtyInfo</td>";//历史订单
		echo"<td align='right'>$OrderQty</td>";//订单需求数量
		echo"<td align='right'>$StockQty</td>";//使用库存数
		echo"<td align='right'>$FactualQty</td>";//采购数量
		echo"<td align='right'>$AddQty</td>";//增购数量
		echo"<td align='right'>$tStockQty</td>";//实物库存
		echo"<td align='right'>$rkQty</td>";//收货进度
		echo"<td><div align='right' style='color:#FF6600;font-weight: bold;'>$Mantissa</div></td>";//欠数
		echo"<td align='right'>$llQty</td>";
		echo"<td align='center'>$DeliveryWeek</td>";//供应商交货期
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));	
	}
else{
	echo"<tr><td height='30' clos='17'>记录异常，此订单没有发现需求记录.</td></tr>";
	}
echo"</table>";
/*更新订单状态*/
//$upSql="UPDATE yw1_ordersheet SET Estate='$ordercolor' WHERE POrderId='$POrderId' and Estate!='0' and Estate!='4' ORDER BY Id DESC LIMIT 1";
//$result = mysql_query($upSql);
?>