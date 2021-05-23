<?php 
//ewen 2013-03-13 OK
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 收货明细");
$checkSql = mysql_fetch_array(mysql_query("SELECT A.Qty,B.GoodsName,B.BarCode,B.Unit,E.Forshort,F.Name AS Buyer 
										  FROM $DataIn.nonbom6_cgsheet A 
										  LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
										  LEFT JOIN $DataIn.nonbom6_cgmain C ON C.Id=A.Mid
										  LEFT JOIN $DataPublic.nonbom3_retailermain E ON E.CompanyId=C.CompanyId
										  LEFT JOIN $DataPublic.staffmain F ON F.Number=C.BuyerId
										  WHERE A.Id='$cgId' LIMIT 1",$link_id));
$cgQty=$checkSql["Qty"];
?>
<table cellpadding="1"  cellspacing="0">
  <tr valign="top">
    <th height="37" colspan="6" scope="col">非BOM配件收货记录</th>
  </tr>
  <tr>
    <td height="25" colspan="6" class="A0100">
    配件名称：<?php  echo $checkSql["GoodsName"];?> <br>
    配件条码：<?php  echo $checkSql["BarCode"];?> <br>
    计量单位：<?php  echo $checkSql["Unit"];?> <br>
    采购流水：<?php  echo $cgId;?> <br>
    采购总数：<?php  echo $cgQty;?><br>
    <p>
    采购：<?php  echo $checkSql["Buyer"];?> <br>
    供应商：<?php  echo $checkSql["Forshort"];?><br>
    </p>
    </td>
  </tr>
    <tr class="">
    <td width="50" height="25" class="A0111" align="center">序号</td>
    <td width="150" class="A0101" align="center">收货日期</td>
	<td width="150" class="A0101" align="center">收货单号</td>
    <td width="150" class="A0101" align="center">收货凭证</td>
    <td width="80" class="A0101" align="center">收货数量</td>
    <td width="80" class="A0101" align="center">收货员</td>
  </tr>
  <?php 
//入库明细
$UnionSTR="SELECT B.Date,B.BillNumber,B.Bill,A.Mid,A.Qty,C.Name 
FROM $DataIn.nonbom7_insheet A 
LEFT JOIN $DataIn.nonbom7_inmain B ON B.Id=A.Mid
LEFT JOIN $DataPublic.staffmain C ON C.Number=B.Operator
WHERE A.cgId='$cgId'";
$result = mysql_query($UnionSTR,$link_id);
$fQtySum=0;
if($myrow = mysql_fetch_array($result)){
	$i=1;
	$DirRK=anmaIn("download/nonbom_rk/",$SinkOrder,$motherSTR);
	do{
		$Date=$myrow["Date"];
		$BillNumber=$myrow["BillNumber"];
		$Bill=$myrow["Bill"];
		$Mid= $myrow["Mid"];
		$fQty= $myrow["Qty"];
		$Name= $myrow["Name"];
		$fQtySum+=$fQty;
		if($Bill==1){
			$Bill=$Mid.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$DirRK\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>查看</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		echo "<tr>
		<td class='A0111' align='center' height='25'>$i</td>
		<td class='A0101' align='center'>$Date</td>
		<td class='A0101' align='center'>$BillNumber</td>
		<td class='A0101' align='center'>$Bill</td>
		<td class='A0101' align='center'>$fQty</td>
		<td class='A0101' align='center'>$Name</td>";
		$i++;		
		}while ($myrow = mysql_fetch_array($result));		
//数组处理完毕
if($cgQty==$fQtySum){
	$Info="<span class='greenB'>（全部收货）</span>";
	}
else{
	if($fQty==0){
		$Info="<span class='redB'>（未收货）</span>";
		}
	else{
		$Info="<span class='yellowB'>（部分收货）</span>";
		}
	}
?>  
  <tr>
    <td colspan="4" class="A0111" align="center" height="25">收货合计<?php  echo $Info?></td>
    <td class="A0101" align="center"><?php  echo $fQtySum?></td>
    <td class="A0101" align="center">&nbsp;</td>
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