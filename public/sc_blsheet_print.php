<?php 
//电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 备料单记录列表");
echo"<table width='730' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
echo"<tr bgcolor='#CCCCCC'>
<td width='70' align='center' class='A1111'>备料单信息</td>
<td width='120' align='center' class='A1101'>订单信息</td>
<td width='40' align='center' class='A1101'>序号</td>
<td width='50' align='center' class='A1101'>采购</td>
<td width='200' align='center' class='A1101'>配件名称</td>
<td width='60' align='center' class='A1101'>备料数量</td>
<td width='40' align='center' class='A1101'>仓库</td>
<td width='150' align='center' class='A1101'>备注</td>
</tr>";
$POrderIdSTR=$POrderId==""?"":" AND B.POrderId='$POrderId'";
$mySql="SELECT B.Num,B.Date FROM $DataIn.yw9_blsheet B WHERE 1 AND B.Num='$Num' LIMIT 1";
$mainResult = mysql_query($mySql,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	do{
	////////////////////////////////
		$m=1;
		//主单信息
		$Num=$mainRows["Num"];
		$Date=$mainRows["Date"];
		//检查记录总数
		$checkRecordRow=mysql_fetch_array(mysql_query("SELECT count(*) AS Row1Sum 
		FROM $DataIn.yw9_blsheet B
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=B.POrderId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		WHERE B.Num='$Num' AND T.mainType<2 $POrderIdSTR",$link_id));
		$Row1Sum=$checkRecordRow["Row1Sum"];
		echo "<tr><td scope='col' class='A0111' rowspan='$Row1Sum' valign='top'>编号:<br>$Num<p>备料单日期:<br>$Date</td>";		//备料单信息
		//检查2级项目明细
		$checkOrderSql=mysql_query("
		SELECT B.POrderId,Y.OrderPO,Y.Qty,P.cName,C.Forshort
		FROM $DataIn.yw9_blsheet B
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=B.POrderId
		LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		WHERE B.Num='$Num' $POrderIdSTR ORDER BY B.Id
		",$link_id);
		if($checkOrderRow=mysql_fetch_array($checkOrderSql)){
			$TempB=1;
			do{
				$POrderId=$checkOrderRow["POrderId"];
				$OrderPO=$checkOrderRow["OrderPO"];
				$Qty=$checkOrderRow["Qty"];
				$cName=$checkOrderRow["cName"];
				$Forshort=$checkOrderRow["Forshort"];
				if($TempB!=1){//非二级首行时
					echo"<tr>";
					}
				$checkStockSql=mysql_query("SELECT G.OrderQty,D.StuffCname,M.Name,B.Remark
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataPublic.staffmain M ON M.Number=G.BuyerId
				LEFT JOIN $DataIn.base_mposition B ON B.Id=D.SendFloor
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				WHERE POrderId='$POrderId' AND T.mainType<2 ORDER BY D.SendFloor",$link_id);
				$RecordSum=mysql_num_rows($checkStockSql);
				echo"<td class='A0101' height='20' rowspan='$RecordSum' valign='top'>
				订单数量:$Qty
				<p>客户:$Forshort<br>订单PO:$OrderPO<br>订单流水号:<br>$POrderId
				<p>产品名称:<br>$cName</td>";//订单信息
				//检查3级项目:包括需求明细表、配件资料表，仓库分区表、人事表、供应商资料表
				if($checkStockRow=mysql_fetch_array($checkStockSql)){
					$TempC=1;
					do{
						$Name=$checkStockRow["Name"];
						$StuffCname=$checkStockRow["StuffCname"];
						$OrderQty=$checkStockRow["OrderQty"];
						$Remark=$checkStockRow["Remark"];
						if($TempC!=1){
							echo "<tr>";
							}
						echo"<td class='A0101' align='center'>$TempC</td>";		//序号
						echo"<td class='A0101'>$Name</td>";			//采购
						echo"<td class='A0101'>$StuffCname</td>";	//配件名称
						echo"<td class='A0101' align='right'>$OrderQty</td>";		//备料数量
						echo"<td class='A0101' align='center'>$Remark</td>";//仓库楼层
						echo"<td class='A0101' align='center'>&nbsp;</td>";//备料信息
						echo"</tr>";
						$TempC++;
						}while($checkStockRow=mysql_fetch_array($checkStockSql));
					}//3级完成
				$TempB++;
				}while($checkOrderRow=mysql_fetch_array($checkOrderSql));
			}//2级完成

	///////////////////////////////
		}while($mainRows = mysql_fetch_array($mainResult));
	}
echo"</table>";
List_Title($Th_Col,"0",1);
?>