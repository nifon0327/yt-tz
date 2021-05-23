<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck1_rksheet
$DataIn.ck1_rkmain
$DataIn.cg1_stocksheet
二合一已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 收货明细");
//解密
$outArray=explode("|",$Sid);
$RuleStr=$outArray[0];
$EncryptStr=$outArray[1];
$StockId=anmaOut($RuleStr,$EncryptStr);
$checkSql = mysql_fetch_array(mysql_query("SELECT SUM(S.FactualQty+S.AddQty) AS cgQty FROM $DataIn.cg1_stocksheet S WHERE S.StockId='$StockId' LIMIT 1",$link_id));
$cgQty=$checkSql["cgQty"];
?>
<table width="350" cellpadding="1"  cellspacing="0">
  <tr valign="top">
    <th height="37" colspan="5" scope="col">需求单收货记录</th>
  </tr>
  <tr>
    <td height="25" colspan="5" class="A0100">流 水 号：<?php  echo $StockId?>  <br>采购总数：<?php  echo $cgQty?></td>
  </tr>
    <tr class="">
    <td width="50" height="25" class="A0111" align="center">序号</td>
    <td width="150" class="A0101" align="center">收货日期</td>
	<td width="150" class="A0101" align="center">收货单号</td>
    <td width="80" class="A0101" align="center">收货数量</td>
  </tr>
  <?php 
//入库明细
$UnionSTR="SELECT M.Date,M.BillNumber,concat('1') AS Sign,R.Qty 
FROM $DataIn.ck1_rksheet R 
LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id WHERE R.StockId='$StockId'";
$result = mysql_query($UnionSTR,$link_id);
$fQtySum=0;
if($myrow = mysql_fetch_array($result)){
	$i=1;
	do{
		$Date=$myrow["Date"];
		$BillNumber=$myrow["BillNumber"];
		//检查是否存在文件，是则可以打开
		//检查是否存在文件
		//$FilePath1="../download/deliverybill/$BillNumber.jpg";
		//if(file_exists($FilePath1)){
			//$BillNumber="<a href='$FilePath1' target='_blank'>$BillNumber</a>";
			//}
		$fQty= $myrow["Qty"];
		$fQtySum+=$fQty;
		echo "<tr>
		<td class='A0111' align='center' height='25'>$i</td>
		<td class='A0101' align='center'>$Date</td>
		<td class='A0101' align='center'>$BillNumber</td>
		<td class='A0101' align='center'>$fQty</td>";
		$i++;		
		}while ($myrow = mysql_fetch_array($result));		
//数组处理完毕
if($cgQty==$fQtySum){
	$Info="（全部收货）";
	}
else{
	if($fQty==0){
		$Info="（未收货）";
		}
	else{
		$Info="（部分收货）";
		}
	}
?>  
<tr class="">
    <td width="50" height="25" class="A0111" align="center">序号</td>
    <td width="150" class="A0101" align="center">收货日期</td>
	<td width="150" class="A0101" align="center">收货单号</td>
    <td width="80" class="A0101" align="center">收货数量</td>
  </tr>
  <tr>
    <td colspan="3" class="A0111" align="center" height="25">收货合计<?php  echo $Info?></td>
    <td class="A0101" align="center"><?php  echo $fQtySum?></td>
  </tr>
</table>
<?php 
	}
else{
	echo"没有记录";
	}
?>
</form>
</body>
</html>