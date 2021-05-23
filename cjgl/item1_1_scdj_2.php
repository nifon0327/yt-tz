<?php

$CheckSql=mysql_query("SELECT SC.Id,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId,SC.Remark,
D.StuffId,D.StuffCname,D.Picture,G.DeliveryDate,G.DeliveryWeek,SM.PurchaseID
FROM  $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
LEFT JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
WHERE 1 AND SC.POrderId='$POrderId' AND SC.sPOrderId = '$sPOrderId'" ,$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){

	$StuffId=$CheckRow["StuffId"];
	$Picture=$CheckRow["Picture"];
	$StuffCname=$CheckRow["StuffCname"];
    $PurchaseID=$CheckRow["PurchaseID"];
	$Qty=$CheckRow["Qty"];  //工单数量(生产数量)
	//已完成的工序数量
	$CheckCfQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS cfQty 
	FROM $DataIn.sc1_cjtj C 
	WHERE  C.sPOrderId = '$sPOrderId' AND C.StockId = $StockId",$link_id));
	$OverPQty=$CheckCfQty["cfQty"]==""?0:$CheckCfQty["cfQty"];
	//未完成订单数
	$UnPQty=$Qty-$OverPQty;
	}
?>
  <table width="800" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
    <td width="150" height="50" class="A1111">PO</td>
    <td width="150" class="A1101">配件ID</td>
    <td width="390" class="A1101">半成品名称</td>
    <td width="200" class="A1101">工单流水号
      <input name="POrderId"  type="hidden" id="POrderId"  value="<?php   echo $POrderId?>">
      <input name="sPOrderId" type="hidden" id="sPOrderId" value="<?php   echo $sPOrderId?>">
      <input name="StockId"   type="hidden" id="StockId"   value="<?php   echo $StockId?>"></td>
  </tr>
  <tr align="center">
    <td height="50" class="A0111"><?php echo $PurchaseID?></td>
    <td class="A0101"><?php    echo $StuffId?></td>
    <td class="A0101"><?php    echo $StuffCname?></td>
    <td class="A0101"><?php    echo $sPOrderId?></td>
  </tr>
</table>
<br>
<table width="800" height="221" border="0" cellpadding="0" cellspacing="0">
	<tr align="center" bgcolor="#d6efb5">
		<td height="60" colspan="5" class="A0000">登记生产数量</td>
  </tr>

	<tr align="center" bgcolor="#d6efb5">
		<td width="259" height="50" class="A1111">&nbsp;</td>
		<td width="259" class="A1101">工单</td>
		<td width="260" class="A1101">已生产</td>
		<td width="260" class="A1101">未生产</td>
		<td width="260" class="A1101">本次登记</td>
	</tr>
  <tr align="center">
    <td height="50" bgcolor="#d6efb5" class="A0111">数量</td>
  	<td class="A0101" bgcolor="#FFFFFF"><?php echo $Qty?></td>
    <td class="A0101" bgcolor="#FFFFFF"><?php echo $OverPQty?></td>
    <td class="A0101" bgcolor="#FFFFFF"><input name="UnPQty" type="text" class="I0000C" id="UnPQty" value="<?php    echo $UnPQty?>" size="13" readonly></td>
    <td class="A0101" bgcolor="#FFFFFF"><input name="Qty" type="text" class="I0000C" id="Qty" placeholder="输入生产数量" value="<?php    echo $UnPQty?>" size="15" onfocus="ClearStr()"></td>
  </tr>
  
  
  <tr>
    <td height="61" colspan="5"  align="center" class="A0000" bgcolor="#d6efb5">
    
	<table border="0" cellpadding="0" cellspacing="8" align="right" width="100%">
      <tr align="center">
	   <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="取消" onclick="closeMaskDiv()"></td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="提交" onclick="ToSaveDj()"></td>
      </tr>
    </table></td>
  </tr>
</table>