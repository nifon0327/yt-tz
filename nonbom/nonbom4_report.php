<?php 
//ewen 2013-03-12 OK
//配合ipad使用
if($ipadTag != "yes"){
	include "../model/modelhead.php";
	}
else{
	include "../basic/parameter.inc";
	echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
	<link rel='stylesheet' href='../model/css/sharing.css'>
	<link rel='stylesheet' href='../model/Totalsharing.css'>
	<link rel='stylesheet' href='../model/keyright.css'>
	<link rel='stylesheet' href='../model/SearchDiv.css'>
	<SCRIPT src='../model/pagefun.js' type=text/javascript></script>
	<SCRIPT src='../model/checkform.js' type=text/javascript></script>
	<SCRIPT src='../model/lookup.js' type=text/javascript></script>
	<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
	}
//读取配件库存值
$CheckSql=mysql_query("SELECT A.GoodsName,B.wStockQty,B.oStockQty,B.mStockQty,B.lStockQty
  FROM $DataPublic.nonbom4_goodsdata A 
  LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId 
  WHERE A.GoodsId='$GoodsId'",$link_id);; 
if($CheckRow = mysql_fetch_array($CheckSql))	{
	 $wStockQty=$CheckRow["wStockQty"];
	 $oStockQty=$CheckRow["oStockQty"];
	 $mStockQty=$CheckRow["mStockQty"];
     $lStockQty=$CheckRow["lStockQty"];
	 $GoodsName=$CheckRow["GoodsName"];
	 $thisDate=date("Y-m-d");
	}

$UStuffCname=urlencode($StuffCname);
//申购中数据:没下采购单的
$UnionSTR="SELECT Date, concat('1') AS Sign,SUM(Qty) AS Qty FROM (
SELECT DATE_FORMAT(A.Date,'%Y-%m-%d') AS Date,A.Qty
FROM $DataIn.nonbom6_cgsheet A 
WHERE A.GoodsId='$GoodsId' AND A.Mid='0' 
) A GROUP BY Date";
//已下单采购总数
$UnionSTR.=" UNION ALL  
SELECT Date, concat('2') AS Sign,SUM(Qty) AS Qty FROM (
      SELECT DATE_FORMAT(B.Date,'%Y-%m-%d') AS Date,A.Qty
      FROM $DataIn.nonbom6_cgsheet A 
     LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.Mid 
     WHERE A.GoodsId='$GoodsId' AND A.Mid!='0' 
) A GROUP BY Date";
//入库数据
$UnionSTR.=" UNION ALL 
SELECT Date, concat('3') AS Sign,SUM(Qty) AS Qty FROM (
         SELECT DATE_FORMAT(B.Date,'%Y-%m-%d') AS Date,A.Qty
         FROM $DataIn.nonbom7_insheet A 
          LEFT JOIN $DataIn.nonbom7_inmain B ON B.Id=A.Mid 
          WHERE A.GoodsId='$GoodsId'
) A GROUP BY Date";
//转入数据
$UnionSTR.=" UNION ALL 
SELECT Date, concat('4') AS Sign,SUM(Qty) AS Qty FROM (
    SELECT DATE_FORMAT(A.Date,'%Y-%m-%d') AS Date,A.Qty
    FROM $DataIn.nonbom9_insheet A WHERE A.GoodsId='$GoodsId' 
) A GROUP BY Date";
//领料数据
$UnionSTR.=" UNION ALL 
SELECT Date, concat('5') AS Sign,SUM(Qty) AS Qty FROM (
   SELECT DATE_FORMAT(A.Date,'%Y-%m-%d') AS Date,A.Qty
  FROM $DataIn.nonbom8_outsheet A WHERE A.GoodsId='$GoodsId' AND  A.Estate=0 
) A GROUP BY Date";

//报废
$UnionSTR.=" UNION ALL 
SELECT Date, concat('6') AS Sign,SUM(Qty) AS Qty FROM (
    SELECT DATE_FORMAT(A.Date,'%Y-%m-%d') AS Date,A.Qty
     FROM $DataIn.nonbom10_outsheet A WHERE A.GoodsId='$GoodsId' 
) A GROUP BY Date";


//退回
$UnionSTR.=" UNION ALL 
SELECT Date, concat('7') AS Sign,SUM(Qty) AS Qty FROM (
         SELECT DATE_FORMAT(A.Date,'%Y-%m-%d') AS Date,A.Qty
        FROM $DataIn.nonbom8_reback  A WHERE A.GoodsId='$GoodsId' 
) A GROUP BY Date";


//个人报废
$UnionSTR.=" UNION ALL 
SELECT Date, concat('8') AS Sign,SUM(Qty) AS Qty FROM (
   SELECT DATE_FORMAT(A.Date,'%Y-%m-%d') AS Date,concat('8') AS Sign,A.Qty
   FROM $DataIn.nonbom8_bf  A  WHERE A.GoodsId='$GoodsId' 
) A GROUP BY Date";

$result = mysql_query($UnionSTR,$link_id);
$DateTemp=array();
$QtyTemp=array();
$SignTemp=array();
$sum1=$sum2=$sum3=$sum4=$sum5=$sum6=$sum7=$sum8=0;
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
//数组处理完毕
?>
<form name="form1" method="post" action="">
<table cellpadding="1"  cellspacing="0">
  <tr valign="top">
    <th height="37" colspan="13" scope="col">非BOM配件数据分析报表</th>
  </tr>
  <tr>
    <td height="25" colspan="5" class="A0100">配件：<?php  echo "$GoodsId-$GoodsName"; ?>
      <input name="GoodsId" type="hidden" id="GoodsId" value="<?php  echo $GoodsId?>">
    </td>
	<td colspan="4" class="A0100">最低库存:<?php  echo $mStockQty?></td>
    <td colspan="4" class="A0100" align="right">报表日期：<?php  echo date("Y年m月d日")?></td>
  </tr>
    <tr class=''>
    <td width="35" height="21" class="A0111" align="center">序号</td>
    <td width="70" class="A0101" align="center">日期</td>
    <td width="70" class="A0101" align="center">申购中数量</td>
    <td width="70" class="A0101" align="center">采购数量</td>
    <td width="70" class="A0101" align="center">入库数量</td>
    <td width="70" class="A0101" align="center">转入数量</td>
    <td width="70" class="A0101" align="center">领料数量</td>
    <td width="70" class="A0101" align="center">报废数量</td>
    <td width="70" class="A0101" align="center">退仓数量</td>
    <td width="70" class="A0101" align="center">个人报废</td>
    <td width="70" class="A0101" align="center">在库库存</td>
    <td width="70" class="A0101" align="center">领用库存</td>
    <td width="70" class="A0101" align="center">可用库存</td>
  </tr>
  <?php 
$NumOfCol=8;
$ColTemp=$NumOfCol;//当前列
$DateTemp="";
$c2TEMP=$c3TEMP=$c4TEMP=0;
$Rowtemp=0;
for($i=0;$i<$count;$i++){
	$Date=$grade[Date][$i];	
	$Qty=$grade[Qty][$i];
	$Sign=$grade[Sign][$i];//有数据的列
	
//	echo "$Date : $Sign:" . $Qty ."<BR>";
	
	if($DateTemp!=$grade[Date][$i]){//新行,如果日期与参照日期不一致，表示新行开始
		$DateTemp=$grade[Date][$i];//重新设置参照日期
		if($ColTemp!=$NumOfCol){//如果当前列数不是9，表示上一行未结束,先补足上一行
			for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
				echo "<td  class='A0101'>&nbsp</td>";
				}
			$c3Row=$c3TEMP>=0?$c3TEMP:0;
			echo "<td class='A0101'><div align='right'>$c2TEMP</div></td>";
			echo "<td class='A0101'><div align='right'>$c4TEMP</div></td>";
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
		case 1://申购中数量
		   $sum1+=$Qty;
		   break;
		case 2://采购数量，采购库存增加
		   $sum2+=$Qty;	
		   $c3TEMP+=$Qty;
		   break;
		case 3://入库数量，在库增加
		   $sum3+=$Qty;
		   $c2TEMP+=$Qty;
		   break;
		case 4://转入数量
		   $sum4+=$Qty;
		   $c2TEMP+=$Qty;
		   $c3TEMP+=$Qty;
		   break;
	    case 5://领料数量，在库减少
		   $sum5+=$Qty;
		   $c2TEMP-=$Qty;
		   $c3TEMP-=$Qty;
           $c4TEMP+=$Qty;
		   break;
		case 6://报废数量
		   $sum6+=$Qty;
		   $c2TEMP-=$Qty;
		   $c3TEMP-=$Qty;
		   break;
		case 7://退回数量
		   $sum7+=$Qty;
		   $c2TEMP+=$Qty;
		   $c3TEMP+=$Qty;
           $c4TEMP-=$Qty;
		   break;
		case 8://个人报废
		   $sum8+=$Qty;
		   $c4TEMP-=$Qty;
		   	$c3Row=$c3TEMP>=0?$c3TEMP:0;
		    echo"<td  class='A0101'><div align='right'>$c2TEMP</div></td>";
		    echo"<td  class='A0101'><div align='right'>$c4TEMP</div></td>";
	        echo"<td  class='A0101'><div align='right'>$c3Row</div></td></tr>";
		   break;
	 }
}
if($ColTemp!=$NumOfCol){//上一行未结束
	for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
		echo "<td  class='A0101'>&nbsp</td>";
		}
		$c3Row=$c3TEMP>=0?$c3TEMP:0;
	echo "<td class='A0101'><div align='right'>$c2TEMP </div></td>";//结束上一行
    echo"<td  class='A0101'><div align='right'>$c4TEMP</div></td>";
	echo "<td class='A0101' height='20'><div align='right'>$c3Row </div></td></tr>";//结束上一行
	}

//采购未回数量=采购数量-入库数量
//$Mantissa=$sum2-$sum3+$sum7;
//采购库存=采购数量+转入数量-领料数量-报废数量
//$OrderSurplus=$sum2-$sum4+$sum5-$sum6+$sum7;
if($i==0){
	echo "<tr><td height='21' class='A0111' align='center' colspan='13'>没有资料</td></tr>";
	}
?>  
<tr class=''>
    <td height="21" class="A0111" align="center">序号</td>
    <td class="A0101" align="center">日期</td>
    <td align="center" class="A0101">申购中数量</td>
    <td align="center" class="A0101">采购数量</td>
    <td class="A0101" align="center">入库数量</td>
    <td class="A0101" align="center">转入数量</td>
    <td class="A0101" align="center">领料数量</td>
    <td class="A0101" align="center">报废数量</td>
    <td class="A0101" align="center">退仓数量</td>
    <td class="A0101" align="center">个人报废</td>
    <td class="A0101" align="center">在库库存</td>
    <td class="A0101" align="center">领用库存</td>
    <td class="A0101" align="center">可用库存</td>
  </tr>
  <tr>
    <td colspan="2" class="A0111" height="20">合计：</td>
    <td align="right"  class="A0101"><?php  echo $sum1;?></td>
    <td align="right"  class="A0101"><?php  echo $sum2;?></td>
    <td class="A0101" align="right"><?php  echo $sum3;?></td>
    <td class="A0101" align="right"><?php  echo $sum4;?></td>
    <td class="A0101" align="right"><?php  echo $sum5;?></td>
    <td class="A0101" align="right"><?php  echo $sum6;?></td>
    <td class="A0101" align="right"><?php  echo $sum7;?></td>
    <td class="A0101" align="right"><?php  echo $sum8;?></td>
	 <td class="A0101" align="right"><?php  echo $c2TEMP;?></td>
	 <td class="A0101" align="right"><?php  echo $c4TEMP;?></td>
	 <td class="A0101" align="right"><?php  echo $c3TEMP;?></td>
  </tr>
  <tr>
    <td height="21" colspan="13" class="A0100">&nbsp;</td>
  </tr>
  <tr class=''>
    <td height="21" colspan="2" class="A0111" align="center">项目</td>
    <td height="21" colspan="4"  class="A0101" align="center">库存表的数据</td>
    <td colspan="4"  class="A0101" align="center">分析到的数据</td>
    <td colspan="3"  class="A0101" align="center">分析结果或提示</td>
    </tr>
  <tr>
    <td height="24" colspan="2" class="A0111 " align="center">在库库存<input name="wStockQty" type="hidden" id="wStockQty" value="<?php  echo $c2TEMP?>"></td>
    <td colspan="4" align="center" class="A0101"><?php  echo $wStockQty?></td>
    <td colspan="4" align="center" class="A0101"><?php  echo $c2TEMP?></td>
    <td colspan="3" class="A0101">
	<?php 
	$tempA=round($wStockQty,4);
	$tempB=round($c2TEMP,4);
	$tempS=$tempA-$tempB;
	if($tempS==0 && ($wStockQty*1)>=0){
		echo"正确";
		}
	else{
		echo"不正确";
		if($wStockQty>=0){
			$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=145 and UserId=$Login_P_Number LIMIT 1",$link_id);
			if($TRow = mysql_fetch_array($TResult)){
				echo"<input type='button' name='Submit' value='更正库存' onClick='javascript:ErrorCorrection();'>";
				}
			}
		
		}
	?>
	</td>
  </tr>
  <tr>
    <td height="24" colspan="2" class="A0111 " align="center">可用库存<input name="oStockQty" type="hidden" id="oStockQty" value="<?php  echo $c3TEMP?>"></td>
    <td colspan="4" align="center" class="A0101"><?php  echo $oStockQty?></td>
    <td colspan="4" align="center" class="A0101"><?php  echo $c3TEMP?></td>
    <td colspan="3" class="A0101">
	<?php 
	if($oStockQty==$c3TEMP){
		echo"正确";
		}
	else{
		echo"不正确";
		if( $oStockQty>=0){
			$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=145 and UserId=$Login_P_Number LIMIT 1",$link_id);
			if($TRow = mysql_fetch_array($TResult) || $Login_P_Number == '11008'){
				echo"<input type='button' name='Submit' value='更正库存' onClick='javascript:ErrorCorrection();'>";
				}
			}
		
		}
	?>
	</td>
  </tr>


  <tr>
    <td height="24" colspan="2" class="A0111 " align="center">领用库存<input name="lStockQty" type="hidden" id="lStockQty" value="<?php  echo $c4TEMP?>"></td>
    <td colspan="4" align="center" class="A0101"><?php  echo $lStockQty?></td>
    <td colspan="4" align="center" class="A0101"><?php  echo $c4TEMP?></td>
    <td colspan="3" class="A0101">
	<?php 
	if($lStockQty==$c4TEMP){
		echo"正确";
		}
	else{
		echo"不正确";
		if( $lStockQty>=0){
			$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=145 and UserId=$Login_P_Number LIMIT 1",$link_id);
			if($TRow = mysql_fetch_array($TResult) || $Login_P_Number == '11008'){
				echo"<input type='button' name='Submit' value='更正库存' onClick='javascript:ErrorCorrection();'>";
				}
			}
		
		}
	?>
	</td>
  </tr>
  <tr>
    <td height="24" colspan="13" class="A0111"><div class="blueB">
    1､在库库存=入库数量+转入数量+退回数量-领用数量-报废数量<br />
    2､领用库存=领用数量-个人报废-退回数量<br />
    3､采购库存=采购数量+转入数量+退回数量-领用数量-报废数量<br />
    </div>
    </td>
    </tr>

</table>
</form>
</body>
</html>
<script>
function ErrorCorrection(){
	var GoodsId=document.form1.GoodsId.value;
	var temp2=document.form1.wStockQty.value;
	var temp3=document.form1.oStockQty.value;
	var temp4=document.form1.lStockQty.value;
	/*if((temp2*1<0) || (temp3*1<0)){
	//if(1<0){	
		alert("分析结果有负数，无法更正，需检查数据是否多领料等现象!或请管理员处理");
		return false;
		}
	else{*/
		myurl="nonbom4_report_updated.php?GoodsId="+GoodsId+"&wStockQty="+temp2+"&oStockQty="+temp3+"&lStockQty="+temp4; 
		//alert (myurl);
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
	//	}
	}
</script>
