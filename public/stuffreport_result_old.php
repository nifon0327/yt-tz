<?php 
/*
已更新$DataIn.电信---yang 20120801
*/
include "../model/modelhead.php";
//读取配件库存值
$CheckSql=mysql_query("SELECT S.dStockQty,S.tStockQty,S.oStockQty,S.mStockQty,D.StuffId,D.StuffCname 
FROM $DataIn.ck9_stocksheet S,$DataIn.stuffdata D 
WHERE D.StuffId='$Idtemp' AND S.StuffId=D.stuffId",$link_id);; 
if($CheckRow = mysql_fetch_array($CheckSql))	{
	$dStockQty=$CheckRow["dStockQty"];
	$tStockQty=$CheckRow["tStockQty"];
	$oStockQty=$CheckRow["oStockQty"];
	$StuffId=$CheckRow["StuffId"];
	$mStockQty=$CheckRow["mStockQty"];
	$StuffCname=$CheckRow["StuffCname"];
	$thisDate=date("Y-m-d");
	}
//订单数据
$UStuffCname=urlencode($StuffCname);

$UnionSTR="SELECT M.OrderDate AS Date,concat('1') AS Sign,SUM(G.OrderQty) AS Qty 
FROM $DataIn.cg1_stocksheet G
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
WHERE G.StuffId='$StuffId' AND G.POrderId!='' GROUP BY M.OrderDate";

//采购数据（包括已下采购单和没下采购单）
$UnionSTR.="
UNION ALL
SELECT M.OrderDate AS Date,concat('2') AS Sign,SUM(G.FactualQty+G.AddQty) AS Qty 
FROM $DataIn.cg1_stocksheet G,$DataIn.yw1_ordermain M,$DataIn.yw1_ordersheet S
WHERE S.POrderId=G.POrderId AND S.OrderNumber=M.OrderNumber AND G.StuffId='$StuffId' GROUP BY M.OrderDate";

//已下采购单的特采数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('3') AS Sign,SUM(S.FactualQty) AS Qty 
FROM $DataIn.cg1_stocksheet S,$DataIn.cg1_stockmain M WHERE S.StuffId='$StuffId' AND S.POrderId='' AND S.FactualQty>0 AND M.Id=S.Mid GROUP BY M.Date";

//未下采购单的特采数据
$UnionSTR.="
UNION ALL
SELECT concat('0000-00-00') AS Date,concat('3') AS Sign,SUM(FactualQty) AS Qty 
FROM $DataIn.cg1_stocksheet WHERE Mid=0 AND StuffId='$StuffId' AND POrderId=''";

//入库数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('4') AS Sign,SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id WHERE R.StuffId='$StuffId' GROUP BY M.Date";


//备品转入数据
$UnionSTR.="
UNION ALL
SELECT Date,concat('5') AS Sign,SUM(Qty) AS Qty FROM $DataIn.ck7_bprk WHERE StuffId='$StuffId' GROUP BY Date";

//领料数据
$UnionSTR.="
UNION ALL
SELECT A.Date,concat('6') AS Sign,SUM(A.Qty) AS Qty FROM (
SELECT IFNULL(M.Date,B.Date) AS Date,S.Qty 
FROM $DataIn.ck5_llsheet S LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id LEFT JOIN  $DataIn.yw9_blmain B ON S.Pid=B.Id WHERE S.StuffId='$StuffId') A GROUP BY A.Date";

//已出货数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('7') AS Sign,SUM(S.Qty) AS Qty 
FROM $DataIn.ck5_llsheet S LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
WHERE S.StuffId='$StuffId' AND Y.Estate=0 GROUP BY M.Date";

//报废数据,只有审核通过的才算 modify by zx 2010-11-30
$UnionSTR.="
UNION ALL
SELECT Date,concat('8') AS Sign,SUM(Qty) AS Qty FROM $DataIn.ck8_bfsheet WHERE Estate=0 AND StuffId='$StuffId' GROUP BY Date";

//退换数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('9') AS Sign,SUM(S.Qty) AS Qty FROM $DataIn.ck2_thsheet S LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id WHERE S.StuffId='$StuffId' GROUP BY M.Date";

//补仓数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('10') AS Sign,SUM(S.Qty) AS Qty FROM $DataIn.ck3_bcsheet S LEFT JOIN $DataIn.ck3_bcmain M ON S.Mid=M.Id WHERE S.StuffId='$StuffId' GROUP BY M.Date";
echo $UnionSTR;
$result = mysql_query($UnionSTR,$link_id);

$DateTemp=array();
$QtyTemp=array();
$SignTemp=array();
$sum1=0;	$sum2=0;	$sum3=0;	$sum4=0;	$sum5=0;
$sum6=0;	$sum7=0;	$sum8=0;	$sum9=0;  $sum10=0;
if($myrow = mysql_fetch_array($result)){
	//$i=1;
	do{
		$Qty= $myrow["Qty"];
		$Sign= $myrow["Sign"];
		if($myrow["Date"]==""){
			  $Date="0000-00-00";
			}
		else{
			$Date=substr($myrow["Date"],0,10);
			}
		
		if($Qty>0 or $Qty<0){
			$DateTemp[]=$Date;
			$QtyTemp[]=$Qty;
			$SignTemp[]=$Sign;
			//echo $i." - ".$Sign."/".$Date."/".$Qty."<br>";$i++;			
			}
		
		}while ($myrow = mysql_fetch_array($result));		
	}
else{
	echo"没有记录";
	}
$grade = array("Date"=>$DateTemp,"Qty"=>$QtyTemp,"Sign"=>$SignTemp);
$tt=array_multisort($grade["Date"], SORT_STRING, SORT_ASC,$grade["Sign"], SORT_NUMERIC, SORT_ASC,$grade["Qty"], SORT_NUMERIC, SORT_ASC);
$count=count($DateTemp);
//数组处理完毕
?>
<form name="form1" method="post" action="">
<table height="315" cellpadding="1"  cellspacing="0">
  <tr valign="top">
    <th height="37" colspan="13" scope="col">配件数据分析报表</th>
  </tr>
  <tr>
    <td height="25" colspan="6" class="A0100">配件：<?php  echo "$StuffId-$StuffCname"; ?>
      <input name="StuffId" type="hidden" id="StuffId" value="<?php  echo $StuffId?>">
    </td>
    <td colspan="2" class="A0100">初始库存:<?php  echo $dStockQty?></td>
	<td colspan="2" class="A0100">最低库存:<?php  echo $mStockQty?></td>
    <td colspan="3" class="A0100" align="right">报表日期：<?php  echo date("Y年m月d日")?></td>
  </tr>
    <tr class=''>
    <td width="35" height="21" class="A0111" align="center">序号</td>
    <td width="70" class="A0101" align="center">采购日期</td>
	<td width="50" class="A0101" align="center">订单<br>数量</td>
    <td width="50" class="A0101" align="center">采购<br>数量</td>
	<td width="50" class="A0101" align="center">特采<br>数量</td>
    <td width="50" class="A0101" align="center">入库<br>数量</td>
	<?php 
   if($Login_uType==2){
	echo "<td width='50' class='A0101' align='center'>车间<br>退料</td>";
	}
	else{
	echo "<td width='50' class='A0101' align='center'>备品<br>转入</td>";
	}
	?>
    <td width="50" class="A0101" align="center">领料<br>数量</td>
    <td width="50" class="A0101" align="center">出货<br>数量</td>
    <td width="50" class="A0101" align="center">报废<br>数量</td>
	<td width="50" class="A0101" align="center">退换<br>数量</td>
    <td width="50" class="A0101" align="center">补仓<br>数量</td>
    <td width="50" class="A0101" align="center">在库</td>
    <td width="50" class="A0101" align="center">可用<br>库存</td>
  </tr>
  <?php 
$NumOfCol=10;
$ColTemp=$NumOfCol;//当前列
$DateTemp="";
$c2TEMP=$dStockQty;//当天在库,初始值为初始库存
$c3TEMP=$dStockQty;//当天可用库存,初始值为初始库存
$Rowtemp=0;
for($i=0;$i<$count;$i++){
	$Date=$grade[Date][$i];	
	$Qty=$grade[Qty][$i];
	$Sign=$grade[Sign][$i];//有数据的列
	
	if($DateTemp!=$grade[Date][$i]){//新行,如果日期与参照日期不一致，表示新行开始
		$DateTemp=$grade[Date][$i];//重新设置参照日期
		if($ColTemp!=$NumOfCol){//如果当前列数不是9，表示上一行未结束,先补足上一行
			for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
				echo "<td  class='A0101'>&nbsp</td>";
				}
			$c3Row=$c3TEMP>=0?$c3TEMP:0;
			echo "<td class='A0101'><div align='right'>$c2TEMP</div></td>";
			echo "<td class='A0101'><div align='right'>$c3Row</div></td></tr>";//结束上一行
			}
		//新行正式开始
		$ColTemp=0;
		$Rowtemp++;
		//新行前两列：序号列和日期列
		if($Date=="0000-00-00"){
			$Date="<span title='全部还没有下采购单的特采单数量' style='CURSOR: pointer'>◆</span>";
			}
		echo"<tr><td class='A0111' align='center'>$Rowtemp</td><td class='A0101' align='center'>$Date</td>";
	}

	for($ColTemp=$ColTemp+1;$ColTemp<$Sign*1;$ColTemp++){
		echo "<td  class='A0101'>&nbsp</td>";
		}
	echo"<td  class='A0101'><div align='right'>$Qty</div></td>";
	switch($ColTemp){
		case 1://订单数量
		   $c3TEMP=$c3TEMP-$Qty;$sum1=$sum1+$Qty;
		   break;
		case 2://采购数量
		   $sum2=$sum2+$Qty;	$c3TEMP=$c3TEMP+$Qty;
		   break;
		case 3://特采数量
		   $sum3=$sum3+$Qty;	$c3TEMP=$c3TEMP+$Qty;
		   break;
	    case 4://入库数量
		   $c2TEMP=$c2TEMP+$Qty;	$sum4=$sum4+$Qty;
		   break;
		case 5://备品转入
		   $c2TEMP=$c2TEMP+$Qty;	$sum5=$sum5+$Qty;	$c3TEMP=$c3TEMP+$Qty;
		   break;
		case 6://领料数量
		   $c2TEMP=$c2TEMP-$Qty;	$sum6=$sum6+$Qty;
		   break;
	    case 7: //已出货数量
	         $sum7=$sum7+$Qty;
			 break;
	    case 8://报废数量
		   $c2TEMP=$c2TEMP-$Qty;	$sum8=$sum8+$Qty; $c3TEMP=$c3TEMP-$Qty;
		   //echo "$Date:" . $Qty;
		   break;
		case 9://退换数量
		   $c2TEMP=$c2TEMP-$Qty;	$sum9=$sum9+$Qty;
		   break;
		case 10://补仓数量
		   $c2TEMP=$c2TEMP+$Qty;	$sum10=$sum10+$Qty;	
		   	$c3Row=$c3TEMP>=0?$c3TEMP:0;
		    echo"<td  class='A0101'><div align='right'>$c2TEMP</div></td>";
	        echo"<td  class='A0101'><div align='right'>$c3Row</div></td></tr>";
		   break;
	 }
}
/*	//订单数量
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
	if($ColTemp==7){
		$c2TEMP=$c2TEMP-$Qty;	$sum7=$sum7+$Qty; $c3TEMP=$c3TEMP-$Qty;}
	//退换数量
	if($ColTemp==8){
		$c2TEMP=$c2TEMP-$Qty;	$sum8=$sum8+$Qty;}
	//补仓数量
	if($ColTemp==9){
		$c2TEMP=$c2TEMP+$Qty;	$sum9=$sum9+$Qty;		$c3Row=$c3TEMP>=0?$c3TEMP:0;
		echo"<td  class='A0101'><div align='right'>$c2TEMP</div></td>";
		echo"<td  class='A0101'><div align='right'>$c3Row</div></td></tr>";
		}
	}
*/	
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
$OrderSurplus=($dStockQty+$sum2+$sum3+$sum5)-($sum1+$sum8);
 
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
	<td width="50" class="A0101" align="center">订单<br>数量</td>
    <td width="50" class="A0101" align="center">采购<br>数量</td>
	<td width="50" class="A0101" align="center">特采<br>数量</td>
    <td width="50" class="A0101" align="center">入库<br>数量</td>
	<?php 
   if($Login_uType==2){
	echo "<td width='50' class='A0101' align='center'>车间<br>退料</td>";
	}
	else{
	echo "<td width='50' class='A0101' align='center'>备品<br>转入</td>";
	}
	?>
    <td width="50" class="A0101" align="center">领料<br>数量</td>
    <td width="50" class="A0101" align="center">出货<br>数量</td>
    <td width="50" class="A0101" align="center">报废<br>数量</td>
	<td width="50" class="A0101" align="center">退换<br>数量</td>
    <td width="50" class="A0101" align="center">补仓<br>数量</td>
    <td width="50" class="A0101" align="center">在库</td>
    <td width="50" class="A0101" align="center">可用<br>库存</td>
  </tr>
  <tr>
    <td colspan="2" class="A0111">合计：</td>
    <td  class="A0101" align="right"><?php  echo $sum1?></td>
    <td class="A0101" align="right"><?php  echo $sum2?></td>
    <td class="A0101" align="right"><?php  echo $sum3?></td>
    <td class="A0101" align="right"><?php  echo $sum4?></td>
    <td class="A0101" align="right"><?php  echo $sum5?></td>
    <td class="A0101" align="right"><?php  echo $sum6?></td>
    <td class="A0101" align="right"><?php  echo $sum7?></td>
    <td class="A0101" align="right"><?php  echo $sum8?></td>
	<td class="A0101" align="right"><?php  echo $sum9?></td>
    <td class="A0101" align="right"><?php  echo $sum10?></td>
	 <td class="A0101" align="right"><?php  echo $c2TEMP?></td>
	 <td class="A0101" align="right"><?php  echo $c3TEMP?></td>
  </tr>
  <tr>
    <td height="21" colspan="13" class="A0100">&nbsp;</td>
  </tr>
  <tr class=''>
    <td height="21" colspan="2" class="A0111" align="center">项目</td>
    <td height="21" colspan="2"  class="A0101" align="center">库存表的数据</td>
    <td colspan="2"  class="A0101" align="center">分析到的数据</td>
    <td colspan="7"  class="A0101" align="center">分析结果或提示</td>
    </tr>
  <tr>
    <td height="24" colspan="2" class="A0111" align="center" class=''>在库<input name="tStockQty" type="hidden" id="tStockQty" value="<?php  echo $c2TEMP?>"></td>
    <td colspan="2" align="center" class="A0101"><?php  echo $tStockQty?></td>
    <td colspan="2" align="center" class="A0101"><?php  echo $c2TEMP?></td>
    <td colspan="7" class="A0101">
	<?php 
	if($tStockQty==$c2TEMP && $tStockQty>=0){
		echo"正确";
		}
	else{
		echo"不正确";
		}
	?>
	</td>
  </tr>
  <tr>
    <td height="24" colspan="2" class="A0111" align="center" class=''>可用库存<input name="oStockQty" type="hidden" id="oStockQty" value="<?php  echo $c3TEMP?>"></td>
    <td colspan="2" align="center" class="A0101"><?php  echo $oStockQty?></td>
    <td colspan="2" align="center" class="A0101"><?php  echo $c3TEMP?></td>
    <td colspan="7" class="A0101">
	<?php 
	if($oStockQty==$c3TEMP){
		echo"正确";
		}
	else{
		echo"不正确";
		}
	?>
	</td>
  </tr>
  <tr><td colspan="15" class="A0100">&nbsp;</td></tr>
  <tr>
    <td height="24" colspan="2" align="center" class="A0111" class=''>数据追踪</td>
    <td colspan="4" align="center" class="A0101">横向追踪数据(以订单为主线) <a href="ck_rk_report.php?StuffId=<?php  echo $StuffId?>" target="_blank">入库追踪</a></td>
    <td colspan="4" align="center" class="A0101">纵向追踪数据(以操作时间先后顺序流水记录)<a href="stuffreport_track.php?StuffId=<?php  echo $StuffId?>&StuffCname=<?php  echo $UStuffCname?>" target="_blank">配件追踪</a></td>
    <td colspan="3" align="center" class="A0101">&nbsp;
      <?php 
	//145权限(库存更正)
	if($tStockQty!=$c2TEMP || $oStockQty!=$c3TEMP){
		if($c2TEMP>=0 && $c3TEMP>=0){
			$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=145 and UserId=$Login_P_Number LIMIT 1",$link_id);
			if($TRow = mysql_fetch_array($TResult)){
				echo"<input type='button' name='Submit' value='更正库存' onClick='javascript:ErrorCorrection();'>";
				}
			}
		}
	  ?>
    </td>
    </tr>
</table>
</form>
</body>
</html>
<script>
function ErrorCorrection(){
	var StuffId=document.form1.StuffId.value;
	var temp2=document.form1.tStockQty.value;
	var temp3=document.form1.oStockQty.value;
	if((temp2*1<0) || (temp3*1<0)){
	//if(1<0){	
		alert("分析结果有负数，无法更正，需检查数据是否多领料等现象!或请管理员处理");
		return false;
		}
	else{
		myurl="stuffreport_updated.php?StuffId="+StuffId+"&tStockQty="+temp2+"&oStockQty="+temp3; 
		var ajax=InitAjax(); 
	        ajax.open("GET",myurl,true);
	        ajax.onreadystatechange =function(){
		 if(ajax.readyState==4 && ajax.status ==200){// && ajax.status ==200
			alert("配件的库存数据已更正！");
			document.form1.submit();
	            }
		 }
	         ajax.send(null); 
               /* retCode=openUrl(myurl);
		if (retCode!=-2){
			alert("配件的库存数据已更正！");
			document.form1.submit();
			}
		else{
			alert("配件的库存更正失败！");
			}
                  */
		}
	}
</script>
