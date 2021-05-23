<?php
$CheckSql=mysql_query("SELECT P.CompanyId,C.Forshort,S.OrderPO,
S.ProductId,P.cName,P.eCode,S.POrderId,S.Qty AS OrderQty,SC.Qty
FROM $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet S ON  S.POrderId = SC.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
WHERE 1 AND S.POrderId='$POrderId' AND SC.sPOrderId = '$sPOrderId'" ,$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$CompanyId=$CheckRow["CompanyId"];
	$Forshort=$CheckRow["Forshort"];
	$OrderPO=$CheckRow["OrderPO"];
	$ProductId=$CheckRow["ProductId"];
	$cName=$CheckRow["cName"];
	$eCode=$CheckRow["eCode"];
	$OrderQty=$CheckRow["OrderQty"];		//订单数量
	$Qty=$CheckRow["Qty"];  //工单数量(生产数量)
	//已完成的工序数量
	$CheckCfQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS cfQty 
	FROM $DataIn.sc1_cjtj C 
	WHERE C.POrderId='$POrderId' AND C.sPOrderId = '$sPOrderId' AND C.StockId = $StockId",$link_id));
	$OverPQty=$CheckCfQty["cfQty"]==""?0:$CheckCfQty["cfQty"];
	//未完成订单数
	$UnPQty=$Qty-$OverPQty;
	}

$Relation=0;
$RelationResult=mysql_query("SELECT Relation FROM $DataIn.sc1_newrelation  
						  WHERE POrderId='$POrderId' LIMIT 1",$link_id);
if($RelationRows = mysql_fetch_array($RelationResult)){
          $Relation=$RelationRows["Relation"];	
}			  
else{					  
	$BoxResult = mysql_query("SELECT P.Relation FROM $DataIn.pands P 
				 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId 
				 LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				 WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040' ",$link_id);
		if($BoxRows = mysql_fetch_array($BoxResult)){
			$Relation=$BoxRows["Relation"];
		 	if ($Relation!=""){
				$RelationArray=explode("/",$Relation);
				$Relation=$RelationArray[1];	
		   }
	   }
}

	
?>
  <table width="800" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
    <td width="182" height="50" class="A1111">客户</td>
<!--    <td width="116" class="A1101">PO</td>-->
    <td width="117" class="A1101">产品ID</td>
    <td width="390" class="A1101">产品名称</td>
    <td width="338" class="A1101">产品条码</td>
    <td width="195" class="A1101">工单流水号
      <input name="POrderId" type="hidden" id="POrderId" value="<?php    echo $POrderId?>">
      <input name="sPOrderId" type="hidden" id="sPOrderId" value="<?php    echo $sPOrderId?>">
      <input name="StockId" type="hidden" id="StockId" value="<?php    echo $StockId?>"></td>
  </tr>
  <tr align="center">
    <td height="50" class="A0111"><?php echo $Forshort?></td>
<!--    <td class="A0101">--><?php //   echo $OrderPO?><!--</td>-->
    <td class="A0101"><?php    echo $ProductId?></td>
    <td class="A0101"><?php    echo $cName?></td>
    <td class="A0101"><?php    echo $eCode?></td>
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
  
  <tr align="center">
    <td height="61" colspan="5"  align="center" class="A0000" bgcolor="#d6efb5">
    <input name="tmpRelation" type="hidden" id="tmpRelation" value="<?php echo $Relation ?>">
    <input name="Relation" type="text" class="I0000C" id="Relation" value="" size="15"  ><?php echo $Relation ?>数量/每个外箱 <div class="redB">(指每个外箱装的产品数量，跟显示值不同则要更改)</div>
    </td>
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