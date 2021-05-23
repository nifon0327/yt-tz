<?php 
/*
已更新$DataIn.电信---yang 20120801
*/

//配合ipad使用
$StuffId;
//读取配件库存值
$CheckSql=mysql_query("SELECT S.dStockQty,S.tStockQty,S.oStockQty,S.mStockQty,D.StuffId,D.StuffCname 
FROM $DataIn.ck9_stocksheet S,$DataIn.stuffdata D 
WHERE D.StuffId='$StuffId' AND S.StuffId=D.stuffId",$link_id);; 
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

$UnionSTR="SELECT A.Date,A.Sign,SUM(IFNULL(A.Qty,0)) AS Qty FROM (
SELECT M.OrderDate AS Date,concat('1') AS Sign,G.OrderQty AS Qty 
FROM $DataIn.cg1_stocksheet G
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
WHERE G.StuffId='$StuffId' AND G.POrderId!='' 
) A  GROUP BY A.Date";

//采购数据（包括已下采购单和没下采购单）
$UnionSTR.="
UNION ALL
SELECT M.OrderDate AS Date,concat('2') AS Sign,SUM(G.FactualQty+G.AddQty) AS Qty 
FROM $DataIn.cg1_stocksheet G,$DataIn.yw1_ordermain M,$DataIn.yw1_ordersheet S
WHERE S.POrderId=G.POrderId AND S.OrderNumber=M.OrderNumber AND G.StuffId='$StuffId' GROUP BY M.OrderDate";

//已下采购单的特采数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('3') AS Sign,SUM(S.FactualQty+S.AddQty) AS Qty 
FROM $DataIn.cg1_stocksheet S,$DataIn.cg1_stockmain M WHERE S.StuffId='$StuffId' AND S.POrderId='' AND S.FactualQty>0 AND M.Id=S.Mid GROUP BY M.Date";

//未下采购单的特采数据
$UnionSTR.="
UNION ALL
SELECT concat('0000-00-00') AS Date,concat('3') AS Sign,IFNULL(SUM(FactualQty+AddQty),0) AS Qty 
FROM $DataIn.cg1_stocksheet WHERE Mid=0 AND StuffId='$StuffId' AND POrderId=''";

//入库数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('4') AS Sign,SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id WHERE R.StuffId='$StuffId' GROUP BY M.Date";


//备品转入数据
$UnionSTR.="
UNION ALL
SELECT Date,concat('5') AS Sign,SUM(Qty) AS Qty FROM $DataIn.ck7_bprk WHERE StuffId='$StuffId' AND  Estate=0  GROUP BY Date";

//领料数据
$UnionSTR.="
UNION ALL
SELECT A.Date,concat('6') AS Sign,SUM(A.Qty) AS Qty FROM (
SELECT IFNULL(DATE_FORMAT(M.Date,'%Y-%m-%d'),DATE_FORMAT(B.Date,'%Y-%m-%d')) AS Date,S.Qty 
FROM $DataIn.ck5_llsheet S LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id LEFT JOIN  $DataIn.yw9_blmain B ON S.Pid=B.Id WHERE S.StuffId='$StuffId') A GROUP BY  DATE_FORMAT(A.Date,'%Y-%m-%d')";

//已出货数据
$UnionSTR.="
UNION ALL
SELECT B.Date,concat('7') AS Sign,SUM(B.Qty) AS Qty
FROM (
		SELECT M.Date,A.Qty 
		FROM (
			SELECT Y.POrderId,SUM(S.Qty) AS Qty 
			FROM $DataIn.ck5_llsheet S 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
			LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
			WHERE S.StuffId='$StuffId' AND Y.Estate=0 GROUP BY Y.POrderId
		)A 
			LEFT JOIN  $DataIn.ch1_shipsheet C ON C.POrderId=A.POrderId 
			LEFT JOIN  $DataIn.ch1_shipmain M ON M.Id=C.MId 
		GROUP BY A.POrderId
)B
GROUP BY B.Date";
//LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id 
//报废数据,只有审核通过的才算 modify by zx 2010-11-30
$UnionSTR.="
UNION ALL
SELECT Date,concat('8') AS Sign,SUM(Qty) AS Qty FROM $DataIn.ck8_bfsheet WHERE (Estate=0 OR Estate=3)  AND StuffId='$StuffId' GROUP BY Date";

//退换数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('9') AS Sign,SUM(S.Qty) AS Qty FROM $DataIn.ck2_thsheet S LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id WHERE S.StuffId='$StuffId' GROUP BY M.Date";

//补仓数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('10') AS Sign,SUM(S.Qty) AS Qty FROM $DataIn.ck3_bcsheet S LEFT JOIN $DataIn.ck3_bcmain M ON S.Mid=M.Id WHERE S.StuffId='$StuffId' GROUP BY M.Date";
//echo $UnionSTR;
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

$grade = array("Date"=>$DateTemp,"Qty"=>$QtyTemp,"Sign"=>$SignTemp);
$tt=array_multisort($grade["Date"], SORT_STRING, SORT_ASC,$grade["Sign"], SORT_NUMERIC, SORT_ASC,$grade["Qty"], SORT_NUMERIC, SORT_ASC);
$count=count($DateTemp);

$colTitle=array("采购日期",
				  "订单数量","采购数量","特采数量","入库数量",$uType==2?"车间退料":"备品转入",
				  "领料数量","出货数量","报废数量","退换数量","补仓数量",
				  "在库","可用");


$NumOfCol=10;
$ColTemp=$NumOfCol;//当前列
$DateTemp="";
$c2TEMP=$dStockQty;//当天在库,初始值为初始库存
$c3TEMP=$dStockQty;//当天可用库存,初始值为初始库存
$Rowtemp=0;
$colData = array();
for($i=0;$i<$count;$i++){
	$Date=$grade[Date][$i];	
	$Qty=$grade[Qty][$i];
	$Sign=$grade[Sign][$i];//有数据的列
	
//	echo "$Date : $Sign:" . $Qty ."<BR>";
	
	if($DateTemp!=$grade[Date][$i]){//新行,如果日期与参照日期不一致，表示新行开始
		$DateTemp=$grade[Date][$i];//重新设置参照日期
		if($ColTemp!=$NumOfCol){//如果当前列数不是9，表示上一行未结束,先补足上一行
			for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
				//echo "<td  class='A0101'>&nbsp</td>";
				$colData[$DateTemp][]="";
			}
			$c3Row=$c3TEMP>=0?$c3TEMP:0;
			$colData[$DateTemp][]="$c2TEMP";
			$colData[$DateTemp][]="$c3Row";
			//echo "<td class='A0101'><div align='right'>$c2TEMP</div></td>";
		//	echo "<td class='A0101'><div align='right'>$c3Row</div></td></tr>";//结束上一行
			}
		//新行正式开始
		$ColTemp=0;
		$Rowtemp++;
		//新行前两列：序号列和日期列
		if($Date=="0000-00-00"){
			//$Date="<span title='全部还没有下采购单的特采单数量' style='CURSOR: pointer'>◆</span>";
		}
		
		//echo"<tr><td class='A0111' align='center'>$Rowtemp</td><td class='A0101' align='center'>$Date</td>";
	}

	for($ColTemp=$ColTemp+1;$ColTemp<$Sign*1;$ColTemp++){
		//echo "<td  class='A0101'>&nbsp</td>";
		$colData[$DateTemp][]="";
	}
	$colData[$DateTemp][]="$Qty";
	//echo"<td  class='A0101'><div align='right'>$Qty</div></td>";
	
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
		   break;
		case 9://退换数量
		   $c2TEMP=$c2TEMP-$Qty;	$sum9=$sum9+$Qty;
		   break;
		case 10://补仓数量
		   $c2TEMP=$c2TEMP+$Qty;	$sum10=$sum10+$Qty;	
		   	$c3Row=$c3TEMP>=0?$c3TEMP:0;
			
			$colData[$DateTemp][]="$c2TEMP";
			$colData[$DateTemp][]="$c3Row";
		    //echo"<td  class='A0101'><div align='right'>$c2TEMP</div></td>";
	        //echo"<td  class='A0101'><div align='right'>$c3Row</div></td></tr>";
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
		//echo "<td  class='A0101'>&nbsp</td>";
		$colData[$DateTemp][]="";
		}
		$c3Row=$c3TEMP>=0?$c3TEMP:0;
		$colData[$DateTemp][]="$c2TEMP";
			$colData[$DateTemp][]="$c3Row";
	//echo "<td class='A0101'><div align='right'>$c2TEMP </div></td>";//结束上一行
	//echo "<td class='A0101'><div align='right'>$c3Row </div></td></tr>";//结束上一行
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


	if($tStockQty==$c2TEMP && $tStockQty>=0){
		//echo"正确";
		}
	else{
		//echo"<span class='redN'>不正确</span>";
		}
	
	if($oStockQty==$c3TEMP){
		//echo"正确";
		}
	else{
		//echo"<span class='redN'>不正确</span>";
		}

	//145权限(库存更正)
	if($tStockQty!=$c2TEMP || $oStockQty!=$c3TEMP){
		if(($c2TEMP>=0 || $c3TEMP>=0) || ($c2TEMP<0 && $tStockQty>0) ||  ($c3TEMP<0 && $oStockQty>0)){
			$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=145 and UserId=$Login_P_Number LIMIT 1",$link_id);
			if($TRow = mysql_fetch_array($TResult) || $Login_P_Number == '11008'){
				//echo"<input type='button' name='Submit' value='更正库存' onClick='javascript:ErrorCorrection();'>";
				}
			}
		}
	 
	 $jsonArray = array("ColData"=>$colData,"ColTitle"=>$colTitle);
	 ?>