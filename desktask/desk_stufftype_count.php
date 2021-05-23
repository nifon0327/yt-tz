<?php   
/*电信---yang 20120801
$DataIn.stuffdata
$DataIn.cg1_stocksheet
$DataIn.yw1_ordersheet
$DataIn.yw1_ordermain
$DataIn.ck1_rksheet
$DataIn.ck1_rkmain 
$DataIn.ck7_bprk
$DataIn.ck5_llsheet
$DataIn.ck5_llmain
$DataIn.ck8_bfsheet
$DataIn.ck9_stocksheet
$DataIn.taskuserdata
二合一已更新
*/
include "../model/modelhead.php";
$TypeIdSTR=" AND D.TypeId='$Idtemp'";
//读取配件分类库存值
$CheckSql=mysql_query("SELECT SUM(S.dStockQty) AS dStockQty,SUM(S.tStockQty) AS tStockQty,SUM(S.oStockQty) AS oStockQty,D.TypeId,T.TypeName 
FROM $DataIn.ck9_stocksheet S,$DataIn.stuffdata D,$DataIn.stufftype T
WHERE S.StuffId=D.stuffId AND D.TypeId=T.TypeId $TypeIdSTR GROUP BY D.TypeId
",$link_id);; 
if($CheckRow = mysql_fetch_array($CheckSql))	{
	$dStockQty=$CheckRow["dStockQty"];
	$tStockQty=$CheckRow["tStockQty"];
	$oStockQty=$CheckRow["oStockQty"];
	$TypeName=$CheckRow["TypeName"];
	$thisDate=date("Y-m-d");
	}
//月份订单总数
$UnionSTR="SELECT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month,concat('1') AS Sign,SUM(G.OrderQty) AS Qty 
FROM $DataIn.cg1_stocksheet G
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
WHERE 1 $TypeIdSTR GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')";

//月份采购总数（包括已下采购单和没下采购单）
$UnionSTR.="
UNION ALL
SELECT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month,concat('2') AS Sign,SUM(G.FactualQty+G.AddQty) AS Qty 
FROM $DataIn.cg1_stocksheet G,$DataIn.yw1_ordermain M,$DataIn.yw1_ordersheet S,$DataIn.stuffdata D 
WHERE S.POrderId=G.POrderId AND S.OrderNumber=M.OrderNumber AND G.StuffId=D.StuffId $TypeIdSTR GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')";

//月份已下采购单的特采总数
$UnionSTR.="
UNION ALL
SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,concat('3') AS Sign,SUM(S.FactualQty) AS Qty 
FROM $DataIn.cg1_stocksheet S,$DataIn.cg1_stockmain M,$DataIn.stuffdata D  WHERE S.StuffId=D.StuffId AND S.POrderId='' AND S.FactualQty>0 AND M.Id=S.Mid $TypeIdSTR GROUP BY DATE_FORMAT(M.Date,'%Y-%m')";

//月份未下采购单的特采总数
$UnionSTR.="
UNION ALL
SELECT concat('0000-00') AS Month,concat('3') AS Sign,SUM(S.FactualQty) AS Qty 
FROM $DataIn.cg1_stocksheet S,$DataIn.stuffdata D  WHERE S.Mid=0 AND S.StuffId=D.StuffId AND S.POrderId='' $TypeIdSTR";

//月份入库总数
$UnionSTR.="
UNION ALL
SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,concat('4') AS Sign,SUM(R.Qty) AS Qty 
FROM $DataIn.ck1_rksheet R,$DataIn.ck1_rkmain M ,$DataIn.stuffdata D
WHERE R.Mid=M.Id AND R.StuffId=D.StuffId $TypeIdSTR GROUP BY DATE_FORMAT(M.Date,'%Y-%m')";

//月份备品转入总数
$UnionSTR.="
UNION ALL
SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,concat('5') AS Sign,SUM(M.Qty) AS Qty FROM $DataIn.ck7_bprk M,$DataIn.stuffdata D WHERE M.StuffId=D.StuffId $TypeIdSTR GROUP BY DATE_FORMAT(M.Date,'%Y-%m')";

//月份领料总数
$UnionSTR.="
UNION ALL
SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,concat('6') AS Sign,SUM(S.Qty) AS Qty 
FROM $DataIn.ck5_llsheet S,$DataIn.ck5_llmain M,$DataIn.stuffdata D WHERE S.Mid=M.Id AND S.StuffId=D.StuffId $TypeIdSTR GROUP BY DATE_FORMAT(M.Date,'%Y-%m')";

//月份报废总数,审核通过的才计算，否则不计算  modify by zx 20101130 
$UnionSTR.="
UNION ALL
SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,concat('7') AS Sign,SUM(M.Qty) AS Qty FROM $DataIn.ck8_bfsheet M,$DataIn.stuffdata D WHERE M.Estate=0 AND M.StuffId=D.StuffId $TypeIdSTR GROUP BY DATE_FORMAT(M.Date,'%Y-%m')";



//add by zx 20100721  退换数据
$UnionSTR.="
UNION ALL
SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month ,concat('8') AS Sign,SUM(S.Qty) AS Qty FROM $DataIn.ck2_thsheet S 
LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
WHERE 1 $TypeIdSTR GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ";


//add by zx 20100721  补仓数据
$UnionSTR.="
UNION ALL
SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,concat('9') AS Sign,SUM(S.Qty) AS Qty FROM $DataIn.ck3_bcsheet S
LEFT JOIN $DataIn.ck3_bcmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
WHERE 1 $TypeIdSTR GROUP BY DATE_FORMAT(M.Date,'%Y-%m')";

$result = mysql_query($UnionSTR,$link_id);

$DateTemp=array();
$QtyTemp=array();
$SignTemp=array();
$sum1=0;	$sum2=0;	$sum3=0;	$sum4=0;	$sum5=0;
$sum6=0;	$sum7=0;    $sum8=0;	$sum9=0;    //add by zx 20100324  $sum8=0;退货	$sum9=0;补货  
if($myrow = mysql_fetch_array($result)){
	//$i=1;
	do{
		$Month=$myrow["Month"];
		$Qty= $myrow["Qty"];
		$Sign= $myrow["Sign"];
		if($Qty>0){
			$MonthTemp[]=$Month;
			$QtyTemp[]=$Qty;
			$SignTemp[]=$Sign;
			//echo $i." - ".$Sign."/".$Month."/".$Qty."<br>";$i++;			
			}
		
		}while ($myrow = mysql_fetch_array($result));		
	}
else{
	echo"没有记录";
	}
$grade = array("Month"=>$MonthTemp,"Qty"=>$QtyTemp,"Sign"=>$SignTemp);
$tt=array_multisort($grade["Month"], SORT_STRING, SORT_ASC,$grade["Sign"], SORT_NUMERIC, SORT_ASC,$grade["Qty"], SORT_NUMERIC, SORT_ASC);
$count=count($MonthTemp);
//数组处理完毕
?>
<form name="form1" method="post" action="">
<table height="315" cellpadding="1"  cellspacing="0">
  <tr valign="top">
    <th height="37" colspan="11" scope="col">配件分类数据分析报告</th>
  </tr>
  <tr>
    <td height="25" colspan="5" class="A0100">分类名称：<?php    echo $TypeName?></td>
    <td colspan="2" class="A0100">初始库存:<?php    echo $dStockQty?></td>
    <td colspan="4" class="A0100" align="right">报表日期：<?php    echo date("Y年m月d日")?></td>
  </tr>
    <tr class=''>
    <td width="35" height="21" class="A0111" align="center">序号</td>
    <td width="70" class="A0101" align="center">采购日期</td>
	<td width="65" class="A0101" align="center">订单数量</td>
    <td width="65" class="A0101" align="center">采购数量</td>
	<td width="65" class="A0101" align="center">特采数量</td>
    <td width="65" class="A0101" align="center">入库数量</td>
	<td width="65" class="A0101" align="center">备品转入</td>
    <td width="65" class="A0101" align="center">领料数量</td>
    <td width="65" class="A0101" align="center">报废数量</td>
    <td width="65" class="A0101" align="center">退换数量</td>
    <td width="65" class="A0101" align="center">补仓数量</td>    
    <td width="65" class="A0101" align="center">在库</td>
    <td width="65" class="A0101" align="center">可用库存</td>
  </tr>
  <?php   
$NumOfCol=9;
$ColTemp=$NumOfCol;//当前列
$MonthTemp="";
$c2TEMP=$dStockQty;//当天在库,初始值为初始库存
$c3TEMP=$dStockQty;//当天可用库存,初始值为初始库存
$Rowtemp=0;
for($i=0;$i<$count;$i++){
	$Month=$grade[Month][$i];	
	$Qty=$grade[Qty][$i];
	$Sign=$grade[Sign][$i];//有数据的列
	
	if($MonthTemp!=$grade[Month][$i]){//新行,如果日期与参照日期不一致，表示新行开始
		$MonthTemp=$grade[Month][$i];//重新设置参照日期
		if($ColTemp!=$NumOfCol){//如果当前列数不是9，表示上一行未结束,先补足上一行
			for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
				echo "<td  class='A0101'>&nbsp;</td>";
				}
			$c3Row=$c3TEMP>=0?$c3TEMP:0;
			echo "<td class='A0101'><div align='right'>$c2TEMP</div></td>";
			echo "<td class='A0101'><div align='right'>$c3Row</div></td></tr>";//结束上一行
			}
		//新行正式开始
		$ColTemp=0;
		$Rowtemp++;
		//新行前两列：序号列和日期列
		if($Month=="0000-00"){
			$Month="<span title='全部还没有下采购单的特采单数量' style='CURSOR: pointer'>◆</span>";
			}
		echo"<tr><td class='A0111' align='center'>$Rowtemp</td><td class='A0101' align='center'>$Month</td>";
		}

	for($ColTemp=$ColTemp+1;$ColTemp<$Sign*1;$ColTemp++){
		echo "<td  class='A0101'>&nbsp</td>";
		}
	echo"<td  class='A0101'><div align='right'>$Qty</div></td>";
	//订单数量
	if($ColTemp==1){
		$c3TEMP=$c3TEMP-$Qty;$sum1=$sum1+$Qty;}
	//采购数量
	if($ColTemp==2){
		$sum2=$sum2+$Qty;	$c3TEMP=$c3TEMP+$Qty;}
	//特采数量
	if($ColTemp==3){
		$sum3=$sum3+$Qty;	$c3TEMP=$c3TEMP+$Qty;}
	//入库数量
	if($ColTemp==4){
		$c2TEMP=$c2TEMP+$Qty;	$sum4=$sum4+$Qty;}
	//备品转入
	if($ColTemp==5){
		$c2TEMP=$c2TEMP+$Qty;	$sum5=$sum5+$Qty;	$c3TEMP=$c3TEMP+$Qty;}
	//领料数量
	if($ColTemp==6){
		$c2TEMP=$c2TEMP-$Qty;	$sum6=$sum6+$Qty;}
	//报废数量
	/*
	if($ColTemp==7){
		$c2TEMP=$c2TEMP-$Qty;	$sum7=$sum7+$Qty;	$c3TEMP=$c3TEMP-$Qty;	$c3Row=$c3TEMP>=0?$c3TEMP:0;
		echo"<td  class='A0101'><div align='right'>$c2TEMP</div></td>";
		echo"<td  class='A0101'><div align='right'>$c3Row</div></td></tr>";
		}
	*/	

	//报废数量
	if($ColTemp==7){
		$c2TEMP=$c2TEMP-$Qty;	$sum7=$sum7+$Qty;	$c3TEMP=$c3TEMP-$Qty;	
		}
    //退料数量（供应商）  add by zx 20100324 
	if($ColTemp==8){
		$c2TEMP=$c2TEMP-$Qty;	$sum8=$sum8+$Qty;		
		}
	//补料数量（供应商）	 add by zx 20100324
	if($ColTemp==9){
		$c2TEMP=$c2TEMP+$Qty;	$sum9=$sum9+$Qty;	$c3Row=$c3TEMP>=0?$c3TEMP:0;
		echo"<td  class='A0101'><div align='right'>$c2TEMP</div></td>";
		echo"<td  class='A0101'><div align='right'>$c3Row</div></td></tr>";
		}

	}
	
if($ColTemp!=$NumOfCol){//上一行未结束
	for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
		echo "<td  class='A0101'>&nbsp</td>";
		}
		$c3Row=$c3TEMP>=0?$c3TEMP:0;
	echo "<td class='A0101'><div align='right'>$c2TEMP </div></td>";//结束上一行
	echo "<td class='A0101'><div align='right'>$c3Row </div></td></tr>";//结束上一行
	}

//采购未回数量=（采购数量+特采数量+退换数量）-（入库数量+补仓数量）
$Mantissa=($sum2+$sum3)-($sum4);
//可用库存=（初始库存+采购数量+特采数量+备品转入）-（订单需求+报废数量）
$OrderSurplus=($dStockQty+$sum2+$sum3+$sum5)-($sum1+$sum7);
 
//计算已出货数量
$ship_Result = mysql_query("SELECT SUM(G.OrderQty) AS Qty FROM cg1_stocksheet G,yw1_ordersheet S 
WHERE G.POrderId=S.POrderId AND S.Estate=0 AND G.StuffId='$StuffId'",$link_id);
if($ship_Row = mysql_fetch_array($ship_Result)){
	$ship_Qty1=$sum1-$ship_Row["Qty"];
	$ship_Qty2=$sum1-$ship_Qty1;
	}

?>  
<tr class=''>
    <td width="35" height="21" class="A0111" align="center">序号</td>
    <td width="70" class="A0101" align="center">采购日期</td>
	<td width="50" class="A0101" align="center">订单数量</td>
    <td width="50" class="A0101" align="center">采购数量</td>
	<td width="50" class="A0101" align="center">特采数量</td>
    <td width="50" class="A0101" align="center">入库数量</td>
	<td width="50" class="A0101" align="center">备品转入</td>
    <td width="50" class="A0101" align="center">领料数量</td>
    <td width="50" class="A0101" align="center">报废数量</td>
    <td width="65" class="A0101" align="center">退换数量</td>
    <td width="65" class="A0101" align="center">补仓数量</td>     
    <td width="50" class="A0101" align="center">在库</td>
    <td width="50" class="A0101" align="center">可用库存</td>
  </tr>
  <tr>
    <td colspan="2" class="A0111">合计：</td>
    <td  class="A0101" align="right"><?php    echo $sum1?></td>
    <td class="A0101" align="right"><?php    echo $sum2?></td>
    <td class="A0101" align="right"><?php    echo $sum3?></td>
    <td class="A0101" align="right"><?php    echo $sum4?></td>
    <td class="A0101" align="right"><?php    echo $sum5?></td>
    <td class="A0101" align="right"><?php    echo $sum6?></td>
    <td class="A0101" align="right"><?php    echo $sum7?></td>
    <td class="A0101" align="right"><?php    echo $sum8?></td>
    <td class="A0101" align="right"><?php    echo $sum9?></td>     
	<td class="A0101" align="right"><?php    echo $c2TEMP?></td>
	<td class="A0101" align="right"><?php    echo $c3TEMP?></td>
  </tr>
</table>
</form>
</body>
</html>